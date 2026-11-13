<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\AI_assistant\Service;

use OCP\IL10N;
use OCP\TaskProcessing\EShapeType;
use OCP\TaskProcessing\IManager;
use OCP\TaskProcessing\ShapeDescriptor;
use OCP\TaskProcessing\ShapeEnumValue;
use OCP\TaskProcessing\Task;
use OCP\TaskProcessing\TaskTypes\TextToText;
use OCP\TaskProcessing\TaskTypes\TextToTextChat;

/**
 * Resolves the chat model menu and applies a session model to one task.
 * The instance-wide provider preference is never changed.
 */
class ChatModelService {

	public function __construct(
		private IManager $taskProcessingManager,
		private IL10N $l10n,
	) {
	}

	/**
	 * Model menu copied from the task type that already exposes it, preferring the
	 * chat task types in the order a message is scheduled.
	 *
	 * @param array<string, array<string, mixed>> $availableTaskTypes
	 * @return array{shape: ShapeDescriptor, values: list<ShapeEnumValue>, default: ?string}|null
	 */
	public function getChatModelField(array $availableTaskTypes): ?array {
		foreach ($this->primaryChatTaskTypeIds() as $typeId) {
			if (!isset($availableTaskTypes[$typeId])) {
				continue;
			}
			$field = $this->extractModelField($availableTaskTypes[$typeId]);
			if ($field !== null) {
				return $field;
			}
		}

		if (isset($availableTaskTypes[TextToText::ID])) {
			$field = $this->extractModelField($availableTaskTypes[TextToText::ID]);
			if ($field !== null) {
				return $field;
			}
		}

		foreach ($availableTaskTypes as $typeId => $taskType) {
			if (!is_string($typeId) || !str_contains($typeId, 'text')) {
				continue;
			}
			$field = $this->extractModelField($taskType);
			if ($field !== null) {
				return $field;
			}
		}

		return $this->syntheticProviderField($availableTaskTypes);
	}

	/**
	 * @throws BadRequestException
	 */
	public function normalizeModel(?string $model): ?string {
		if ($model === null) {
			return null;
		}
		$model = trim($model);
		if ($model === '') {
			return null;
		}
		if (strlen($model) > 512) {
			throw new BadRequestException($this->l10n->t('The selected model is not available'));
		}
		try {
			$types = $this->taskProcessingManager->getAvailableTaskTypes();
		} catch (\Throwable $e) {
			throw new BadRequestException($this->l10n->t('The selected model is not available'), previous: $e);
		}
		$field = $this->getChatModelField($types);
		if ($field === null || !$this->enumContains($field['values'], $model)) {
			throw new BadRequestException($this->l10n->t('The selected model is not available'));
		}
		return $model;
	}

	/**
	 * Set input['model'] only when this task type accepts it and the value is allowed.
	 * An empty enum means any string is accepted.
	 */
	public function applyModelToInput(string $taskTypeId, array &$input, ?string $model): bool {
		if ($model === null || $model === '') {
			return false;
		}
		try {
			$types = $this->taskProcessingManager->getAvailableTaskTypes();
		} catch (\Throwable $e) {
			return false;
		}
		$taskType = $types[$taskTypeId] ?? null;
		if (!is_array($taskType)) {
			return false;
		}
		$shape = $taskType['optionalInputShape']['model'] ?? null;
		if (!$shape instanceof ShapeDescriptor) {
			return false;
		}
		$enumValues = $taskType['optionalInputShapeEnumValues']['model'] ?? [];
		if (is_array($enumValues) && $enumValues !== [] && !$this->enumContains($enumValues, $model)) {
			return false;
		}
		$input['model'] = $model;
		return true;
	}

	/**
	 * Prefer the provider that corresponds to the selected model for this task only.
	 */
	public function applyPreferredProvider(Task $task, string $model): void {
		if ($model === '' || !method_exists($task, 'setPreferredProvider')) {
			return;
		}
		if (!method_exists($this->taskProcessingManager, 'getProviders')) {
			return;
		}
		try {
			$providers = $this->taskProcessingManager->getProviders();
		} catch (\Throwable $e) {
			return;
		}
		if (!is_array($providers)) {
			return;
		}
		foreach ($providers as $provider) {
			if (!is_object($provider) || !method_exists($provider, 'getTaskTypeId') || !method_exists($provider, 'getId')) {
				continue;
			}
			if ($provider->getTaskTypeId() !== $task->getTaskTypeId()) {
				continue;
			}
			if ($this->providerMatchesModel($provider, $model)) {
				$task->setPreferredProvider((string)$provider->getId());
				return;
			}
		}
	}

	/**
	 * @return list<string>
	 */
	private function primaryChatTaskTypeIds(): array {
		$ids = [];
		if (class_exists('OCP\\TaskProcessing\\TaskTypes\\MultimodalContextAgentInteraction')) {
			/** @psalm-suppress UndefinedClass */
			$ids[] = \OCP\TaskProcessing\TaskTypes\MultimodalContextAgentInteraction::ID;
		}
		if (class_exists('OCP\\TaskProcessing\\TaskTypes\\ContextAgentInteraction')) {
			/** @psalm-suppress UndefinedClass */
			$ids[] = \OCP\TaskProcessing\TaskTypes\ContextAgentInteraction::ID;
		}
		if (class_exists('OCP\\TaskProcessing\\TaskTypes\\MultimodalChatWithTools')) {
			/** @psalm-suppress UndefinedClass */
			$ids[] = \OCP\TaskProcessing\TaskTypes\MultimodalChatWithTools::ID;
		}
		$ids[] = TextToTextChat::ID;
		if (class_exists('OCP\\TaskProcessing\\TaskTypes\\TextToTextChatWithTools')) {
			/** @psalm-suppress UndefinedClass */
			$ids[] = \OCP\TaskProcessing\TaskTypes\TextToTextChatWithTools::ID;
		}
		return $ids;
	}

	/**
	 * @param array<string, mixed> $taskType
	 * @return array{shape: ShapeDescriptor, values: list<ShapeEnumValue>, default: ?string}|null
	 */
	private function extractModelField(array $taskType): ?array {
		$shape = $taskType['optionalInputShape']['model'] ?? null;
		if (!$shape instanceof ShapeDescriptor || $shape->getShapeType() !== EShapeType::Enum) {
			return null;
		}
		$rawValues = $taskType['optionalInputShapeEnumValues']['model'] ?? [];
		if (!is_array($rawValues) || $rawValues === []) {
			return null;
		}
		$values = [];
		foreach ($rawValues as $rawValue) {
			if ($rawValue instanceof ShapeEnumValue) {
				if ($rawValue->getValue() !== '') {
					$values[] = $rawValue;
				}
				continue;
			}
			if (is_array($rawValue) && isset($rawValue['value']) && (string)$rawValue['value'] !== '') {
				$values[] = new ShapeEnumValue(
					(string)($rawValue['name'] ?? $rawValue['value']),
					(string)$rawValue['value'],
				);
			}
		}
		if ($values === []) {
			return null;
		}
		$default = $taskType['optionalInputShapeDefaults']['model'] ?? null;
		if (!is_string($default) || $default === '' || !$this->enumContains($values, $default)) {
			$default = null;
		}
		return [
			'shape' => $shape,
			'values' => $values,
			'default' => $default,
		];
	}

	/**
	 * One provider per model, with no model enum on the task type itself.
	 *
	 * @param array<string, array<string, mixed>> $availableTaskTypes
	 * @return array{shape: ShapeDescriptor, values: list<ShapeEnumValue>, default: ?string}|null
	 */
	private function syntheticProviderField(array $availableTaskTypes): ?array {
		if (!method_exists($this->taskProcessingManager, 'getProviders')) {
			return null;
		}
		try {
			$providers = $this->taskProcessingManager->getProviders();
		} catch (\Throwable $e) {
			return null;
		}
		if (!is_array($providers)) {
			return null;
		}
		$primary = $this->primaryChatTaskTypeIds();
		$values = [];
		$seen = [];
		foreach ($providers as $provider) {
			if (!is_object($provider) || !method_exists($provider, 'getTaskTypeId') || !method_exists($provider, 'getId') || !method_exists($provider, 'getName')) {
				continue;
			}
			$typeId = (string)$provider->getTaskTypeId();
			if (!in_array($typeId, $primary, true) || !isset($availableTaskTypes[$typeId])) {
				continue;
			}
			$id = (string)$provider->getId();
			if ($id === '' || isset($seen[$id])) {
				continue;
			}
			$seen[$id] = true;
			$name = trim((string)$provider->getName());
			$values[] = new ShapeEnumValue($name !== '' ? $name : $id, $id);
		}
		if (count($values) < 2) {
			return null;
		}
		return [
			'shape' => new ShapeDescriptor(
				$this->l10n->t('Model'),
				$this->l10n->t('The model used to generate the completion'),
				EShapeType::Enum,
			),
			'values' => $values,
			'default' => null,
		];
	}

	/**
	 * @param list<ShapeEnumValue|array{value?: string}> $values
	 */
	private function enumContains(array $values, string $model): bool {
		foreach ($values as $value) {
			if ($value instanceof ShapeEnumValue && $value->getValue() === $model) {
				return true;
			}
			if (is_array($value) && isset($value['value']) && (string)$value['value'] === $model) {
				return true;
			}
		}
		return false;
	}

	private function providerMatchesModel(object $provider, string $model): bool {
		$id = method_exists($provider, 'getId') ? (string)$provider->getId() : '';
		$name = method_exists($provider, 'getName') ? (string)$provider->getName() : '';
		if ($id === $model || $name === $model) {
			return true;
		}
		// Require a separator so "gpt-4" does not match "gpt-4o".
		foreach ([':', '/'] as $separator) {
			if ($id !== '' && str_ends_with($id, $separator . $model)) {
				return true;
			}
		}
		if ($name !== '' && (
			str_ends_with($name, ' ' . $model)
			|| str_ends_with($name, ' - ' . $model)
			|| str_ends_with($name, ': ' . $model)
			|| str_ends_with($name, ' (' . $model . ')')
		)) {
			return true;
		}
		return false;
	}
}

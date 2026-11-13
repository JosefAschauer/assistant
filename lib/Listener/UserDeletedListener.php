<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\AI_assistant\Listener;

use OCA\AI_assistant\Service\AssignmentsService;
use OCA\AI_assistant\Service\ChatService;
use OCA\AI_assistant\Service\InternalException;
use OCA\AI_assistant\Service\SessionSummaryService;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\UserDeletedEvent;
use Psr\Log\LoggerInterface;

/**
 * @template-implements IEventListener<UserDeletedEvent>
 */
class UserDeletedListener implements IEventListener {

	public function __construct(
		private ChatService $chatService,
		private AssignmentsService $assignmentsService,
		private LoggerInterface $logger,
		private SessionSummaryService $sessionSummaryService,
	) {
	}

	public function handle(Event $event): void {
		if (!($event instanceof UserDeletedEvent)) {
			return;
		}

		$userId = $event->getUid();

		try {
			$this->assignmentsService->deleteAllForUser($userId);
		} catch (InternalException $e) {
			$this->logger->error('Error while deleting assignments for user ' . $userId, ['exception' => $e]);
		}

		try {
			$this->chatService->deleteAllUserChatData($userId);
		} catch (InternalException $e) {
			$this->logger->error('Error while deleting chat data for user ' . $userId, ['exception' => $e]);
		}

		try {
			$this->sessionSummaryService->deleteSummaryJobsForUser($userId);
		} catch (InternalException $e) {
			$this->logger->error('Error while deleting summary jobs for user ' . $userId, ['exception' => $e]);
		}
	}
}

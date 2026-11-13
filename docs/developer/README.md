<!--
  - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Nextcloud Custom Assistant developer doc

This fork's app id is `ai_assistant`. The PHP namespace is `OCA\AI_assistant`. It does not register `OCA.Assistant` or `OCA.TPAssistant`, so the stock assistant can stay enabled and keep those names. This app registers `OCA.AIAssistant`.

OCS routes live under `/ocs/v2.php/apps/ai_assistant/`. The stock app keeps `/ocs/v2.php/apps/assistant/`.

This documentation is about [integrating this fork](./web-integration.md) in the frontend of other Nextcloud applications
and about using the [OCS API](./ocs-api.md) to support its features in your application or client. Chat model selection is described in the [user documentation](../user/README.md#choose-a-model).


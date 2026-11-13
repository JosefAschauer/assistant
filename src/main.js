/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import {
	handleNotification, addAssistantMenuEntry,
	openAssistantForm,
	openAssistantTask,
} from './assistant.js'
import { subscribe } from '@nextcloud/event-bus'
import { loadState } from '@nextcloud/initial-state'

/**
 * - Expose OCA.AIAssistant.openAssistantForm to let apps use the assistant
 * - Add a header right menu entry
 * - Listen to notification event
 */
function init() {
	if (!OCA.AIAssistant) {
		/**
		 * @namespace
		 */
		OCA.AIAssistant = {
			openAssistantForm,
			openAssistantTask,
		}

		subscribe('notifications:action:execute', handleNotification)
		if (loadState('ai_assistant', 'assistant-enabled')) {
			addAssistantMenuEntry()
			OCA.AIAssistant.last_target_language = loadState('ai_assistant', 'last-target-language')
		}
	}
}

init()

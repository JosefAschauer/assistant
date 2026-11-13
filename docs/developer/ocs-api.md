<!--
  - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Using the Nextcloud Custom Assistant OCS API

This fork's OCS API allows you to run/schedule tasks, get the task history, manage tasks, and store a chat model on a conversation. Paths use the app id `ai_assistant`. The stock assistant keeps its own API under `assistant`.

This API is documented with OpenAPI. You can view it with the [ocs_api_viewer app](https://apps.nextcloud.com/apps/ocs_api_viewer):
* install the ocs_api_viewer app
* install this fork (`ai_assistant`)
* browse the ocs_api_viewer app
* select "ai_assistant" in the left sidebar

## Chat model

A chat session includes `model`, a string or `null`.

* `POST /ocs/v2.php/apps/ai_assistant/chat/sessions` accepts `model`. Omit it, or send an empty string, to create a conversation with no saved model.
* `PUT /ocs/v2.php/apps/ai_assistant/chat/sessions/{sessionId}` changes `model` only when the JSON body contains the key. An empty string clears it. A body that only updates the title or `is_remembered` leaves the model as it is.
* A value longer than 512 characters, or a value that is not in the current model menu, is rejected.

Scheduling a reply does not write the instance-wide provider preference. The saved model is applied to that task only. See the [user documentation](../user/README.md#choose-a-model) for when the menu exists and when a Context Agent reply does not follow the selection.

For the moment we have different endpoints for Text Processing, Speech to text and Image generation.
This will be unified in the near future so there might be breaking changes to this API.

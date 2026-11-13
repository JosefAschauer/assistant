<!--
  - SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# User documentation

## Personal settings

The Assistant personal settings can be found under the "Artificial intelligence" section.
You can disable the assistant top menu entry there. You can also disable the AI-related smart pickers.

## Assistant

### Run a task

To run a task with this fork, open **Nextcloud Custom Assistant** via its header icon (on the top right). The stock **Nextcloud Assistant** icon, when that app is enabled, opens the other app.

![top right icon](../img/top.right.button.png)

Then choose a task type at the top of the assistant.
Then fill up the input form and press the submit button at the bottom right.

![assistant](../img/assistant.png)

You task will run immediately if possible or be scheduled for later execution.

![getting results](../img/getting.results.png)

### Notifications

If the task was scheduled, you can request to receive a notification when it has finished.
You can display the task results by clicking the "View results" button in the notification.

![notifications](../img/notifications.png)

### History

On the left side of the assistant, there is a task history list.
This task history is filtered to only show tasks of the currently selected task type.
You can then relaunch previous tasks, delete them or just cancel them if they were scheduled.

![task history](../img/history.png)

### Chat with AI

In the *Chat with AI* tab of the Assistant, you can have a back-and-forth conversation with the connected AI. Just type a message into the bottom text field, hit enter and you will get a response. 
You can have different conversations, click on *New conversation* to open a new one.

![chait with ai](../img/chat.with.ai.png)

Every conversation has its own context, but you can use the "Remember this" toggle to add a conversation to the long-term memory of the AI. The context of remembered conversations is then available in any other conversation. To reverse this and delete a conversation from memory, turn the toggle off again. That toggle does not change the model selected for the conversation.

### Choose a model

Chat with AI uses the same model control as the other canvases, such as Work with text. At the bottom right of the conversation, open the three-dot menu and choose **Show advanced options**. The model field is there.

The selected model is kept on that conversation only. It is not an instance-wide provider setting. A new conversation starts without a saved model until you pick one. Clear the field to drop the saved model.

The three-dot menu appears when a model list is available: from the chat task type, from Work with text, from another text task type, or from the installed chat providers when there are at least two and no task type offers a list. With a single provider and no list, there is no menu.

When the Context Agent runs the reply, its inner language-model tasks are scheduled by that app. The conversation model is then used only if the agency task accepts a model input, or if one of its providers matches the selection.

In the Assistant page of your Personal settings, you can find a list of all your remembered conversations.

![remembered conversations](../img/remembered.conversations.png)


## Smart picker

The assistant app provides 3 smart pickers that you can access in Talk,
Text and in every place where rich text edition is possible.
To use them, type "/" and then "ai". They will appear in the filtered provider list.

In Talk:

![smart picker in talk](../img/smart.picker.talk.png)

In Text:

![smart picker in text](../img/smart.picker.in.text.png)

This will open the known Assistant view, and any generated result can be directly inserted in the current context.

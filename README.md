<!--
  - SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Nextcloud Custom Assistant

Fork of [Nextcloud Assistant](https://github.com/nextcloud/assistant). This is not the official app. License: AGPL-3.0-or-later. Original copyright notices stay in place.

It can be installed beside the stock assistant. The app id is `ai_assistant`. The header icon is named **Nextcloud Custom Assistant**, so the two icons can be told apart.

## Choose a chat model

In **Chat with AI**, the model is chosen the same way as on the other canvases, such as Work with text:

1. Open **Nextcloud Custom Assistant** from the header.
2. Select **Chat with AI**.
3. At the bottom right, open the three-dot menu.
4. Choose **Show advanced options**.
5. Pick a model.

The choice is stored on that conversation. Later messages in the same conversation use it. Another conversation keeps its own choice. Clearing the field stores no model, and the conversation then follows the administrator's provider choice again. The **Remember this** toggle does not change the selected model.

The three-dot menu is shown only when there is a model list. That list is taken from the chat task type that already exposes one, then from Work with text, then from another text task type. If none of those has a list, and at least two chat providers are installed, the menu lists those providers. One provider and no model list means there is nothing to choose, so the menu stays hidden.

The choice does not change the instance-wide provider preference. For each reply, the model is passed as the task input `model` when that task type accepts it. Otherwise the matching provider for that task type is preferred for that task only. A shorter name does not match a longer one: `gpt-4` does not select `gpt-4o`.

If a reply is handled by the Context Agent, the inner language-model tasks are scheduled by the `context_agent` app. The conversation model then applies only when the agency task type accepts `model`, or when a provider id or name for that task matches the selection.

Details are in the [user documentation](docs/user/README.md) and the [developer documentation](docs/developer/README.md).

This app brings a user interface to use the Nextcloud text processing feature.

It allows users to launch AI tasks, be notified when they finish and see the results.
The assistant also appears in others apps like Text to easily process parts of a document.

More details on this fork's OCS API and frontend integration are in the
[developer doc](docs/developer/README.md). The stock assistant keeps its own API under the app id `assistant`.

### How to use it

The **Nextcloud Custom Assistant** header icon opens this fork. Once clicked, the assistant is displayed and you can select a task type and
set the input you want to process. If the stock assistant is also enabled, its own header icon stays **Nextcloud Assistant**.

The task might run immediately or be scheduled depending on the time estimation given by the AI provider.
Once a task is scheduled, it will run as a background job. When it is finished, you will receive a notification
from which the results can be displayed.

Other apps can integrate with the assistant. For example, Text will display an inline button besides every paragraph
to directly select a task type to process this paragraph. Selecting a task this way will open the assistant with the task
being pre-selected and the input text set.

More details and screenshots are in the [user doc](docs/user/README.md).

## Features

In the assistant, the list of available tasks depends on the available providers installed via other apps.
This means you have complete freedom over which service/software will actually run your AI tasks.

### Text processing

So far, the [Local Large language model](https://github.com/nextcloud/llm2#readme)
and the [OpenAi/LocalAI integration](https://apps.nextcloud.com/apps/integration_openai) apps
include text processing providers to:
* Summarize
* Extract topics
* Generate a headline
* Get an answer from a free prompt
* Reformulate (OpenAi/LocalAi only)
* Context writer: Generate text with a specified style. The style can be described or provided via an example text.
* Chat with AI

### Text to image (Image generation)

Known providers:
* [OpenAi/LocalAI integration](https://apps.nextcloud.com/apps/integration_openai)
* [Text2Image Stable Diffusion](https://apps.nextcloud.com/apps/text2image_stablediffusion)

### Speech to text (Audio transcription)

Known providers:
* [OpenAi/LocalAI integration](https://apps.nextcloud.com/apps/integration_openai)
* [Local Whisper Speech-To-Text](https://apps.nextcloud.com/apps/stt_whisper)

More details on how to set this up in the [admin docs](https://docs.nextcloud.com/server/latest/admin_manual/ai/index.html)

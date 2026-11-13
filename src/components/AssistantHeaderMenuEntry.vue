<!--
  - SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<NcHeaderButton
		id="ai-assistant"
		:aria-label="label"
		:description="label"
		:title="label"
		@click="$emit('click')">
		<template #icon>
			<AssistantIcon :size="20" />
		</template>
	</NcHeaderButton>
</template>

<script>
import AssistantIcon from './icons/AssistantIcon.vue'

import NcHeaderButton from '@nextcloud/vue/components/NcHeaderButton'

export default {
	name: 'AssistantHeaderMenuEntry',
	components: {
		AssistantIcon,
		NcHeaderButton,
	},
	emits: ['click'],
	computed: {
		label() {
			return t('assistant', 'Nextcloud Custom Assistant')
		},
	},
}
</script>

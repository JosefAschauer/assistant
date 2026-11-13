<?php

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\AI_assistant\Tests;

use OCA\AI_assistant\AppInfo\Application;

class TextProcessingServiceTest extends \PHPUnit\Framework\TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('ai_assistant', $app::APP_ID);
	}
}

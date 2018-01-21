<?php

namespace ComposePress\Settings;

use ComposePress\Core\Abstracts\Plugin;

class PluginMock extends Plugin {
	public $safe_slug = 'testing_plugin';

	public function __construct() {
	}

	/**
	 * @return void
	 */
	public function activate() {
		// TODO: Implement activate() method.
	}

	/**
	 * @return void
	 */
	public function deactivate() {
		// TODO: Implement deactivate() method.
	}

	/**
	 * @return void
	 */
	public function uninstall() {
		// TODO: Implement uninstall() method.
	}
}
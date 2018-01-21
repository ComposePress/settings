<?php

namespace ComposePress\Settings\Managers;

use ComposePress\Core\Abstracts\Manager;

class Field extends Manager {
	const MODULE_NAMESPACE = '\ComposePress\Settings\UI\Fields';
	protected $modules = [
		'Checkbox',
		'ColorPicker',
		'File',
		'Html',
		'Text',
	];
}
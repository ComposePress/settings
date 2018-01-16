<?php

namespace ComposePress\Core\Settings\Managers;

use ComposePress\Core\Abstracts\Manager;

class Field extends Manager {
	const MODULE_NAMESPACE = '\ComposePress\Core\Settings\UI\Fields';
	protected $modules = [ 'Text' ];
}
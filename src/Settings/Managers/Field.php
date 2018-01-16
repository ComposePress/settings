<?php

namespace ComposePress\Settings\Managers;

use ComposePress\Settings\Abstracts\Manager;

class Field extends Manager {
	const MODULE_NAMESPACE = '\ComposePress\Settings\UI\Fields';
	protected $modules = [ 'Text' ];
}
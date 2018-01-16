<?php

namespace pcfreak30\ComposePress\Settings\Managers;

use pcfreak30\ComposePress\Abstracts\Manager;

class Field extends Manager {
	const MODULE_NAMESPACE = '\pcfreak30\ComposePress\Settings\UI\Fields';
	protected $modules = [ 'Text' ];
}
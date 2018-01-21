<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Html extends FieldType {
	const NAME = 'html';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		echo self::get_description( $params );
	}

}
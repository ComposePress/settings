<?php

namespace ComposePress\Settings\UI\Fields;

use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Checkbox extends FieldType {
	const NAME = 'checkbox';

	public static function render( Field $field ) {
		parent::render( $field );
		$params = $field->args;
		$value  = esc_attr( self::get_value( $field ) );

		$html = '<fieldset>';
		$html .= sprintf( '<label for="%1$s">', $field->name );
		$html .= sprintf( '<input type="hidden" name="%1$s" value="0" />', $field->name );
		$html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s" name="%1$s" value="1" %3$s />', $field->name, $field->name, checked( $value, '1', false ) );
		$html .= sprintf( '%1$s</label>', self::get_description( $params, false ) );
		$html .= '</fieldset>';

		echo $html;
	}

}
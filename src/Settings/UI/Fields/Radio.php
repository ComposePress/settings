<?php

namespace ComposePress\Settings\UI\Fields;

use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Radio extends FieldType {
	const NAME = 'radio';

	public static function render( Field $field ) {
		parent::render( $field );
		$params = $field->args;
		$value  = esc_attr( self::get_value( $field ) );


		$html = '<fieldset>';
		foreach ( $params['options'] as $key => $label ) {
			$html .= sprintf( '<label for="%1$s">', $field->name );
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s" name="%1$s" value="%2$s" %3$s />', $field->name, $key, checked( $value, $key, false ) );
			$html .= sprintf( '%1$s</label><br>', $label );
		}
		$html .= '</fieldset>';

		$html .= self::get_description( $params );
		echo $html;
	}

}
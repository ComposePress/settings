<?php

namespace ComposePress\Settings\UI\Fields;

use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Select extends FieldType {
	const NAME = 'select';

	public static function render( Field $field ) {
		parent::render( $field );
		$params = $field->args;
		$value  = esc_attr( self::get_value( $field ) );

		$size    = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$default = isset( $params['default'] );
		$html    = sprintf( '<select class="%1$s" name="%2$s" id="%2$s">', $size, $field->name );
		if ( $default ) {
			if ( ! is_string( $default ) ) {
				$default = __( '-- Select --', $field->plugin->safe_slug );
			}
			$params['options'] = array_merge( [ '' => $default ], $params['options'] );
		}
		foreach ( $params['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
		}

		$html .= '</select>';
		$html .= self::get_description( $params );
		echo $html;
	}

}
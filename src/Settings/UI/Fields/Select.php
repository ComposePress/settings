<?php

namespace ComposePress\Settings\UI\Fields;

use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Select extends FieldType {
	const NAME = 'select';

	public static function render( Field $field ) {
		parent::render( $field );
		$params = $field->args;
		$value  = self::get_value( $field );
		if ( ! empty( $value ) ) {
			$value = (array) $value;
			$value = array_map( 'esc_attr', $value );
			$value = array_flip( $value );
		}


		$size     = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$default  = isset( $params['default'] );
		$multiple = isset( $params['multiple'] ) && true === $params['multiple'] ? 'multiple' : '';
		$html     = sprintf( '<select class="%1$s" name="%2$s" id="%2$s" %3$s>', $size, $field->name, $multiple );
		if ( $default ) {
			if ( ! is_string( $default ) ) {
				$default = __( '-- Select --', $field->plugin->safe_slug );
			}
			$params['options'] = array_merge( [ '' => $default ], $params['options'] );
		}
		foreach ( $params['options'] as $key => $label ) {
			$selected = isset( $value[ $key ] ) ? $key : false;
			$html     .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $selected, $key, false ), $label );
		}

		$html .= '</select>';
		$html .= self::get_description( $params );
		echo $html;
	}

}
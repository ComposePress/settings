<?php

namespace ComposePress\Core\Settings\UI\Fields;

use ComposePress\Core\Settings\Abstracts\FieldType;
use ComposePress\Core\Settings\UI\Field;

class Text extends FieldType {
	const NAME = 'text';

	public static function render( Field $field ) {
		parent::render( $field );
		$params      = $field->args;
		$value       = esc_attr( self::get_value( $field ) );
		$size        = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$placeholder = empty( $params['placeholder'] ) ? '' : ' placeholder="' . $params['placeholder'] . '"';

		$html = sprintf( '<input class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"%4$s/>', $size, $field->name, $value, $placeholder );
		$html .= self::get_description( $params );

		echo $html;
	}

}
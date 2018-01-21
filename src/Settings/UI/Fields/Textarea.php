<?php

namespace ComposePress\Settings\UI\Fields;

use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Textarea extends FieldType {
	const NAME = 'textarea';

	public static function render( Field $field ) {
		parent::render( $field );
		$params      = $field->args;
		$value       = esc_textarea( self::get_value( $field ) );
		$size        = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$placeholder = empty( $params['placeholder'] ) ? '' : ' placeholder="' . $params['placeholder'] . '"';
		$rows        = null !== $params['rows'] ? $params['rows'] : 5;
		$cols        = null !== $params['cols'] ? $params['cols'] : 55;

		$html = sprintf( '<textarea rows="%1$d" cols="%2$d" class="%2$s-text" id="%3$s" name="%3$s" value="%4$s"%5$s></textarea>', $rows, $cols, $size, $field->name, $value, $placeholder );

		$html .= self::get_description( $params );

		echo $html;
	}

}
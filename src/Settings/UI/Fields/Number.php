<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Number extends FieldType {
	const NAME = 'number';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;
		$value  = esc_attr( self::get_value( $field ) );

		$size        = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$type        = isset( $params['type'] ) ? $params['type'] : 'number';
		$placeholder = empty( $params['placeholder'] ) ? '' : ' placeholder="' . $params['placeholder'] . '"';
		$min         = empty( $params['min'] ) ? '' : ' min="' . $params['min'] . '"';
		$max         = empty( $params['max'] ) ? '' : ' max="' . $params['max'] . '"';
		$step        = empty( $params['max'] ) ? '' : ' step="' . $params['step'] . '"';

		$html = sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s" name="%3$s" value="%4$s"%5$s%6$s%7$s%8$s/>', $type, $size, $field->name, $value, $placeholder, $min, $max, $step );
		$html .= self::get_description( $params );

		echo $html;
	}
}
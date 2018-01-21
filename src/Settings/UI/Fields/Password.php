<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Password extends FieldType {
	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;
		$value  = esc_attr( self::get_value( $field ) );
		$size   = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';

		$html = sprintf( '<input type="password" class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"/>', $size, $field->name, $value );
		$html .= self::get_description( $params );

		echo $html;
	}

}
<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class File extends FieldType {
	const NAME = 'file';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		$value = esc_attr( self::get_value( $field ) );
		$size  = isset( $params['size'] ) && ! null === $params['size'] ? $params['size'] : 'regular';
		$label = isset( $params['options']['button_label'] ) ? $params['options']['button_label'] : __( 'Choose File' );

		$html = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s" name="%2$s" value="%4$s"/>', $size, $field->name, $field->name, $value );
		$html .= sprintf( '<input type="button" class="button wpsa-browse" value="%s" />', $label );
		$html .= self::get_description( $params );

		echo $html;
	}
}
<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Image extends FieldType {
	const NAME = 'image';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		$value = esc_attr( self::get_value( $field ) );
		$size  = isset( $params['size'] ) && ! null === $params['size'] ? $params['size'] : 'regular';
		$label = isset( $params['options']['button_label'] ) ? $params['options']['button_label'] : __( 'Choose Image' );

		$img     = wp_get_attachment_image_src( $value );
		$img_url = $img ? $img[0] : '';
		$html    = sprintf( '<input type="hidden" class="%1$s-text wpsa-image-id" id="%2$s" name="%2$s" value="%3$s"/>', $size, $field->name, $value );
		$html    .= sprintf( '<p class="wpsa-image-preview"><img src="%1$s" /></p>', $img_url );

		$html .= sprintf( '<input type="button" class="button wpsa-image-browse" value="%s" />', $label );
		$html .= self::get_description( $params );

		echo $html;
	}
}
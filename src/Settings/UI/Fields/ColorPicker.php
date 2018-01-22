<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class ColorPicker extends FieldType {
	const NAME = 'color_picker';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		$value = esc_attr( self::get_value( $field ) );
		$size  = isset( $params['size'] ) && ! null === $params['size'] ? $params['size'] : 'regular';
		$html  = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s" name="%2$s" value="%4$s" data-default-color="%5$s" />', $size, $field->name, $field->name, $value, $params['std'] );
		$html  .= self::get_description( $params );

		echo $html;
	}

	public static function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_add_inline_script( 'wp-color-picker', '(function(){
			$(function(){
					$(\'.wp-color-picker-field\').wpColorPicker();
			})
		})(jQuery)' );
	}

}
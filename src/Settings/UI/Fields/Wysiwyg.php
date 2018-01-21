<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Wysiwyg extends FieldType {
	const NAME = 'wysiwyg';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;


		$value = self::get_value( $field );
		$size  = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : '500px';

		$html = sprintf( '<div style="max-width: %1$s;">', $size );

		$editor_settings = [
			'teeny'         => true,
			'textarea_name' => $field->name,
			'textarea_rows' => 10,
		];

		if ( isset( $params['options'] ) && is_array( $params['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $params['options'] );
		}
		ob_start();

		wp_editor( $value, $field->name, $editor_settings );

		$html .= ob_get_clean();
		$html .= '</div>';

		echo $html;
	}
}
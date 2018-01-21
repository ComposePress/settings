<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class MultiCheckbox extends FieldType {
	const NAME = 'multi_checkbox';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;
		$value  = self::get_value( $field );
		if ( ! empty( $value ) ) {
			$value = array_flip( $value );
		}
		$html = '<fieldset>';
		foreach ( (array) $params['options'] as $key => $label ) {
			$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
			$html    .= '<label>';
			$html    .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s" name="%1$s[]" value="%2$s" %3$s />', $field->name, $key, checked( $checked, $key, false ) );
			$html    .= sprintf( '%1$s</label><br>', $label );
		}

		$html .= self::get_description( $params );
		$html .= '</fieldset>';

		echo $html;
	}
}
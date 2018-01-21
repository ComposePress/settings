<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Pages extends FieldType {
	const NAME = 'pages';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		$options       = isset( $params['options'] ) && null !== $params['options'] ? $params['options'] : [];
		$dropdown_args = array(
			'selected' => esc_attr( self::get_value( $field ) ),
			'name'     => $field->name,
			'id'       => $field->name,
			'echo'     => 0,
		);

		$dropdown_args = array_merge( $dropdown_args, $options );

		$html = wp_dropdown_pages( $dropdown_args );

		echo $html;
	}

}
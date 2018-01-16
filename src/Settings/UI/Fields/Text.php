<?php

namespace pcfreak30\ComposePress\Settings\UI\Fields;

use pcfreak30\ComposePress\Settings\Abstracts\FieldType;
use pcfreak30\ComposePress\Settings\UI\Field;

class Text extends FieldType {
	const NAME = 'text';

	public static function render( Field $field ) {
		parent::render( $field );
		$params      = $field->args;
		$value       = esc_attr( self::$instance->get_value( $field->page->name, $field->name ) );
		$size        = isset( $params['size'] ) && null !== $params['size'] ? $params['size'] : 'regular';
		$placeholder = empty( $params['placeholder'] ) ? '' : ' placeholder="' . $params['placeholder'] . '"';

		$html = sprintf( '<input class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"%4$s/>', $size, $field->name, $value, $placeholder );
		$html .= self::get_description( $params );

		echo $html;
	}

}
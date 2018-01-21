<?php

namespace ComposePress\Settings\Abstracts;

use ComposePress\Core\Abstracts\Component;
use ComposePress\Settings\Registry;
use ComposePress\Settings\UI\Field;

abstract class FieldType extends Component {

	const NAME = '';

	/**
	 * @param string $page
	 * @param array  $params
	 *
	 * @return void
	 */
	public static function render( Field $field ) {
		if ( ! empty( $params['script'] ) ) {
			echo '(function($){' . $params['script'] . '})(jQuery);';
		}
	}

	public static function enqueue_scripts() {
	}

	/**
	 * @param string                                $option_value
	 * @param string                                $option_slug
	 * @param \ComposePress\Settings\Abstracts\Page $page
	 *
	 * @return mixed
	 */
	public static function sanitize( $option_value, $option_slug, Page $page ) {
		return $option_value;
	}

	protected static function get_description( $params, $html = true ) {
		$desc = '';
		if ( ! empty( $params['desc'] ) ) {
			$desc = $params['desc'];
			if ( $html ) {
				$desc = sprintf( '<p class="description">%s</p>', $desc );
			}
		}

		return $desc;
	}

	protected static function get_value( Field $field ) {
		return Registry::get_page( $field->plugin->safe_slug, $field->page->name, $field->name );
	}

	/**
	 *
	 */
	public function init() {
		add_action( $this->plugin->safe_slug . '_admin_ui_field_' . static::NAME, [
			get_called_class(),
			'render',
		], 10 );
		add_action( $this->plugin->safe_slug . '_admin_ui_field_' . static::NAME . '_sanitize', [
			get_called_class(),
			'sanitize',
		], 10, 3 );
		add_action( $this->plugin->safe_slug . '_admin_ui_enqueue_field_js', [ __CLASS__, 'enqueue_scripts' ] );
	}
}
<?php

namespace pcfreak30\ComposePress\Settings\Abstracts;

use pcfreak30\ComposePress\Abstracts\Component;
use pcfreak30\ComposePress\Settings\Registry;
use pcfreak30\ComposePress\Settings\UI\Field;

abstract class FieldType extends Component {

	const NAME = '';

	protected static $instance;

	public function __construct() {
		self::$instance = $this;
	}

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
	 * @param string                                          $option_value
	 * @param string                                          $option_slug
	 * @param \pcfreak30\ComposePress\Settings\Abstracts\Page $page
	 *
	 * @return mixed
	 */
	public static function sanitize( $option_value, $option_slug, Page $page ) {
		return $option_value;
	}

	protected static function get_description( $params ) {
		$desc = '';
		if ( ! empty( $args['desc'] ) ) {
			$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
		}

		return $desc;
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

	protected function get_value( $page, $name ) {
		return Registry::get_page( $this->plugin->safe_slug, $page, $name );
	}
}
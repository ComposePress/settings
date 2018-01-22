<?php

namespace ComposePress\Settings\Abstracts;

use ComposePress\Core\Abstracts\Component;
use ComposePress\Settings\Managers\Field;
use ComposePress\Settings\Managers\Page;
use ComposePress\Settings\Registry;
use ComposePress\Settings\UI\Factory;

/**
 * Class UI
 *
 * @package ComposePress\Settings\Abstracts
 */
abstract class UI extends Component {

	/**
	 * @var string
	 */
	protected $capability = 'manage_options';
	/**
	 * @var string
	 */
	protected $network_capability = 'manage_options';
	/**
	 * @var \ComposePress\Settings\Managers\Page
	 */
	protected $pages_manager;
	/**
	 * @var \ComposePress\Settings\Managers\Field
	 */
	protected $fields_manager;
	/**
	 * @var bool
	 */
	protected $primary_menu = false;
	/**
	 * @var string
	 */
	protected $parent_menu;

	/**
	 * UI constructor.
	 *
	 * @param \ComposePress\Settings\Managers\Page  $pages_manager
	 * @param \ComposePress\Settings\Managers\Field $fields_manager
	 */
	public function __construct( Page $pages_manager, Field $fields_manager ) {
		$this->pages_manager  = $pages_manager;
		$this->fields_manager = $fields_manager;
	}

	/**
	 *
	 */
	public function init() {
		if ( is_admin() ) {
			if ( ! $this->primary_menu && empty( $this->parent_menu ) ) {
				throw new \Exception( sprintf( __( '%s::parent_menu must not be empty if %s::primary_menu is false' ), get_class( $this ), get_class( $this ) ) );
			}
			$this->setup_components();
			$this->setup_menu();
			$this->add_enqueue_scripts();
			$this->register_setting();
			$this->setup_ajax();
		}
	}

	/**
	 *
	 */
	protected function setup_menu() {
		add_action( 'admin_init', [
			$this,
			'settings_init',
		] );
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', [
				$this,
				'settings_init',
			] );

			return;
		}
		if ( ! is_multisite() ) {
			add_action( 'admin_menu', [
				$this,
				'settings_init',
			] );
		}
	}

	/**
	 *
	 */
	public function add_enqueue_scripts() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	protected function register_setting() {
		register_setting( $this->plugin->safe_slug, $this->plugin->safe_slug, [
			'sanitize_callback' => [
				$this,
				'sanitize_options',
			],
		] );
	}

	/**
	 *
	 */
	protected function setup_ajax() {
		add_action( "admin_post_update_{$this->plugin->safe_slug}_settings", [ $this, 'save_settings' ] );
	}

	/**
	 * @param $options
	 *
	 * @return array
	 */
	public function sanitize_options( $options ) {

		if ( ! $options ) {
			return $options;
		}
		$page        = $this->get_current_page();
		$sections    = wp_list_pluck( $page->tabs, 'sections' );
		$sections [] = $page->sections;
		$sections    = call_user_func_array( 'array_merge', $sections );
		foreach ( $options[ $page->name ] as $setting_name => $setting_value ) {
			foreach ( $sections as $section ) {
				foreach ( $section->fields as $field ) {
					if ( $field->name != $setting_name ) {
						continue;
					}
					$options[ $page->name . '.' . $setting_name ] = apply_filters( "jolt_cache_admin_ui_field_{$field->type}_sanitize", $setting_value, $setting_name, $page );
					unset( $options[ $page->name ][ $setting_name ] );
				}
			}
		}

		$options = Registry::undotify( $options );

		return $options;
	}

	/**
	 * @return \ComposePress\Settings\Abstracts\Page|bool
	 */
	protected function get_current_page() {
		$pages   = apply_filters( "{$this->plugin->safe_slug}_admin_ui_pages", [] );
		$page_id = $this->get_current_page_id();

		foreach ( $pages as $page ) {
			if ( $page_id === $page->name || ( $page->name === $this->plugin->safe_slug && 1 === count( $pages ) ) ) {
				return $page;
			}

		}

		return false;
	}

	protected function get_current_page_id() {
		$screen = get_current_screen();
		if ( empty( $screen ) ) {
			return $_GET['page'];
		}

		$page = get_current_screen()->id;
		$page = str_replace( [
			'toplevel_page_',
			'settings_page_',
			$this->plugin->slug . '_page_',
		], '', $page );

		return $page;
	}

	/**
	 *
	 */
	public function settings_init() {
		/** @var \ComposePress\Settings\Abstracts\Page[] $pages */
		$pages = apply_filters( "{$this->plugin->safe_slug}_admin_ui_pages", [] );
		remove_action( 'admin_init', [
			$this,
			'settings_init',
		] );
		remove_action( 'network_admin_menu', [
			$this,
			'settings_init',
		] );

		remove_action( 'admin_menu', [
			$this,
			'settings_init',
		] );
		if ( ! empty( $pages ) ) {
			$cap = apply_filters( "{$this->plugin->safe_slug}_admin_ui_capability", $this->capability );
			if ( is_multisite() ) {
				$cap = apply_filters( "{$this->plugin->safe_slug}_admin_ui_network_capability", $this->capability );
			}

			if ( $this->primary_menu ) {
				add_menu_page( __( $this->plugin->get_plugin_info( 'Name' ), $this->plugin->safe_slug ), __( $this->plugin->get_plugin_info( 'Name' ), $this->plugin->safe_slug ), $cap, $this->plugin->safe_slug, static::get_icon_url(), [
					$this,
					'settings_ui',
				] );
			}
			if ( ! $this->primary_menu ) {
				add_submenu_page( $this->plugin->safe_slug, $this->plugin->get_plugin_info( 'Name' ), $this->plugin->get_plugin_info( 'Name' ), $cap, $this->plugin->safe_slug, [
					$this,
					'settings_ui',
				] );
			}
			$menu = $this->primary_menu ? $this->plugin->safe_slug : $this->parent_menu;
			if ( 1 === count( $pages ) ) {
				$page = end( $pages );
				add_submenu_page( $menu, $page->title, $page->title, $page->capability, $this->plugin->safe_slug, [
					$this,
					'settings_ui',
				] );
				Factory::init( $this->plugin );
				$page->register_settings();
				$page->build_settings();

				return;
			}


			foreach ( $pages as $page ) {
				add_submenu_page( $menu, $page->title, $page->title, $page->capability, $this->plugin->safe_slug . '_' . $page->name, [
					$this,
					'settings_ui',
				] );
			}

			$page = $this->get_current_page();
			if ( ! empty( $page ) ) {
				Factory::init( $this->plugin );
				$page->register_settings();
				$page->build_settings();
			}
		}

	}

	/**
	 * @return string
	 */
	public function get_icon_url() {
		return '';
	}

	/**
	 * @return \ComposePress\Settings\Managers\Page
	 */
	public function get_pages_manager() {
		return $this->pages_manager;
	}

	/**
	 * @param \ComposePress\Settings\Managers\Page $pages_manager
	 */
	public function set_pages_manager( $pages_manager ) {
		$this->pages_manager = $pages_manager;
	}

	/**
	 * @return \ComposePress\Settings\Managers\Field
	 */
	public function get_fields_manager() {
		return $this->fields_manager;
	}

	/**
	 * @param \ComposePress\Settings\Managers\Field $fields_manager
	 */
	public function set_fields_manager( $fields_manager ) {
		$this->fields_manager = $fields_manager;
	}

	/**
	 *
	 */
	public function settings_ui() {
		/** @var \ComposePress\Settings\Abstracts\Page[] $pages */
		$pages           = apply_filters( "{$this->plugin->safe_slug}_admin_ui_pages", [] );
		$current_page_id = $this->get_current_page_id();
		if ( $this->plugin->safe_slug === $current_page_id ) {
			$current_page = null;
			foreach ( $pages as $page ) {
				if ( $page->is_default() ) {
					$current_page = $page;
					break;
				}
			}

			if ( ! $current_page ) {
				/** @var \ComposePress\Settings\Abstracts\Page[] $page_objects */
				$page_objects = array_values( $pages );
				$current_page = $this->plugin->safe_slug . '_' . $page_objects[0]->name;
			}
		}
		$current_page = str_replace( $this->plugin->safe_slug . '_', '', $current_page );
		if ( isset( $pages[ $current_page ] ) ) {
			$pages[ $current_page ]->render();
		}
	}

	/**
	 *
	 */
	public function use_primary_menu() {
		$this->primary_menu = true;
		$this->parent_menu  = null;
	}

	/**
	 * @param $menu
	 */
	public function use_sub_menu( $menu ) {
		$this->primary_menu = false;
		$this->parent_menu  = $menu;
	}

	/**
	 *
	 */
	public function save_settings() {
		global $wp_settings_errors;
		if ( check_ajax_referer( $this->plugin->slug . '_save-settings' ) && ! empty( $_POST['page'] ) ) {
			$whitelist_options = apply_filters( 'whitelist_options', [] );
			$options           = $whitelist_options[ $this->plugin->safe_slug ];
			if ( empty( $options ) ) {
				return;
			}
			$pages = $this->get_pages();
			if ( ! isset( $pages[ $_POST['page'] ] ) ) {
				return;
			}
			/** @var \ComposePress\Settings\Abstracts\Page $page */
			$page               = $pages[ $_POST['page'] ];
			$wp_settings_fields = $this->wp_settings_fields;
			$option             = [ $_POST['page'] => [] ];
			$input_data         = file_get_contents( 'php://input' );
			$pairs              = explode( '&', $input_data );
			foreach ( $pairs as $pair ) {
				$nv    = explode( "=", $pair );
				$name  = urldecode( $nv[0] );
				$value = urldecode( $nv[1] );
				if ( 0 < preg_match( '~' . preg_quote( '[]', '~' ) . '$~', $name ) ) {
					$name            = str_replace( '[]', '', $name );
					$data[ $name ][] = $value;
					continue;
				}
				$data[ $name ] = $value;
			}
			foreach ( $wp_settings_fields[ $page->full_name ] as $section ) {
				foreach ( $section as $field ) {
					if ( isset( $data[ $field['id'] ] ) ) {
						$value                                    = $data[ $field['id'] ];
						$value                                    = wp_unslash( $value );
						$option[ $_POST['page'] ][ $field['id'] ] = $value;
					}
				}
			}

			$wp_settings_errors = [];
			update_option( $this->plugin->safe_slug, $option );

			if ( ! empty( $wp_settings_errors ) ) {
				ob_start();
				settings_errors( $page->get_full_name() );
			} else {
				$_GET['settings-updated'] = 1;
				add_settings_error( $page->get_full_name(), 'settings_updated', __( 'Settings saved.', $this->plugin->safe_slug ), 'updated' );
			}
			ob_start();
			settings_errors( $page->get_full_name() );
			$notifications = ob_get_clean();
			echo wp_json_encode( [ 'notifications' => $notifications ] );
		}
		status_header( 403 );
		exit;
	}

	public function get_pages() {
		return apply_filters( "{$this->plugin->safe_slug}_admin_ui_pages", [] );
	}

	/**
	 *
	 */
	public function enqueue_scripts() {
		do_action( "{$this->plugin->safe_slug}_admin_ui_enqueue_field_js" );
	}
}
<?php


namespace ComposePress;


use ComposePress\Core\Abstracts\Component;
use ComposePress\Settings\Registry;

/**
 * Class Settings
 *
 * @package ComposePress\Settings
 */
class Settings extends Component {

	/**
	 *
	 */
	public function init() {
	}

	/**
	 * @param string $page
	 * @param string $setting
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	public function set( $page, $setting, $value = null ) {
		if ( null == $value ) {
			$value   = $setting;
			$option  = Registry::undotify( [ $page => '' ] );
			$keys    = array_keys( $option );
			$page    = array_shift( $keys );
			$keys    = $option[ $page ];
			$setting = array_shift( $keys );
		}

		return Registry::set_page( $this->plugin->safe_slug, $page, $setting, apply_filters( "update_{$this->plugin->safe_slug}_setting", $value, $setting, $page ) );

	}

	/**
	 * @param string $page
	 * @param string $setting
	 *
	 * @return mixed
	 */
	public function get( $page, $setting = null ) {
		if ( null == $setting ) {
			$option  = Registry::undotify( [ $page => '' ] );
			$keys    = array_keys( $option );
			$page    = array_shift( $keys );
			$keys    = $option[ $page ];
			$setting = array_shift( $keys );
		}

		return apply_filters( "get_{$this->plugin->safe_slug}_setting", Registry::get_page( $this->plugin->safe_slug, $page, $setting ), $setting, $page );
	}
}
<?php


namespace ComposePress\Settings;


class Registry {

	private static $skip_dotify = false;

	public static function get_page( $option_name, $page, $setting ) {
		$option = self::get( $option_name, $page, false );
		if ( empty( $option ) ) {
			return false;
		}

		$option = self::dotify( $option );

		return isset( $option[ $setting ] ) ? $option[ $setting ] : false;
	}

	public static function get( $option, $setting, $dotify = true ) {
		$option = get_option( $option );
		if ( empty( $option ) ) {
			return false;
		}
		if ( $dotify ) {
			$option = self::dotify( $option );
		}

		return isset( $option[ $setting ] ) ? $option[ $setting ] : false;
	}

	public static function dotify( $options, $parent = '' ) {
		if ( self::$skip_dotify ) {
			return $options;
		}

		$result = array();

		foreach ( $options as $key => $value ) {
			$new_key = $parent . ( empty( $parent ) ? '' : '.' ) . $key;

			if ( is_array( $value ) ) {
				/** @noinspection PhpMethodParametersCountMismatchInspection */
				$result += self::dotify( $value, $new_key );
			} else {
				$result[ $new_key ] = $value;
			}
		}

		return $result;
	}

	public static function set_page( $option_name, $page, $setting, $value ) {
		$new_options = [ $page => [ $setting => $value ] ];

		return self::mass_set( $option_name, $new_options );
	}

	public static function mass_set( $option_name, $settings ) {
		$option = get_option( $option_name );

		$new_options = self::undotify( $settings );

		$new_options = array_merge_recursive( $option, $new_options );

		self::$skip_dotify = true;

		$result            = update_option( $option_name, $new_options );
		self::$skip_dotify = false;

		return $result;
	}

	public static function undotify( $options ) {
		$new_options = [];
		foreach ( (array) $options as $option => $value ) {
			$parts = explode( '.', $option );
			$count = substr_count( $option, '.' ) + 1;
			$last  = &$new_options;
			for ( $i = 0; $i < $count; $i ++ ) {
				if ( ! isset( $last[ $parts[ $i ] ] ) ) {
					$last[ $parts[ $i ] ] = [];
				}
				$last = &$last[ $parts[ $i ] ];

				if ( $i + 1 === $count ) {
					$last = $value;
				}
			}
		}

		return $new_options;
	}

	public static function set( $option_name, $setting, $value ) {
		$option = get_option( $option_name );

		$new_options = [ $setting => $value ];

		$new_options = self::undotify( $new_options );

		$new_options = array_merge_recursive( $option, $new_options );

		self::$skip_dotify = true;

		$result            = update_option( $option_name, $new_options );
		self::$skip_dotify = false;

		return $result;
	}
}
<?php


namespace pcfreak30\ComposePress\Settings\UI;


use pcfreak30\ComposePress\Abstracts\Component;
use pcfreak30\ComposePress\Settings\Abstracts\Page;

class Factory {

	/**
	 * @var \pcfreak30\ComposePress\Abstracts\Plugin
	 */
	private static $plugin;

	/**
	 * @param \pcfreak30\ComposePress\Abstracts\Plugin $plugin
	 */
	public static function init( $plugin ) {
		self::$plugin = $plugin;
	}

	/**
	 * @param string                                          $id
	 * @param string                                          $title
	 * @param \pcfreak30\ComposePress\Settings\Abstracts\Page $page
	 * @param null                                            $description
	 * @param bool                                            $tab
	 * @param mixed                                           $callback
	 *
	 * @return mixed
	 */
	public static function section( $id, $title, Component $parent, $description = null, $callback = null ) {

		if ( ! ( $parent instanceof Page ) && ! ( $parent instanceof Tab ) ) {
			throw new \Exception( sprintf( __( '% requires either a valid Page or Tab to connect to', self::$plugin->safe_slug ), __METHOD__ ) );
		}

		/** @var \pcfreak30\ComposePress\Settings\UI\Section $section */
		$section = self::$plugin->container->create( '\pcfreak30\ComposePress\Settings\UI\Section', [
			$id,
			$title,
			$parent,
			$description,
			$callback,
		] );

		$section->parent = $parent;

		$parent->add_section( $section );

		return $section;
	}

	/**
	 * @param string                                          $id
	 * @param string                                          $title
	 * @param \pcfreak30\ComposePress\Settings\Abstracts\Page $page
	 *
	 * @return \pcfreak30\ComposePress\Settings\UI\Tab
	 */
	public static function tab( $id, $title, Page $page ) {
		/** @var \pcfreak30\ComposePress\Settings\UI\Tab $tab */
		$tab         = self::$plugin->container->create( '\pcfreak30\ComposePress\Settings\UI\Tab', [
			$id,
			$title,
		] );
		$tab->parent = $page;

		$page->add_tab( $tab );

		return $tab;
	}

	/**
	 * @param string                                      $name
	 * @param string                                      $title
	 * @param string                                      $type
	 * @param \pcfreak30\ComposePress\Settings\UI\Section $section
	 *
	 * @return mixed
	 */
	public static function field( $name, $title, $type, Section $section, $args = [] ) {
		/** @var \pcfreak30\ComposePress\Settings\UI\Field $field */
		$field         = self::$plugin->container->create( '\pcfreak30\ComposePress\Settings\UI\Field', [
			$name,
			$title,
			$type,
			$section,
			$args,
		] );
		$field->parent = $section;

		$section->add_field( $field );

		return $field;
	}


}
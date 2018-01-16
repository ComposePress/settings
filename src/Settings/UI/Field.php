<?php


namespace ComposePress\Settings\UI;

use ComposePress\Core\Abstracts\Component;
use ComposePress\Settings\Abstracts\Page;


/**
 * Class Field
 *
 * @package ComposePress\Settings\UI
 * @property \ComposePress\Settings\UI\Field   $fields
 * @property string                            $name
 * @property string                            $title
 * @property string                            $type
 * @property array                             $args
 * @property \ComposePress\Settings\UI\Section $parent
 */
class Field extends Component {
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var array
	 */
	protected $args = [];

	/**
	 * @var Page
	 */
	protected $page;

	/**
	 * Section constructor.
	 *
	 * @param  string                             $name
	 * @param  string                             $title
	 * @param  string                             $type
	 * @param   \ComposePress\Settings\UI\Section $section
	 * @param array                               $args
	 */
	public function __construct( $name, $title, $type, $section, $args = [] ) {

		$this->name   = $name;
		$this->title  = $title;
		$this->type   = $type;
		$this->parent = $section;
		$this->args   = $args;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * @return array
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * @param array $args
	 */
	public function set_args( $args ) {
		$this->args = $args;
	}

	/**
	 *
	 */
	public function init() {
		add_settings_field( $this->name, $this->title, [
			$this,
			'render',
		], $this->get_page()->full_name, $this->parent->id );
	}

	/**
	 * @return Component|Page
	 */
	public function get_page() {
		if ( null === $this->page ) {
			$page = $this->parent->parent;
			if ( $page instanceof Tab ) {
				$page = $page->parent;
			}
			$this->page = $page;
		}

		return $this->page;
	}

	/**
	 *
	 */
	public function render() {
		do_action( "{$this->plugin->safe_slug}_admin_ui_field_{$this->type}", $this );
	}
}
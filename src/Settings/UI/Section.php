<?php


namespace ComposePress\Core\Settings\UI;


use ComposePress\Core\Abstracts\Component;

/**
 * Class Section
 *
 * @package ComposePress\Core\Settings\UI
 * @property  string                                     $id
 * @property  string                                     $title
 * @property  callable                                   $callback
 * @property  \ComposePress\Core\Settings\Abstracts\Page $parent
 * @property bool                                        tab
 */
class Section extends Component {
	/**
	 * @var string
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var mixed
	 */
	protected $callback;

	/**
	 * @var \ComposePress\Core\Settings\UI\Field[]
	 */
	protected $fields = [];
	/**
	 * @var null
	 */
	protected $description;

	/**
	 * Section constructor.
	 *
	 * @param string                                 $id
	 * @param string                                 $title
	 * @param \ComposePress\Core\Abstracts\Component $parent
	 * @param string                                 $description
	 * @param callable                               $callback
	 */
	public function __construct( $id, $title, Component $parent, $description = null, $callback = null ) {

		$this->id          = $id;
		$this->title       = $title;
		$this->callback    = $callback;
		$this->description = $description;
		$this->parent      = $parent;
	}

	/**
	 * @return bool
	 */
	public function is_tab() {
		return $this->tab;
	}

	/**
	 * @param bool $tab
	 */
	public function set_tab( $tab ) {
		$this->tab = $tab;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
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
	public function get_callback() {
		return $this->callback;
	}

	/**
	 * @param mixed $callback
	 */
	public function set_callback( $callback ) {
		$this->callback = $callback;
	}

	/**
	 * @return string
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * @param string $page
	 */
	public function set_page( $page ) {
		$this->page = $page;
	}

	/**
	 * @param $field
	 */
	public function add_field( Field $field ) {
		$this->fields[] = $field;
	}

	/**
	 *
	 */
	public function init() {
		if ( null !== $this->description ) {
			$this->description = '<div class="inside">' . $this->description . '</div>';
			$this->callback    = [ $this, 'render_description' ];
		}

		$page = $this->parent;
		if ( $page instanceof Tab ) {
			$page = $page->parent;
		}

		add_settings_section( $this->id, $this->title, $this->callback, $page->full_name );

		foreach ( $this->fields as $field ) {
			$field->init();
		}
	}

	/**
	 * @return \ComposePress\Core\Settings\UI\Field[]
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * @return null
	 */
	public function get_description() {
		return $this->description;
	}

	public function render_description() {
		echo $this->description;
	}
}
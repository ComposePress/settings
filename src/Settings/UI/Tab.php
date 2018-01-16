<?php


namespace ComposePress\Settings\UI;


use ComposePress\Settings\Abstracts\Component;

/**
 * Class Tab
 *
 * @package ComposePress\Settings\UI
 * @property \ComposePress\Settings\UI\Section[] $sections
 */
class Tab extends Component {
	/**
	 * @var string
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var array
	 */
	protected $sections = [];

	/**
	 * Tab constructor.
	 *
	 * @param string $id
	 * @param string $title
	 */
	public function __construct( $id, $title ) {
		$this->id    = $id;
		$this->title = $title;
	}

	/**
	 * @return array
	 */
	public function get_sections() {
		return $this->sections;
	}

	/**
	 * @param array $sections
	 */
	public function set_sections( $sections ) {
		$this->sections = $sections;
	}

	/**
	 * @param \ComposePress\Settings\UI\Section $section
	 */
	public function add_section( Section $section ) {
		$this->sections[] = $section;
	}

	/**
	 *
	 */
	public function init() {
		foreach ( $this->sections as $section ) {
			$section->init();
		}
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
}
<?php

namespace ComposePress\Settings\Abstracts;

use ComposePress\Core\Abstracts\Component;
use ComposePress\Settings\UI\Section;
use ComposePress\Settings\UI\Tab;

/**
 * Class Page
 *
 * @package ComposePress\Settings\Abstracts
 * @property string    $name
 * @property string    $full_name
 * @property string    $title
 * @property string    $capability
 * @property Section[] $sections
 */
abstract class Page extends Component {
	/**
	 *
	 */
	const NAME = '';
	/**
	 *
	 */
	const TITLE = '';
	/**
	 * @var string
	 */
	const CAPABILITY = '';
	/**
	 * @var string
	 */
	const NETWORK_CAPABILITY = '';
	/**
	 * @var \ComposePress\Settings\UI\Section[]
	 */
	protected $sections = [];

	/**
	 * @var \ComposePress\Settings\UI\Tab[]
	 */
	protected $tabs = [];

	/**
	 * @var bool
	 */
	protected $default = false;

	/**
	 * @return \ComposePress\Settings\UI\Section[]
	 */
	public function get_sections() {
		return $this->sections;
	}

	/**
	 *
	 */
	public function init() {
		add_filter( "{$this->plugin->safe_slug}_admin_ui_pages", [ $this, 'register' ] );
	}

	/**
	 * @param $pages
	 *
	 * @return mixed
	 */
	public function register( $pages ) {
		$pages[ $this->get_name() ] = $this;

		return $pages;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return static::NAME;
	}

	/**
	 * @return string
	 */
	public function get_capability() {
		if ( is_network_admin() ) {
			return static::NETWORK_CAPABILITY;
		}

		return static::CAPABILITY;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( static::TITLE, $this->plugin->safe_slug );
	}

	/**
	 * @return mixed
	 */
	public abstract function register_settings();

	/**
	 *
	 */
	public function build_settings() {
		foreach ( $this->sections as $section ) {
			$section->init();
		}
		foreach ( $this->tabs as $tab ) {
			$tab->init();
		}
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 *
	 */
	public function enqueue_scripts() {
		if ( ! ( $this->plugin_page === $this->get_full_name() || $this->plugin_page === $this->plugin->safe_slug ) ) {
			return false;
		}
		global $wp_settings_errors;
		$old_wp_settings_errors = $wp_settings_errors;

		$wp_settings_errors = [];
		add_settings_error( $this->get_full_name(), 'settings_update_failure', __( 'There was an error in saving the settings, please try again.', $this->plugin->safe_slug ), 'updated' );
		ob_start();
		settings_errors( $this->get_full_name() );
		$settings_failure_notification = ob_get_clean();

		$wp_settings_errors = $old_wp_settings_errors;
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		ob_start();
		?>
		<script>
					(function ($) {
						$(function () {
							var unsaved = false;
							//Initiate Color Picker
							$('.wp-color-picker-field').wpColorPicker();

							// Switches option sections
							$('.group').hide();
							var activetab = window.location.hash;
							if ((!activetab.length || !$(activetab).length) && typeof(localStorage) != 'undefined') {
								activetab = localStorage.getItem("' . $this->plugin_page . '_activetab");
							}
							if (activetab != '' && $(activetab).length) {
								$(activetab).fadeIn();
							} else {
								$('.group:first').fadeIn();
							}
							if (activetab != '' && $(activetab + '-tab').length) {
								$(activetab + '-tab').addClass('nav-tab-active');
							}
							else {
								$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
							}
							$('.nav-tab-wrapper a').click(function (evt) {
								$('.nav-tab-wrapper a').removeClass('nav-tab-active');
								$(this).addClass('nav-tab-active').blur();
								var clicked_group = $(this).attr('href');
								if (typeof(localStorage) != 'undefined') {
									localStorage.setItem("' . $this->plugin_page . '_activetab", $(this).attr('href'));
								}
								$('.group').hide();
								$(clicked_group).fadeIn();
								if (history.pushState) {
									history.pushState(null, null, $(this).attr('href'));
								} else {
									location.hash = $(this).attr('href');
								}
								evt.preventDefault();
							});
							$("#wpbody-content").prepend($("<div />", {
								class: "notifications"
							}));
							$("form").submit(function (event) {
								event.preventDefault();
								var data = {};
								$('form tr').filter(function () {
									return $(this).css('display') !== 'none';
								}).find('input:not([type=checkbox]):not([type=radio]),input:checked, select, textarea').serializeArray().map(function (x) {
									if (x.name.endsWith('[]')) {
										var name = x.name.replace('[]', '');
										data[ name ] = (data[ name ] || []);
										data[ name ].push(x.value);
										return;
									}
									data[ x.name ] = x.value;
								});
								data[ "_wpnonce" ] = <?php echo wp_json_encode( wp_create_nonce( $this->plugin->slug . '_save-settings' ) ) ?>;
								data[ "action" ] = <?php echo wp_json_encode( "update_{$this->plugin->safe_slug}_settings" ) ?>;
								data[ "page" ] = $(this).data("page");
								$(".notifications").html($("<div />", {
																		class: "notice notice-info"
																	}).append($("<i />", {
																		class: "spinner is-active"
									}).css({
																		float: "none",
																		width: "auto",
																		height: "auto",
																		padding: "10px 0 10px 50px",
																		backgroundPosition: "20px 0;"
																	})
																	).append("<?php  _e( 'Saving...', $this->plugin->safe_slug ) ?>")
								);
								$.post("<?php echo admin_url( 'admin-post.php' )  ?>", data, null, "json")
									.done(function (response) {
										$(".notifications").html(response.notifications).children().hide();
										if (0 < $(".notifications .updated").length) {
											unsaved = false;
										}
									}).fail(function () {
									$(".notifications").html("<?php str_replace( "\n", '', trim( $settings_failure_notification ) ) ?>").children().hide();
								}).then(function () {
									$(".notifications").children().fadeIn()
									$(document).trigger("wp-updates-notice-added");
								});
							});
							$("form").on("change", "input, textarea, select", function () {
								unsaved = true;
							})
							$(window).on("beforeunload", function () {
								if (!unsaved) {
									return;
								}
								return "<?php _e( 'You have unsaved changes on this page. Are you sure you want to leave without saving?', $this->plugin->safe_slug ) ?>";
							});
							$('.wpsa-browse').on('click', function (event) {
								event.preventDefault();
								var attachment;
								var self = $(this);
								// Create the media frame.
								var file_frame = wp.media.frames.file_frame = wp.media({
									title: self.data('uploader_title'),
									button: {
										text: self.data('uploader_button_text'),
									},
									multiple: false
								})
									.on('select', function () {
										attachment = file_frame.state().get('selection').first().toJSON();
										self.prev('.wpsa-url').val(attachment.url).change();
									})
									// Finally, open the modal
									.open();
							});
							$('.wpsa-image-browse').on('click', function (event) {
								event.preventDefault();
								var attachment;
								var self = $(this);
								// Create the media frame.
								var file_frame = wp.media.frames.file_frame = wp.media({
									title: self.data('uploader_title'),
									button: {
										text: self.data('uploader_button_text'),
									},
									multiple: false,
									library: { type: 'image' }
								})
									.on('select', function () {
										attachment = file_frame.state().get('selection').first().toJSON();
										var url;
										if (attachment.sizes && attachment.sizes.thumbnail)
											url = attachment.sizes.thumbnail.url;
										else
											url = attachment.url;
										self.parent().children('.wpsa-image-id').val(attachment.id).change();
										self.parent().children('.wpsa-image-preview').children('img').attr('src', url);
									})
									// Finally, open the modal
									.open();
							});
						});
					})(jQuery);
		</script>
		<?php
		wp_add_inline_script( 'jquery-core', $data = trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', ob_get_clean() ) ) );
	}

	/**
	 * @return string
	 */
	public function get_full_name() {
		return $this->plugin->safe_slug . '_' . $this->get_name();
	}

	/**
	 *
	 */
	public function render() {
		if ( empty( $this->sections ) && empty( $this->tabs ) ) {
			return false;
		}
		if ( 1 < count( $this->tabs ) ): ?>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->tabs as $tab ):
					?>
					<a href="#<?php echo $tab->id ?>" class="nav-tab"
					   id="<?php echo $tab->id ?>-tab"><?php echo $tab->title ?></a>
				<?php
				endforeach;
				?>
			</h2>
		<?php
		endif;
		?>
		<div class="metabox-holder">
			<form data-page="<?= $this->get_name() ?>">
				<?php
				foreach ( $this->tabs as $tab ):
					?>
					<div id="<?php echo $tab->id; ?>" class="group" style="display: none;">
						<?php foreach ( $tab->sections as $section ): ?>
							<?php if ( $section->title ): ?>
								<h2><?php echo $section->title ?></h2>
							<?php endif; ?>
							<?php
							if ( $section->callback ) {
								call_user_func( $section->callback, $section );
							}
							?>
							<table class="form-table">
								<?php do_settings_fields( $this->get_full_name(), $section->id ); ?>
							</table>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<hr/>
				<?php
				foreach ( $this->sections as $section ) :
					?>
					<table class="form-table">
						<?php if ( $section->title ): ?>
							<h2><?php echo $section->title ?></h2>
						<?php endif; ?>
						<?php
						if ( $section->callback ) {
							call_user_func( $section->callback, $section );
						}
						?>
						<?php do_settings_fields( $this->get_full_name(), $section->id ); ?>
					</table>
				<?php endforeach; ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * @param \ComposePress\Settings\UI\Section $section
	 */
	public function add_section( Section $section ) {
		$this->sections[] = $section;
	}

	/**
	 * @param \ComposePress\Settings\UI\Tab $tab
	 */
	public function add_tab( Tab $tab ) {
		$this->tabs[] = $tab;
	}

	/**
	 * @return bool
	 */
	public function is_default() {
		return $this->default;
	}

	/**
	 * @param bool $default
	 */
	public function set_default( $default ) {
		$this->default = $default;
	}

	/**
	 * @return \ComposePress\Settings\UI\Tab[]
	 */
	public function get_tabs() {
		return $this->tabs;
	}
}

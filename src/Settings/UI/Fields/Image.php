<?php


namespace ComposePress\Settings\UI\Fields;


use ComposePress\Settings\Abstracts\FieldType;
use ComposePress\Settings\UI\Field;

class Image extends FieldType {
	const NAME = 'image';

	public static function render( Field $field ) {
		parent::render( $field );

		$params = $field->args;

		$value = esc_attr( self::get_value( $field ) );
		$size  = isset( $params['image_max_display_size'] ) && is_array( $params['size'] ) ? $params['size'] : [
			300,
			300,
		];
		if ( 2 !== count( $size ) ) {
			$size[] = end( $size );
		}
		$button_label          = isset( $params['button_label'] ) ? $params['button_label'] : __( 'Choose Image' );
		$uploader_button_label = isset( $params['uploader_button_label'] ) ? $params['uploader_button_label'] : '';
		$uploader_title        = isset( $params['uploader_title'] ) ? $params['uploader_title'] : '';

		if ( ! empty( $uploader_button_label ) ) {
			$uploader_button_label = sprintf( 'data-uploader-button-text="%s"', $uploader_button_label );
		}
		if ( ! empty( $uploader_title ) ) {
			$uploader_title = sprintf( 'data-uploader-title="%s"', $uploader_title );
		}


		$img     = wp_get_attachment_image_src( $value, 'full' );
		$img_url = $img ? $img[0] : '';
		$html    = sprintf( '<input type="hidden" class="wpsa-image-id" id="%1$s" name="%1$s" value="%2$s"/>', $field->name, $value );
		$html    .= sprintf( '<div class="wpsa-image-preview" style="max-width: %1$dpx; max-height:  %2$dpx"><div class="wpsa-image-preview-inner"><img src="%3$s" /></div></div>', $size[0], $size[1], $img_url );
		$html    .= sprintf( '<input type="button" class="button wpsa-image-browse" value="%s" %s%s/>', $button_label, $uploader_button_label, $uploader_title );
		$html    .= self::get_description( $params );

		echo $html;
	}

	public static function enqueue_scripts() {
		parent::enqueue_scripts();
		ob_start();
		?>
		<script>
					(function ($) {
						$(function () {
							$('.wpsa-image-browse').on('click', function (event) {
								event.preventDefault();
								var attachment;
								var self = $(this);
								// Create the media frame.
								var file_frame = wp.media.frames.file_frame = wp.media({
									title: self.data('uploaderTitle'),
									button: {
										text: self.data('uploaderButtonText')
									},
									multiple: false,
									library: { type: 'image' }
								})
								  .on('select', function () {
										attachment = file_frame.state().get('selection').first().toJSON();
										var url;
										if (attachment.sizes && attachment.sizes.full)
											url = attachment.sizes.full.url;
										else
											url = attachment.url;
										self.parent().children('.wpsa-image-id').val(attachment.id).change();
										self.parent().children('.wpsa-image-preview').find('img').attr('src', url);
										self.trigger('imageSelected');
									})
									// Finally, open the modal
									.open();
							}).on('imageSelected', function () {
								$(this).parent().find('.wpsa-image-preview-inner').append($('<span />', { class: 'close' })).end().end().addClass('showing-image');
							});
							$(document).on('click', '.wpsa-image-preview .close', function () {
								$(this).siblings('img').removeAttr('src').closest('.wpsa-image-preview').siblings('.wpsa-image-id').val('').change().end().find('.close').remove().end().siblings('.wpsa-image-browse').removeClass('showing-image');
							});
							$('.wpsa-image-preview').each(function () {
								var $this = $(this);
								if ($(this).find('img').attr('src')) {
									$this.siblings('.wpsa-image-browse').trigger('imageSelected');
								}
							})
						});
					})(jQuery);
		</script>
		<?php
		wp_add_inline_script( 'jquery-core', trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', ob_get_clean() ) ) );
		wp_add_inline_style( 'common', '
.wpsa-image-browse.showing-image {
	margin-top: 10px !important;
}
.wpsa-image-preview-inner {
	position:relative;
	display:inline-block;		
}
.wpsa-image-preview-inner > img{
	max-width:100%;
}
.close {
  position: absolute;
  right: 5px;
  top: 5px;
  width: 32px;
  height: 32px;
  opacity: 0.7;
  cursor:pointer;
}
.close:hover {
  opacity: 1;
}
.close:before, .close:after {
  position: absolute;
  left: 15px;
  content: "";
  height: 22px;
  width: 8px;
  background-color: red;
}
.close:before {
  transform: rotate(45deg);
}
.close:after {
  transform: rotate(-45deg);
}
' );
	}
}
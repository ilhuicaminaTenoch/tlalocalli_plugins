<?php
/**
 * The style "default" of the Promo block
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_promo');

$css_image =   (!empty($args['image_bg_color']) ? 'background-color:' . esc_attr($args['image_bg_color']) . ';' : '')
			 . (!empty($args['image']) && !is_array($args['image']) ? 'background-image:url(' . esc_url($args['image']) . ');' : '')
			 . (!empty($args['image']) ? 'width:'.trim($args['image_width']).';' : '')
			 . (!empty($args['image_position']) ? $args['image_position'].': 0;' : '');

$css_text = 'width: '.esc_attr($args['text_width']).';'
			. (!empty($args['image']) ? 'float: '.($args['image_position']=='left' ? 'right' : 'left').';' : '');

$css_text_inner = (!empty($args['text_margins']) ? ' margin:'.esc_attr($args['text_margins']).';' : '')
			. (!empty($args['text_bg_color']) ? 'background-color:' . esc_attr($args['text_bg_color']) . ';' : '');

?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?> 
	class="sc_promo sc_promo_<?php
		echo esc_attr($args['type']);
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
		if (!empty($args['size'])) echo ' sc_promo_size_'.esc_attr($args['size']);
		if (empty($args['text_paddings'])) echo ' sc_promo_no_paddings';
		if (!empty($args['image']) && empty($args['image_cover'])) echo ' sc_promo_image_fit';
		if (!empty($args['image']) && !empty($args['image_position'])) echo ' sc_promo_image_position_'.esc_attr($args['image_position']);
		if (empty($args['image'])) echo ' sc_promo_no_image';
		?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>><?php
	
	// Image
	if (!empty($args['image'])) {
		$video = array('','');
		if (!empty($args['video_url']) || !empty($args['video_embed'])) {
			$video = explode('<!-- .sc_popup -->', trx_addons_get_video_layout(array(
														'link' => isset($args['video_url']) ? $args['video_url'] : '',
														'embed' => isset($args['video_embed']) ? $args['video_embed'] : '',
														'cover' => $args['image'],
														'show_cover' => false,
														'popup' => !empty($args['video_in_popup'])
														)
													));
		} else if (is_array($args['image'])) {
			$images = array();
			foreach ( $args['image'] as $v ) {
				$images[] = array('url' => trx_addons_get_attachment_url($v, 'full'));
			}
		}
		?><div class="sc_promo_image" style="<?php echo esc_attr($css_image); ?>"><?php
			if (!empty($video[0])) 
				trx_addons_show_layout($video[0]);
			else if (is_array($args['image']))
				trx_addons_show_layout(trx_addons_get_slider_layout(apply_filters('trx_addons_sc_promo_slider', array(
					'mode' => 'custom',
					'noresize' => 1,
					'controls' => 'yes',
					'controls_pos' => 'side',
					'pagination' => 'yes',
					'pagination_pos' => 'bottom',
					'slides_space' => 0
					)), $images));
		?></div><?php
		if (!empty($video[1])) trx_addons_show_layout($video[1]);
	}

	// Text
	if (!empty($args['title']) || !empty($args['subtitle']) || !empty($args['description']) || !empty($args['content']) 
		|| (!empty($args['link']) && !empty($args['link_text'])) || !empty($args['link_image'])) {
		?><div class="sc_promo_text<?php
			if (!trx_addons_is_off($args['text_float'])) echo ' sc_float_'.esc_attr($args['text_float']);
			if ((int) $args['full_height'] == 1) echo ' trx_addons_stretch_height';
			?>" style="<?php echo esc_attr($css_text); ?>">
			<div class="sc_promo_text_inner<?php 
				if (!trx_addons_is_off($args['text_align'])) echo ' sc_align_'.esc_attr($args['text_align']);
				if (!trx_addons_is_off($args['text_width'])) echo ' sc_content_width_'.esc_attr(str_replace('/', '_', $args['text_width']));
				?>" style="<?php echo esc_attr($css_text_inner); ?>"><?php
				if (!empty($args['icon'])) {
					if (!empty($args['icon_type']) && $args['icon_type'] == 'sow')
						echo siteorigin_widget_get_icon($args['icon']);
					else {
						?><div class="sc_promo_icon" data-icon="<?php echo esc_attr($args['icon']); ?>"><span class="<?php echo esc_attr($args['icon']); ?>"></span></div><?php
					}
				}
				trx_addons_sc_show_titles('sc_promo', $args, $args['size']);
				if (!empty($args['content']) && 'tiny' != $args['size']) {
					?><div class="sc_promo_content sc_item_content"><?php echo do_shortcode($args['content']); ?></div><?php
				}
				if ('tiny' != $args['size']) {
					trx_addons_sc_show_links('sc_promo', $args);
				}
			?></div>
		</div><!-- /.sc_promo_text --><?php
	}
	if ( 'tiny' == $args['size'] ) {
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_promo_link', 'sc_promo', $args)); ?>"></a><?php
		}
	}
?></div><!-- /.sc_promo -->
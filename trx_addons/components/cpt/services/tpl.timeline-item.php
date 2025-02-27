<?php
/**
 * The style "timeline" of the Services
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.24
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');

$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();
if (empty($args['id'])) $args['id'] = 'sc_services_'.str_replace('.', '', mt_rand());
if (empty($args['featured'])) $args['featured'] = 'image';
if (empty($args['featured_position'])) $args['featured_position'] = 'top';

$svg_present = false;
$price_showed = false;

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ((int) $args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'])); ?>"><?php
}
?>
<div class="sc_services_item<?php
	echo isset($args['hide_excerpt']) && (int) $args['hide_excerpt'] > 0 ? ' without_content' : ' with_content';
	echo $args['featured']=='image' 
					? ' with_image' 
					: ($args['featured']=='icon' 
						? ' with_icon' 
						: ($args['featured']=='number' 
							? ' with_number' 
							: ''));
	echo ' sc_services_item_featured_'.esc_attr($args['featured']!='none' ? $args['featured_position'] : 'none');
?>"<?php
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>><?php
	
	// Timeline
	?><div class="sc_services_item_timeline"></div><?php

	// Featured image or icon
	if ($args['featured'] != 'none') {
		if ( has_post_thumbnail() && $args['featured']=='image') {
			?><div class="sc_services_item_marker sc_services_item_thumb" style="background-image: url(<?php
				$trx_addons_attachment_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'masonry' );
				if (!empty($trx_addons_attachment_src[0]))
					echo esc_url($trx_addons_attachment_src[0]);
				?>);"></div><?php
	
		} else if ($args['featured']=='icon' && !empty($meta['icon'])) {
			$svg = $img = '';
			if (trx_addons_is_url($meta['icon'])) {
				$img = $meta['icon'];
				$meta['icon'] = basename($meta['icon']);
			} else if (!empty($args['icons_animation']) && (int) $args['icons_animation'] > 0 && ($svg = trx_addons_get_file_dir('css/icons.svg/'.trx_addons_esc($meta['icon']).'.svg')) != '')
				$svg_present = true;
			?><a href="<?php echo esc_url($link); ?>"
				 id="<?php echo esc_attr($args['id'].'_'.trim($meta['icon']).'_'.trim($number)); ?>"
				 class="sc_services_item_marker sc_services_item_icon <?php
						echo !empty($svg) 
								? 'sc_icon_type_svg'
								: (!empty($img) 
									? 'sc_icon_type_img'
									: esc_attr($meta['icon'])
									);
						?>"<?php
				 if (!empty($meta['icon_color'])) {
					 echo ' style="background-color:'.esc_attr($meta['icon_color']).'"';
				 }
			?>><?php
				if (!empty($svg)) {
					trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
				} else if (!empty($img)) {
					$attr = trx_addons_getimagesize($img);
					?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt=""<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
				}
			?></a><?php
		} else if ($args['featured']=='number') {
			?><span class="sc_services_item_marker sc_services_item_number"><?php
				printf("%02d", $number);
			?></span><?php
		}
	}
	?>	
	<div class="sc_services_item_info">
		<?php
		if ( has_post_thumbnail() && (empty($args['featured']) || $args['featured']!='image')) {
			trx_addons_get_template_part('templates/tpl.featured.php',
											'trx_addons_args_featured',
											apply_filters('trx_addons_filter_args_featured', array(
															'class' => 'sc_services_item_featured',
															'thumb_size' => apply_filters('trx_addons_filter_thumb_size',
																trx_addons_get_thumb_size(in_array($args['featured_position'], array('top', 'bottom')) 
																	? 'medium' 
																	: 'big'),
																'services-timeline'),
															'post_info' => !empty($meta['price']) 
																				? '<span class="sc_services_item_price">'.esc_html($meta['price']).'</span>'
																				: ''
															),
															'services-timeline'
															)
										);
			$price_showed = true;
		}
		?>
		<div class="sc_services_item_header">
			<?php
			$title_tag = $args['featured']=='number' ? 'h4' : 'h6';
			if ($args['featured']=='number' && $args['featured_position']=='top') {
				?><div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div><?php
			}
			?>
			<<?php echo esc_html($title_tag); ?> class="sc_services_item_title<?php if (!$price_showed && !empty($meta['price'])) echo ' with_price'; ?>"><a href="<?php echo esc_url($link); ?>"><?php
				the_title();
				// Price
				if (!$price_showed && !empty($meta['price'])) {
					?><div class="sc_services_item_price"><?php echo esc_html($meta['price']); ?></div><?php
				}
			?></a></<?php echo esc_html($title_tag); ?>>
			<?php
			if ($args['featured']!='number' || $args['featured_position']!='top') {
				?><div class="sc_services_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY));?></div><?php
			}
			?>
		</div>
		<?php if (!isset($args['hide_excerpt']) || (int) $args['hide_excerpt']==0) { ?>
			<div class="sc_services_item_content"><?php the_excerpt(); ?></div>
			<div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_services', $args)); ?>"><?php esc_html_e('Learn more', 'trx_addons'); ?></a></div>
		<?php } ?>
	</div>
</div>
<?php
if (!empty($args['slider']) || (int) $args['columns'] > 1) {
	?></div><?php
}
if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && $svg_present) {
	wp_enqueue_script( 'vivus', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js'), array('jquery'), null, true );
	wp_enqueue_script( 'trx-addons-sc-icons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js'), array('jquery'), null, true );
}
?>
<?php
/**
 * Shortcode: Blogger
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_blogger_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_blogger_merge_styles');
	function trx_addons_sc_blogger_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/_blogger.scss';
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_blogger_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_blogger_merge_styles_responsive');
	function trx_addons_sc_blogger_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/_blogger.responsive.scss';
		return $list;
	}
}



// trx_sc_blogger
//-------------------------------------------------------------
/*
[trx_sc_blogger id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_blogger' ) ) {
	function trx_addons_sc_blogger($atts, $content='') {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_blogger', $atts, array(
			// Individual params
			"type" => 'default',
			"hide_excerpt" => 0,
			'post_type' => 'post',
			'taxonomy' => 'category',
			"cat" => '',
			"count" => 3,
			"columns" => '',
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"title" => '',
			"subtitle" => '',
			"description" => '',
			"link" => '',
			"link_style" => 'default',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => 'left',
			"title_style" => 'default',
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		
		if (!empty($atts['ids'])) {
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$atts['count'] = count(explode(',', $atts['ids']));
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default.php'
										),
                                        'trx_addons_args_sc_blogger', 
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_blogger', $atts, $content);
	}
}


// Add [trx_sc_blogger] in the VC shortcodes list
if (!function_exists('trx_addons_sc_blogger_add_in_vc')) {
	function trx_addons_sc_blogger_add_in_vc() {

		add_shortcode("trx_sc_blogger", "trx_addons_sc_blogger");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_blogger", 'trx_addons_sc_blogger_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Blogger extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_blogger_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_blogger_add_in_vc_params')) {
	function trx_addons_sc_blogger_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_blogger');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
		$tax_obj = get_taxonomy($taxonomy);
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_blogger",
				"name" => esc_html__("Blogger", 'trx_addons'),
				"description" => wp_kses_data( __("Display posts from specified category in many styles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_blogger',
				"class" => "trx_sc_blogger",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
/*
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
					        'save_always' => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger')),
							"type" => "dropdown"
						),
*/
						// Attention! It's our custom param's type and it need values list as normal associative array 'key' => 'value'
						// not in VC-style 'value' => 'key'
						// 'style' => 'icons' | 'images'
						// 'mode' => 'inline' | 'dropdown'
						// 'return' => 'slug' | 'full'
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
					        'save_always' => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' ),
							"mode" => 'inline',
							"return" => 'slug',
							"style" => "images",
							"type" => "icons"
						),
						array(
							"param_name" => "hide_excerpt",
							"heading" => esc_html__("Excerpt", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => array('classic')
							),
							"std" => "0",
							"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "post_type",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 0,
							"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
																		 $taxonomy == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy)
																		)),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_blogger' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Blogger extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_blogger',
				esc_html__('ThemeREX Blogger', 'trx_addons'),
				array(
					'classname' => 'widget_blogger',
					'description' => __('Display blog posts', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}


		// Return array with all widget's fields
		function get_widget_form() {
			// Prepare lists
			list($vc_edit, $vc_params) = trx_addons_get_sow_form_params('TRX_Addons_SOW_Widget_Blogger');
			$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
			$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
			$tax_obj = get_taxonomy($taxonomy);
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Layout', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), $this->get_sc_name(), 'sow' ),
						'state_emitter' => array(
							'callback' => 'conditional',
							'args'     => array(
								'use_type[default]: val=="default"',
								'use_type[hide]: val!="default"',
							)
						),
						"mode" => 'inline',
						"return" => 'slug',
						"style" => "images",
						'type' => 'icons'
					),
					"hide_excerpt" => array(
						"label" => esc_html__("Hide excerpt", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
						'state_handler' => array(
							"use_type[default]" => array('show'),
							"use_type[hide]" => array('hide')
						),
						"default" => false,
						"type" => "checkbox"
					),
					"post_type" => array(
						"label" => esc_html__("Post type", 'trx_addons'),
						"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
						"default" => 'post',
						"options" => trx_addons_get_list_posts_types(),
						"type" => "select"
					),
					"taxonomy" => array(
						"label" => esc_html__("Taxonomy", 'trx_addons'),
						"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
						"default" => 'category',
						"options" => trx_addons_get_list_taxonomies(false, $post_type),
						"type" => "select_dynamic"
					),
					"cat" => array(
						"label" => esc_html__("Category", 'trx_addons'),
						"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
						"default" => 0,
						"options" => trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
																	 $taxonomy == 'category' 
																		? trx_addons_get_list_categories() 
																		: trx_addons_get_list_terms(false, $taxonomy)
																	),
						"type" => "select_dynamic"
					)
				),
				trx_addons_sow_add_query_param(''),
				trx_addons_sow_add_slider_param(),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_blogger', __FILE__, 'TRX_Addons_SOW_Widget_Blogger');


// TRX_Addons Widget
//------------------------------------------------------
} else {

	class TRX_Addons_SOW_Widget_Blogger extends TRX_Addons_Widget {
	
		function __construct() {
			$widget_ops = array('classname' => 'widget_blogger', 'description' => esc_html__('Show blog posts', 'trx_addons'));
			parent::__construct( 'trx_addons_sow_widget_blogger', esc_html__('ThemeREX Blogger', 'trx_addons'), $widget_ops );
		}
	
		// Show widget
		function widget($args, $instance) {
			extract($args);
	
			$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '');
	
			$output = trx_addons_sc_blogger(apply_filters('trx_addons_filter_widget_args',
															$instance,
															$instance, 'trx_addons_sow_widget_blogger')
															);
	
			if (!empty($output)) {
		
				// Before widget (defined by themes)
				trx_addons_show_layout($before_widget);
				
				// Display the widget title if one was input (before and after defined by themes)
				if ($widget_title) trx_addons_show_layout($before_title . $widget_title . $after_title);
		
				// Display widget body
				trx_addons_show_layout($output);
				
				// After widget (defined by themes)
				trx_addons_show_layout($after_widget);
			}
		}
	
		// Update the widget settings
		function update($new_instance, $instance) {
			$instance = array_merge($instance, $new_instance);
			$instance['hide_excerpt'] = isset( $new_instance['hide_excerpt'] ) && (int)$new_instance['hide_excerpt'] > 0 ? 1 : 0;
			$instance['slider'] = isset( $new_instance['slider'] ) && (int)$new_instance['slider'] > 0 ? 1 : 0;
			return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_sow_widget_blogger');
		}
	
		// Displays the widget settings controls on the widget panel
		function form($instance) {
			// Set up some default widget settings
			$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
				'widget_title' => '',
				// Layout params
				"type" => "default",
				"hide_excerpt" => 0,
				// Query params
				'post_type' => 'post',
				'taxonomy' => 'category',
				"cat" => '',
				"count" => 3,
				"columns" => '',
				"offset" => 0,
				"orderby" => 'date',
				"order" => 'desc',
				"ids" => '',
				// Slider params
				"slider" => 0,
				"slider_pagination" => "none",
				"slider_controls" => "none",
				"slides_space" => 0,
				// Title params
				"title" => "",
				"subtitle" => "",
				"description" => "",
				"link" => '',
				"link_style" => 'default',
				"link_image" => '',
				"link_text" => __('Learn more', 'trx_addons'),
				"title_align" => "left",
				"title_style" => "default",
				"title_tag" => '',
				// Common params
				"id" => "",
				"class" => "",
				"css" => ""
				), 'trx_addons_sow_widget_blogger')
			);
		
			do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_sow_widget_blogger');
			
			$this->show_field(array('name' => 'widget_title',
									'title' => __('Widget title:', 'trx_addons'),
									'value' => $instance['widget_title'],
									'type' => 'text'));
		
			do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_sow_widget_blogger');
			
			$this->show_field(array('title' => __('Layout parameters', 'trx_addons'),
									'type' => 'info'));
			
			$this->show_field(array('name' => 'type',
									'title' => __('Layout:', 'trx_addons'),
									'value' => $instance['type'],
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger'),
									'params' => array(
													"mode" => 'inline',
													"return" => 'slug',
													"style" => "images"
													),
									'type' => 'icons'));
			
			$this->show_field(array('name' => 'hide_excerpt',
									'title' => '',
									'label' => __('Hide excerpt', 'trx_addons'),
									'value' => (int) $instance['hide_excerpt'],
									'type' => 'checkbox'));
			
			$this->show_field(array('title' => __('Query parameters', 'trx_addons'),
									'type' => 'info'));

			$this->show_field(array('name' => 'post_type',
									'title' => __('Post type:', 'trx_addons'),
									'value' => $instance['post_type'],
									'options' => trx_addons_get_list_posts_types(),
									'class' => 'trx_addons_post_type_selector',
									'type' => 'select'));

			$this->show_field(array('name' => 'taxonomy',
									'title' => __('Taxonomy:', 'trx_addons'),
									'value' => $instance['taxonomy'],
									'options' => trx_addons_get_list_taxonomies(false, $instance['post_type']),
									'class' => 'trx_addons_taxonomy_selector',
									'type' => 'select'));


			$tax_obj = get_taxonomy($instance['taxonomy']);

			$this->show_field(array('name' => 'cat',
									'title' => __('Category:', 'trx_addons'),
									'value' => $instance['cat'],
									'options' => trx_addons_array_merge(
											array(0 => sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
											trx_addons_get_list_terms(false, $instance['taxonomy'], array('pad_counts' => true))),
									'class' => 'trx_addons_terms_selector',
									'type' => 'select'));

			$this->show_fields_query_param($instance, '');
			$this->show_fields_slider_param($instance);
			$this->show_fields_title_param($instance);
			$this->show_fields_id_param($instance);
		
			do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_sow_widget_blogger');
		}
	}

	// Load widget
	if (!function_exists('trx_addons_sow_widget_blogger_load')) {
		add_action( 'widgets_init', 'trx_addons_sow_widget_blogger_load' );
		function trx_addons_sow_widget_blogger_load() {
			register_widget('TRX_Addons_SOW_Widget_Blogger');
		}
	}
}
?>
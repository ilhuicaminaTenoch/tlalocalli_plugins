<?php
/**
 * Shortcode: Icons
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_icons_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_icons_merge_styles');
	function trx_addons_sc_icons_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/_icons.scss';
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_icons_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_icons_merge_styles_responsive');
	function trx_addons_sc_icons_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/_icons.responsive.scss';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_icons_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_icons_merge_scripts');
	function trx_addons_sc_icons_merge_scripts($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/vivus.js';
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/icons.js';
		return $list;
	}
}



// trx_sc_icons
//-------------------------------------------------------------
/*
[trx_sc_icons id="unique_id" columns="2" values="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_icons' ) ) {
	function trx_addons_sc_icons($atts, $content='') {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_icons', $atts, array(
			// Individual params
			"type" => "default",
			"align" => "center",
			"size" => "medium",
			"color" => "",
			"columns" => "",
			"icons" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"icons_animation" => "0",
			"link" => '',
			"link_style" => 'default',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['icons']))
			$atts['icons'] = (array) vc_param_group_parse_atts( $atts['icons'] );

		$output = '';
		if (is_array($atts['icons']) && count($atts['icons']) > 0) {
			if (empty($atts['columns'])) $atts['columns'] = count($atts['icons']);
			$atts['columns'] = max(1, min(count($atts['icons']), $atts['columns']));
	
			foreach ($atts['icons'] as $k=>$v)
				if (!empty($v['description'])) $atts['icons'][$k]['description'] = trim( vc_value_from_safe( $v['description'] ) );
	
			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/tpl.default.php'
											),
											'trx_addons_args_sc_icons', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_icons', $atts, $content);
	}
}


// Add [trx_sc_icons] in the VC shortcodes list
if (!function_exists('trx_addons_sc_icons_add_in_vc')) {
	function trx_addons_sc_icons_add_in_vc() {
		
		add_shortcode("trx_sc_icons", "trx_addons_sc_icons");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_icons", 'trx_addons_sc_icons_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Icons extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_icons_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_icons_add_in_vc_params')) {
	function trx_addons_sc_icons_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_icons",
				"name" => esc_html__("Icons", 'trx_addons'),
				"description" => wp_kses_data( __("Insert icons or images with title and description", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_icons',
				"class" => "trx_sc_icons",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'icons'), 'trx_sc_icons')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Align", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "center",
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_title_aligns()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "size",
							"heading" => esc_html__("Icon size", 'trx_addons'),
							"description" => wp_kses_data( __("Select icon's size", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(trx_addons_get_list_sc_icon_sizes()),
					        'save_always' => true,
							"std" => "medium",
							"type" => "dropdown"
						),
						array(
							'param_name' => 'color',
							'heading' => esc_html__( 'Color', 'trx_addons' ),
							'description' => esc_html__( 'Select custom color for each icon', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'colorpicker',
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of columns for icons. If empty - auto detect by items number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "icons_animation",
							"heading" => esc_html__("Animation", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Animate icons", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'icons',
							'heading' => esc_html__( 'Icons', 'trx_addons' ),
							"description" => wp_kses_data( __("Select icons, specify title and/or description for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'description' => '',
									'color' => '',
									'image' => '',
									'link' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_icons') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Enter title for this item', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'link',
										'heading' => esc_html__( 'Link', 'trx_addons' ),
										'description' => esc_html__( 'URL to link this block', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Enter short description for this item', 'trx_addons' ),
										'type' => 'textarea_safe',
									),
									array(
										'param_name' => 'color',
										'heading' => esc_html__( 'Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom color for this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'colorpicker',
									),
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "attach_image"
									),
								), trx_addons_vc_add_icon_param('')
							), 'trx_sc_icons')
						)
					),
					trx_addons_vc_add_title_param(false, false),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_icons' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Icons extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_icons',
				esc_html__('ThemeREX Icons', 'trx_addons'),
				array(
					'classname' => 'widget_icons',
					'description' => __('Display set of icons', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}


		// Return array with all widget's fields
		function get_widget_form() {
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Layout', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'icons'), $this->get_sc_name(), 'sow'),
						'type' => 'select'
					),
					"align" => array(
						"label" => esc_html__("Align", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
						"default" => "center",
						"options" => trx_addons_get_list_sc_title_aligns(),
						"type" => "select"
					),
					"size" => array(
						"label" => esc_html__("Size", 'trx_addons'),
						"description" => wp_kses_data( __("Select icon's size", 'trx_addons') ),
						"default" => "medium",
						"options" => trx_addons_get_list_sc_icon_sizes(),
						"type" => "select"
					),
					"color" => array(
						"label" => esc_html__("Color", 'trx_addons'),
						"description" => wp_kses_data( __("Select custom color for each icon", 'trx_addons') ),
						"type" => "color"
					),
					"columns" => array(
						"label" => esc_html__("Columns", 'trx_addons'),
						"description" => wp_kses_data( __("Specify number of columns for icons. If empty - auto detect by items number", 'trx_addons') ),
						"type" => "number"
					),
					"icons_animation" => array(
						"label" => esc_html__("Animation", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
						"default" => false,
						"type" => "checkbox"
					),
					'icons' => array(
						'label' => __('Icons', 'trx_addons'),
						'item_name'  => __( 'Icon', 'trx_addons' ),
						'item_label' => array(
							'selector'     => "[name*='title']",
							'update_event' => 'change',
							'value_method' => 'val'
						),
						'type' => 'repeater',
						'fields' => apply_filters('trx_addons_sc_param_group_fields', array_merge(array(
								'title' => array(
									'label' => __('Title', 'trx_addons'),
									'description' => esc_html__( 'Enter title of the item', 'trx_addons' ),
									'type' => 'text'
								),
								'link' => array(
									'label' => __('Link', 'trx_addons'),
									'description' => esc_html__( 'URL to link this item', 'trx_addons' ),
									'type' => 'link'
								),
								'description' => array(
									'rows' => 10,
									'label' => __('Item description', 'trx_addons'),
									'description' => esc_html__( 'Enter short description of the item', 'trx_addons' ),
									'type' => 'tinymce'
								),
								'color' => array(
									'label' => __('Color', 'trx_addons'),
									"description" => wp_kses_data( __("Select custom color for this item", 'trx_addons') ),
									'type' => 'color'
								),
								'image' => array(
									'label' => __('Image', 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload imageto show it above title (instead icon)", 'trx_addons') ),
									'type' => 'media'
								)
							),
							trx_addons_sow_add_icon_param('')
						), $this->get_sc_name())
					)
				),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_icons', __FILE__, 'TRX_Addons_SOW_Widget_Icons');
}
?>
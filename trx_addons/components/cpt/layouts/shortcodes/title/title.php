<?php
/**
 * Shortcode: Display site meta and/or title and/or breadcrumbs
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_title_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_title_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_title_merge_styles');
	function trx_addons_sc_layouts_title_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'title/_title.scss';
		return $list;
	}
}



// trx_sc_layouts_title
//-------------------------------------------------------------
/*
[trx_sc_layouts_title id="unique_id" icon="hours" text1="Opened hours" text2="8:00am - 5:00pm"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_title' ) ) {
	function trx_addons_sc_layouts_title($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_title', $atts, array(
			// Individual params
			"type" => "default",
			"image" => "",
			"use_featured_image" => "0",
			"height" => "",
			"align" => '',
			"meta" => "0",
			"title" => "0",
			"breadcrumbs" => "0",
			"hide_on_desktop" => "0",
			"hide_on_notebook" => "0",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			"hide_on_frontpage" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'title/tpl.'.trx_addons_esc($atts['type']).'.php',
                                        TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'title/tpl.default.php'
                                        ),
                                        'trx_addons_args_sc_layouts_title',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_title', $atts, $content);
	}
}


// Add [trx_sc_layouts_title] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc')) {
	function trx_addons_sc_layouts_title_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_title", "trx_addons_sc_layouts_title");
		
		if (!trx_addons_exists_visual_composer()) return;

		vc_lean_map("trx_sc_layouts_title", 'trx_addons_sc_layouts_title_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Title extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_title_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_title_add_in_vc_params')) {
	function trx_addons_sc_layouts_title_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_title",
				"name" => esc_html__("Layouts: Title and Breadcrumbs", 'trx_addons'),
				"description" => wp_kses_data( __("Insert post meta and/or title and/or breadcrumbs", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_title',
				"class" => "trx_sc_layouts_title",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_title')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the inner content in this block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_sc_title_aligns(true, false)),
							"std" => "inherit",
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Show post title", 'trx_addons'),
							"description" => wp_kses_data( __("Show post/page title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "meta",
							"heading" => esc_html__("Show post meta", 'trx_addons'),
							"description" => wp_kses_data( __("Show post meta: date, author, categories list, etc.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "breadcrumbs",
							"heading" => esc_html__("Show breadcrumbs", 'trx_addons'),
							"description" => wp_kses_data( __("Show breadcrumbs under the title", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Show", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "image",
							"heading" => esc_html__("Background image", 'trx_addons'),
							"description" => wp_kses_data( __("Background image of the block", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"type" => "attach_image"
						),
						array(
							"param_name" => "use_featured_image",
							"heading" => esc_html__("Post featured image", 'trx_addons'),
							"description" => wp_kses_data( __("Use post's featured image as background of the block instead image above (if present)", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "0",
							"value" => array(esc_html__("Replace with featured image", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Height of the block", 'trx_addons'),
							"description" => wp_kses_data( __("Specify height of this block. If empty - use default height", 'trx_addons') ),
							'dependency' => array(
								'element' => 'use_featured_image',
								'value' => '1'
							),
							'edit_field_class' => 'vc_col-sm-4',
							"admin_label" => true,
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(false, true),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_title');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Layouts_Title extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_title',
				esc_html__('ThemeREX Layouts: Title', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_title',
					'description' => __('Insert post/page title, meta and/or breadcrumbs', 'trx_addons')
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
						"description" => wp_kses_data( __("Select shortcodes's type", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', array(
							'default' => esc_html__('Default', 'trx_addons')
						), $this->get_sc_name()),
						'type' => 'select'
					),
					'title' => array(
						'label' => __('Show title', 'trx_addons'),
						"description" => wp_kses_data( __("Show post/page title", 'trx_addons') ),
						'default' => true,
						'type' => 'checkbox'
					),
					'meta' => array(
						'label' => __('Show post meta', 'trx_addons'),
						"description" => wp_kses_data( __("Show post/page meta - publish date, author, categories, etc.", 'trx_addons') ),
						'default' => true,
						'type' => 'checkbox'
					),
					'breadcrumbs' => array(
						'label' => __('Show breadcrumbs', 'trx_addons'),
						"description" => wp_kses_data( __("Show breadcrumbs", 'trx_addons') ),
						'default' => true,
						'type' => 'checkbox'
					),
					'image' => array(
						'label' => __('Background image', 'trx_addons'),
						"description" => wp_kses_data( __("Background image of the block", 'trx_addons') ),
						'type' => 'media'
					),
					'use_featured_image' => array(
						'label' => __('Replace with featured image', 'trx_addons'),
						"description" => wp_kses_data( __("Use post's featured image as background of the block instead image above (if present)", 'trx_addons') ),
						'default' => false,
						'type' => 'checkbox'
					),
					"height" => array(
						"label" => esc_html__("Height of the block", 'trx_addons'),
						"description" => wp_kses_data( __("Specify height of this block. If empty - use default height", 'trx_addons') ),
						"type" => "measurement"
					),
					"align" => array(
						"label" => esc_html__("Alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of the inner content in this block", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_title_aligns(true, false),
						"default" => "inherit",
						"type" => "select"
					),
				),
				trx_addons_sow_add_hide_param(false, true),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_title', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Title');
}
?>
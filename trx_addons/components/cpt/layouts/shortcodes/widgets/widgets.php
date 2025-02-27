<?php
/**
 * Shortcode: Display selected widgets area
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.19
 */


// trx_sc_layouts_widgets
//-------------------------------------------------------------
/*
[trx_sc_layouts_widgets id="unique_id" widgets="slug"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_widgets' ) ) {
	function trx_addons_sc_layouts_widgets($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_widgets', $atts, array(
			// Individual params
			"type" => "default",
			"widgets" => "",
			"columns" => "",
			"hide_on_desktop" => "0",
			"hide_on_notebook" => "0",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_widgets',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_widgets', $atts, $content);
	}
}


// Add [trx_sc_layouts_widgets] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc')) {
	function trx_addons_sc_layouts_widgets_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_widgets", "trx_addons_sc_layouts_widgets");
		
		if (!trx_addons_exists_visual_composer()) return;

		vc_lean_map("trx_sc_layouts_widgets", 'trx_addons_sc_layouts_widgets_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Widgets extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_widgets_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_widgets_add_in_vc_params')) {
	function trx_addons_sc_layouts_widgets_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_widgets",
				"name" => esc_html__("Layouts: Widgets", 'trx_addons'),
				"description" => wp_kses_data( __("Insert selected widgets area", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_widgets',
				"class" => "trx_sc_layouts_widgets",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_layouts_widgets')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "widgets",
							"heading" => esc_html__("Widgets", 'trx_addons'),
							"description" => wp_kses_data( __("Select previously filled widgets area", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "widgetised_sidebars"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Select number columns to show widgets. If 0 - autodetect by the widgets number", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array(0,1,2,3,4,5,6),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_widgets');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Layouts_Widgets extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_widgets',
				esc_html__('ThemeREX Layouts: Widgets', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_widgets',
					'description' => __('Insert selected widgets area', 'trx_addons')
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
					"widgets" => array(
						"label" => esc_html__("Widgets", 'trx_addons'),
						"description" => wp_kses_data( __("Select previously filled widgets area", 'trx_addons') ),
						"options" => trx_addons_get_list_sidebars(),
						"type" => "select"
					),
					"columns" => array(
						"label" => esc_html__("Columns", 'trx_addons'),
						"description" => wp_kses_data( __("Select number columns to show widgets. If 0 - autodetect by the widgets number", 'trx_addons') ),
						"options" => trx_addons_get_list_range(0, 6),
						"type" => "select"
					)
				),
				trx_addons_sow_add_hide_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_widgets', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Widgets');
}
?>
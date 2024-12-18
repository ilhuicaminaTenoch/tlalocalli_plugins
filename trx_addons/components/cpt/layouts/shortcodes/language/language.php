<?php
/**
 * Shortcode: Display WPML Language Selector
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.18
 */

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_language_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_language_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_language_merge_styles');
	function trx_addons_sc_layouts_language_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/_language.scss';
		return $list;
	}
}

	

// trx_sc_layouts_language
//-------------------------------------------------------------
/*
[trx_sc_layouts_language id="unique_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_language' ) ) {
	function trx_addons_sc_layouts_language($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_language', $atts, array(
			// Individual params
			"type" => "default",
			"flag" => "both",
			"title_link" => "name",
			"title_menu" => "name",
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

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/superfish.js'), array('jquery'), null, true );
		// Menu support
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.js'), array('jquery'), null, true );
		}

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'language/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_language',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_language', $atts, $content);
	}
}


// Add [trx_sc_layouts_language] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc')) {
	function trx_addons_sc_layouts_language_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_language", "trx_addons_sc_layouts_language");

		if (!trx_addons_exists_visual_composer()) return;

		vc_lean_map("trx_sc_layouts_language", 'trx_addons_sc_layouts_language_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Language extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_language_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_language_add_in_vc_params')) {
	function trx_addons_sc_layouts_language_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_language",
				"name" => esc_html__("Layouts: Language", 'trx_addons'),
				"description" => wp_kses_data( __("Insert WPML Language Selector", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_language',
				"class" => "trx_sc_layouts_language",
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
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_language')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "flag",
							"heading" => esc_html__("Show flag", 'trx_addons'),
							"description" => wp_kses_data( __("Where do you want to show flag?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
					        'save_always' => true,
							"std" => "both",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_positions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_link",
							"heading" => esc_html__("Show link's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select link's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_parts()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title_menu",
							"heading" => esc_html__("Show menu item's title", 'trx_addons'),
							"description" => wp_kses_data( __("Select menu item's title type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "name",
							"value" => array_flip(trx_addons_get_list_sc_layouts_language_parts()),
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_language');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')
	//&& function_exists('trx_addons_exists_wpml') && trx_addons_exists_wpml()
	) {
		
	class TRX_Addons_SOW_Widget_Layouts_Language extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_language',
				esc_html__('ThemeREX Layouts: Language', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_language',
					'description' => __('Insert WPML Language Selector', 'trx_addons')
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
					"flag" => array(
						"label" => esc_html__("Show flag", 'trx_addons'),
						"description" => wp_kses_data( __("Where do you want to show flag?", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_layouts_language_positions(),
						"default" => "both",
						"type" => "select"
					),
					"title_link" => array(
						"label" => esc_html__("Show link's title", 'trx_addons'),
						"description" => wp_kses_data( __("Select link's title type", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_layouts_language_parts(),
						"default" => "name",
						"type" => "select"
					),
					"title_menu" => array(
						"label" => esc_html__("Show menu item's title", 'trx_addons'),
						"description" => wp_kses_data( __("Select menu item's title type", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_layouts_language_parts(),
						"default" => "name",
						"type" => "select"
					)
				),
				trx_addons_sow_add_hide_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_language', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Language');
}
?>
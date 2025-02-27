<?php
/**
 * Shortcode: Display WooCommerce Currency Switcher with items number and totals
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.14
 */

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_currency_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_currency_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_currency_merge_styles');
	function trx_addons_sc_layouts_currency_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'currency/_currency.scss';
		return $list;
	}
}

	

// trx_sc_layouts_currency
//-------------------------------------------------------------
/*
[trx_sc_layouts_currency id="unique_id" text="Shopping currency"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_currency' ) ) {
	function trx_addons_sc_layouts_currency($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_currency', $atts, array(
			// Individual params
			"type" => "default",
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
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'currency/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'currency/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_currency',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_currency', $atts, $content);
	}
}


// Add [trx_sc_layouts_currency] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc')) {
	function trx_addons_sc_layouts_currency_add_in_vc() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_currency", "trx_addons_sc_layouts_currency");

		if (!trx_addons_exists_visual_composer()) return;

		vc_lean_map("trx_sc_layouts_currency", 'trx_addons_sc_layouts_currency_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Currency extends WPBakeryShortCode {}
	}

	add_action('init', 'trx_addons_sc_layouts_currency_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_currency_add_in_vc_params')) {
	function trx_addons_sc_layouts_currency_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_currency",
				"name" => esc_html__("Layouts: Currency", 'trx_addons'),
				"description" => wp_kses_data( __("Insert Currency Switcher", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_currency',
				"class" => "trx_sc_layouts_currency",
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
							), 'trx_sc_layouts_currency')),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_currency');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget') 
	//&& function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce() && class_exists('WOOCS')
	) {
	class TRX_Addons_SOW_Widget_Layouts_Currency extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_currency',
				esc_html__('ThemeREX Layouts: Currency', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_currency',
					'description' => __('Insert Currency Switcher', 'trx_addons')
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
					)
				),
				trx_addons_sow_add_hide_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_currency', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Currency');
}
?>
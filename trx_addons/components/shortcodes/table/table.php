<?php
/**
 * Shortcode: Table
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.3
 */

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_table_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_table_merge_styles');
	function trx_addons_sc_table_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'table/_table.scss';
		return $list;
	}
}


// trx_sc_table
//-------------------------------------------------------------
/*
[trx_sc_table id="unique_id" style="default" aligh="left"]
*/
if ( !function_exists( 'trx_addons_sc_table' ) ) {
	function trx_addons_sc_table($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_table', $atts, array(
			// Individual params
			"type" => "default",
			"width" => "100%",
			"align" => "none",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_style" => 'default',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			"content" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		
		$atts['css'] .= trx_addons_get_css_dimensions_from_values($atts['width']);

		if (!empty($content))
			$atts['content'] = do_shortcode(str_replace(
												array('<p><table', 'table></p>', '><br />'),
												array('<table', 'table>', '>'),
												html_entity_decode($content, ENT_COMPAT, 'UTF-8')
												)
								);
		
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'table/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'table/tpl.default.php'
										),
										'trx_addons_args_sc_table', 
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_table', $atts, $content);
	}
}


// Add [trx_sc_table] in the VC shortcodes list
if (!function_exists('trx_addons_sc_table_add_in_vc')) {
	function trx_addons_sc_table_add_in_vc() {
		
		add_shortcode("trx_sc_table", "trx_addons_sc_table");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_table", 'trx_addons_sc_table_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Table extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_table_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_table_add_in_vc_params')) {
	function trx_addons_sc_table_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_table",
				"name" => esc_html__("Table", 'trx_addons'),
				"description" => wp_kses_data( __("Insert a table", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_table',
				"class" => "trx_sc_table",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_table'),
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'table'), 'trx_sc_table')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Table alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the table", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(trx_addons_get_list_sc_title_aligns()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'trx_addons'),
							"description" => wp_kses_data( __("Width of the table", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							'heading' => __( 'Content', 'trx_addons' ),
							"description" => wp_kses_data( __("Content, created with any table-generator, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'trx_addons') ),
							'param_name' => 'content',
							'value' => '',
							'holder' => 'div',
							'type' => 'textarea_html',
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
				
			), 'trx_sc_table' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Table extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_table',
				esc_html__('ThemeREX Table', 'trx_addons'),
				array(
					'classname' => 'widget_table',
					'description' => __('Insert table from any table-generator', 'trx_addons')
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
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'table'), $this->get_sc_name(), 'sow'),
						'type' => 'select'
					),
					"align" => array(
						"label" => esc_html__("Table alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of the table", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_title_aligns(),
						"default" => "none",
						"type" => "select"
					),
					"width" => array(
						"label" => esc_html__("Width", 'trx_addons'),
						"description" => wp_kses_data( __("Width of the table", 'trx_addons') ),
						"type" => "measurement"
					),
					"content" => array(
						"label" => esc_html__("Content", 'trx_addons'),
						"description" => wp_kses_data( __("Content, created with any table-generator, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'trx_addons') ),
						"type" => "textarea"
					)
				),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_table', __FILE__, 'TRX_Addons_SOW_Widget_Table');
}
?>
<?php
/**
 * Shortcode: Price block
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_price_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_price_merge_styles');
	function trx_addons_sc_price_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'price/_price.scss';
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_price_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_price_merge_styles_responsive');
	function trx_addons_sc_price_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'price/_price.responsive.scss';
		return $list;
	}
}



// trx_sc_price
//-------------------------------------------------------------
/*
[trx_sc_price id="unique_id" title="Our plan" link="#" link_text="Buy now"]Description[/trx_sc_price]
*/
if ( !function_exists( 'trx_addons_sc_price' ) ) {
	function trx_addons_sc_price($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_price', $atts, array(
			// Individual params
			"type" => 'default',
			"columns" => "",
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"prices" => "",
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
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['prices']))
			$atts['prices'] = (array) vc_param_group_parse_atts( $atts['prices'] );

		$output = '';
		if (is_array($atts['prices']) && count($atts['prices']) > 0) {
			if (empty($atts['columns'])) $atts['columns'] = count($atts['prices']);
			$atts['columns'] = max(1, min(count($atts['prices']), $atts['columns']));
			$atts['slider'] = $atts['slider'] > 0 && count($atts['prices']) > $atts['columns'];
			$atts['slides_space'] = max(0, (int) $atts['slides_space']);
			if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
	
			foreach ($atts['prices'] as $k=>$v)
				if (!empty($v['description'])) $atts['prices'][$k]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', vc_value_from_safe( $v['description'] ) );
	
			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'price/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'price/tpl.default.php'
											),
											'trx_addons_args_sc_price',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_price', $atts, $content);
	}
}


// Add [trx_sc_price] in the VC shortcodes list
if (!function_exists('trx_addons_sc_price_add_in_vc')) {
	function trx_addons_sc_price_add_in_vc() {
		
		add_shortcode("trx_sc_price", "trx_addons_sc_price");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_price", 'trx_addons_sc_price_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Price extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_price_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_price_add_in_vc_params')) {
	function trx_addons_sc_price_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_price",
				"name" => esc_html__("Price", 'trx_addons'),
				"description" => wp_kses_data( __("Add block with prices", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_price',
				"class" => "trx_sc_price",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'price'), 'trx_sc_price')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of columns for icons. If empty - auto detect by items number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'prices',
							'heading' => esc_html__( 'Prices', 'trx_addons' ),
							"description" => wp_kses_data( __("Select icon, specify price, title and/or description for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
											array(
												'title' => esc_html__( 'One', 'trx_addons' ),
												'subtitle' => '',
												'description' => '',
												'details' => '',
												'link' => '',
												'link_text' => '',
												'label' => '',
												'price' => '',
												'before_price' => '',
												'after_price' => '',
												'image' => '',
												'bg_color' => '',
												'bg_image' => '',
												'icon' => '',
												'icon_fontawesome' => 'empty',
												'icon_openiconic' => 'empty',
												'icon_typicons' => 'empty',
												'icon_entypo' => 'empty',
												'icon_linecons' => 'empty'
											),
										), 'trx_sc_action') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Title of the price', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'admin_label' => true,
										'type' => 'textfield',
									),
									array(
										'param_name' => 'subtitle',
										'heading' => esc_html__( 'Subtitle', 'trx_addons' ),
										'description' => esc_html__( 'Subtitle of the price', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'label',
										'heading' => esc_html__( 'Label', 'trx_addons' ),
										'description' => esc_html__( 'If not empty - colored band with this text is showed at the top corner of price block', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Price description', 'trx_addons' ),
										'type' => 'textfield',
									),
									array(
										'param_name' => 'before_price',
										'heading' => esc_html__( 'Before price', 'trx_addons' ),
										'description' => esc_html__( 'Any text before the price value', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'price',
										'heading' => esc_html__( 'Price', 'trx_addons' ),
										'description' => esc_html__( 'Price value', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'after_price',
										'heading' => esc_html__( 'After price', 'trx_addons' ),
										'description' => esc_html__( 'Any text after the price value', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'details',
										'heading' => esc_html__( 'Details', 'trx_addons' ),
										'description' => esc_html__( 'Price details', 'trx_addons' ),
										'type' => 'textarea',
									),
									array(
										'param_name' => 'link',
										'heading' => esc_html__( 'Link', 'trx_addons' ),
										'description' => esc_html__( 'Specify URL of the button under details', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-6 vc_new_row',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'link_text',
										'heading' => esc_html__( 'Link text', 'trx_addons' ),
										'description' => esc_html__( 'Specify caption of the button under details', 'trx_addons' ),
										'dependency' => array(
											'element' => 'link',
											'not_empty' => true,
										),
										'edit_field_class' => 'vc_col-sm-6',
										'admin_label' => true,
										'type' => 'textfield',
									),
								),
								trx_addons_vc_add_icon_param(''),
								array(
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image to display it at top of this item", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										"type" => "attach_image"
									),
									array(
										"param_name" => "bg_image",
										"heading" => esc_html__("Background image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image to use it as background of this item", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-4',
										"type" => "attach_image"
									),
									array(
										'param_name' => 'bg_color',
										'heading' => esc_html__( 'Background Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom background color of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'colorpicker'
									),
								)
							), 'trx_sc_price')
						)
					),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_price' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Price extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_price',
				esc_html__('ThemeREX Price', 'trx_addons'),
				array(
					'classname' => 'widget_price',
					'description' => __('Display price table', 'trx_addons')
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
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'price'), $this->get_sc_name(), 'sow'),
						'type' => 'select'
					),
					"columns" => array(
						"label" => esc_html__("Columns", 'trx_addons'),
						"description" => wp_kses_data( __("Specify number of columns for icons. If empty - auto detect by items number", 'trx_addons') ),
						"type" => "number"
					),
					'prices' => array(
						'label' => __('Prices', 'trx_addons'),
						'item_name'  => __( 'Price', 'trx_addons' ),
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
								'subtitle' => array(
									'label' => __('Subtitle', 'trx_addons'),
									'description' => esc_html__( 'Enter subtitle of the item', 'trx_addons' ),
									'type' => 'text'
								),
								'label' => array(
									'label' => __('Label', 'trx_addons'),
									'description' => esc_html__( 'If not empty - colored band with this text is showed at the top corner of price block', 'trx_addons' ),
									'type' => 'text'
								),
								'description' => array(
									'rows' => 5,
									'label' => __('Description', 'trx_addons'),
									'description' => esc_html__( 'Enter short description of the item', 'trx_addons' ),
									'type' => 'tinymce'
								),
								'before_price' => array(
									'label' => __('Before price', 'trx_addons'),
									'description' => esc_html__( 'Any text before the price value', 'trx_addons' ),
									'type' => 'text'
								),
								'price' => array(
									'label' => __('Price', 'trx_addons'),
									'description' => esc_html__( 'Price value', 'trx_addons' ),
									'type' => 'text'
								),
								'after_price' => array(
									'label' => __('After price', 'trx_addons'),
									'description' => esc_html__( 'Any text after the price value', 'trx_addons' ),
									'type' => 'text'
								),
								'details' => array(
									'rows' => 5,
									'label' => __('Details', 'trx_addons'),
									'description' => esc_html__( 'Price details', 'trx_addons' ),
									'type' => 'tinymce'
								),
								'link' => array(
									'label' => __('Link', 'trx_addons'),
									'description' => esc_html__( 'Specify URL of the button under details', 'trx_addons' ),
									'type' => 'link'
								),
								'link_text' => array(
									'label' => __('Link text', 'trx_addons'),
									"description" => wp_kses_data( __("Specify caption of the button under details", 'trx_addons') ),
									'type' => 'text'
								)
							),
							trx_addons_sow_add_icon_param(''),
							array(
								'image' => array(
									'label' => __('or Icon image', 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image to display it at top of this item", 'trx_addons') ),
									'type' => 'media'
								),
								'bg_image' => array(
									'label' => __('Background image', 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image to use it as background of this item", 'trx_addons') ),
									'type' => 'media'
								),
								'bg_color' => array(
									'label' => __('Background color', 'trx_addons'),
									'description' => esc_html__( 'Select custom background color of this item', 'trx_addons' ),
									'type' => 'color'
								)
							)
						), $this->get_sc_name())
					)
				),
				trx_addons_sow_add_slider_param(),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_price', __FILE__, 'TRX_Addons_SOW_Widget_Price');
}
?>
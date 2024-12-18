<?php
/**
 * Widget: Socials
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_socials_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_socials_load' );
	function trx_addons_widget_socials_load() {
		register_widget('trx_addons_widget_socials');
	}
}

// Widget Class
class trx_addons_widget_socials extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_socials', 'description' => esc_html__('Socials - show links to the profiles in your favorites social networks', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_socials', esc_html__('ThemeREX Socials', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$description = isset($instance['description']) ? $instance['description'] : '';
		$align = isset($instance['align']) ? $instance['align'] : '';

		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'socials/tpl.default.php',
										'trx_addons_args_widget_socials', 
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, compact('title', 'align', 'description')),
											$instance, 'trx_addons_widget_socials')
									);
	}

	// Update the widget settings.
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = wp_kses_data($new_instance['description']);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_socials');
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'description' => ''
			), 'trx_addons_widget_socials')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_socials');
		
		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_socials');
		
		$this->show_field(array('name' => 'description',
								'title' => __('Short description:', 'trx_addons'),
								'value' => $instance['description'],
								'type' => 'textarea'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_socials');
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_socials_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_socials_merge_styles');
	function trx_addons_widget_socials_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'socials/_socials.scss';
		return $list;
	}
}



// trx_widget_socials
//-------------------------------------------------------------
/*
[trx_widget_socials id="unique_id" title="Widget title"]
*/
if ( !function_exists( 'trx_addons_sc_widget_socials' ) ) {
	function trx_addons_sc_widget_socials($atts, $content=''){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_socials', $atts, array(
			// Individual params
			"title" => "",
			"description" => "",
			"align" => "left",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_socials';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_socials' 
								. (trx_addons_exists_visual_composer() ? ' vc_widget_socials wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_socials', 'widget_socials') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_socials', $atts, $content);
	}
}


// Add [trx_widget_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc')) {
	function trx_addons_sc_widget_socials_add_in_vc() {
		
		add_shortcode("trx_widget_socials", "trx_addons_sc_widget_socials");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_widget_socials", 'trx_addons_sc_widget_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_socials_add_in_vc_params')) {
	function trx_addons_sc_widget_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_socials",
				"name" => esc_html__("Social Icons", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with social icons, that specified in the Theme Customizer", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_socials',
				"class" => "trx_widget_socials",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'trx_addons') ),
							"type" => "textarea"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Align", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of this item", 'trx_addons') ),
							"std" => "left",
							"value" => array(
								esc_html__('Left', 'trx_addons') => 'left',
								esc_html__('Center', 'trx_addons') => 'center',
								esc_html__('Right', 'trx_addons') => 'right'
							),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_socials' );
	}
}
?>
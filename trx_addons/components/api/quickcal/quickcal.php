<?php
/**
 * Plugin support: QuickCal Appointments
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_quickcal' ) ) {
	function trx_addons_exists_quickcal() {
		return class_exists( 'quickcal_plugin' );
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_quickcal_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_quickcal_importer_required_plugins', 10, 2 );
	function trx_addons_quickcal_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'quickcal')!==false && !trx_addons_exists_quickcal() )
			$not_installed .= '<br>' . esc_html__('QuickCal Appointments', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_quickcal_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options', 'trx_addons_quickcal_importer_set_options', 10, 1 );
	function trx_addons_quickcal_importer_set_options($options=array()) {
		if ( trx_addons_exists_quickcal() && in_array('quickcal', $options['required_plugins']) ) {
			$options['additional_options'][] = 'quickcal_%';				// Add slugs to export options of this plugin
			$options['additional_options'][] = 'booked_%';				// Attention! QuickCal use Booked plugin options
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_quickcal_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_quickcal_importer_check_row', 9, 4 );
	/**
	 * Check if row will be imported to the table 'wp_posts'
	 * 
	 * @hooked trx_addons_filter_importer_import_row
	 *
	 * @param boolean $flag		Allow import or not
	 * @param string  $table	Table name
	 * @param array   $row		Row data
	 * @param array   $list		List of required plugins
	 * 
	 * @return boolean			Allow import or not
	 */
	function trx_addons_quickcal_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'quickcal' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_quickcal() || ( function_exists( 'trx_addons_exists_booked' ) && trx_addons_exists_booked() ) ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( 'quickcal_appointments', 'booked_appointments' ) );
			}
		}
		return $flag;
	}
}


// VC support
//------------------------------------------------------------------------

// Add [cff] in the VC shortcodes list
if (!function_exists('trx_addons_sc_quickcal_add_in_vc')) {
	function trx_addons_sc_quickcal_add_in_vc() {

		if (!trx_addons_exists_visual_composer() || !trx_addons_exists_quickcal()) return;
		
		vc_lean_map( "quickcal-appointments", 'trx_addons_sc_quickcal_add_in_vc_params_ba');
		class WPBakeryShortCode_QuickCal_Appointments extends WPBakeryShortCode {}

		vc_lean_map( "quickcal-calendar", 'trx_addons_sc_quickcal_add_in_vc_params_bc');
		class WPBakeryShortCode_QuickCal_Calendar extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_quickcal_add_in_vc', 20);
}



// Params for QuickCal Appointments
if (!function_exists('trx_addons_sc_quickcal_add_in_vc_params_ba')) {
	function trx_addons_sc_quickcal_add_in_vc_params_ba() {
		return array(
				"base" => "quickcal-appointments",
				"name" => esc_html__("QuickCal Appointments", "trx_addons"),
				"description" => esc_html__("Display the currently logged in user's upcoming appointments", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_appointments',
				"class" => "trx_sc_single trx_sc_quickcal_appointments",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}
			
// Params for QuickCal Calendar
if (!function_exists('trx_addons_sc_quickcal_add_in_vc_params_bc')) {
	function trx_addons_sc_quickcal_add_in_vc_params_bc() {
		return array(
				"base" => "quickcal-calendar",
				"name" => esc_html__("QuickCal Calendar", "trx_addons"),
				"description" => esc_html__("Insert quickcal calendar", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_calendar',
				"class" => "trx_sc_single trx_sc_quickcal_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "calendar",
						"heading" => esc_html__("Calendar", "trx_addons"),
						"description" => esc_html__("Select quickcal calendar to display", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(trx_addons_array_merge(array(0 => esc_html__('- Select calendar -', 'trx_addons')), trx_addons_get_list_terms(false, 'booked_custom_calendars'))),
						"type" => "dropdown"
					),
					array(
						"param_name" => "year",
						"heading" => esc_html__("Year", "trx_addons"),
						"description" => esc_html__("Year to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("Y"),
						"value" => date("Y"),
						"type" => "textfield"
					),
					array(
						"param_name" => "month",
						"heading" => esc_html__("Month", "trx_addons"),
						"description" => esc_html__("Month to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date("m"),
						"value" => date("m"),
						"type" => "textfield"
					)
				)
			);
	}
}
?>
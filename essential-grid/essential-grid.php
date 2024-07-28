<?php
/*
@package Essential_Grid
@author ThemePunch <info@themepunch.com>
@link http://codecanyon.net/item/essential-grid-wordpress-plugin/7563340
@copyright 2024 ThemePunch
@wordpress-plugin
Plugin Name: Essential Grid
Plugin URI: https://www.essential-grid.com
Description: Essential Grid - Inject life into your websites using the most impressive WordPress gallery plugin
Version: 3.1.4
Author: ThemePunch
Author URI: https://themepunch.com
Text Domain: essential-grid
Domain Path: /languages
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (class_exists('Essential_Grid')) {
	die('ERROR: It looks like you have more than one instance of Essential Grid installed. Please remove additional instances for this plugin to work again.');
}

define('ESG_TP_TOOLS', '6.7.15');
define('ESG_REVISION', '3.1.4');

define('ESG_PLUGIN_SLUG', apply_filters('essgrid_set_slug', 'essential-grid'));
define('ESG_TEXTDOMAIN', apply_filters('essgrid_set_textdomain', 'essential-grid'));

define('ESG_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ESG_PLUGIN_SLUG_PATH',	plugin_basename(__FILE__));
define('ESG_PLUGIN_ADMIN_PATH', ESG_PLUGIN_PATH . 'admin');
define('ESG_PLUGIN_PUBLIC_PATH', ESG_PLUGIN_PATH . 'public');
define('ESG_PLUGIN_URL', get_esg_plugin_url()); //str_replace('essential-grid/index.php', '', plugins_url('essential-grid/index.php', __FILE__))

global $esg_dev_mode,
       $esg_wc_is_localized,
       $esg_loadbalancer;

$esg_dev_mode = file_exists(ESG_PLUGIN_PATH . 'public/assets/js/dev/esg.js');
$esg_wc_is_localized = false; //used to determinate if already done for cart button on this skin

require_once(ESG_PLUGIN_PATH . 'admin/includes/loadbalancer.class.php');
$esg_loadbalancer = new Essential_Grid_LoadBalancer();
$esg_loadbalancer->refresh_server_list();

require_once(ESG_PLUGIN_PATH . 'includes/db.class.php');

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once(ESG_PLUGIN_PATH . 'includes/Detection/Exception/MobileDetectException.php');
require_once(ESG_PLUGIN_PATH . 'includes/Detection/MobileDetect.php');
require_once(ESG_PLUGIN_PATH . 'includes/base.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/post-type.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/addons/addon.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/addons.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/image-optimization.class.php');
require_once(ESG_PLUGIN_PATH . 'public/essential-grid.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/global-css.class.php');
include_once(ESG_PLUGIN_PATH . 'includes/coloreasing.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/colorpicker.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/navigation.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.areas.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.cart.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.filter.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination-left.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination-right.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.sorting.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/item-skin.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/item-element.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/wpml.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/woocommerce.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/meta.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/meta-link.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/fonts.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/search.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/aq_resizer.class.php');
require_once(ESG_PLUGIN_PATH . 'includes/wordpress-update-fix.class.php');

new Essential_Grid_Post_Type();
new Essential_Grid_Wpml();

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, ['Essential_Grid_Db', 'create_tables']);
register_activation_hook(__FILE__, ['Essential_Grid_Item_Skin', 'propagate_default_item_skins']);
register_activation_hook(__FILE__, ['Essential_Grid_Navigation', 'propagate_default_navigation_skins']);
register_activation_hook(__FILE__, ['Essential_Grid_Global_Css', 'propagate_default_global_css']);
register_activation_hook(__FILE__, ['ThemePunch_Fonts', 'propagate_default_fonts']);
register_activation_hook(__FILE__, ['Essential_Grid', 'activation_hooks']);
Essential_Grid_Woocommerce::add_hooks();

/*----------------------------------------------------------------------------*
 * FrontEnd Special Functionality
 *----------------------------------------------------------------------------*/
if (!is_admin()) {
	/**
	 * load VC components in FrontEnd Editor of VC
	 * @since: 2.0
	 */
	function EssGridCheckVc()
	{
		if (function_exists('vc_is_inline') && vc_is_inline()) {
			require_once(ESG_PLUGIN_PATH . 'admin/essential-grid-admin.class.php');
			Essential_Grid_Admin::add_to_VC();
		}
	}
	add_action('vc_before_init', 'EssGridCheckVc');
}


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if (is_admin()) {
	require_once(ESG_PLUGIN_PATH . 'admin/includes/license.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/favorite.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/essential-grid-admin.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/update.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/dialogs.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/import.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/export.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/import-port.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/import-post.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/plugin-update.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/newsletter.class.php');
	require_once(ESG_PLUGIN_PATH . 'admin/includes/library.class.php');
	
	add_action('plugins_loaded', ['Essential_Grid_Db', 'create_tables'], 5);
	add_action('plugins_loaded', ['Essential_Grid_Admin', 'do_update_checks'], 5);
	add_action('plugins_loaded', ['Essential_Grid_Admin', 'get_instance'], 10);
	add_action('plugins_loaded', ['Essential_Grid_Admin', 'visual_composer_include'], 15);
}

// move Essential Grid construct into after_setup_theme
// to allow users add filters / actions in theme functions.php
add_action('after_setup_theme', 'esg_after_theme_setup', 10);
function esg_after_theme_setup()
{
	Essential_Grid::get_instance();
	add_action('widgets_init', ['Essential_Grid', 'register_custom_sidebars']);
	add_action('widgets_init', ['Essential_Grid', 'register_custom_widget']);

	add_filter('the_content', ['Essential_Grid', 'fix_shortcodes']);
	add_filter('post_thumbnail_html', ['Essential_Grid', 'post_thumbnail_replace'], 20, 5);

	add_shortcode('ess_grid', ['Essential_Grid', 'register_shortcode']);
	add_shortcode('ess_grid_ajax_target', ['Essential_Grid', 'register_shortcode_ajax_target']);
	add_shortcode('ess_grid_nav', ['Essential_Grid', 'register_shortcode_filter']);
	add_shortcode('ess_grid_search', ['Essential_Grid_Search', 'register_shortcode_search']);

	if (!is_admin()) {
		new Essential_Grid_Search();
	}
}

function get_esg_plugin_url(){
	$url = str_replace('index.php', '', plugins_url('index.php', __FILE__ ));
	if(strpos($url, 'http') === false){
		$site_url	= get_site_url();
		$url		= (substr($site_url, -1) === '/') ? substr($site_url, 0, -1). $url : $site_url. $url;
	}
	
	return str_replace([chr(10), chr(13)], '', $url);
}

<?php
/**
 * Plugin Name: Custom Modal Plugin
 * Description: Plugin para mostrar un modal al cargar la página usando un shortcode.
 * Version: 1.0.2
 * Author: Manuel Moreno
 */

// Evitar el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts y estilos
function custom_modal_enqueue_scripts() {
    wp_enqueue_style('custom-modal-style', plugins_url('modal-style.css', __FILE__));
    wp_enqueue_script('custom-modal-script', plugins_url('modal-script.js', __FILE__), array('jquery'), "1.0.6", true);
}
add_action('wp_enqueue_scripts', 'custom_modal_enqueue_scripts');

?>

<?php
/**
 * Plugin Name: Custom Modal Plugin
 * Description: Plugin para mostrar un modal al cargar la página usando un shortcode.
 * Version: 1.0
 * Author: Manuel Moreno
 */

// Evitar el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts y estilos
function custom_modal_enqueue_scripts() {
    wp_enqueue_style('custom-modal-style', plugins_url('modal-style.css', __FILE__));
    wp_enqueue_script('custom-modal-script', plugins_url('modal-script.js', __FILE__), array('jquery'), "1.0.4", true);
}
add_action('wp_enqueue_scripts', 'custom_modal_enqueue_scripts');

// Shortcode para mostrar el modal
function custom_modal_shortcode() {
    ob_start();
    ?>
    <div id="custom-modal" class="custom-modal">
        <div class="custom-modal-content">
            <h2>¿Eres mayor de edad?</h2>
            <h4>En este sitio encontaras los mejores servicios para tu evetos.</h4><br>
            <h4>¡Arma tu peda sin pedos!</h4>
            <div class="sc_services_item_button sc_item_button">
                <a href="#" id="btn-yes" class="sc_button color_style_default sc_button_default sc_button_size_normal custom-modal-btn">Soy mayor de edad</a>
            </div>
            <div class="sc_services_item_button sc_item_button">
                <a class="sc_button color_style_default sc_button_default sc_button_size_normal custom-modal-btn"
                    href="#" id="btn-no">Soy menor de edad</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_modal', 'custom_modal_shortcode');

<?php
/*
Plugin Name: PikQR Zamawianie Utworów
Description: Wtyczka do zamawiania utworów muzycznych i głosowania na nie.
Version: 1.0
Author: Kacper Kasiubowski
*/

require_once(plugin_dir_path(__FILE__) . 'install.php');


// Rejestracja skryptów i stylów
add_shortcode('pikqr_form', 'pikqr_form_shortcode');
add_shortcode('pikqr_lista_zamowien', 'pikqr_lista_zamowien_shortcode');
// Shortcode dla formularza zamówień
function pikqr_form_shortcode() {
    ob_start();
    include('zamowienia.php');
    return ob_get_clean();
}



// Shortcode dla listy zamówień
function pikqr_lista_zamowien_shortcode() {
    ob_start();
    include('lista-zamowien.php');
    return ob_get_clean();
}



function pikqr_admin_scripts() {
    wp_enqueue_script('pikqr-custom-admin-js', plugin_dir_url(__FILE__) . '/admin-js.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'pikqr_admin_scripts');




function pikqr_enqueue_scripts() {
    wp_enqueue_script('pikqr-lista-zamowien-js', plugins_url('/js-lista-zamowien.js', __FILE__), array('jquery'), '1.0', true);
    
}
add_action('wp_enqueue_scripts', 'pikqr_enqueue_scripts');

function pikqr_enqueue_styles() {
    // Rejestrowanie stylów
    wp_enqueue_style('pikqr-custom-style', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'pikqr_enqueue_styles');

register_activation_hook(__FILE__, 'pikqr_install_db');
?>

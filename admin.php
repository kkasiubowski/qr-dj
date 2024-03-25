<?php
// Zapewnienie, że WordPress został załadowany
if (!defined('ABSPATH')) exit;

// Dodanie zakładki w panelu administracyjnym
function pikqr_dodaj_menu_admina() {
    add_menu_page(
        'Zamówienia Utworów', // Tytuł strony
        'Zamówienia Utworów', // Tytuł menu
        'manage_options', // Capabilities - kto ma dostęp
        'pikqr-zamowienia', // Slug menu
        'pikqr_wyswietl_strone_admina', // Funkcja wyświetlająca zawartość strony
        'dashicons-format-audio', // Ikona menu
        6 // Pozycja w menu
    );
}

add_action('admin_menu', 'pikqr_dodaj_menu_admina');

// Funkcja wyświetlająca stronę administracyjną
function pikqr_wyswietl_strone_admina() {
    ?>
    <div class="wrap">
        <h2>Zamówienia Utworów</h2>
        <div id="lista-zamowien-admin"></div>
    </div>

    
    <?php
}

// Obsługa AJAX dla pobierania zamówień
function pikqr_pobierz_zamowienia_callback() {
    global $wpdb;
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';
    $zamowienia = $wpdb->get_results("SELECT * FROM $tabela_zamowien ORDER BY czas DESC");

    if($zamowienia) {
        foreach($zamowienia as $zamowienie) {
            echo '<div>' . esc_html($zamowienie->wykonawca) . ' - ' . esc_html($zamowienie->tytul) .
                 ' | Głosy: ' . esc_html($zamowienie->liczba_glosow) . '</div>';
        }
    } else {
        echo 'Brak zamówień.';
    }

    wp_die(); // Kończy działanie skryptu AJAX w WordPressie
}

add_action('wp_ajax_pobierz_zamowienia', 'pikqr_pobierz_zamowienia_callback');

// Tutaj należy dodać obsługę akcji AJAX 'usun_zamowienie'
function pikqr_usun_zamowienie_callback() {
    global $wpdb;
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';

    // Pobieranie ID zamówienia z żądania AJAX
    $zamowienie_id = isset($_POST['zamowienie_id']) ? intval($_POST['zamowienie_id']) : 0;

    if ($zamowienie_id) {
        $wpdb->delete($tabela_zamowien, ['id' => $zamowienie_id], ['%d']);
        wp_send_json_success(['message' => 'Zamówienie zostało usunięte.']);
    } else {
        wp_send_json_error(['message' => 'Nieprawidłowe ID zamówienia.']);
    }

    wp_die(); // Zakończenie obsługi AJAX.
}

add_action('wp_ajax_usun_zamowienie', 'pikqr_usun_zamowienie_callback');
?>
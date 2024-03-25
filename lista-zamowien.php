<?php
if (!defined('ABSPATH')) exit; // Zapewnienie bezpieczeństwa

// Funkcja wyświetlająca listę zamówień
function pikqr_wyswietl_liste_zamowien() {
    global $wpdb;
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';
    $zamowienia = $wpdb->get_results("SELECT * FROM $tabela_zamowien ORDER BY liczba_glosow DESC, czas DESC");

    if ($zamowienia) {
        echo '<ul id="lista-zamowien">';
        foreach ($zamowienia as $zamowienie) {
            echo '<li>' . esc_html($zamowienie->wykonawca) . ' - ' . esc_html($zamowienie->tytul) .
                ' <button class="glosuj" data-id="' . esc_attr($zamowienie->id) . '">Zagłosuj</button> ' .
                '<span class="liczba-glosow" id="liczba-glosow-' . esc_attr($zamowienie->id) . '">' . esc_html($zamowienie->liczba_glosow) . '</span>' .
                '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Brak zamówień do wyświetlenia.</p>';
    }
}


// Rejestracja akcji AJAX dla zalogowanych i niezalogowanych użytkowników
add_action('wp_ajax_pikqr_glosuj', 'pikqr_glosuj_callback');
add_action('wp_ajax_nopriv_pikqr_glosuj', 'pikqr_glosuj_callback');

function pikqr_glosuj_callback() {
    global $wpdb; // Globalna zmienna WordPressa do operacji na bazie danych
    $zamowienie_id = intval($_POST['zamowienie_id']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $tabela_glosow = $wpdb->prefix . 'pikqr_glosy';
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';

    // Sprawdzanie, czy użytkownik już głosował
    $czy_glosowano = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $tabela_glosow WHERE zamowienie_id = %d AND ip = %s",
        $zamowienie_id, $ip
    ));

    if ($czy_glosowano > 0) {
        // Głos już został oddany
        wp_send_json_error(['message' => 'Już zagłosowałeś na ten utwór.']);
    } else {
        // Dodanie głosu
        $wpdb->insert(
            $tabela_glosow,
            ['zamowienie_id' => $zamowienie_id, 'ip' => $ip],
            ['%d', '%s']
        );

        // Aktualizacja liczby głosów
        $wpdb->query($wpdb->prepare(
            "UPDATE $tabela_zamowien SET liczba_glosow = liczba_glosow + 1 WHERE id = %d",
            $zamowienie_id
        ));

        // Pobranie aktualnej liczby głosów
        $nowa_liczba_glosow = $wpdb->get_var($wpdb->prepare(
            "SELECT liczba_glosow FROM $tabela_zamowien WHERE id = %d",
            $zamowienie_id
        ));

        wp_send_json_success(['liczba_glosow' => $nowa_liczba_glosow]);
    }

    // Zakończenie działania skryptu
    die();
}


// Wywołanie funkcji wyświetlającej listę zamówień
pikqr_wyswietl_liste_zamowien();

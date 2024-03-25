<?php
if (!defined('ABSPATH')) exit; // Zapewnienie bezpieczeństwa

// Funkcja do sprawdzania limitu zamówień z jednego IP
function pikqr_sprawdz_limit_zamowien($ip) {
    global $wpdb;
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';
    $limit_czasu = current_time('mysql', 1) - DAY_IN_SECONDS; // 24 godziny wstecz
    $liczba_zamowien = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $tabela_zamowien WHERE ip = %s AND czas > FROM_UNIXTIME(%d)",
        $ip, $limit_czasu
    ));

    return $liczba_zamowien < 2; // Limit to 2 zamówienia na 24 godziny
}

// Przetwarzanie formularza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pikqr_zamowienie_submit'])) {
    $wykonawca = sanitize_text_field($_POST['wykonawca']);
    $tytul = sanitize_text_field($_POST['tytul']);
    $ip = $_SERVER['REMOTE_ADDR'];

    if (pikqr_sprawdz_limit_zamowien($ip)) {
        global $wpdb;
        $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';
        
        $wpdb->insert(
            $tabela_zamowien,
            array(
                'wykonawca' => $wykonawca,
                'tytul' => $tytul,
                'ip' => $ip,
                'liczba_glosow' => 0, // Domyślna wartość dla nowych zamówień
            ),
            array('%s', '%s', '%s', '%d')
        );

        echo '<p>Dziękujemy! Twoje zamówienie zostało złożone.</p>';
    } else {
        echo '<p>Przekroczono limit zamówień z tego adresu IP w ciągu ostatnich 24 godzin.</p>';
    }
}
?>

<!-- Formularz zamówienia -->
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
    <label for="wykonawca">Wykonawca:</label>
    <input type="text" id="wykonawca" name="wykonawca" required><br>
    <label for="tytul">Tytuł:</label>
    <input type="text" id="tytul" name="tytul" required><br>
    <input type="submit" name="pikqr_zamowienie_submit" value="Zamów utwór">
</form>

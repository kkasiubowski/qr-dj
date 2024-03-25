<?php
function pikqr_install_db() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $tabela_zamowien = $wpdb->prefix . 'pikqr_zamowienia';
    $tabela_glosow = $wpdb->prefix . 'pikqr_glosy';

    // SQL do tworzenia tabeli zamówień
    $sql_zamowienia = "CREATE TABLE IF NOT EXISTS $tabela_zamowien (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        wykonawca tinytext NOT NULL,
        tytul tinytext NOT NULL,
        liczba_glosow INT DEFAULT 0 NOT NULL,
        czas TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        ip VARCHAR(55) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL do tworzenia tabeli głosów
    $sql_glosy = "CREATE TABLE IF NOT EXISTS $tabela_glosow (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        zamowienie_id mediumint(9) NOT NULL,
        ip VARCHAR(55) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY (zamowienie_id) REFERENCES $tabela_zamowien(id) ON DELETE CASCADE
    ) $charset_collate;";

    // Bezpośrednie wykonanie zapytań SQL
    $wpdb->query($sql_zamowienia);
    $wpdb->query($sql_glosy);
}
?>
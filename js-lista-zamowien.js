jQuery(document).ready(function($) {
    $('.glosuj').click(function() {
        var przycisk = $(this);
        var idZamowienia = przycisk.data('id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pikqr_glosuj',
                zamowienie_id: idZamowienia
            },
            success: function(response) {
                if (response.success) {
                    $('#liczba-glosow-' + idZamowienia).text(response.data.liczba_glosow);
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});

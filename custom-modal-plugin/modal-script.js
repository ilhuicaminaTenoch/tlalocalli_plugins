jQuery(document).ready(function($) {
    // Mostrar el modal al cargar la página
    $('#custom-modal').fadeIn();
    $('body').addClass('modal-open');
    $(window).scrollTop(0);  // Desplazar al inicio de la página

    // Cerrar el modal cuando se haga clic en el botón "Sí"
    $('#btn-yes').on('click', function() {
        $('#custom-modal').fadeOut();
        $('body').removeClass('modal-open');
    });

    // Redirigir cuando se haga clic en el botón "No"
    $('#btn-no').on('click', function() {
        window.location.href = 'https://www.alcoholinformate.org.mx/'; // Cambia esta URL a la página deseada
    });

    // Evitar que el modal se cierre cuando se haga clic fuera del contenido del modal
    $(window).on('click', function(event) {
        if ($(event.target).is('.custom-modal')) {
            event.stopPropagation();
        }
    });
});

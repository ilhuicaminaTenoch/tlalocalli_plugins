jQuery(document).ready(function($) {
    // Función para establecer una cookie
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Función para obtener una cookie
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Mostrar el modal si la cookie de edad no está establecida
    if (!getCookie('isOfAge')) {
        $('#custom-modal').fadeIn();
        $('body').addClass('modal-open');  // Añadir clase para bloquear el scroll
        $(window).scrollTop(0);  // Desplazar al inicio de la página
    }

    // Cerrar el modal y establecer la cookie cuando se haga clic en el botón "Sí"
    $('#btn-yes').on('click', function() {
        setCookie('isOfAge', 'true', 30);  // Establecer cookie por 30 días
        $('#custom-modal').fadeOut();
        $('body').removeClass('modal-open');  // Quitar clase para restaurar el scroll
    });

    // Redirigir cuando se haga clic en el botón "No"
    $('#btn-no').on('click', function() {
        window.location.href = 'https://www.google.com'; // Cambia esta URL a la página deseada
    });

    // Evitar que el modal se cierre cuando se haga clic fuera del contenido del modal
    $('.custom-modal-content').on('click', function(event) {
        event.stopPropagation();
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('.custom-modal')) {
            event.stopPropagation();
        }
    });
});

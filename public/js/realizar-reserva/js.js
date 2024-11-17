document.addEventListener('DOMContentLoaded', function() {
    // Obtener la URL completa
    var url = new URL(window.location.href);

    // Obtener los parámetros de consulta
    var parametros = new URLSearchParams(url.search);

    // Extraer las fechas de los parámetros de consulta
    var fechaEntradaStr = parametros.get('fechaEntrada');
    var fechaSalidaStr = parametros.get('fechaSalida');

    // Crear fechas a partir de las cadenas obtenidas
    var fechaEntrada = new Date(fechaEntradaStr);
    var fechaSalida = new Date(fechaSalidaStr);

    // Función para validar la fecha del servicio
    function validarFecha(fecha) {
        if (!fecha) return true; // Si la fecha no se ha introducido, no es necesario validar
        var fechaServicio = new Date(fecha);
        return fechaServicio >= fechaEntrada && fechaServicio <= fechaSalida;
    }

    // Función para que el formulario no se envíe al hacer click en el botón de validar
    document.getElementById('validar-fechas').addEventListener('click', function(event) {
        // Evitar el envío del formulario
        event.preventDefault();
        
        // Obtener la fecha del servicio restaurante (Si se introduce)
        var fechaRestaurante = document.getElementById('fecha-restaurante').value;
        // Obtener la fecha del servicio spa (Si se introduce)
        var fechaSpa = document.getElementById('fecha-spa').value;
        // Obtener la fecha del servicio tour (Si se introduce)
        var fechaTours = document.getElementById('fecha-tours').value;

        var mensaje = ''; // Aquí se guardará el mensaje a mostrar
        let mensajeColor = 'text-danger'; // Inicialmente el mensaje se mostrará en rojo

        // Validar las fechas de los servicios
        if (!validarFecha(fechaRestaurante)) {
            mensaje += 'La fecha para el restaurante no es válida. Debe estar entre la fecha de entrada y la fecha de salida de la reserva.\n';
        }
        if (!validarFecha(fechaSpa)) {
            mensaje += 'La fecha para el spa no es válida. Debe estar entre la fecha de entrada y la fecha de salida de la reserva.\n';
        }
        if (!validarFecha(fechaTours)) {
            mensaje += 'La fecha para los tours no es válida. Debe estar entre la fecha de entrada y la fecha de salida de la reserva.\n';
        }

        // Verificar si la estancia es de 2 días y 1 noche y si lo es no se podrá seleccionar servicios para dicha estancia
        const diferencia = (fechaSalida - fechaEntrada) / (1000 * 60 * 60 * 24);
        if (diferencia === 1) {
            if (fechaRestaurante || fechaSpa || fechaTours) {
                mensaje += 'No se pueden seleccionar servicios adicionales para una estancia de 2 días y 1 noche.\n';
            }
        }

        // Mostrar mensaje de error si ocurre algún problema
        const mensajeError = document.getElementById('mensaje-error');
        const submitButton = document.getElementById('guardar-reserva');
        if (mensaje) {
            mensajeError.textContent = mensaje;
            mensajeError.className = mensajeColor; // Aplicamos el color que definimos antes
            submitButton.disabled = true; // Deshabilitar el botón de realizar reserva
        } else {
            mensajeError.textContent = 'Servicios reservados.';
            mensajeError.className = 'text-success'; // Si no hay error lo mostramos en verde
            submitButton.disabled = false; // Habilitar el botón de realizar reserva
        }
    });

    // Función para ir a la imagen anterior en el carrusel
    document.querySelector('.carousel-control-prev').addEventListener('click', function() {
        // Obtiene todas las imagenes del carrusel
        var items = document.querySelectorAll('.carousel-item');

        // Encuentra el índice de la imagen actualmente activa
        var activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));

        // Elimina la clase 'active' de la imagen actualmente activa
        items[activeIndex].classList.remove('active');

        // Calcula el índice de la imagen anterior
        // Si estamos en la primera imagen, volvemos a la última
        var prevIndex = (activeIndex - 1 + items.length) % items.length;

        // Añade la clase 'active' a la imagen anterior
        items[prevIndex].classList.add('active');
    });

    // Función para ir a la imagen siguiente en el carrusel
    document.querySelector('.carousel-control-next').addEventListener('click', function() {
        // Obtiene todas las imagenes del carrusel
        var items = document.querySelectorAll('.carousel-item');

        // Encuentra el índice de la imagen actualmente activa
        var activeIndex = Array.from(items).findIndex(item => item.classList.contains('active'));

        // Elimina la clase 'active' de la imagen actualmente activa
        items[activeIndex].classList.remove('active');

        // Calcula el índice de la imagen anterior
        // Si estamos en la primera imagen, volvemos a la última
        var nextIndex = (activeIndex + 1) % items.length;

        // Añade la clase 'active' a la imagen anterior
        items[nextIndex].classList.add('active');
    });

    // Inicializar el mapa
    var map = L.map('mapContainer').setView([40.4168, -3.7038], 13); // Coordenadas de Madrid, España

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([40.4168, -3.7038]).addTo(map) // Coordenadas de Madrid, España
        .bindPopup('Ubicación en Madrid, España.')
        .openPopup();

    // Deshabilitar el botón de enviar al enviar el formulario
    document.getElementById('reservaForm').addEventListener('submit', function(event) {
        var submitButton = document.getElementById('guardar-reserva');
        submitButton.disabled = true;
        submitButton.innerText = 'Enviando...'; // Cambia el texto del botón para indicar que se está enviando
    });
});
document.addEventListener('DOMContentLoaded', function () {
    // Establecer la fecha mínima para el campo de fecha de entrada
    var fechaEntrada = document.querySelector('#fecha-entrada');
    if (fechaEntrada) {
        var hoy = new Date();
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0');
        var yyyy = hoy.getFullYear();

        hoy = yyyy + '-' + mm + '-' + dd;
        fechaEntrada.setAttribute('min', hoy);
    }

    // Establecer la fecha mínima para el campo de fecha de salida (del día siguiente a la fecha de entrada en adelante)
    var fechaSalida = document.querySelector('#fecha-salida');
    if (fechaSalida) {
        var manana = new Date();
        manana.setDate(manana.getDate() + 1); // Añadir un día a la fecha actual
        var ddManana = String(manana.getDate()).padStart(2, '0');
        var mmManana = String(manana.getMonth() + 1).padStart(2, '0');
        var yyyyManana = manana.getFullYear();
        manana = yyyyManana + '-' + mmManana + '-' + ddManana;
        fechaSalida.setAttribute('min', manana);
    }

    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var fechaEntrada = document.querySelector('#fecha-entrada');
            var fechaSalida = document.querySelector('#fecha-salida');
            var isValid = true;
            

            // Validación de ubicación
            var ubicacion = document.querySelector('#ubicacion');
            if (ubicacion && !ubicacion.value.trim()) {
                ubicacion.classList.add('is-invalid');
                document.querySelector('#ubicacion-error').style.display = 'block';
                isValid = false;
            } else if (ubicacion) {
                ubicacion.classList.remove('is-invalid');
                document.querySelector('#ubicacion-error').style.display = 'none';
            }

            // Validación de fechas
            if (fechaEntrada && fechaSalida) {
                if (!fechaEntrada.value) {
                    fechaEntrada.classList.add('error');
                    isValid = false;
                } else {
                    fechaEntrada.classList.remove('error');
                }

                if (!fechaSalida.value) {
                    fechaSalida.classList.add('error');
                    isValid = false;
                } else {
                    fechaSalida.classList.remove('error');
                }

                
                if (fechaEntrada.value && fechaSalida.value) {
                    var fechaEntradaDate = new Date(fechaEntrada.value);
                    var fechaSalidaDate = new Date(fechaSalida.value);

                    if (fechaSalidaDate <= fechaEntradaDate) {
                        fechaSalida.classList.add('is-invalid');
                        document.querySelector('#fecha-salida-error').classList.add('show');
                        isValid = false;
                    } else {
                        fechaSalida.classList.remove('is-invalid');
                        document.querySelector('#fecha-salida-error').classList.remove('show');
                    }
                }
            }

            // Validación de adultos y niños
            var adultos = document.querySelector('#adultos');
            var ninos = document.querySelector('#ninos');
            if (adultos && ninos && parseInt(adultos.value) === 0 && parseInt(ninos.value) === 0) {
                adultos.classList.add('is-invalid');
                ninos.classList.add('is-invalid');
                document.querySelector('#adultos-error').style.display = 'block';
                document.querySelector('#ninos-error').style.display = 'block';
                isValid = false;
            } else if (adultos && ninos) {
                adultos.classList.remove('is-invalid');
                ninos.classList.remove('is-invalid');
                document.querySelector('#adultos-error').style.display = 'none';
                document.querySelector('#ninos-error').style.display = 'none';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Actualiza las edades de los niños cuando cambie el número de niños
        var numNinosInput = document.querySelector('#ninos');
        if (numNinosInput) {
            numNinosInput.addEventListener('input', actualizarEdadesDeNinos);
        }
    }

    // Agregar autocompletado a la ubicación
    var ubicacionInput = document.querySelector('#ubicacion');
    if (ubicacionInput) {
        ubicacionInput.addEventListener('input', function () {
            var query = ubicacionInput.value;

            if (query.length > 0) {
                fetch(`/buscarUbicaciones?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        var datalist = document.querySelector('#dList');
                        datalist.innerHTML = '';

                        data.forEach(item => {
                            var option = document.createElement('option');
                            option.value = item.nombre;
                            datalist.appendChild(option);
                        });
                    });
            }
        });
    }
});

function actualizarEdadesDeNinos() {
    var numNinos = parseInt(document.getElementById('ninos').value, 10);
    var contenedorEdades = document.getElementById('edades-de-ninos');

    // Limpia campos de edad existentes
    contenedorEdades.innerHTML = '';

    for (let i = 1; i <= numNinos; i++) {
        var div = document.createElement('div');
        div.className = 'form-group';
        div.innerHTML = `
            <label for="edad-nino-${i}">Edad del niño ${i}</label>
            <select id="edad-nino-${i}" name="edad-nino-${i}" class="form-control">
                ${crearOpcionesDeEdad()}
            </select>
        `;
        contenedorEdades.appendChild(div);
    }
}

function crearOpcionesDeEdad() {
    var opciones = '';
    for (let edad = 0; edad <= 17; edad++) {
        opciones += `<option value="${edad}">${edad} años</option>`;
    }
    return opciones;
}


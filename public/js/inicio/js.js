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

    // Establecer la fecha mínima para el campo de fecha de salida (del dia siguiente a la fecha de entrada en adelante)
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
            if (!ubicacion.value.trim()) {
                ubicacion.classList.add('is-invalid');
                document.querySelector('#ubicacion-error').style.display = 'block';
                isValid = false;
            } else {
                ubicacion.classList.remove('is-invalid');
                document.querySelector('#ubicacion-error').style.display = 'none';
            }

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

            if (parseInt(adultos.value) === 0 && parseInt(ninos.value) === 0) {
                adultos.classList.add('is-invalid');
                ninos.classList.add('is-invalid');
                document.querySelector('#adultos-error').style.display = 'block';
                document.querySelector('#ninos-error').style.display = 'block';
                isValid = false;
            } else {
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
    var numNinos = parseInt(document.getElementById('ninos').value, 10); //Guarda el número de niños
    var contenedorEdades = document.getElementById('edades-de-ninos');//Almacena el id del contenedor de las edades de los niños

    // Limpia campos de edad existentes
    contenedorEdades.innerHTML = '';

    //Bucle for que por cada niño introducido crea un nuevo campo donde introducir su edad
    for (let i = 1; i <= numNinos; i++) {
        var div = document.createElement('div'); //Variable que almacena un contenedor que aprece cuando se introduce al menos un niño.
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

//Función que guarda las posibles edades de los niños
function crearOpcionesDeEdad() {
    var opciones = ''; //Variable que almacena el número de opciones
    for (let edad = 0; edad <= 17; edad++) { //Bucle que recorre todas las edades posibles
        opciones += `<option value="${edad}">${edad} años</option>`; //Muestra todas las posibles opciones
    }
    return opciones; //Devuelve la opción elegida
}

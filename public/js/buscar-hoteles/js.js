document.addEventListener("DOMContentLoaded", function () {
    // Establece la fecha mínima para el campo de fecha de entrada
    var fechaEntrada = document.querySelector("#fecha-entrada");
    if (fechaEntrada) {
        var hoy = new Date(); // Obtiene la fecha actual
        var dd = String(hoy.getDate()).padStart(2, "0"); // Obtiene el día y lo formatea
        var mm = String(hoy.getMonth() + 1).padStart(2, "0"); // Obtiene el mes y lo formatea
        var yyyy = hoy.getFullYear(); // Obtiene el año

        hoy = yyyy + "-" + mm + "-" + dd; // Formatea la fecha en formato YYYY-MM-DD
        fechaEntrada.setAttribute("min", hoy); // Establece la fecha mínima en el campo de entrada
    }

    // Establece la fecha mínima para el campo de fecha de salida (del día siguiente a la fecha de entrada en adelante)
    var fechaSalida = document.querySelector("#fecha-salida");
    if (fechaSalida) {
        var manana = new Date(); // Obtiene la fecha actual
        manana.setDate(manana.getDate() + 1); // Añade un día a la fecha actual
        var ddManana = String(manana.getDate()).padStart(2, "0"); // Obtiene el día y lo formatea
        var mmManana = String(manana.getMonth() + 1).padStart(2, "0"); // Obtiene el mes y lo formatea
        var yyyyManana = manana.getFullYear(); // Obtiene el año
        manana = yyyyManana + "-" + mmManana + "-" + ddManana; // Formatea la fecha en formato YYYY-MM-DD
        fechaSalida.setAttribute("min", manana); // Establece la fecha mínima en el campo de salida
    }

    // Maneja el evento de envío del formulario
    var form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            var fechaEntrada = document.querySelector("#fecha-entrada");
            var fechaSalida = document.querySelector("#fecha-salida");
            var isValid = true; // Variable para determinar si el formulario es válido

            // Validación de ubicación
            var ubicacion = document.querySelector("#ubicacion");
            if (ubicacion && !ubicacion.value.trim()) {
                ubicacion.classList.add("is-invalid"); // Añade una clase para mostrar un error
                document.querySelector("#ubicacion-error").style.display =
                    "block"; // Muestra el mensaje de error
                isValid = false; // Marca el formulario como no válido
            } else if (ubicacion) {
                ubicacion.classList.remove("is-invalid"); // Elimina la clase de error si el campo es válido
                document.querySelector("#ubicacion-error").style.display =
                    "none"; // Oculta el mensaje de error
            }

            // Validación de fechas
            if (fechaEntrada && fechaSalida) {
                if (!fechaEntrada.value) {
                    fechaEntrada.classList.add("error"); // Añade una clase para mostrar un error
                    isValid = false; // Marca el formulario como no válido
                } else {
                    fechaEntrada.classList.remove("error"); // Elimina la clase de error si el campo es válido
                }

                if (!fechaSalida.value) {
                    fechaSalida.classList.add("error"); // Añade una clase para mostrar un error
                    isValid = false; // Marca el formulario como no válido
                } else {
                    fechaSalida.classList.remove("error"); // Elimina la clase de error si el campo es válido
                }

                // Verifica que la fecha de salida sea posterior a la fecha de entrada
                if (fechaEntrada.value && fechaSalida.value) {
                    var fechaEntradaDate = new Date(fechaEntrada.value);
                    var fechaSalidaDate = new Date(fechaSalida.value);

                    if (fechaSalidaDate <= fechaEntradaDate) {
                        fechaSalida.classList.add("is-invalid"); // Añade una clase para mostrar un error
                        document
                            .querySelector("#fecha-salida-error")
                            .classList.add("show"); // Muestra el mensaje de error
                        isValid = false; // Marca el formulario como no válido
                    } else {
                        fechaSalida.classList.remove("is-invalid"); // Elimina la clase de error si el campo es válido
                        document
                            .querySelector("#fecha-salida-error")
                            .classList.remove("show"); // Oculta el mensaje de error
                    }
                }
            }

            // Validación de adultos y niños
            var adultos = document.querySelector("#adultos");
            var ninos = document.querySelector("#ninos");
            if (
                adultos &&
                ninos &&
                parseInt(adultos.value) === 0 &&
                parseInt(ninos.value) === 0
            ) {
                adultos.classList.add("is-invalid"); // Añade una clase para mostrar un error
                ninos.classList.add("is-invalid"); // Añade una clase para mostrar un error
                document.querySelector("#adultos-error").style.display =
                    "block"; // Muestra el mensaje de error
                document.querySelector("#ninos-error").style.display = "block"; // Muestra el mensaje de error
                isValid = false; // Marca el formulario como no válido
            } else if (adultos && ninos) {
                adultos.classList.remove("is-invalid"); // Elimina la clase de error si el campo es válido
                ninos.classList.remove("is-invalid"); // Elimina la clase de error si el campo es válido
                document.querySelector("#adultos-error").style.display = "none"; // Oculta el mensaje de error
                document.querySelector("#ninos-error").style.display = "none"; // Oculta el mensaje de error
            }

            // Previene el envío del formulario si no es válido
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Actualiza los campos de edad de los niños cuando cambia el número de niños
        var numNinosInput = document.querySelector("#ninos");
        if (numNinosInput) {
            numNinosInput.addEventListener("input", actualizarEdadesDeNinos);
        }
    }

    // Agrega autocompletado a la ubicación
    var ubicacionInput = document.querySelector("#ubicacion");
    if (ubicacionInput) {
        ubicacionInput.addEventListener("input", function () {
            var query = ubicacionInput.value;

            if (query.length > 0) {
                fetch(`/buscarUbicaciones?query=${query}`)
                    .then((response) => response.json())
                    .then((data) => {
                        var datalist = document.querySelector("#dList");
                        datalist.innerHTML = "";

                        // Añade nuevas opciones basadas en los resultados de la búsqueda
                        data.forEach((item) => {
                            var option = document.createElement("option");
                            option.value = item.nombre;
                            datalist.appendChild(option);
                        });
                    });
            }
        });
    }
});

// Función para actualizar los campos de edad de los niños
function actualizarEdadesDeNinos() {
    var numNinos = parseInt(document.getElementById("ninos").value, 10);
    var contenedorEdades = document.getElementById("edades-de-ninos");

    // Limpia campos de edad existentes
    contenedorEdades.innerHTML = "";

    // Crea campos para cada niño basado en el número de niños
    for (let i = 1; i <= numNinos; i++) {
        var div = document.createElement("div");
        div.className = "form-group";
        div.innerHTML = `
            <label for="edad-nino-${i}">Edad del niño ${i}</label>
            <select id="edad-nino-${i}" name="edad-nino-${i}" class="form-control">
                ${crearOpcionesDeEdad()}
            </select>
        `;
        contenedorEdades.appendChild(div);
    }
}

// Función para crear opciones de edad para el select
function crearOpcionesDeEdad() {
    var opciones = "";
    for (let edad = 0; edad <= 17; edad++) {
        opciones += `<option value="${edad}">${edad} años</option>`;
    }
    return opciones;
}

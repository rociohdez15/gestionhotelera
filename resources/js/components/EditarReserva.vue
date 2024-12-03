<template>
    <div class="editar-reserva">
        <h1 class="titulo"><strong>Editar Reserva</strong></h1>

        <div v-if="reserva">
            <h2><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</h2>
            <h2>
                <strong>Nombre del Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </h2>

            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Fecha de Entrada:</strong></h2>
                <label for="fechaEntrada" class="form-label me-2 mb-0"></label>
                <input
                    type="date"
                    class="form-control rounded-input me-2"
                    id="fechaEntrada"
                    v-model="fechaEntrada"
                    style="max-width: 200px"
                    @change="validarFechas"
                />
            </div>
            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Fecha de Salida:</strong></h2>
                <label for="fechaSalida" class="form-label me-2 mb-0"></label>
                <input
                    type="date"
                    class="form-control rounded-input"
                    id="fechaSalida"
                    v-model="fechaSalida"
                    style="max-width: 200px"
                    @change="validarFechas"
                />
            </div>

            <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>

            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Nº Adultos:</strong></h2>
                <label for="numAdultos" class="form-label me-2 mb-0"></label>
                <input
                    type="number"
                    class="form-control rounded-input me-2"
                    id="numAdultos"
                    v-model="numAdultos"
                    :max="maxAdults"
                    min="1"
                    @change="updateMaxChildren"
                    style="max-width: 80px"
                />
            </div>
            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Nº Niños:</strong></h2>
                <label for="numNinos" class="form-label me-2 mb-0"></label>
                <input
                    type="number"
                    class="form-control rounded-input me-2"
                    id="numNinos"
                    v-model="numNinos"
                    min="0"
                    :max="maxChildren"
                    @change="updateChildAgeFields"
                    style="max-width: 80px"
                />
            </div>

            <h2><strong>Hotel: </strong>{{ hotel.nombre }}</h2>
            <h2><strong>Precio Reserva: </strong>{{ reserva.preciototal }}</h2>
            <h2><strong>Dirección del Hotel:</strong> {{ hotel.direccion }}</h2>

            <h2><strong>Edades de los Niños:</strong></h2>
            <div id="edades_ninos">
                <ul>
                    <li
                        v-for="(nino, index) in edadesNinos"
                        :key="index"
                        style="display: flex; align-items: center"
                    >
                        <h2 style="margin-right: 10px">
                            <strong>• Niño {{ index + 1 }}:</strong>
                        </h2>
                        <label :for="'nino-' + index" class="visually-hidden"
                            >Edad del Niño {{ index + 1 }}</label
                        >
                        <input
                            type="number"
                            v-model="nino.edad"
                            class="form-control rounded-input"
                            :id="'edad-nino-' + index"
                            min="0"
                            max="17"
                            style="max-width: 80px; display: inline-block"
                            placeholder="Edad"
                        />
                        <h2 style="margin-left: 10px"><strong>años</strong></h2>
                    </li>
                </ul>
            </div>

            <br />
            <div class="d-flex justify-content-center mb-3">
                <button
                    class="btn btn-primary"
                    @click="actualizarReserva"
                    :disabled="!isFormValid"
                >
                    Actualizar Reserva
                </button>
            </div>
        </div>

        <div v-else>
            <p>No se encontraron detalles de la reserva.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditarReserva",
    data() {
        return {
            reserva: null,
            hotel: null,
            cliente: null,
            edadesNinos: [],
            numAdultos: null,
            numNinos: null,
            maxChildren: 0,
            maxAdults: 4,
            reservaID: null,
            habitacionID: null,
            fechaEntrada: null,
            fechaSalida: null,
            habitacionID: null,
            hotelID: null,
            originalFechaEntrada: null,
            originalFechaSalida: null,
            errorMessage: "",
        };
    },
    mounted() {
        var appElement = document.getElementById("app");
        this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
        this.hotel = JSON.parse(appElement.getAttribute("data-hotel"));
        this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));
        this.edadesNinos = JSON.parse(
            appElement.getAttribute("data-edades-ninos")
        );
        this.reservaID = appElement.getAttribute("data-reserva-id");

        this.fechaEntrada = this.reserva.fechainicio;
        this.fechaSalida = this.reserva.fechafin;
        this.numAdultos = this.reserva.num_adultos;
        this.numNinos = this.reserva.num_ninos;
        this.hotelID = this.hotel.hotelID;
        this.habitacionID = this.reserva.habitacionID;

        this.originalFechaEntrada = this.fechaEntrada;
        this.originalFechaSalida = this.fechaSalida;

        if (this.edadesNinos && Array.isArray(this.edadesNinos)) {
            this.edadesNinos = this.edadesNinos.map((nino) => ({
                edad: nino.edad || null,
            }));
        } else {
            this.edadesNinos = [];
        }

        this.updateMaxChildren();
    },
    computed: {
        isFormValid() {
            return this.errorMessage === "" && this.allChildAgesFilled;
        },
        allChildAgesFilled() {
            return this.edadesNinos.every((nino) => nino.edad !== null);
        },
    },
    methods: {
        updateMaxChildren() {
            var numAdultos = this.numAdultos || 0;
            var numNinos = this.numNinos || 0;
            var totalPersonas = numAdultos + numNinos;

            if (totalPersonas > 4) {
                if (numAdultos > 4 - numNinos) {
                    this.numAdultos = 4 - numNinos;
                }
                if (numNinos > 4 - numAdultos) {
                    this.numNinos = 4 - numAdultos;
                }
            }

            this.maxChildren = 4 - this.numAdultos;
            this.maxAdults = 4 - this.numNinos;

            this.updateChildAgeFields();
        },
        updateChildAgeFields() {
            var numNinos = this.numNinos || 0;
            var edadesExistentes = this.edadesNinos.slice(0, numNinos);
            this.edadesNinos = [];
            for (var i = 0; i < numNinos; i++) {
                this.edadesNinos.push({
                    edad: edadesExistentes[i] ? edadesExistentes[i].edad : null,
                });
            }
        },
        async validarFechas() {
            this.errorMessage = "";
            if (!this.fechaEntrada || !this.fechaSalida) {
                return;
            }

            var hoy = new Date();
            var entrada = new Date(this.fechaEntrada);
            var salida = new Date(this.fechaSalida);

            if (entrada < hoy || salida < hoy) {
                this.errorMessage =
                    "Las fechas deben ser posteriores al día actual.";
                return;
            }

            if (salida < entrada) {
                this.errorMessage =
                    "La fecha de salida no puede ser anterior a la fecha de entrada.";
                return;
            }
        },
        async verificarHabitacionDisponible() {
            try {
                var response = await fetch(
                    `/verificar-habitacion/${this.hotelID}?numAdultos=${this.numAdultos}`
                );

                if (response.ok) {
                    var disponible = await response.json();

                    if (disponible.habitacionID) {
                        this.habitacionID = disponible.habitacionID;
                    } else {
                        window.location.href =
                            "/listarreservas?error=No hay habitaciones disponibles para el número de adultos especificado.";
                        return false;
                    }

                    return disponible;
                } else {
                    window.location.href =
                        "/listarreservas?error=No hay habitaciones disponibles para el número de adultos especificado.";
                    return false;
                }
            } catch (error) {
                window.location.href =
                    "/listarreservas?error=Se produjo un error al verificar la disponibilidad de la habitación.";
                return false;
            }
        },
        async actualizarReserva() {
            var entrada = new Date(this.fechaEntrada);
            var salida = new Date(this.fechaSalida);

            if (
                this.fechaEntrada !== this.originalFechaEntrada ||
                this.fechaSalida !== this.originalFechaSalida
            ) {
                console.log("Enviando datos para comprobar reserva:", {
                    fechaEntrada: entrada.toISOString().split("T")[0],
                    fechaSalida: salida.toISOString().split("T")[0],
                });

                var response = await fetch(
                    `/comprobar-reserva/${this.hotelID}?fechaEntrada=${
                        entrada.toISOString().split("T")[0]
                    }&fechaSalida=${salida.toISOString().split("T")[0]}`,
                    {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    }
                );

                if (response.ok) {
                    var existeReserva = await response.json();
                    if (existeReserva) {
                        window.location.href =
                            "/listarreservas?error=Ya existe una reserva en el hotel durante las fechas seleccionadas.";
                        return;
                    }
                } else {
                    var errorText = await response.text();
                    window.location.href = `/listarreservas?error=Error al comprobar la disponibilidad: ${errorText}`;
                    return;
                }
            }

            var habitacionDisponible =
                await this.verificarHabitacionDisponible();
            if (!habitacionDisponible) {
                return;
            }

            var reservaActualizada = {
                fechaEntrada: this.fechaEntrada,
                fechaSalida: this.fechaSalida,
                numAdultos: this.numAdultos,
                numNinos: this.numNinos,
                edadesNinos: this.edadesNinos.map((nino) => nino.edad),
                habitacionID: this.habitacionID,
            };

            try {
                var actualizarResponse = await fetch(
                    `/editarreserva/${this.reservaID}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify(reservaActualizada),
                    }
                );

                if (actualizarResponse.ok) {
                    var result = await actualizarResponse.json();
                    if (result.success) {
                        window.location.href =
                            "/listarreservas?success=" +
                            encodeURIComponent(result.success);
                    } else {
                        window.location.href =
                            "/listarreservas?error=" +
                            encodeURIComponent(result.error);
                    }
                } else {
                    var errorText = await actualizarResponse.text();
                    window.location.href = `/listarreservas?error=Error al actualizar la reserva: ${errorText}`;
                }
            } catch (e) {
                window.location.href =
                    "/listarreservas?error=Error al actualizar la reserva.";
            }
        },
    },
};
</script>

<style scoped>
.editar-reserva {
    padding: 20px;
    background-color: #c9c3c3;
    border: 1px solid #c9c3c3;
    border-radius: 8px;
    max-width: 600px;
    margin: 20px auto;
}

.titulo {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
}

.rounded-input {
    border-radius: 12px;
    border: 1px solid #ccc;
}

.editar-reserva p,
.editar-reserva label,
.editar-reserva strong,
.editar-reserva h2,
.editar-reserva h3 {
    color: black;
    line-height: 2;
}

.me-2 {
    margin-right: 0.5rem;
}
</style>

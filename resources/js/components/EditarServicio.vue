<template>
    <div class="editar-servicio">
        <h1 class="titulo"><strong>Editar Servicio</strong></h1>

        <div v-if="servicio">
            <p><strong>ID de Servicio:</strong> {{ servicio.servicioID }}</p>
            <p><strong>Nombre del Servicio:</strong> {{ servicio.nombre }}</p>
            <p><strong>Hotel:</strong> {{ reserva.hotel_nombre }}</p>
            <p>
                <strong>Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </p>

            <div class="mb-3 d-flex align-items-center">
                <label
                    for="fechaServicio"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                    <strong>Fecha:</strong>
                </label>
                <input
                    type="date"
                    class="form-control rounded-input me-2"
                    id="fechaServicio"
                    style="max-width: 200px"
                    v-model="fecha"
                    @change="validarFecha"
                />
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label
                    for="horaServicio"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                    <strong>Hora:</strong>
                </label>
                <input
                    type="time"
                    class="form-control rounded-input me-2"
                    id="horaServicio"
                    style="max-width: 200px"
                    v-model="hora"
                />
            </div>

            <div class="mb-3 d-flex align-items-center">
                <label
                    for="descripcionServicio"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                    <strong>Servicio:</strong>
                </label>
                <p class="form-control-static me-2">
                    {{ servicio.descripcion }}
                </p>
            </div>

            <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>

            <br />
            <div class="d-flex justify-content-center mb-3">
                <button
                    class="btn btn-primary"
                    @click="actualizarServicio"
                    :disabled="!isFormValid"
                >
                    Actualizar Servicio
                </button>
            </div>
        </div>

        <div v-else>
            <p>No se encontraron detalles del servicio.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditarServicio",
    data() {
        return {
            servicio: null,
            hotel: null,
            cliente: null,
            reserva: null,
            fecha: "",
            hora: "",
            errorMessage: "",
            isFormValid: false,
            fechaInicio: null,
            fechaFin: null,
        };
    },
    mounted() {
        const appElement = document.getElementById("app4");
        this.servicio = JSON.parse(appElement.getAttribute("data-servicio"));
        this.hotel = JSON.parse(appElement.getAttribute("data-hotel"));
        this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));
        this.fecha = appElement.getAttribute("data-fecha");

        this.reserva = {
            hotel_nombre: appElement.getAttribute("data-hotel-nombre"),
            fechainicio: appElement.getAttribute("data-fecha-inicio"),
            fechafin: appElement.getAttribute("data-fecha-fin"),
        };

        const horaCompleta = appElement.getAttribute("data-hora");
        const [hora, minutos] = horaCompleta.split(':');
        this.hora = `${hora}:${minutos}`;

        this.fechaInicio = new Date(this.reserva.fechainicio);
        this.fechaFin = new Date(this.reserva.fechafin);
    },
    methods: {
        validarFecha() {
            const fechaSeleccionada = new Date(this.fecha);
            this.isFormValid = (fechaSeleccionada >= this.fechaInicio && fechaSeleccionada <= this.fechaFin);
            this.errorMessage = this.isFormValid ? "" : "La fecha seleccionada no está dentro del rango permitido.";
        },

        async actualizarServicio() {
            if (!this.isFormValid) {
                return;
            }

            const servicioActualizado = {
                fecha: this.fecha,
                hora: this.hora,
                descripcion: this.servicio.descripcion,
            };

            try {
                const response = await fetch(`/editarservicio/${this.servicio.servicioID}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(servicioActualizado),
                });

                if (response.ok) {
                    window.location.href = "/listarservicios?success=Servicio actualizado correctamente";
                } else {
                    const errorText = await response.text();
                    window.location.href = `/listarservicios?error=${encodeURIComponent(errorText)}`;
                }
            } catch (e) {
                window.location.href = "/listarservicios?error=Error al actualizar el servicio.";
            }
        },
    },
};
</script>

<style scoped>
.editar-servicio {
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
</style>

<template>
    <div class="editar-reserva">
        <h1 class="titulo"><strong>Registrar Check-In</strong></h1>

        <div v-if="reserva">
            <h2><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</h2>
            <h2>
                <strong>Nombre del Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </h2>

            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Fecha de Check-In: </strong></h2>
                <label
                    for="fechaCheckin"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                </label>
                <input
                    type="datetime-local"
                    class="form-control rounded-input me-2"
                    id="fechaCheckin"
                    v-model="fechaCheckin"
                    style="max-width: 200px"
                    @change="validarFechas"
                />
            </div>
            <div class="mb-3 d-flex align-items-center">
                <h2 class="me-2"><strong>Fecha de Check-Out: </strong></h2>
                <label class="form-label me-2" style="margin-bottom: 0">
                </label>
                <span style="color: black">{{ reserva.fecha_checkout }}</span>
            </div>

            <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>

            <br />
            <div class="d-flex justify-content-center mb-3">
                <button
                    class="btn btn-primary"
                    @click="registrarCheckin"
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
            cliente: null,
            fechaCheckin: null,
            errorMessage: "",
        };
    },
    mounted() {
        var appElement = document.getElementById("app3");
        this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
        this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));

        // Convertir la fecha de checkin al formato "YYYY-MM-DDTHH:MM" para el campo de tipo datetime-local
        var fechaCheckin = new Date(this.reserva.fecha_checkin);

        // Asegurarse de que la fecha se maneje correctamente en la zona horaria local
        var offset = fechaCheckin.getTimezoneOffset() * 60000; 
        var fechaLocal = new Date(fechaCheckin.getTime() - offset); 
        var fechaISO = fechaLocal.toISOString();
        this.fechaCheckin = fechaISO.slice(0, 16); 
    },

    computed: {
        isFormValid() {
            return this.errorMessage === "";
        },
    },
    methods: {
        async validarFechas() {
            this.errorMessage = "";

            if (!this.fechaCheckin) return;

            var hoy = new Date();
            var checkin = new Date(this.fechaCheckin);

            // Extraer solo la fecha y la hora
            var hoySoloFecha = hoy.toISOString().split("T")[0];
            var checkinSoloFecha = checkin.toISOString().split("T")[0];
            var checkinHora = checkin.getHours();

            if (checkinSoloFecha !== hoySoloFecha) {
                this.errorMessage =
                    "La fecha de check-in debe ser igual al día actual.";
            } else if (checkinHora < 14 || checkinHora > 22) {
                this.errorMessage =
                    "La hora de check-in debe estar entre las 14:00 y las 22:00.";
            } else {
                this.errorMessage = "";
            }
        },
        async registrarCheckin() {
            var checkin = new Date(this.fechaCheckin);

            var actualizaCheckin = {
                fechaCheckin: this.fechaCheckin,
            };

            try {
                var actualizarResponse = await fetch(
                    `/registrarcheckin/${this.reserva.reservaID}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify(actualizaCheckin),
                    }
                );

                console.log("Estado de la respuesta:", actualizarResponse.status);

                if (actualizarResponse.ok) {
                    console.log("Actualización exitosa.");
                    // Redirigir después de una actualización exitosa
                    window.location.href = "/listadocheckin?success=Reserva actualizada correctamente";
                } else {
                    var errorText = await actualizarResponse.text();
                    console.error("Error en la actualización:", errorText);
                    this.errorMessage = `Error: ${errorText}`;
                }
            } catch (e) {
                console.error("Excepción al realizar la solicitud:", e);
                this.errorMessage = "Error al actualizar la reserva.";
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

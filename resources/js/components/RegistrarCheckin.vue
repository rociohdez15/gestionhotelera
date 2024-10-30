<template>
    <div class="editar-reserva">
        <h1 class="titulo"><strong>Registrar Check-In</strong></h1>

        <div v-if="reserva">
            <p><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</p>
            <p>
                <strong>Nombre del Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </p>

            <div class="mb-3 d-flex align-items-center">
                <label
                    for="fechaCheckin"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                    <strong>Fecha de Check-In:</strong>
                </label>
                <input
                    type="datetime-local"
                    class="form-control rounded-input"
                    id="fechaCheckin"
                    v-model="fechaCheckin"
                    style="max-width: 200px"
                    @change="validarFechas"
                />
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label class="form-label me-2" style="margin-bottom: 0">
                    <strong>Fecha de Check-Out:</strong>
                </label>
                <span>{{ reserva.fecha_checkout }}</span>
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
    const appElement = document.getElementById("app3");
    this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
    this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));

    // Convertir la fecha de checkout al formato "YYYY-MM-DDTHH:MM" para el campo de tipo datetime-local
    const fechaCheckin = new Date(this.reserva.fecha_checkin);
    
    // Asegurarse de que la fecha se maneje correctamente en la zona horaria local
    const offset = fechaCheckin.getTimezoneOffset() * 60000; // Obtener el offset en milisegundos
    const fechaLocal = new Date(fechaCheckin.getTime() - offset); // Ajustar a la zona local
    const fechaISO = fechaLocal.toISOString();
    this.fechaCheckin = fechaISO.slice(0, 16); // YYYY-MM-DDTHH:MM
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

            const hoy = new Date();
            const checkin = new Date(this.fechaCheckin);

            // Extraer solo la fecha en formato "YYYY-MM-DD" para ambas fechas
            const hoyDateOnly = hoy.toISOString().split("T")[0];
            const checkinDateOnly = checkin.toISOString().split("T")[0];

            if (checkinDateOnly < hoyDateOnly) {
                this.errorMessage =
                    "La fecha de check-in debe ser igual al día actual.";
            } else if (checkinDateOnly > hoyDateOnly) {
                this.errorMessage =
                    "La fecha de check-in debe ser igual al día actual.";
            } else {
                // Aquí la fecha de checkout es igual al día actual, no hay error.
                this.errorMessage = "";
            }
        },
        async registrarCheckin() {
            const checkin = new Date(this.fechaCheckin);

            

            // Solo se necesita la fecha de checkout para la actualización
            const actualizaCheckin = {
                fechaCheckin: this.fechaCheckin,
            };

            try {
                const actualizarResponse = await fetch(
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

                if (actualizarResponse.ok) {
                    window.location.href =
                        "/listarreservas?success=Reserva actualizada correctamente";
                } else {
                    const errorText = await actualizarResponse.text();
                    window.location.href = `/listarreservas?error=${encodeURIComponent(
                        errorText
                    )}`;
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
</style>
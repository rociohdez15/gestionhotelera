<template>
    <div class="editar-reserva">
        <h1 class="titulo"><strong>Registrar Check-Out</strong></h1>

        <div v-if="reserva">
            <p><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</p>
            <p>
                <strong>Nombre del Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </p>

            <div class="mb-3 d-flex align-items-center">
                <label class="form-label me-2" style="margin-bottom: 0">
                    <strong>Fecha de Check-in:</strong>
                </label>
                <span>{{ reserva.fecha_checkin }}</span>
            </div>
            <div class="mb-3 d-flex align-items-center">
                <label
                    for="fechaCheckout"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                    <strong>Fecha de Check-Out:</strong>
                </label>
                <input
                    type="datetime-local"
                    class="form-control rounded-input"
                    id="fechaCheckout"
                    v-model="fechaCheckout"
                    style="max-width: 200px"
                    @change="validarFechas"
                />
            </div>

            <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>

            <br />
            <div class="d-flex justify-content-center mb-3">
                <button
                    class="btn btn-primary"
                    @click="registrarCheckout"
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
            fechaCheckout: null,
            errorMessage: "",
        };
    },
    mounted() {
    const appElement = document.getElementById("app2");
    this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
    this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));

    // Convertir la fecha de checkout al formato "YYYY-MM-DDTHH:MM" para el campo de tipo datetime-local
    const fechaCheckout = new Date(this.reserva.fecha_checkout);
    
    // Asegurarse de que la fecha se maneje correctamente en la zona horaria local
    const offset = fechaCheckout.getTimezoneOffset() * 60000; 
    const fechaLocal = new Date(fechaCheckout.getTime() - offset); 
    const fechaISO = fechaLocal.toISOString();
    this.fechaCheckout = fechaISO.slice(0, 16); // YYYY-MM-DDTHH:MM
},

    computed: {
        isFormValid() {
            return this.errorMessage === "";
        },
    },
    methods: {
        async validarFechas() {
            this.errorMessage = "";

            if (!this.fechaCheckout) return;

            const hoy = new Date();
            const checkout = new Date(this.fechaCheckout);

            // Extraer solo la fecha en formato "YYYY-MM-DD" para ambas fechas
            const hoyDateOnly = hoy.toISOString().split("T")[0];
            const checkoutDateOnly = checkout.toISOString().split("T")[0];

            if (checkoutDateOnly < hoyDateOnly) {
                this.errorMessage =
                    "La fecha de checkout debe ser igual al día actual.";
            } else if (checkoutDateOnly > hoyDateOnly) {
                this.errorMessage =
                    "La fecha de checkout debe ser igual al día actual.";
            } else {
                this.errorMessage = "";
            }
        },
        async registrarCheckout() {
            const checkout = new Date(this.fechaCheckout);

            const actualizaCheckout = {
                fechaCheckout: this.fechaCheckout,
            };

            try {
                const actualizarResponse = await fetch(
                    `/registrarcheckout/${this.reserva.reservaID}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify(actualizaCheckout),
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

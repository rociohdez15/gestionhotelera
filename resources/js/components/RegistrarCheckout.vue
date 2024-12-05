<template>
    <div class="editar-reserva">
        <h1 class="titulo"><strong>Registrar Check-Out</strong></h1>

        <div v-if="reserva">
            <h2><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</h2>
            <h2>
                <strong>Nombre del Cliente:</strong> {{ cliente.nombre }},
                {{ cliente.apellidos }}
            </h2>

            <div class="mb-3 d-flex align-items-center">
                <h2><strong class="me-2">Fecha de Check-in: </strong></h2>
                <label class="form-label me-2" style="margin-bottom: 0">
                </label>
                <span style="color: black;">{{ reserva.fecha_checkin }}</span>
            </div>
            <div class="mb-3 d-flex align-items-center">
                <h2><strong class="me-2">Fecha de Check-Out: </strong></h2>
                <label
                    for="fechaCheckout"
                    class="form-label me-2"
                    style="margin-bottom: 0"
                >
                </label>
                <input
                    type="datetime-local"
                    class="form-control rounded-input me-2"
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
        var appElement = document.getElementById("app2");
        this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
        this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));

        // Convertir la fecha de checkout al formato "YYYY-MM-DDTHH:MM" para el campo de tipo datetime-local
        var fechaCheckout = new Date(this.reserva.fecha_checkout);
        
        // Asegurarse de que la fecha se maneje correctamente en la zona horaria local
        var offset = fechaCheckout.getTimezoneOffset() * 60000; 
        var fechaLocal = new Date(fechaCheckout.getTime() - offset); 
        var fechaISO = fechaLocal.toISOString();
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

            var hoy = new Date();
            var checkout = new Date(this.fechaCheckout);

            // Extraer solo la fecha y la hora
            var hoySoloFecha = hoy.toISOString().split("T")[0];
            var checkoutSoloFecha = checkout.toISOString().split("T")[0];
            var checkoutHora = checkout.getHours();
            var checkoutMinutos = checkout.getMinutes();

            if (checkoutSoloFecha !== hoySoloFecha) {
                this.errorMessage =
                    "La fecha de checkout debe ser igual al día actual.";
            } else if (checkoutHora > 12 || (checkoutHora === 12 && checkoutMinutos > 30)) {
                this.errorMessage =
                    "La hora del check-out debe ser antes de las 12:30.";
            } else {
                this.errorMessage = "";
            }
        },
        async registrarCheckout() {
            var checkout = new Date(this.fechaCheckout);

            var actualizaCheckout = {
                fechaCheckout: this.fechaCheckout,
            };

            try {
                var actualizarResponse = await fetch(
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
                        "/listadocheckout?success=Reserva actualizada correctamente";
                } else {
                    var errorText = await actualizarResponse.text();
                    window.location.href = `/listadocheckout?error=${encodeURIComponent(
                        errorText
                    )}`;
                }
            } catch (e) {
                window.location.href =
                    "/listadocheckout?error=Error al actualizar la reserva.";
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
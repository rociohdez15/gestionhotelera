<template>
    <div class="anadir-servicio">
        <h1 class="titulo"><strong>Añadir Servicio</strong></h1>

        <div>
            <div class="mb-3">
                <label for="reservaID" class="form-label">
                    <strong>Reserva:</strong>
                </label>
                <select
                    id="reservaID"
                    class="form-select rounded-input"
                    v-model="reservaID"
                    @change="cargarReserva"
                >
                    <option
                        v-for="reserva in reservas"
                        :key="reserva.reservaID"
                        :value="reserva.reservaID"
                    >
                        {{ reserva.nombre }} - {{ reserva.fechaInicio }} a
                        {{ reserva.fechaFin }}
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="nombreServicio" class="form-label">
                    <strong>Nombre del Servicio:</strong>
                </label>
                <select
                    id="nombreServicio"
                    class="form-select rounded-input"
                    v-model="nombreServicio"
                >
                    <option value="" disabled selected>
                        Seleccione un servicio
                    </option>
                    <option value="restaurante">Restaurante</option>
                    <option value="tour">Tour</option>
                    <option value="spa">Spa</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="horarioServicio" class="form-label">
                    <strong>Horario:</strong>
                </label>
                <input
                    type="time"
                    class="form-control rounded-input"
                    id="horarioServicio"
                    v-model="horarioServicio"
                />
            </div>

            <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>

            <br />
            <div class="d-flex justify-content-center mb-3">
                <button
                    class="btn btn-primary"
                    :disabled="!isFormValid"
                >
                    Añadir Servicio
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            reservaID: null,
            reservas: []
        };
    },
    mounted() {
        this.cargarReservas(); // Llama a la función para cargar las reservas
    },
    methods: {
        cargarReservas() {
            // Asegúrate de que los elementos estén disponibles
            this.$nextTick(() => {
                const reservasElements = this.$el.querySelectorAll('[data-reserva-id]');
                if (reservasElements.length === 0) {
                    console.warn('No se encontraron elementos con [data-reserva-id].');
                    return; // Salir si no hay elementos
                }

                // Mapeo de los elementos encontrados a un array de reservas
                this.reservas = Array.from(reservasElements).map(element => ({
                    reservaID: element.dataset.reservaId,
                    nombre: element.dataset.nombre,
                    fechaInicio: element.dataset.fechaInicio,
                    fechaFin: element.dataset.fechaFin
                }));
            });
        },
        cargarReserva() {
            console.log("Reserva seleccionada:", this.reservaID);
        }
    }
}

</script>

<style scoped>
.anadir-servicio {
    padding: 20px;
    background-color: #e0e0e0;
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

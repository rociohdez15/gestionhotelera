<template>
  <div class="anadir-servicio">
    <h1 class="titulo"><strong>Añadir Servicio</strong></h1>

    <div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-3"><strong>Reserva:</strong></h2>
        <select
          id="reservaID"
          class="form-select rounded-input"
          v-model="reservaID"
          @change="cargarReserva"
        >
          <option value="" disabled>Seleccionar una reserva</option>
          <option
            v-for="reserva in reservas"
            :key="reserva.reservaID"
            :value="reserva.reservaID"
          >
            Reserva: {{ reserva.reservaID }} - Fecha inicio:
            {{ reserva.fechainicio }} Fecha fin:
            {{ reserva.fechafin }}
          </option>
        </select>
      </div>

      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-3"><strong>Servicio:</strong></h2>
        <label for="nombreServicio" class="form-label"> </label>
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

      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-3"><strong>Fecha y hora:</strong></h2>
        <label for="horarioServicio" class="form-label">
        </label>
        <input
          type="datetime-local"
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
          @click="anadirServicio"
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
      reservaID: "", // Valor inicial vacío para mostrar la opción por defecto
      nombreServicio: "", // Valor inicial vacío para mostrar la opción por defecto
      reservas: [],
      horarioServicio: "",
      errorMessage: "",
      isFormValid: false,
      fechaInicio: null,
      fechaFin: null,
    };
  },
  mounted() {
    const reservasData = document.getElementById("app5").dataset.reservas;
    if (reservasData) {
      try {
        this.reservas = JSON.parse(reservasData);
        console.log("Reservas en mounted:", this.reservas); // Verifica que las reservas se están recibiendo correctamente
      } catch (e) {
        console.error("Error al parsear los datos de reservas:", e);
      }
    } else {
      console.error("No se encontraron datos de reservas en el elemento con ID 'app5'");
    }
  },
  methods: {
    cargarReserva() {
      console.log("Reserva seleccionada:", this.reservaID);
      const reservaSeleccionada = this.reservas.find(
        (reserva) => reserva.reservaID === this.reservaID
      );
      if (reservaSeleccionada) {
        this.fechaInicio = new Date(reservaSeleccionada.fechainicio);
        this.fechaFin = new Date(reservaSeleccionada.fechafin);
      }
    },
    validarFechaHora() {
      const fechaHoraSeleccionada = new Date(this.horarioServicio);
      const fechaSeleccionada = fechaHoraSeleccionada.toISOString().split('T')[0];
      const horaSeleccionada = fechaHoraSeleccionada.getHours();

      this.isFormValid =
        fechaHoraSeleccionada >= this.fechaInicio &&
        fechaHoraSeleccionada <= this.fechaFin &&
        horaSeleccionada >= 8 &&
        horaSeleccionada < 24;

      if (!this.isFormValid) {
        if (fechaHoraSeleccionada < this.fechaInicio || fechaHoraSeleccionada > this.fechaFin) {
          this.errorMessage = "La fecha seleccionada no está dentro del rango permitido.";
        } else if (horaSeleccionada < 8 || horaSeleccionada >= 24) {
          this.errorMessage = "La hora seleccionada debe estar entre las 08:00 y las 00:00.";
        }
      } else {
        this.errorMessage = "";
      }
    },
    async anadirServicio() {
      this.validarFechaHora();
      if (!this.isFormValid) {
        return;
      }

      const nuevoServicio = {
        reservaID: this.reservaID,
        nombreServicio: this.nombreServicio,
        fechaHora: this.horarioServicio,
      };

      try {
        const response = await fetch(`/guardarservicio`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify(nuevoServicio),
        });

        if (response.ok) {
          window.location.href =
            "/listarservicios?success=Servicio añadido correctamente";
        } else {
          const errorText = await response.text();
          window.location.href = `/listarservicios?error=${encodeURIComponent(
            errorText
          )}`;
        }
      } catch (e) {
        console.error("Error al añadir el servicio:", e);
        window.location.href =
          "/listarservicios?error=Error al añadir el servicio.";
      }
    },
  },
  watch: {
    horarioServicio() {
      this.validarFechaHora();
    }
  }
};
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

.d-flex {
  display: flex;
}

.align-items-center {
  align-items: center;
}

.me-3 {
  margin-right: 1rem;
}
</style>
<template>
  <div class="editar-reserva">
    <h1 class="titulo"><strong>Editar Reserva</strong></h1>

    <div v-if="reserva">
      <p><strong>ID de Reserva:</strong> {{ reserva.reservaID }}</p>
      <p>
        <strong>Nombre del Cliente:</strong> {{ cliente.nombre }}, {{ cliente.apellidos }}
      </p>

      <div class="mb-3 d-flex align-items-center">
        <label for="fechaEntrada" class="form-label me-2" style="margin-bottom: 0"><strong>Fecha de Entrada:</strong></label>
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
        <label for="fechaSalida" class="form-label me-2" style="margin-bottom: 0"><strong>Fecha de Salida:</strong></label>
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
        <label for="numAdultos" class="form-label me-2" style="margin-bottom: 0"><strong>Número de Adultos:</strong></label>
        <input
          type="number"
          class="form-control rounded-input me-2"
          id="numAdultos"
          v-model="numAdultos"
          :max="maxAdults"
          min="1"
          @change="updateMaxChildren"
          style="max-width: 80px;"
        />
      </div>
      <div class="mb-3 d-flex align-items-center">
        <label for="numNinos" class="form-label me-2" style="margin-bottom: 0"><strong>Número de Niños:</strong></label>
        <input
          type="number"
          class="form-control rounded-input me-2"
          id="numNinos"
          v-model="numNinos"
          min="0"
          :max="maxChildren"
          @change="updateChildAgeFields"
          style="max-width: 80px;"
        />
      </div>

      <h2><strong>Hotel: </strong>{{ hotel.nombre }}</h2>
      <h2><strong>Precio Reserva: </strong>{{ reserva.preciototal }}</h2>
      <p><strong>Dirección del Hotel:</strong> {{ hotel.direccion }}</p>
      

      <h3><strong>Edades de los Niños:</strong></h3>
      <div id="edades_ninos">
        <ul>
          <li v-for="(nino, index) in edadesNinos" :key="index">
            <strong>• Niño {{ index + 1 }}</strong>: 
            <label :for="'nino-' + index" class="visually-hidden">Edad del Niño {{ index + 1 }}</label>
            <input
              type="number"
              v-model="nino.edad"  
              class="form-control rounded-input"
              :id="'edad-nino-' + index"
              min="0"
              max="17"
              style="max-width: 80px; display: inline-block;"
              placeholder="Edad"
            /> años
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
    const appElement = document.getElementById("app");
    this.reserva = JSON.parse(appElement.getAttribute("data-reserva"));
    this.hotel = JSON.parse(appElement.getAttribute("data-hotel"));
    this.cliente = JSON.parse(appElement.getAttribute("data-cliente"));
    this.edadesNinos = JSON.parse(appElement.getAttribute("data-edades-ninos"));
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
      this.edadesNinos = this.edadesNinos.map(nino => ({
        edad: nino.edad || null 
      }));
    } else {
      this.edadesNinos = []; 
    }

    this.updateMaxChildren();
  },
  computed: {
    isFormValid() {
      return this.errorMessage === "";
    },
  },
  methods: {
    updateMaxChildren() {
      const numAdultos = this.numAdultos || 0;
      const numNinos = this.numNinos || 0;
      const totalPersonas = numAdultos + numNinos;

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
      const numNinos = this.numNinos || 0;
      const edadesExistentes = this.edadesNinos.slice(0, numNinos);
      this.edadesNinos = [];
      for (let i = 0; i < numNinos; i++) {
        this.edadesNinos.push({ edad: edadesExistentes[i] ? edadesExistentes[i].edad : null });
      }
    },
    async validarFechas() {
      this.errorMessage = "";
      if (!this.fechaEntrada || !this.fechaSalida) {
        return;
      }

      const hoy = new Date();
      const entrada = new Date(this.fechaEntrada);
      const salida = new Date(this.fechaSalida);

      if (entrada < hoy || salida < hoy) {
        this.errorMessage = "Las fechas deben ser posteriores al día actual.";
        return;
      }

      if (salida < entrada) {
        this.errorMessage = "La fecha de salida no puede ser anterior a la fecha de entrada.";
        return;
      }
    },
    async verificarHabitacionDisponible() {
      try {
        const response = await fetch(`/verificar-habitacion/${this.hotelID}?numAdultos=${this.numAdultos}`);

        if (response.ok) {
          const disponible = await response.json();

          if (disponible.habitacionID) {
            this.habitacionID = disponible.habitacionID;
          } else {
            window.location.href = "/listarreservas?error=No hay habitaciones disponibles para el número de adultos especificado.";
            return false;
          }

          return disponible;
        } else {
          window.location.href = "/listarreservas?error=No hay habitaciones disponibles para el número de adultos especificado.";
          return false;
        }
      } catch (error) {
        window.location.href = "/listarreservas?error=Se produjo un error al verificar la disponibilidad de la habitación.";
        return false;
      }
    },
    async actualizarReserva() {
      const entrada = new Date(this.fechaEntrada);
      const salida = new Date(this.fechaSalida);

      if (this.fechaEntrada !== this.originalFechaEntrada || this.fechaSalida !== this.originalFechaSalida) {
        const response = await fetch(
          `/comprobar-reserva/${this.hotelID}?fechaEntrada=${
            entrada.toISOString().split("T")[0]
          }&fechaSalida=${salida.toISOString().split("T")[0]}`
        );

        if (response.ok) {
          const existeReserva = await response.json();
          if (existeReserva) {
            window.location.href = "/listarreservas?error=Ya existe una reserva en el hotel durante las fechas seleccionadas.";
            return;
          }
        } else {
          window.location.href = "/listarreservas?error=Error al comprobar la disponibilidad.";
          return;
        }
      }

      const habitacionDisponible = await this.verificarHabitacionDisponible();
      if (!habitacionDisponible) {
        return;
      }

      const reservaActualizada = {
        fechaEntrada: this.fechaEntrada,
        fechaSalida: this.fechaSalida,
        numAdultos: this.numAdultos,
        numNinos: this.numNinos,
        edadesNinos: this.edadesNinos.map((nino) => nino.edad),
        habitacionID: this.habitacionID,
      };

      try {
        const actualizarResponse = await fetch(
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
          window.location.href = "/listarreservas?success=Reserva actualizada correctamente";
        } else {
          const errorText = await actualizarResponse.text();
          window.location.href = `/listarreservas?error=${encodeURIComponent(errorText)}`;
        }
      } catch (e) {
        window.location.href = "/listarreservas?error=Error al actualizar la reserva.";
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
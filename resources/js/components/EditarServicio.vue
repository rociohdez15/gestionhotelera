<template>
  <div class="editar-servicio">
      <h1 class="titulo"><strong>Editar Servicio</strong></h1>

      <div v-if="servicio">
          <h2><strong>ID de Servicio:</strong> {{ servicio.servicioID }}</h2>
          <h2><strong>Nombre del Servicio:</strong> {{ servicio.nombre }}</h2>
          <h2><strong>Hotel:</strong> {{ reserva.hotel_nombre }}</h2>
          <h2>
              <strong>Cliente:</strong> {{ cliente.nombre }},
              {{ cliente.apellidos }}
          </h2>

          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Fecha:</strong></h2>
              <label for="fechaServicio" class="form-label me-2 mb-0"></label>
              <input
                  type="date"
                  class="form-control rounded-input me-2"
                  id="fechaServicio"
                  v-model="fecha"
                  style="max-width: 200px"
                  @change="validarFecha"
              />
          </div>

          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Hora:</strong></h2>
              <label for="horaServicio" class="form-label me-2 mb-0"></label>
              <input
                  type="time"
                  class="form-control rounded-input me-2"
                  id="horaServicio"
                  v-model="hora"
                  style="max-width: 200px"
                  @change="validarHora"
              />
          </div>

          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Servicio:</strong></h2>
              <label for="descripcionServicio" class="form-label me-2 mb-0"></label>
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
      validarHora() {
          const [hora, minutos] = this.hora.split(':');
          const horaSeleccionada = parseInt(hora, 10);
          if (horaSeleccionada < 8 || horaSeleccionada >= 24) {
              this.isFormValid = false;
              this.errorMessage = "La hora seleccionada debe estar entre las 08:00 y las 00:00.";
          } else {
              this.isFormValid = true;
              this.errorMessage = "";
          }
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

              console.log("Estado de la respuesta:", response.status);

              if (response.ok) {
                  console.log("Actualización exitosa.");
                  
                  window.location.href = "/listarservicios?success=Servicio actualizado correctamente";
              } else {
                  const errorText = await response.text();
                  console.error("Error en la actualización:", errorText);
                  this.errorMessage = `Error: ${errorText}`;
              }
          } catch (e) {
              console.error("Excepción al realizar la solicitud:", e);
              this.errorMessage = "Error al actualizar el servicio.";
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

.editar-servicio p,
.editar-servicio label,
.editar-servicio strong,
.editar-servicio h2,
.editar-servicio h3 {
  color: black;
  line-height: 2;
}

.me-2 {
  margin-right: 0.5rem;
}
</style>
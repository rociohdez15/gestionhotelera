<template>
  <div class="añadir-habitacion">
      <h1 class="titulo"><strong>Añadir Habitación</strong></h1>

      <div>
          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Nº de Habitación:</strong></h2>
              <label for="numeroHabitacion" class="form-label me-2"></label>
              <input
                  type="text"
                  class="form-control flex-grow-1"
                  id="numeroHabitacion"
                  v-model="habitacion.numhabitacion"
                  placeholder="Ingrese el número de la habitación"
              />
          </div>
          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Nº Huéspedes:</strong></h2>
              <label for="numeroHuespedes" class="form-label me-2"></label>
              <input
                  type="number"
                  class="form-control flex-grow-1"
                  id="numeroHuespedes"
                  v-model="habitacion.tipohabitacion"
                  placeholder="Ingrese el número de huéspedes"
              />
          </div>
          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>Precio:</strong></h2>
              <label for="precioHabitacion" class="form-label me-2"></label>
              <input
                  type="text"
                  class="form-control flex-grow-1"
                  id="precioHabitacion"
                  v-model="habitacion.precio"
                  placeholder="Ingrese el precio de la habitación"
              />
          </div>
          <div class="mb-3 d-flex align-items-center">
              <h2 class="me-2"><strong>ID Hotel:</strong></h2>
              <label for="idHotel" class="form-label me-2"></label>
              <input
                  type="text"
                  class="form-control flex-grow-1"
                  id="idHotel"
                  v-model="habitacion.hotelID"
                  placeholder="Ingrese el ID del hotel"
              />
          </div>

          <div v-if="errorMessage" class="alert alert-danger">
              {{ errorMessage }}
          </div>

          <br />
          <div class="d-flex justify-content-center mb-3">
              <button
                  class="btn btn-primary"
                  @click="añadirHabitacion"
                  :disabled="!isFormValid"
              >
                  Añadir Habitación
              </button>
          </div>
      </div>
  </div>
</template>

<script>
export default {
  name: "AñadirHabitacion",
  data() {
      return {
          habitacion: {
              numhabitacion: '',
              tipohabitacion: '',
              precio: '',
              hotelID: ''
          },
          errorMessage: "",
          isFormValid: true,
      };
  },
  methods: {
      validateForm() {
          this.isFormValid = true;
          this.errorMessage = "";

          if (!/^[0-9]+$/.test(this.habitacion.numhabitacion)) {
              this.errorMessage += "El número de habitación debe contener solo números.\n";
              this.isFormValid = false;
          }
          if (!/^[0-9]+$/.test(this.habitacion.tipohabitacion)) {
              this.errorMessage += "El número de huéspedes debe contener solo números.\n";
              this.isFormValid = false;
          }
          if (!/^\d+(\.\d{1,2})?$/.test(this.habitacion.precio)) {
              this.errorMessage += "El precio debe ser un número válido con hasta dos decimales.\n";
              this.isFormValid = false;
          }
          if (!/^[0-9]+$/.test(this.habitacion.hotelID)) {
              this.errorMessage += "El ID del hotel debe contener solo números.\n";
              this.isFormValid = false;
          }
      },
      async añadirHabitacion() {
          this.validateForm();

          if (!this.isFormValid) {
              console.log("Formulario no válido");
              return;
          }

          const formData = new FormData();
          formData.append('numhabitacion', this.habitacion.numhabitacion);
          formData.append('tipohabitacion', this.habitacion.tipohabitacion);
          formData.append('precio', this.habitacion.precio);
          formData.append('hotelID', this.habitacion.hotelID);

          // Iterar sobre el FormData y registrar cada par clave-valor en la consola
          for (let [key, value] of formData.entries()) {
              console.log(`${key}:`, value);
          }

          try {
              const response = await fetch(`/anadir-habitaciones`, {
                  method: "POST",
                  headers: {
                      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                  },
                  body: formData,
              });

              console.log("Estado de la respuesta:", response.status);

              if (response.ok) {
                  console.log("Habitación añadida correctamente.");
                  // Redirigir después de una adición exitosa
                  window.location.href = "/gestionarhabitaciones?success=La habitación se ha añadido correctamente";
              } else {
                  const errorText = await response.text();
                  console.error("Error en la adición:", errorText);
                  this.errorMessage = `Error: ${errorText}`;
              }
          } catch (e) {
              console.error("Error al realizar la solicitud:", e);
              this.errorMessage = "Error al añadir la habitación.";
          }
      },
      resetForm() {
          this.habitacion = {
              numhabitacion: '',
              tipohabitacion: '',
              precio: '',
              hotelID: ''
          };
          this.errorMessage = "";
      }
  },
};
</script>

<style scoped>
.añadir-habitacion {
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

.form-control {
  border-radius: 12px;
  border: 1px solid #ccc;
}

.añadir-habitacion p,
.añadir-habitacion label,
.añadir-habitacion strong,
.añadir-habitacion h2,
.añadir-habitacion h3 {
  color: black;
  line-height: 2;
}

.me-2 {
  margin-right: 0.5rem;
}
</style>
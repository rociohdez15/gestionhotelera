<template>
    <div class="editar-habitacion">
      <h1 class="titulo"><strong>Editar Habitación</strong></h1>
  
      <div v-if="habitacion">
        <h2><strong>ID de Habitación:</strong> {{ habitacion.habitacionID }}</h2>
        <div class="mb-3 d-flex align-items-center">
          <h2><strong class="me-2">Nº de Habitación:</strong></h2>
          <label for="numeroHabitacion" class="form-label me-2"></label>
          <input
            type="text"
            class="form-control flex-grow-1"
            id="numeroHabitacion"
            v-model="habitacion.numhabitacion"
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
          />
        </div>
  
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
  
        <br />
        <div class="d-flex justify-content-center mb-3">
          <button
            class="btn btn-primary"
            @click="actualizarHabitacion"
            :disabled="!isFormValid"
          >
            Actualizar Habitación
          </button>
        </div>
      </div>
  
      <div v-else>
        <p>No se encontraron detalles de la habitación.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "EditarHabitacion",
    data() {
      return {
        habitacion: null,
        errorMessage: "",
        isFormValid: true,
      };
    },
    mounted() {
      const appElement = document.getElementById("app8");
      this.habitacion = JSON.parse(appElement.getAttribute("data-habitacion"));
      console.log("Datos de la habitación cargados:", this.habitacion);
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
      async actualizarHabitacion() {
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
  
        
        for (let [key, value] of formData.entries()) {
          console.log(`${key}:`, value);
        }
  
        try {
          const response = await fetch(`/editarhabitacion/${this.habitacion.habitacionID}`, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              "X-HTTP-Method-Override": "PUT" 
            },
            body: formData,
          });
  
          if (response.ok) {
            console.log("Habitación actualizada correctamente");
            window.location.href = "/gestionarhabitaciones?success=Habitación actualizada correctamente";
          } else {
            const errorText = await response.json();
            console.error("Error al actualizar la habitación:", errorText);
            this.errorMessage = `Error al actualizar la habitación: ${errorText.message}`;
          }
        } catch (e) {
          console.error("Error al realizar la solicitud:", e);
          this.errorMessage = "Error al actualizar la habitación.";
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .editar-habitacion {
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
  
  .editar-habitacion p,
  .editar-habitacion label,
  .editar-habitacion strong,
  .editar-habitacion h2,
  .editar-habitacion h3 {
    color: black;
    line-height: 2;
  }
  
  .me-2 {
    margin-right: 0.5rem;
  }
  </style>
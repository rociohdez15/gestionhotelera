<template>
    <div class="editar-hotel">
      <h1 class="titulo"><strong>Editar Hotel</strong></h1>
  
      <div v-if="hotel">
        <h2><strong>ID de Hotel:</strong> {{ hotel.hotelID }}</h2>
        <div class="mb-3">
          <label for="nombreHotel" class="form-label"><strong>Nombre del Hotel:</strong></label>
          <input
            type="text"
            class="form-control"
            id="nombreHotel"
            v-model="hotel.nombre"
          />
        </div>
        <div class="mb-3">
          <label for="direccionHotel" class="form-label"><strong>Dirección:</strong></label>
          <input
            type="text"
            class="form-control"
            id="direccionHotel"
            v-model="hotel.direccion"
          />
        </div>
        <div class="mb-3">
          <label for="ciudadHotel" class="form-label"><strong>Ciudad:</strong></label>
          <input
            type="text"
            class="form-control"
            id="ciudadHotel"
            v-model="hotel.ciudad"
          />
        </div>
        <div class="mb-3">
          <label for="telefonoHotel" class="form-label"><strong>Teléfono:</strong></label>
          <input
            type="text"
            class="form-control"
            id="telefonoHotel"
            v-model="hotel.telefono"
          />
        </div>
        <div class="mb-3">
          <label for="descripcionHotel" class="form-label"><strong>Descripción:</strong></label>
          <textarea
            class="form-control"
            id="descripcionHotel"
            v-model="hotel.descripcion"
            rows="3"
          ></textarea>
        </div>
  
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
  
        <br />
        <div class="d-flex justify-content-center mb-3">
          <button
            class="btn btn-primary"
            @click="actualizarHotel"
            :disabled="!isFormValid"
          >
            Actualizar Hotel
          </button>
        </div>
      </div>
  
      <div v-else>
        <p>No se encontraron detalles del hotel.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "EditarHotel",
    data() {
      return {
        hotel: null,
        errorMessage: "",
        isFormValid: true,
      };
    },
    mounted() {
      const appElement = document.getElementById("app6");
      this.hotel = JSON.parse(appElement.getAttribute("data-hotel"));
      console.log("Datos del hotel cargados:", this.hotel);
    },
    methods: {
      async actualizarHotel() {
        if (!this.isFormValid) {
          console.log("Formulario no válido");
          return;
        }
  
        const hotelActualizado = {
          nombre: this.hotel.nombre,
          direccion: this.hotel.direccion,
          ciudad: this.hotel.ciudad,
          telefono: this.hotel.telefono,
          descripcion: this.hotel.descripcion,
        };
  
        console.log("Datos del hotel a actualizar:", hotelActualizado);
  
        try {
          const response = await fetch(`/editarhotel/${this.hotel.hotelID}`, {
            method: "PUT",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify(hotelActualizado),
          });
  
          if (response.ok) {
            console.log("Hotel actualizado correctamente");
            window.location.href = "/gestionarhoteles?success=Hotel actualizado correctamente";
          } else {
            const errorText = await response.text();
            console.error("Error al actualizar el hotel:", errorText);
            this.errorMessage = `Error al actualizar el hotel: ${errorText}`;
          }
        } catch (e) {
          console.error("Error al realizar la solicitud:", e);
          this.errorMessage = "Error al actualizar el hotel.";
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .editar-hotel {
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
  
  .editar-hotel p,
  .editar-hotel label,
  .editar-hotel strong,
  .editar-hotel h2,
  .editar-hotel h3 {
    color: black;
    line-height: 2;
  }
  
  .me-2 {
    margin-right: 0.5rem;
  }
  </style>
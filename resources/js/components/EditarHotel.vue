<template>
  <div class="editar-hotel">
    <h1 class="titulo"><strong>Editar Hotel</strong></h1>

    <div v-if="hotel">
      <h2><strong>ID de Hotel:</strong> {{ hotel.hotelID }}</h2>
      <div class="mb-3 d-flex align-items-center">
        <h2><strong class="me-2">Hotel:</strong></h2>
        <label for="nombreHotel" class="form-label me-2"></label>
        <input
          type="text"
          class="form-control flex-grow-1"
          id="nombreHotel"
          v-model="hotel.nombre"
        />
      </div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Dirección:</strong></h2>
        <label for="direccionHotel" class="form-label me-2"></label>
        <input
          type="text"
          class="form-control flex-grow-1"
          id="direccionHotel"
          v-model="hotel.direccion"
        />
      </div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Ciudad:</strong></h2>
        <label for="ciudadHotel" class="form-label me-2"></label>
        <input
          type="text"
          class="form-control flex-grow-1"
          id="ciudadHotel"
          v-model="hotel.ciudad"
        />
      </div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Teléfono:</strong></h2>
        <label for="telefonoHotel" class="form-label me-2"></label>
        <input
          type="text"
          class="form-control flex-grow-1"
          id="telefonoHotel"
          v-model="hotel.telefono"
        />
      </div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Descripción:</strong></h2>
        <label for="descripcionHotel" class="form-label me-2"></label>
        <textarea
          class="form-control flex-grow-1"
          id="descripcionHotel"
          v-model="hotel.descripcion"
          rows="3"
        ></textarea>
      </div>

      <!-- Campo para subir nuevas imágenes -->
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Subir nuevas imágenes:</strong></h2>
        <label for="imagenesHotel" class="form-label me-2"></label>
        <input
          type="file"
          class="form-control flex-grow-1"
          id="imagenesHotel"
          multiple
          @change="handleFileUpload"
        />
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
      nuevasImagenes: [],
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
    handleFileUpload(event) {
      this.nuevasImagenes = Array.from(event.target.files);
      console.log("Imágenes seleccionadas:", this.nuevasImagenes);
    },
    async actualizarHotel() {
      if (!this.isFormValid) {
        console.log("Formulario no válido");
        return;
      }

      const formData = new FormData();
      formData.append('nombre', this.hotel.nombre);
      formData.append('direccion', this.hotel.direccion);
      formData.append('ciudad', this.hotel.ciudad);
      formData.append('telefono', this.hotel.telefono);
      formData.append('descripcion', this.hotel.descripcion);

      this.nuevasImagenes.forEach((imagen, index) => {
        formData.append(`imagenes[]`, imagen);
      });

      
      for (let [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
      }

      try {
        const response = await fetch(`/editarhotel/${this.hotel.hotelID}`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "X-HTTP-Method-Override": "PUT" 
          },
          body: formData,
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
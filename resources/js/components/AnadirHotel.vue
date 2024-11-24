<template>
  <div class="añadir-hotel">
    <h1 class="titulo"><strong>Añadir Hotel</strong></h1>

    <div>
      <div class="mb-3 d-flex align-items-center">
        <h2 class="me-2"><strong>Hotel:</strong></h2>
        <label for="nombreHotel" class="form-label me-2"></label>
        <input
          type="text"
          class="form-control flex-grow-1"
          id="nombreHotel"
          v-model="hotel.nombre"
          placeholder="Ingrese el nombre del hotel"
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
          placeholder="Ingrese la dirección del hotel"
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
          placeholder="Ingrese la ciudad del hotel"
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
          placeholder="Ingrese el teléfono del hotel"
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
          placeholder="Ingrese la descripción del hotel"
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
          @click="añadirHotel"
          :disabled="!isFormValid"
        >
          Añadir Hotel
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AñadirHotel",
  data() {
    return {
      hotel: {
        nombre: '',
        direccion: '',
        ciudad: '',
        telefono: '',
        descripcion: ''
      },
      nuevasImagenes: [],
      errorMessage: "",
      isFormValid: true,
    };
  },
  methods: {
    handleFileUpload(event) {
      this.nuevasImagenes = Array.from(event.target.files);
      console.log("Imágenes seleccionadas:", this.nuevasImagenes);
    },
    async añadirHotel() {
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

      // Iterar sobre el FormData y registrar cada par clave-valor en la consola
      for (let [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
      }

      try {
        const response = await fetch(`/anadir-hoteles`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: formData,
        });

        if (response.ok) {
          window.location.href =
            "/gestionarhoteles?success=El hotel se ha añadido correctamente";
        } else {
          const errorText = await response.text();
          window.location.href = `/gestionarhoteles?error=${encodeURIComponent(
            errorText
          )}`;
        }
      } catch (e) {
        console.error("Error al realizar la solicitud:", e);
        this.errorMessage = "Error al añadir el hotel.";
      }
    },
    resetForm() {
      this.hotel = {
        nombre: '',
        direccion: '',
        ciudad: '',
        telefono: '',
        descripcion: ''
      };
      this.nuevasImagenes = [];
      this.errorMessage = "";
    }
  },
};
</script>

<style scoped>
.añadir-hotel {
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

.añadir-hotel p,
.añadir-hotel label,
.añadir-hotel strong,
.añadir-hotel h2,
.añadir-hotel h3 {
  color: black;
  line-height: 2;
}

.me-2 {
  margin-right: 0.5rem;
}
</style>
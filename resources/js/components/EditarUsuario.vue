<template>
    <div class="editar-usuario">
      <h1 class="titulo"><strong>Editar Usuario</strong></h1>
  
      <div v-if="usuario">
        <h2><strong>ID de Usuario:</strong> {{ usuario.id }}</h2>
        <div class="mb-3 d-flex align-items-center">
          <h2><strong class="me-2">Nombre:</strong></h2>
          <label for="nombreUsuario" class="form-label me-2"></label>
          <input
            type="text"
            class="form-control flex-grow-1"
            id="nombreUsuario"
            v-model="usuario.name"
          />
        </div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Apellidos:</strong></h2>
          <label for="apellidosUsuario" class="form-label me-2"></label>
          <input
            type="text"
            class="form-control flex-grow-1"
            id="apellidosUsuario"
            v-model="usuario.apellidos"
          />
        </div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Email:</strong></h2>
          <label for="emailUsuario" class="form-label me-2"></label>
          <input
            type="email"
            class="form-control flex-grow-1"
            id="emailUsuario"
            v-model="usuario.email"
          />
        </div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Rol: </strong>{{ usuario.rolID }}</h2>
        </div>
  
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
  
        <br />
        <div class="d-flex justify-content-center mb-3">
          <button
            class="btn btn-primary"
            @click="actualizarUsuario"
            :disabled="!isFormValid"
          >
            Actualizar Usuario
          </button>
        </div>
      </div>
  
      <div v-else>
        <p>No se encontraron detalles del usuario.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "EditarUsuario",
    data() {
      return {
        usuario: null,
        errorMessage: "",
        isFormValid: true,
      };
    },
    mounted() {
      const appElement = document.getElementById("app10");
      this.usuario = JSON.parse(appElement.getAttribute("data-usuario"));
      console.log("Datos del usuario cargados:", this.usuario);
    },
    methods: {
      validateForm() {
        this.isFormValid = true;
        this.errorMessage = "";
  
        if (!this.usuario.name || this.usuario.name.length > 255) {
          this.errorMessage += "El nombre es requerido y debe tener menos de 255 caracteres.\n";
          this.isFormValid = false;
        }
        if (!this.usuario.apellidos || this.usuario.apellidos.length > 255) {
          this.errorMessage += "Los apellidos son requeridos y deben tener menos de 255 caracteres.\n";
          this.isFormValid = false;
        }
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(this.usuario.email) || this.usuario.email.length > 255) {
          this.errorMessage += "El email es requerido, debe ser válido y tener menos de 255 caracteres.\n";
          this.isFormValid = false;
        }
        if (!Number.isInteger(this.usuario.rolID) || this.usuario.rolID <= 0) {
          this.errorMessage += "El rol ID es requerido y debe ser un número entero positivo.\n";
          this.isFormValid = false;
        }
      },
      async actualizarUsuario() {
        this.validateForm();
  
        if (!this.isFormValid) {
          console.log("Formulario no válido");
          return;
        }
  
        const formData = new FormData();
        formData.append('name', this.usuario.name);
        formData.append('apellidos', this.usuario.apellidos);
        formData.append('email', this.usuario.email);
        formData.append('rolID', this.usuario.rolID);
  
        // Iterar sobre el FormData y registrar cada par clave-valor en la consola
        for (let [key, value] of formData.entries()) {
          console.log(`${key}:`, value);
        }
  
        try {
          const response = await fetch(`/editarusuario/${this.usuario.id}`, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              "X-HTTP-Method-Override": "PUT" // Sobrescribir el método HTTP a PUT
            },
            body: formData,
          });
  
          if (response.ok) {
            console.log("Usuario actualizado correctamente");
            window.location.href = "/listarusuarios?success=Usuario actualizado correctamente";
          } else {
            const errorText = await response.text();
            console.error("Error al actualizar el usuario:", errorText);
            this.errorMessage = `Error al actualizar el usuario: ${errorText}`;
          }
        } catch (e) {
          console.error("Error al realizar la solicitud:", e);
          this.errorMessage = "Error al actualizar el usuario.";
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .editar-usuario {
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
  
  .editar-usuario p,
  .editar-usuario label,
  .editar-usuario strong,
  .editar-usuario h2,
  .editar-usuario h3 {
    color: black;
    line-height: 2;
  }
  
  .me-2 {
    margin-right: 0.5rem;
  }
  </style>
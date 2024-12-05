<template>
    <div class="añadir-usuario">
      <h1 class="titulo"><strong>Añadir Usuario</strong></h1>
  
      <div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Nombre:</strong></h2>
          <label for="nombreUsuario" class="form-label me-2"></label>
          <input
            type="text"
            class="form-control flex-grow-1"
            id="nombreUsuario"
            v-model="usuario.name"
            placeholder="Ingrese el nombre del usuario"
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
            placeholder="Ingrese los apellidos del usuario"
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
            placeholder="Ingrese el email del usuario"
          />
        </div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Contraseña:</strong></h2>
          <label for="passwordUsuario" class="form-label me-2"></label>
          <input
            type="password"
            class="form-control flex-grow-1"
            id="passwordUsuario"
            v-model="usuario.password"
            placeholder="Ingrese la contraseña del usuario"
          />
        </div>
        <div class="mb-3 d-flex align-items-center">
          <h2 class="me-2"><strong>Rol ID:</strong></h2>
          <label for="rolID" class="form-label me-2"></label>
          <input
            type="number"
            class="form-control flex-grow-1"
            id="rolID"
            v-model="usuario.rolID"
            placeholder="Ingrese el rol ID del usuario"
          />
        </div>
  
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
  
        <br />
        <div class="d-flex justify-content-center mb-3">
          <button
            class="btn btn-primary"
            @click="comprobarEmail"
            :disabled="!isFormValid"
          >
            Añadir Usuario
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "AnadirUsuario",
    data() {
      return {
        usuario: {
          name: '',
          apellidos: '',
          email: '',
          password: '',
          rolID: 2 
        },
        errorMessage: "",
        isFormValid: true,
      };
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
        if (!this.usuario.password || this.usuario.password.length < 6) {
          this.errorMessage += "La contraseña es requerida y debe tener al menos 6 caracteres.\n";
          this.isFormValid = false;
        }
        if (!Number.isInteger(this.usuario.rolID) || this.usuario.rolID <= 0) {
          this.errorMessage += "El rol ID es requerido y debe ser un número entero positivo.\n";
          this.isFormValid = false;
        }
      },
      async comprobarEmail() {
        this.validateForm();
  
        if (!this.isFormValid) {
          console.log("Formulario no válido");
          return;
        }
  
        try {
          const emailExists = await fetch(`/comprobar-email?email=${this.usuario.email}`, {
            method: "GET",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
          });
  
          const result = await emailExists.json();
  
          if (result.exists) {
            this.errorMessage = "El email ya está registrado en la base de datos.";
            return;
          }
  
          
          this.añadirUsuario();
        } catch (e) {
          console.error("Error al comprobar el correo:", e);
          this.errorMessage = "Error al comprobar el correo.";
        }
      },
      async añadirUsuario() {
        const formData = new FormData();
        formData.append('name', this.usuario.name);
        formData.append('apellidos', this.usuario.apellidos);
        formData.append('email', this.usuario.email);
        formData.append('password', this.usuario.password);
        formData.append('rolID', this.usuario.rolID);
  
        try {
          const response = await fetch(`/anadir-usuarios`, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: formData,
          });
  
          if (response.ok) {
            window.location.href =
              "/listarusuarios?success=El usuario se ha añadido correctamente";
          } else {
            const errorData = await response.json();
            this.errorMessage = errorData.message;
            if (errorData.errors) {
              for (const key in errorData.errors) {
                this.errorMessage += `\n${errorData.errors[key].join(' ')}`;
              }
            }
          }
        } catch (e) {
          console.error("Error al realizar la solicitud:", e);
          this.errorMessage = "Error al añadir el usuario.";
        }
      },
      resetForm() {
        this.usuario = {
          name: '',
          apellidos: '',
          email: '',
          password: '',
          rolID: 2 
        };
        this.errorMessage = "";
      },
    },
  };
  </script>
  
  <style scoped>
  .añadir-usuario {
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
  
  .añadir-usuario p,
  .añadir-usuario label,
  .añadir-usuario strong,
  .añadir-usuario h2,
  .añadir-usuario h3 {
    color: black;
    line-height: 2;
  }
  
  .me-2 {
    margin-right: 0.5rem;
  }
  </style>
  
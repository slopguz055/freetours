<script setup>
import { ref, reactive, computed } from 'vue';
import Swal from 'sweetalert2';

let errorCorreo = ref('');
let errorPass = ref('');

const form = reactive({
  nombre: '',
  email: '',
  contraseña: ''
});

const isFormValid = computed(() => {
  return form.nombre && form.email && form.contraseña;
});

// Función para crear un nuevo usuario
const crearUsuario = async () => {
  // Regex para comprobar correo y contraseña fuerte
  const regexCorreo = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g;
  const regexPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/g;

  if (!regexCorreo.test(form.email)) {
    errorCorreo.value = 'Dirección de correo introducida inválida.';
  } else {
    errorCorreo.value = '';
  }

  if (!regexPass.test(form.contraseña)) {
    errorPass.value = 'La contraseña debe contener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
  } else {
    errorPass.value = '';
  }

  if (errorCorreo.value || errorPass.value) {
    return;
  }
  
  // Petición POST para crear un nuevo usuario
  try {
    const response = await fetch('http://localhost:8080/API_freetours/api.php/usuarios', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        nombre: form.nombre,
        email: form.email,
        contraseña: form.contraseña
      })
    });
    const data = await response.json();
    console.log('Respuesta del servidor:', data); 

    clearForm();

    Swal.fire({
        position: "center",
        icon: "success",
        title: "Usuario registrado.",
        showConfirmButton: false,
        timer: 3000
      });  
    } catch (error) {
    console.error('Error creating user:', error);
  }
};

// Limpiar el formulario
const clearForm = () => {
  form.nombre = '';
  form.email = '';
  form.contraseña = '';
};
</script>




<template>
  <div class="d-flex flex-column justify-content-center align-items-center mt-5">
    <img src="@/assets/logo/friturs.png" alt="Logo Friturs" class="img-fluid mb-4" style="max-width: 200px;" />
    <h1>Registro de nuevo usuario</h1>
    <form class="p-4 border rounded bg-light mt-3" @submit.prevent="crearUsuario">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" id="nombre" v-model="form.nombre" class="form-control" required placeholder="Ejemplo: Cristian"/>
      </div>
      <div class="mb-3">
        <label for="email" :class="{'form-label': true, 'text-danger': errorCorreo}">Correo:</label>
        <input type="text" id="email" v-model="form.email" :class="{'form-control': true, 'is-invalid': errorCorreo}" required placeholder="Ejemplo: cristian@mail.com"/>
        <p v-if="errorCorreo" class="invalid-feedback">{{ errorCorreo }}</p>
      </div>
      <div class="mb-3">
        <label for="contraseña" :class="{'form-label': true, 'text-danger': errorPass}">Contraseña:</label>
        <input type="password" id="contraseña" v-model="form.contraseña" :class="{'form-control': true,'is-invalid': errorPass}" required placeholder="********"/>
        <p v-if="errorPass" class="invalid-feedback">{{ errorPass }}</p>
      </div>
      <button type="submit" class="btn btn-danger w-100" :disabled="!isFormValid">Crear cuenta</button>
      <p class="text-center mt-3">
        ¿Ya tienes cuenta? <router-link to="/login">Inicia sesión</router-link>
      </p>
    </form>
  </div>
</template>




<style scoped>
</style>
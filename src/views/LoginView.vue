<script setup>
import { ref, computed } from "vue";
import router from "@/router";

const emit = defineEmits(["sesionIniciada"]);

const form = ref({ correo: '', password: '' });
const error = ref('');
const errorCorreo = ref('');

// Comprobación de que el formulario no tenga campos vacíos
const isFormValid = computed(() => {
  return form.value.correo && form.value.password;
});

async function iniciarSesion() {
  
  const regexCorreo = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g;
  if (!regexCorreo.test(form.value.correo)) {
    errorCorreo.value = 'Dirección de correo inválida.';
    return;
  }

  if(errorCorreo.value){
    errorCorreo.value = '';
  }

  try {
    const loginData = {
      email: form.value.correo,
      contraseña: form.value.password
    };

    const response = await fetch('http://localhost:8080/API_freetours/api.php/usuarios?login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(loginData)
    });

    const data = await response.json();

    if (data.status === 'success') {
      emit("sesionIniciada", { email: data.user.email, rol: data.user.rol });
      error.value = '';
      router.push({ name: "home" });
    } else {
      error.value = 'Usuario y/o contraseña incorrectos.';
    }
  } catch (err) {
    error.value = 'Error al cargar los datos';
  }
}
</script>



<template>
  <div class="d-flex flex-column justify-content-center align-items-center mt-5">
    <img src="@/assets/logo/friturs.png" alt="Logo Fritu" class="img-fluid mb-4" style="max-width: 200px;" />
    <h1>Iniciar sesión</h1>
    <form class="p-4 border rounded bg-light mt-3" @submit.prevent="iniciarSesion">
      <div class="mb-3">
        <label for="correo" :class="{'form-label': true, 'text-danger': errorCorreo}">Correo</label>
        <input v-model="form.correo" type="text" :class="{'form-control': true,'is-invalid': errorCorreo}" id="correo" placeholder="Ejemplo: usuario@mail.com" />
        <p v-if="errorCorreo" class="invalid-feedback">{{ errorCorreo }}</p>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input v-model="form.password" type="password" class="form-control" id="password" placeholder="********" />
      </div>
      <p v-if="error" class="text-danger mt-2">{{ error }}</p>
      <button type="submit" class="btn btn-danger w-100" :disabled="!isFormValid">Iniciar sesión</button>
      <p class="text-center mt-3">
        ¿No tienes cuenta? <router-link to="/registro">Regístrate</router-link>
      </p>
    </form>
  </div>
</template>




<style scoped>
</style>
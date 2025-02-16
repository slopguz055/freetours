<script setup>
import { ref } from "vue";
import router from "@/router";

const emit = defineEmits(["sesionIniciada"]);
const form = ref({ usuario: '', password: '' });
const error = ref('');

async function iniciarSesion() {
  try {
    const loginData = {
      email: form.value.usuario,
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
      error.value = 'Usuario o contraseña incorrectos';
    }
  } catch (err) {
    error.value = 'Error al cargar los datos';
  }
}
</script>

<template>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <form class="login-form p-4 border rounded bg-light"> 
      <div class="mb-3">
        <label for="usuario" class="form-label">Correo</label>
        <input v-model="form.usuario" type="text" class="form-control" id="usuario" />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input v-model="form.password" type="password" class="form-control" id="password" />
      </div>
      <p v-if="error" class="text-danger mt-2">{{ error }}</p>
      <button @click.prevent="iniciarSesion" class="btn btn-primary w-100">Iniciar sesión</button> 
    </form>
  </div>
</template>

<style scoped>
.vh-100 {
  height: 100vh;
}
</style>
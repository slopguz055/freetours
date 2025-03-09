<script setup>
import Usuarios from '../../components/admin/Usuarios.vue'
</script>



<template>
  <div>
    <Usuarios ipserver="http://localhost:8080/API_freetours/api.php/usuarios" />
  </div>
</template>
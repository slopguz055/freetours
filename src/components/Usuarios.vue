<template>
  <div class="container">
    <h2>Gestión de usuarios</h2>
    <!-- Muestra un mensaje de carga mientras se cargan los usuarios -->
    <div v-if="loading">Loading...</div>
    <div v-else>
      <div class="row">
        <div class="col-md-4 col-sm-6">
          <!-- Botón para mostrar el modal de creación de usuario -->
          <button @click="mostrarModalCrearUsuario()" class="btn btn-primary btn-block">Crear usuario</button>
        </div>
      </div>

      <!-- Lista de usuarios -->
      <ul class="list-group mt-3">
        <li v-for="user in users" :key="user.id" class="list-group-item">
          <div class="d-flex justify-content-between align-items-center">
            <div class="col-9">
              <!-- Muestra la información del usuario -->
              {{ user.nombre }} - {{ user.id }} - {{ user.email }} - {{ user.rol }}
            </div>
            <div class="col-3">
              <!-- Botones para editar y eliminar usuarios -->
              <button @click="editUser(user)" class="btn btn-sm btn-warning">Editar</button>
              <button @click="deleteUser(user.id)" class="btn btn-sm btn-danger">Eliminar</button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <!-- Modal para crear o editar usuarios -->
  <div class="modal" :class="{ 'd-block': showModalCrearUsuario}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <!-- Título del modal -->
          <h5 v-if="editingUser" class="modal-title">Editando usuario</h5>
          <h5 v-else class="modal-title">Nuevo usuario</h5>
          <!-- Botón para cerrar el modal -->
          <button type="button" class="close" aria-label="Close" @click="cerrarModalCrearUsuario">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Formulario para editar usuario -->
          <div v-if="editingUser">
            <div class="row">
              <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" v-model="newUser.nombre" class="form-control" readonly />
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" id="email" v-model="newUser.email" class="form-control" readonly />
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <label for="rol" class="form-label">Rol:</label>
                <select id="rol" v-model="newUser.rol" class="form-control" required>
                  <option value="admin">Admin</option>
                  <option value="guia">Guía</option>
                  <option value="cliente">Cliente</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Formulario para crear usuario -->
          <div v-else>
            <div class="row">
              <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" v-model="newUser.nombre" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" id="email" v-model="newUser.email" class="form-control" required />
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <label for="rol" class="form-label">Rol:</label>
                <input type="text" id="rol" v-model="newUser.rol" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" id="contraseña" v-model="newUser.contraseña" class="form-control" required />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <!-- Botones para cerrar el modal y guardar cambios o crear usuario -->
          <button type="button" class="btn btn-secondary" @click="cerrarModalCrearUsuario">Cerrar</button>
          <button v-if="editingUser" type="button" class="btn btn-primary" @click="saveChanges">Guardar</button>
          <button v-else type="button" class="btn btn-primary" @click="crearUsuario">Crear</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { defineProps, toRefs } from 'vue';

// Define las propiedades que recibe el componente
const props = defineProps({
  ipserver: String
});

// Convierte las propiedades en referencias reactivas
const { ipserver } = toRefs(props);
const showModalCrearUsuario = ref(false);

// Función para mostrar el modal de creación de usuario
const mostrarModalCrearUsuario = () => {
  showModalCrearUsuario.value = true;
};

// Función para cerrar el modal de creación de usuario
const cerrarModalCrearUsuario = () => {
  clearForm();
  showModalCrearUsuario.value = false;
};

// Variables reactivas para el estado del componente
const loading = ref(true)
const users = ref([])
const newUser = reactive({
  nombre: '',
  email: '',
  contraseña: '',
  rol: ''
})
const editingUser = ref(null)

// Función para cargar los usuarios desde el servidor
const loadUsers = async () => {
  try {
    const response = await fetch(`${ipserver.value}`)
    if (!response.ok) {
      if (response.status === 404) {
        loading.value = false
        throw new Error('Resource not found (404)')
      } else {
        throw new Error(`HTTP error! Status: ${response.status}`)
      }
    }
    const data = await response.json()
    users.value = data
    loading.value = false
  } catch (error) {
    console.error('Error loading users:', error)
  }
}

// Cargar los usuarios cuando el componente se monta
onMounted(() => {
  loadUsers()
})

// Función para crear un nuevo usuario
const crearUsuario = async () => {
  try {
    const response = await fetch(`${ipserver.value}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(newUser)
    })
    const data = await response.json()
    users.value.push(data)
    clearForm()
    showModalCrearUsuario.value = false;
  } catch (error) {
    console.error('Error creating user:', error)
  }
}

// Función para editar un usuario existente
const editUser = (user) => {
  editingUser.value = user
  newUser.nombre = user.nombre
  newUser.email = user.email
  newUser.contraseña = user.contraseña
  newUser.rol = user.rol
  showModalCrearUsuario.value = true;
}

// Función para guardar los cambios de un usuario editado
const saveChanges = async () => {
  try {
    const response = await fetch(`${ipserver.value}/api.php/usuarios?id=${editingUser.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ rol: newUser.rol }) // Solo se actualiza el rol
    })
    const data = await response.json()
    const index = users.value.findIndex(user => user.id === data.id)
    users.value.splice(index, 1, data)
    showModalCrearUsuario.value = false;
    clearForm()
  } catch (error) {
    console.error('Error saving changes:', error)
  }
}

// Función para eliminar un usuario
const deleteUser = async (userId) => {
  try {
    await fetch(`${ipserver.value}/api.php/usuarios?id=${userId}`, {
      method: 'DELETE'
    })
    users.value = users.value.filter((user) => user.id !== userId)
  } catch (error) {
    console.error('Error deleting user:', error)
  }
}

// Función para limpiar el formulario de creación/edición de usuario
const clearForm = () => {
  newUser.nombre = ''
  newUser.email = ''
  newUser.contraseña = ''
  newUser.rol = ''
  editingUser.value = null
}
</script>

<style scoped>
.user-management {
  max-width: 90%;
  margin: 0 auto;
}

.btn-sm {
  width: 45%;
  margin-right: 5%;
}

@media (max-width: 768px) {
  .btn-block {
    display: block;
    width: 100%;
    margin-bottom: 5px;
  }
  .btn-sm {
    width: 60%;
    margin-right: 2%;
  }
}
</style>
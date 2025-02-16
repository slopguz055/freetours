<script setup>

// No se está usando "datos"
const props = defineProps({
    datos: Object,
});

</script>

<template>
    <nav class="navbar navbar-expand navbar-light bg-light">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <RouterLink to="/">Index</RouterLink>
                <RouterLink to="/users">Gestión de Usuarios</RouterLink>
            </ul>
        </div>
    </nav>
</template>
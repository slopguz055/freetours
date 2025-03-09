<template>
    <b-breadcrumb :items="items"></b-breadcrumb>
  </template>
  
  <script>
  import { BBreadcrumb } from 'bootstrap-vue-3'
  
  export default {
    components: {
      BBreadcrumb
    },
    props: {
      items: {
        type: Array,
        required: true
      }
    }
  }
  </script>
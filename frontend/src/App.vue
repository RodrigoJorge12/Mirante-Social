<script setup lang="ts">
  import { RouterLink, RouterView } from 'vue-router'
  import Header from '@/Header.vue';
</script>
<template>
  <Header />
  <RouterView />
</template>

<style>
/* REMOVE QUALQUER max-width que o template do Vite cria */
#app {
  width: 100%;
  max-width: none !important;
  padding: 0;
  margin: 0;
}

/* Isto força o conteúdo das páginas a ocupar 100% da tela */
.page-container {
  width: 100%;
  min-height: 100vh;
  padding-top: 80px; /* espaço pro header fixo */
}
</style>


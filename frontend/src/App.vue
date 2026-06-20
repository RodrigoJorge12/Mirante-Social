<script setup lang="ts">
  import { computed } from 'vue'
  import { RouterLink, RouterView, useRoute } from 'vue-router'
  import Header from '@/Header.vue';
  import Footer from '@/Footer.vue';

  const route = useRoute();
  const hideHeader = computed(() => route.path.startsWith('/personalizedPages'))
</script>
<template>
  <Header v-if="!hideHeader" />
  <RouterView />
  <Footer v-if="!route.path.startsWith('/personalizedPages')" />
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


<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import Header from '@/Header.vue';

const router = useRouter();

function goToLoginPage() {
  router.push("/login"); 
}

</script>

<template>
</template>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  /* min-height: 100vh; ocupa a altura toda da tela */
  background: #fff;
  margin: 0;
  padding: 0;
}

</style>

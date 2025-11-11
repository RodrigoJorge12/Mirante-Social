<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import MiranteSocialButton from "./components/MiranteSocialButton.vue";

const router = useRouter();

function goToLoginPage() {
  router.push("/login"); 
}

</script>

<template>
    <header class="header">
      <h2 class="title">Mirante Social</h2>
      <ul class="list">
        <li><a>Inicio</a></li>
        <li><a>Mapa</a></li>
        <li><a>Projetos</a></li>
        <li><a>Cadastrar Projeto</a></li>
        <li><a>Sobre</a></li>
      </ul>
      <MiranteSocialButton @click="goToLoginPage()">Login</MiranteSocialButton>
      <!-- <button  class="btnLogin">Login</button> -->
    </header>
</template>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  background: #fff;
  margin: 0;
  padding: 0;
}

/* Header fixo no topo e largura total */
.header {
  display: flex;
  align-items: center;
  justify-content: space-around;
  background: #fff;
  border-bottom: 1px solid #eee;
  padding: 1rem;
  width: 100%;
  position: fixed; /* fixa no topo */
  top: 0;
  left: 0;
  z-index: 10;
}
.title{
  color: #10B981;
  font-weight: bold;
}

/* Corrige o conteúdo pra não ficar atrás do header */
.content {
  margin-top: 80px; /* altura aproximada do header */
  text-align: center;
}

/* Lista sem pontos e com espaçamento horizontal */
.list {
  display: flex;
  gap: 1rem;
  list-style: none;
  margin: 0;
  padding: 0;
}
.list li a{
  font-weight: bold;
  color: black;
}

</style>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

function goToLoginPage() {
  router.push("/login"); 
}

</script>

<template>
  <div class="page">
    <header class="header">
      <h2 class="title">Mirante Social</h2>
      <ul class="list">
        <li><a>Inicio</a></li>
        <li><a>Mapa</a></li>
        <li><a>Projetos</a></li>
        <li><a>Cadastrar Projeto</a></li>
        <li><a>Sobre</a></li>
      </ul>
      <button @click="goToLoginPage()" class="btnLogin" ref="btnLogin">Login</button>
    </header>
  </div>
</template>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* ocupa a altura toda da tela */
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
.btnLogin{
  border: none;
  font: inherit;
  color: #10B981;
  background-color: #FFFFFF;
  font-weight: bold;

}
</style>

<script setup lang="ts">
import { ref } from "vue";
import Header from '@/Header.vue';

const email = ref("");
const password = ref("");

function handleLogin() {
  alert(`Login com: ${email.value} / ${password.value}`);
}
</script>

<template>
  <Header />
  <div class="create-user-card">
    <h2 class="title">MIRANTE SOCIAL</h2>
    <form class="formCreateUser">
        <input
        class="inputNome"
        placeholder="Nome Completo"
        required
      />
      <input
        class="inputEmail"
        v-model="email"
        type="email"
        placeholder="Email"
        required
      />
      <input 
        class="inputSenha"
        v-model="password"
        type="password"
        placeholder="Senha"
        required
      />
      <button type="submit" class="btnFormCreateUser">Cadastrar</button>
    </form>
  </div>
</template>

<style scoped>
.create-user-card{
  display: flex;
  flex-direction: column;
  padding-top: 10vh;
  gap: 3vh;
}
.formCreateUser{
  display: flex;
  flex-direction: column;
  gap: 1vh;
}
.title{
  color: #10B981;
  font-weight: bold;
}
.btnFormCreateUser, .reate-user-card button{
  color: #10B981;
  font-weight: bold;
}
</style>

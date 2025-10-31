<script setup lang="ts">
import { ref } from "vue";
import Header from '@/Header.vue';
import { useRouter } from "vue-router";


const email = ref("");
const password = ref("");
const router = useRouter();


function handleLogin() {
  alert(`Login com: ${email.value} / ${password.value}`);
}
function goToCreateUserPage() {
  router.push("/createUser");
}
</script>

<template>
  <div class="login-card">
    <h2 class="title">MIRANTE SOCIAL</h2>
    <form class="formLogin" @submit.prevent="handleLogin">
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
      <button type="submit" class="btnFormLogin">Login</button>
    </form>
    <button>Esqueceu a senha</button>
    <button @click="goToCreateUserPage()">Não tem conta? Cadastre-se</button>
  </div>
</template>

<style scoped>
.login-card{
  display: flex;
  flex-direction: column;
  padding-top: 10vh;
  gap: 3vh;
}
.formLogin{
  display: flex;
  flex-direction: column;
  gap: 1vh;
}
.title{
  color: #10B981;
  font-weight: bold;
}
.btnFormLogin, .login-card button{
  color: #10B981;
  font-weight: bold;
}
</style>

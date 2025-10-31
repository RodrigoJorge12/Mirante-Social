<script setup lang="ts">
import { ref } from "vue";
import Header from '@/Header.vue';
import { PresenterUsuario } from "./Presenters/PresenterUsuario";
import ModalEmailValidation  from './ModalEmailValidation.vue';

const showModal = ref(false)

const presenter = new PresenterUsuario();
const email = ref("");
const password = ref("");
const name = ref("");
async function sendDataCreatUser(event: Event) {
  event.preventDefault();
  await presenter.createUser(name.value, email.value, password.value);
  showModal.value = true
}


function handleLogin() {
  alert(`Login com: ${email.value} / ${password.value}`);
}
</script>

<template>
  <div class="create-user-card">
    <h2 class="title">MIRANTE SOCIAL</h2>
    <form class="formCreateUser">
        <input
        class="inputNome"
        v-model="name"
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
      <button @click="sendDataCreatUser($event)" type="submit" class="btnFormCreateUser">Cadastrar</button>
    </form>
      <ModalEmailValidation
      v-if="showModal"
      :email="email"
      @close="showModal = false"
    />
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

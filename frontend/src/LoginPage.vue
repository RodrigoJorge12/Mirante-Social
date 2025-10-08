<script setup lang="ts">
import { ref } from "vue";

const email = ref("");
const password = ref("");

function handleLogin() {
  alert(`Login com: ${email.value} / ${password.value}`);
}
</script>

<template>
  <div class="login">
    <div class="login-card">
      <h2>Entrar</h2>
      <form @submit.prevent="handleLogin">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          required
        />
        <input
          v-model="password"
          type="password"
          placeholder="Senha"
          required
        />
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #f9fafb;
}

.login-card {
  background: #fff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 350px;
  text-align: center;
}

.login-card h2 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.login-card form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.login-card input {
  padding: 0.7rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.login-card button {
  background: #10b981;
  color: #fff;
  padding: 0.7rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
}

.login-card button:hover {
  background: #059669;
}
</style>

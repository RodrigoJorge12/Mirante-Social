<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'
import { PresenterUsuario } from './Presenters/PresenterUsuario';
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import MiranteSocialErrorToast from "./components/MiranteSocialErrorToast.vue";


const props = defineProps({
  email: { type: String, required: true }
})
const emit = defineEmits(['close', 'verify'])

async function VerifyCode(event: Event) {
  event.preventDefault();
  let presenter = new PresenterUsuario();
  let verified = await presenter.verifyEmailCode(props.email, code.value);
  if(verified.success){
    emit('close');
  }
  // else{
  //   useErrorToast('Falha ao enviar email de validação')
  // }
}
const code = ref('')
</script>

<template>
  <div class="overlay" @click.self="emit('close')">
    <div class="modal">
      <h3>Verificação de e-mail</h3>
      <p>Um código foi enviado para <b>{{ email }}</b> <br>Digite-o aqui para validar seu usuario</p>

      <input
        v-model="code"
        placeholder="Digite o código de 6 dígitos"
        maxlength="6"
        class="input-code"
      />

      <div class="buttons">
        <MiranteSocialButton @click="VerifyCode($event)">Confirmar</MiranteSocialButton>
        <MiranteSocialButton @click="emit('close')">Fechar</MiranteSocialButton>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal {
  background: white;
  color: black;
  border-radius: 12px;
  padding: 2rem;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.input-code {
  width: 100%;
  padding: 8px;
  margin: 16px 0;
  text-align: center;
  font-size: 18px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

.buttons {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

/* button {
  flex: 1;
  padding: 8px;
  border: none;
  background-color: #10B981;
  color: white;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
}

button:last-child {
  background-color: #ccc;
  color: black;
} */
</style>
<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'
import { PresenterUsuario } from './Presenters/PresenterUsuario';
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import { ElNotification } from 'element-plus';


const props = defineProps({
  email: { type: String, required: true }
})
const emit = defineEmits(['close', 'verify'])

async function VerifyCode(event: Event) {
  event.preventDefault();
  let presenter = new PresenterUsuario();
  let verified = await presenter.verifyEmailCode(props.email, code.value);
  if(verified && verified.success){
    emit('close');
    ElNotification({
      title: 'Sucesso',
      message: 'Email validado com sucesso!',
      type: 'success'
    })
  }
  else{
    ElNotification({
      title: 'Erro',
      message: 'Ocorreu um problema ao validar o email.',
      type: 'error',
      zIndex: 10000 
    })
  }
}
const code = ref('')
</script>

<template>
  <div class="overlay" @click.self="emit('close')">
    <div class="modal">
      <div class="modal-icon">📬</div>
      <h3 class="modal-title">Verifique seu e-mail</h3>
      <p class="modal-desc">
        Enviamos um código para<br>
        <strong>{{ email }}</strong><br>
        Digite-o abaixo para confirmar seu cadastro.
      </p>

      <input
        v-model="code"
        placeholder="000000"
        maxlength="6"
        class="input-code"
      />

      <button class="btn-confirm" @click="VerifyCode($event)">Confirmar</button>
      <span class="link-close" @click="emit('close')">Fechar</span>
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
  background: rgba(2, 44, 34, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
}

.modal {
  background: #fff;
  border-radius: 20px;
  padding: 40px 36px;
  width: 90%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 24px 64px rgba(16, 185, 129, 0.18);
  border: 2px solid #F0FDF4;
  font-family: Nunito, sans-serif;
}

.modal-icon {
  font-size: 44px;
  margin-bottom: 16px;
  line-height: 1;
}

.modal-title {
  font-size: 20px;
  font-weight: 900;
  color: #022C22;
  margin: 0 0 12px;
  letter-spacing: -0.02em;
}

.modal-desc {
  font-size: 15px;
  color: #4b7a5e;
  font-weight: 600;
  line-height: 1.6;
  margin: 0 0 24px;
}

.modal-desc strong {
  color: #065F46;
  font-weight: 800;
}

.input-code {
  width: 100%;
  padding: 14px 16px;
  margin-bottom: 20px;
  text-align: center;
  font-size: 24px;
  font-weight: 800;
  letter-spacing: 0.3em;
  border: 2px solid #ECFDF5;
  border-radius: 14px;
  background: #F0FDF4;
  color: #022C22;
  outline: none;
  font-family: Nunito, sans-serif;
  box-sizing: border-box;
  transition: border-color 0.2s, background 0.2s;
}

.input-code:focus {
  border-color: #10B981;
  background: #fff;
}

.input-code::placeholder {
  color: #a0c4a0;
  letter-spacing: 0.2em;
  font-weight: 600;
}

.btn-confirm {
  width: 100%;
  padding: 15px;
  border-radius: 14px;
  border: none;
  background: #059669;
  color: #fff;
  font-size: 16px;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(5, 150, 105, 0.28);
  font-family: Nunito, sans-serif;
  margin-bottom: 16px;
  transition: background 0.2s;
}

.btn-confirm:hover {
  background: #047857;
}

.link-close {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #7aaa7a;
  cursor: pointer;
  font-family: Nunito, sans-serif;
}

.link-close:hover {
  color: #059669;
  text-decoration: underline;
}
</style>
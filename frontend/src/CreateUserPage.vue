<script setup lang="ts">
import { ref, reactive } from "vue"
import { ElForm, ElFormItem, ElInput, ElButton } from "element-plus"
import type { FormInstance, FormRules } from "element-plus"
import { PresenterUsuario } from "./Presenters/PresenterUsuario"
import ModalEmailValidation from "./ModalEmailValidation.vue"
import MiranteSocialButton from "./components/MiranteSocialButton.vue";

const showModal = ref(false)
const presenter = new PresenterUsuario()

interface CreateUserForm {
  name: string
  email: string
  password: string
}

const formRef = ref<FormInstance>()
const form = reactive<CreateUserForm>({
  name: "",
  email: "",
  password: "",
})

// Regras de validação
const rules: FormRules<CreateUserForm> = {
  name: [{ required: true, message: "Nome obrigatório", trigger: "blur" }],
  email: [
    { required: true, message: "E-mail obrigatório", trigger: "blur" },
    { type: "email", message: "Digite um e-mail válido", trigger: ["blur", "change"] },
  ],
  password: [
    { required: true, message: "Senha obrigatória", trigger: "blur" },
    { min: 6, message: "A senha deve ter pelo menos 6 caracteres", trigger: "blur" },
  ],
}

// Envio do formulário
const sendDataCreateUser = async () => {
  if (!formRef.value) return

  try {
    const valid = await formRef.value.validate()
    if (valid) {
      await presenter.createUser(form.name, form.email, form.password)
      showModal.value = true
    }
  } catch {
    // erros de validação são tratados automaticamente pelo Element Plus
  }
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-top">
        <img src="/MiranteSocial.png" alt="Mirante Social" class="auth-logo" />
        <h2>Criar conta gratuita</h2>
        <p>Junte-se à comunidade</p>
      </div>

      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-position="top"
        status-icon
        class="auth-form"
      >
        <el-form-item label="Nome Completo" prop="name">
          <el-input v-model="form.name" placeholder="Seu nome completo" />
        </el-form-item>

        <el-form-item label="E-mail" prop="email">
          <el-input v-model="form.email" placeholder="Digite seu e-mail" clearable />
        </el-form-item>

        <el-form-item label="Senha" prop="password">
          <el-input
            v-model="form.password"
            type="password"
            show-password
            placeholder="Crie uma senha"
          />
        </el-form-item>

        <el-form-item>
          <MiranteSocialButton type="primary" class="register-button" @click="sendDataCreateUser">
            Criar conta
          </MiranteSocialButton>
        </el-form-item>
      </el-form>

      <ModalEmailValidation
        v-if="showModal"
        :email="form.email"
        @close="showModal = false"
      />
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #F0FDF4, #E4F9EE, #FAFFFE);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  font-family: Nunito, sans-serif;
}

.auth-card {
  background: #fff;
  border-radius: 28px;
  border: 2px solid #F0FDF4;
  padding: 44px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 20px 60px rgba(16, 185, 129, 0.12);
  display: flex;
  flex-direction: column;
}

.auth-top {
  text-align: center;
  margin-bottom: 32px;
}

.auth-logo {
  height: 72px;
  width: auto;
  border-radius: 16px;
  display: block;
  margin: 0 auto 18px;
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
}

.auth-top h2 {
  font-size: 22px;
  font-weight: 900;
  color: #022C22;
  letter-spacing: -0.02em;
  margin: 0 0 4px;
}

.auth-top p {
  font-size: 14px;
  color: #7aaa7a;
  margin: 0;
  font-weight: 600;
}

.auth-form :deep(.el-form-item__label) {
  font-family: Nunito, sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #064E3B;
  padding-bottom: 4px;
}

.auth-form :deep(.el-input__wrapper) {
  border-radius: 12px;
  border: 2px solid #ECFDF5;
  background: #F0FDF4;
  box-shadow: none !important;
  padding: 4px 12px;
}

.auth-form :deep(.el-input__wrapper:hover),
.auth-form :deep(.el-input__wrapper.is-focus) {
  border-color: #10B981;
  background: #fff;
}

.auth-form :deep(.el-input__inner) {
  font-family: Nunito, sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #1a2e1a;
  height: 40px;
}

.auth-form :deep(.el-input__inner::placeholder) {
  color: #a0c4a0;
  font-weight: 500;
}

.register-button {
  width: 100%;
  height: 50px;
  font-size: 16px;
  font-weight: 800;
  border-radius: 14px;
  font-family: Nunito, sans-serif;
}
</style>

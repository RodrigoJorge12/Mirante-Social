<script setup lang="ts">
import { ref, reactive } from "vue"
import { useRouter } from "vue-router"
import { ElForm, ElFormItem, ElInput, ElButton, ElMessage } from "element-plus"
import type { FormInstance, FormRules } from "element-plus"
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import { PresenterUsuario } from "./Presenters/PresenterUsuario"
import ForgotPasswordModal from "./ForgotPasswordModal.vue";

interface LoginForm {
  email: string
  password: string
}

const router = useRouter()
const formRef = ref<FormInstance>()
const form = reactive<LoginForm>({
  email: "",
  password: ""
})

// ✅ Tipagem correta do Element Plus
const rules: FormRules<LoginForm> = {
  email: [
    { required: true, message: "O e-mail é obrigatório", trigger: "blur" },
    { type: "email", message: "Digite um e-mail válido", trigger: ["blur", "change"] },
  ],
  password: [
    { required: true, message: "A senha é obrigatória", trigger: "blur" },
    { min: 6, message: "A senha deve ter no mínimo 6 caracteres", trigger: "blur" },
  ],
}
const presenter = new PresenterUsuario()
// ✅ Método correto sem erro de tipo
const submitForm = async () => {
  if (!formRef.value) return

  try {
    const valid = await formRef.value.validate()
    if (valid) {
      const login = await presenter.login(form.email, form.password);
      if(login && login.success){
        ElMessage.success("Bem-vindo de volta! Entrando na plataforma...")
        router.push("/").then(() => {
          window.location.reload();
        });
      }
      else{
        ElMessage.error("E-mail ou senha incorretos. Verifique e tente novamente.")
      }
    }
  } catch {
    ElMessage.error("Verifique os campos e tente novamente.")
  }
}

const forgotDialogVisible = ref(false);
function goToForgotPassword() {
  forgotDialogVisible.value = true;
}

function goToCreateUserPage() {
  router.push("/createUser")
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-top">
        <img src="/MiranteSocial.png" alt="Mirante Social" class="auth-logo" />
        <h2>Bem-vindo de volta</h2>
        <p>Entre para acessar seus projetos</p>
      </div>

      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-position="top"
        status-icon
        class="auth-form"
      >
        <el-form-item label="E-mail" prop="email">
          <el-input v-model="form.email" placeholder="Digite seu e-mail" clearable class="ja-input" />
        </el-form-item>

        <el-form-item label="Senha" prop="password">
          <el-input
            v-model="form.password"
            type="password"
            show-password
            placeholder="Digite sua senha"
            class="ja-input"
          />
        </el-form-item>

        <div class="actions">
          <span class="link" @click="goToForgotPassword">Esqueci minha senha</span>
          <span class="link" @click="goToCreateUserPage">Cadastre-se</span>
        </div>
        <ForgotPasswordModal v-model="forgotDialogVisible" />

        <el-form-item>
          <MiranteSocialButton type="primary" class="login-button" @click="submitForm">
            Entrar
          </MiranteSocialButton>
        </el-form-item>
      </el-form>
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

.actions {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  margin-bottom: 20px;
  margin-top: -4px;
}

.link {
  color: #059669;
  cursor: pointer;
  font-size: 14px;
  font-weight: 700;
  font-family: Nunito, sans-serif;
  text-decoration: none;
}

.link:hover {
  color: #047857;
  text-decoration: underline;
}

.login-button {
  width: 100%;
  height: 50px;
  font-size: 16px;
  font-weight: 800;
  border-radius: 14px;
  font-family: Nunito, sans-serif;
}
</style>

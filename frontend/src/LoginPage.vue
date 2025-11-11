<script setup lang="ts">
import { ref, reactive } from "vue"
import { useRouter } from "vue-router"
import { ElForm, ElFormItem, ElInput, ElButton, ElMessage } from "element-plus"
import type { FormInstance, FormRules } from "element-plus"
import MiranteSocialButton from "./components/MiranteSocialButton.vue";

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

// ✅ Método correto sem erro de tipo
const submitForm = async () => {
  if (!formRef.value) return

  try {
    const valid = await formRef.value.validate()
    if (valid) {
      ElMessage.success(`Login com: ${form.email} / ${form.password}`)
      // aqui você pode chamar o PresenterUsuario ou API de login
    }
  } catch {
    ElMessage.error("Por favor, corrija os erros antes de enviar.")
  }
}

function goToForgotPassword() {
  ElMessage.info("Funcionalidade de recuperação de senha em breve.")
}

function goToCreateUserPage() {
  router.push("/createUser")
}
</script>

<template>
  <div class="login-card">
    <h2 class="title">MIRANTE SOCIAL</h2>

    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-position="top"
      status-icon
    >
      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" placeholder="Digite seu e-mail" clearable />
      </el-form-item>

      <el-form-item label="Senha" prop="password">
        <el-input
          v-model="form.password"
          type="password"
          show-password
          placeholder="Digite sua senha"
        />
      </el-form-item>

      <div class="actions">
        <span class="link" @click="goToForgotPassword">Esqueci minha senha</span>
        <span class="link" @click="goToCreateUserPage">Cadastre-se</span>
      </div>

      <el-form-item>
        <MiranteSocialButton type="primary" class="login-button" @click="submitForm">
          Login
        </MiranteSocialButton>
      </el-form-item>
    </el-form>
  </div>
</template>

<style scoped>
.login-card {
  max-width: 400px;
  margin: 8rem auto;
  padding: 2rem;
  border-radius: 12px;
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.title {
  text-align: center;
  color: #10B981;
  font-weight: bold;
  margin-bottom: 1rem;
}

.actions {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.link {
  color: #10B981;
  cursor: pointer;
  font-size: 14px;
  text-decoration: underline;
}

.login-button {
  width: 100%;
}
</style>

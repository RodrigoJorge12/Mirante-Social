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
  <div class="create-user-card">
    <h2 class="title">MIRANTE SOCIAL</h2>

    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-position="top"
      status-icon
    >
      <el-form-item label="Nome Completo" prop="name">
        <el-input v-model="form.name" placeholder="Nome Completo" />
      </el-form-item>

      <el-form-item label="Email" prop="email">
        <el-input v-model="form.email" placeholder="Email" clearable />
      </el-form-item>

      <el-form-item label="Senha" prop="password">
        <el-input
          v-model="form.password"
          type="password"
          show-password
          placeholder="Senha"
        />
      </el-form-item>

      <el-form-item>
        <MiranteSocialButton type="primary" class="register-button" @click="sendDataCreateUser">
          Cadastrar
        </MiranteSocialButton>
      </el-form-item>
    </el-form>

    <ModalEmailValidation
      v-if="showModal"
      :email="form.email"
      @close="showModal = false"
    />
  </div>
</template>

<style scoped>
.create-user-card {
  max-width: 400px;
  margin: 8rem auto;
  padding: 2rem;
  border-radius: 12px;
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 2vh;
}

.title {
  text-align: center;
  color: #10B981;
  font-weight: bold;
  margin-bottom: 1rem;
}

.register-button {
  width: 100%;
}
</style>

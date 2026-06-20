<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { ElMessage } from "element-plus";
import type { FormInstance, FormRules } from "element-plus";
import MiranteSocialButton from "./components/MiranteSocialButton.vue";

import { PresenterUsuario } from "@/Presenters/PresenterUsuario";

// Recebe v-model:visible do pai
const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
}>();

const presenter = new PresenterUsuario(); // você usa depois

// step = 'email' (primeiro modal) ou 'reset' (segundo modal)
const step = ref<"email" | "reset">("email");

// v-model do dialog
const dialogVisible = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit("update:modelValue", val),
});

// ----- FORM 1: EMAIL -----
const emailFormRef = ref<FormInstance>();

const emailForm = reactive({
  email: "",
});

const emailRules: FormRules = {
  email: [
    { required: true, message: "E-mail é obrigatório", trigger: "blur" },
    { type: "email", message: "E-mail inválido", trigger: ["blur", "change"] },
  ],
};

// ----- FORM 2: RESET SENHA -----
const resetFormRef = ref<FormInstance>();

const resetForm = reactive({
  email: "",          // preenchido a partir do passo 1
  code: "",
  password: "",
  confirmPassword: "",
});

const resetRules: FormRules = {
  code: [
    { required: true, message: "Código obrigatório", trigger: "blur" },
  ],
  password: [
    { required: true, message: "Nova senha obrigatória", trigger: "blur" },
    { min: 6, message: "A senha deve ter pelo menos 6 caracteres", trigger: "blur" },
  ],
  confirmPassword: [
    { required: true, message: "Confirme a nova senha", trigger: "blur" },
    {
      validator: (_rule, value, callback) => {
        if (!value) {
          callback(new Error("Confirme a nova senha"));
        } else if (value !== resetForm.password) {
          callback(new Error("As senhas não conferem"));
        } else {
          callback();
        }
      },
      trigger: ["blur", "change"],
    },
  ],
};

// ----- Helpers -----
function resetAll() {
  step.value = "email";
  emailForm.email = "";
  resetForm.email = "";
  resetForm.code = "";
  resetForm.password = "";
  resetForm.confirmPassword = "";
}

function handleClosed() {
  resetAll();
}

const handleRequestCode = async () => {
  if (!emailFormRef.value) return;

  await emailFormRef.value.validate(async (valid) => {
    if (!valid) return;

    try {
      const response = await presenter.sendPasswordResetEmail(emailForm.email);

      if (response?.success) {
        resetForm.email = emailForm.email;
        step.value = "reset";
        ElMessage.success("Código enviado! Verifique sua caixa de entrada.");
      } else {
        ElMessage.error("Não encontramos esse e-mail. Verifique e tente novamente.");
      }
    } catch (e) {
      console.error(e);
      ElMessage.error("Ocorreu um erro ao enviar o código. Tente novamente em instantes.");
    }
  });
};

const handleResetPassword = async () => {
  if (!resetFormRef.value) return;

  await resetFormRef.value.validate(async (valid) => {
    if (!valid) return;

    try {
      const response = await presenter.resetPassword(resetForm.email, resetForm.code, resetForm.password);


      if (response?.success) {
        ElMessage.success("Senha alterada com sucesso! Já pode fazer login.");
        dialogVisible.value = false;
        resetAll();
      } else {
        ElMessage.error("Código inválido ou expirado. Solicite um novo código.");
      }
    } catch (e) {
      console.error(e);
      ElMessage.error("Ocorreu um erro ao alterar a senha. Tente novamente em instantes.");
    }
  });
};
</script>

<template>
  <el-dialog
    v-model="dialogVisible"
    width="460px"
    @closed="handleClosed"
    class="ja-dialog"
  >
    <template #header>
      <div class="dialog-header">
        <span class="dialog-icon">🔐</span>
        <span class="dialog-title">Recuperar senha</span>
      </div>
    </template>

    <!-- PASSO 1: SÓ EMAIL -->
    <template v-if="step === 'email'">
      <p class="dialog-desc">Digite o e-mail da sua conta e enviaremos um código de recuperação.</p>
      <el-form
        ref="emailFormRef"
        :model="emailForm"
        :rules="emailRules"
        label-position="top"
        class="ja-form"
      >
        <el-form-item label="E-mail" prop="email">
          <el-input
            v-model="emailForm.email"
            placeholder="Digite o e-mail da sua conta"
          />
        </el-form-item>

        <el-form-item>
          <MiranteSocialButton
            type="primary"
            class="full-btn"
            @click="handleRequestCode"
          >
            Enviar código
          </MiranteSocialButton>
        </el-form-item>
      </el-form>
    </template>

    <!-- PASSO 2: CÓDIGO + NOVA SENHA -->
    <template v-else>
      <p class="dialog-desc">Insira o código recebido no seu e-mail e defina uma nova senha.</p>
      <el-form
        ref="resetFormRef"
        :model="resetForm"
        :rules="resetRules"
        label-position="top"
        class="ja-form"
      >
        <el-form-item label="E-mail">
          <el-input v-model="resetForm.email" disabled />
        </el-form-item>

        <el-form-item label="Código recebido por e-mail" prop="code">
          <el-input
            v-model="resetForm.code"
            placeholder="Digite o código de 6 dígitos"
          />
        </el-form-item>

        <el-form-item label="Nova senha" prop="password">
          <el-input
            v-model="resetForm.password"
            type="password"
            show-password
            placeholder="Digite a nova senha"
          />
        </el-form-item>

        <el-form-item label="Repita a nova senha" prop="confirmPassword">
          <el-input
            v-model="resetForm.confirmPassword"
            type="password"
            show-password
            placeholder="Repita a nova senha"
          />
        </el-form-item>

        <el-form-item>
          <MiranteSocialButton
            type="primary"
            class="full-btn"
            @click="handleResetPassword"
          >
            Alterar senha
          </MiranteSocialButton>
        </el-form-item>
      </el-form>
    </template>
  </el-dialog>
</template>

<style scoped>
.dialog-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.dialog-icon {
  font-size: 22px;
  line-height: 1;
}

.dialog-title {
  font-size: 18px;
  font-weight: 900;
  color: #022C22;
  font-family: Nunito, sans-serif;
  letter-spacing: -0.02em;
}

.dialog-desc {
  font-size: 14px;
  color: #4b7a5e;
  font-weight: 600;
  font-family: Nunito, sans-serif;
  margin: 0 0 20px;
  line-height: 1.5;
}

.ja-form :deep(.el-form-item__label) {
  font-family: Nunito, sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #064E3B;
  padding-bottom: 4px;
}

.ja-form :deep(.el-input__wrapper) {
  border-radius: 12px;
  border: 2px solid #ECFDF5;
  background: #F0FDF4;
  box-shadow: none !important;
  padding: 4px 12px;
}

.ja-form :deep(.el-input__wrapper:hover),
.ja-form :deep(.el-input__wrapper.is-focus) {
  border-color: #10B981;
  background: #fff;
}

.ja-form :deep(.el-input__wrapper.is-disabled) {
  background: #f7fdf7;
  border-color: #E4F9EE;
  opacity: 0.7;
}

.ja-form :deep(.el-input__inner) {
  font-family: Nunito, sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #1a2e1a;
  height: 40px;
}

.ja-form :deep(.el-input__inner::placeholder) {
  color: #a0c4a0;
  font-weight: 500;
}

.full-btn {
  width: 100%;
  height: 50px;
  font-size: 16px;
  font-weight: 800;
  border-radius: 14px;
  font-family: Nunito, sans-serif;
}
</style>

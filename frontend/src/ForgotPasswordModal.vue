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
        ElMessage.success("Código enviado para o seu e-mail.");
      } else {
        ElMessage.error("Não foi possível enviar o código. Tente novamente.");
      }
    } catch (e) {
      console.error(e);
      ElMessage.error("Erro ao enviar o código. Tente novamente.");
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
        ElMessage.success("Senha alterada com sucesso!");
        dialogVisible.value = false;
        resetAll();
      } else {
        ElMessage.error("Não foi possível alterar a senha. Verifique o código.");
      }
    } catch (e) {
      console.error(e);
      ElMessage.error("Erro ao alterar a senha. Tente novamente.");
    }
  });
};
</script>

<template>
  <el-dialog
    v-model="dialogVisible"
    title="Recuperar senha"
    width="420px"
    @closed="handleClosed"
  >
    <!-- PASSO 1: SÓ EMAIL -->
    <template v-if="step === 'email'">
      <el-form
        ref="emailFormRef"
        :model="emailForm"
        :rules="emailRules"
        label-position="top"
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
      <el-form
        ref="resetFormRef"
        :model="resetForm"
        :rules="resetRules"
        label-position="top"
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
.full-btn {
  width: 100%;
}
</style>

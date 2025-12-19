<script setup lang="ts">
import { ref, reactive } from "vue"
import { ElForm, ElFormItem, ElInput, ElSelect, ElOption, ElMessage } from "element-plus"
import type { FormInstance, FormRules } from "element-plus"
import MiranteSocialButton from "./components/MiranteSocialButton.vue"
import { PresenterSocialProject } from "./Presenters/PresenterSocialProject"
import LocationPicker from "@/LocationPicker.vue";
import router from "./router"


const fileList = ref<any[]>([])
const wantsPage = ref(false);
const selectedTemplate = ref("");
const handleExceed = () => {
  ElMessage.warning("Apenas uma imagem é permitida.")
}

const handleImageChange = (file: any) => {
  fileList.value = [file] // mantém apenas 1 imagem
  form.image = file.raw    // armazena o File no form
}

// Interface do formulário
interface CompanyForm {
  name: string
  description: string

  address: string
  district: string
  city: string
  state: string
  zipCode: string

  phone: string
  contactEmail: string
  websiteUrl: string

  visualColor: string

  activityArea: string
  targetAudiences: string[]
  image: File | null
  latitude: number | null
  longitude: number | null
}

const formRef = ref<FormInstance>()
const form = reactive<CompanyForm>({
  name: "",
  description: "",

  address: "",
  district: "",
  city: "",
  state: "",
  zipCode: "",

  phone: "",
  contactEmail: "",
  websiteUrl: "",

  visualColor: "",

  activityArea: "",
  targetAudiences: [],
  image: null,
  latitude: null as number | null,
  longitude: null as number | null,
})

// Validações
const rules: FormRules<CompanyForm> = {
  name: [{ required: true, message: "Nome obrigatório", trigger: "blur" }],
  description: [{ required: true, message: "Descrição obrigatória", trigger: "blur" }],
  address: [{ required: true, message: "Endereço obrigatório", trigger: "blur" }],
  city: [{ required: true, message: "Cidade obrigatória", trigger: "blur" }],
  zipCode: [{ required: true, message: "CEP obrigatória", trigger: "blur" }],
  phone: [{ required: true, message: "Telefone obrigatório", trigger: "blur" }],
  contactEmail: [{ required: false, type: 'email', message: "E-mail inválido", trigger: "blur" }],
  visualColor: [{ required: true, message: "Cor principal obrigatória", trigger: "blur" }],
  activityArea: [{ required: true, message: "Area de atividade obrigatória", trigger: "blur" }],
  targetAudiences: [{ required: true, message: "Publico Alvo obrigatório", trigger: "blur" }],
  image: [
  { required: true, message: "A imagem é obrigatória", trigger: "change" }
  ],
  state: [
    { required: true, min: 2, max: 2, message: "UF deve ter 2 caracteres", trigger: "blur" }
  ],
}
function onLocationUpdate(location: { latitude: number; longitude: number }) {
  form.latitude = location.latitude;
  form.longitude = location.longitude;
}
// Envio
const sendDataCreateSocialProject = async () => {
  if (!formRef.value) return

  try {
    const valid = await formRef.value.validate()

    if (valid) {
        const presenter = new PresenterSocialProject();
        const status = await presenter.CreateSocialProject(form.name, form.description, form.address, form.district, form.city, form.state, form.zipCode, form.phone, form.websiteUrl, form.visualColor, form.activityArea, form.targetAudiences, form.image, wantsPage.value, selectedTemplate.value, form.latitude, form.longitude);
        if (status && status.success){
          ElMessage.success("Projeto Social cadastrado com sucesso!");
          router.push("/");
          formRef.value.resetFields();
        } else {
          ElMessage.error("Erro ao cadastrar Projeto Social. Tente novamente.");
        }
    }
  } catch {
    ElMessage.error("Erro ao cadastrar Projeto Social. Tente novamente.");
  }
}
</script>

<template>
  <div class="company-card">
    <h2 class="title">Cadastro de Projeto Social</h2>

    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-position="top"
      status-icon
    >
      <!-- NOME -->
      <el-form-item label="Nome" prop="name">
        <el-input v-model="form.name" placeholder="Nome do Projeto Social/Empresa" />
      </el-form-item>

      <!-- IMAGEM -->
      <el-form-item label="Imagem da Empresa" prop="image">
        <el-upload
            class="upload-demo"
            action=""
            :auto-upload="false"
            :limit="1"
            :on-exceed="handleExceed"
            :on-change="handleImageChange"
            :file-list="fileList"
            list-type="picture-card"
            accept="image/*"
        >
            <i class="el-icon-plus"></i>
        </el-upload>
      </el-form-item>

      <!-- DESCRIÇÃO -->
      <el-form-item label="Descrição" prop="description">
        <el-input
          type="textarea"
          v-model="form.description"
          placeholder="Descrição da empresa"
          :rows="3"
        />
      </el-form-item>

      <!-- ENDEREÇO -->
      <el-form-item label="Endereço" prop="address">
        <el-input v-model="form.address" placeholder="Endereço" />
      </el-form-item>

      <el-form-item label="Bairro">
        <el-input v-model="form.district" placeholder="Bairro" />
      </el-form-item>

      <el-form-item label="Cidade" prop="city">
        <el-input v-model="form.city" placeholder="Cidade" />
      </el-form-item>

      <el-form-item label="Estado (UF)" prop="state">
        <el-input v-model="form.state" placeholder="Ex: RJ" maxlength="2" />
      </el-form-item>

      <el-form-item label="CEP" prop="zipCode">
        <el-input v-model="form.zipCode" placeholder="CEP" />
      </el-form-item>

      <el-form-item label="Localização no mapa">
        <LocationPicker
          :cep="form.zipCode"
          :city="form.city"
          @update:location="onLocationUpdate"
        />
      </el-form-item>

      <!-- CONTATOS -->
      <el-form-item label="Telefone" prop="phone">
        <el-input v-model="form.phone" placeholder="Telefone" />
      </el-form-item>

      <el-form-item label="E-mail de contato">
        <el-input v-model="form.contactEmail" placeholder="email@dominio.com" />
      </el-form-item>

      <el-form-item label="Website">
        <el-input v-model="form.websiteUrl" placeholder="https://site.com" />
      </el-form-item>

      <!-- VISUAL -->
     <el-form-item label="Cor Visual" prop="visualColor">
        <el-color-picker
            v-model="form.visualColor"
            :show-alpha="false"
            color-format="hex"
        />
     </el-form-item>

      <!-- ÁREA DE ATUAÇÃO -->
      <el-form-item label="Área de Atuação" prop="activityArea">
        <el-input v-model="form.activityArea" placeholder="Ex: Educação, Saúde..." />
      </el-form-item>

      <!-- PÚBLICOS-ALVO -->
      <el-form-item label="Públicos-Alvo" prop="targetAudiences">
        <el-select
          v-model="form.targetAudiences"
          multiple
          clearable
          placeholder="Selecione / digite e pressione Enter"
          filterable
          allow-create
        >
          <el-option label="Crianças" value="Crianças" />
          <el-option label="Adolescentes" value="Adolescentes" />
          <el-option label="Adultos" value="Adultos" />
          <el-option label="Idosos" value="Idosos" />
        </el-select>
      </el-form-item>
      <el-form-item label="Deseja página personalizada?">
        <el-switch
            v-model="wantsPage"
            active-text="Sim"
            inactive-text="Não"
        />
      </el-form-item>
      <el-form-item label="Template" v-if="wantsPage">
        <el-select v-model="selectedTemplate" placeholder="Selecione um template">
            <el-option label="Template 1" value="template1" />
            <el-option label="Template 2" value="template2" />
        </el-select>
      </el-form-item>

      <!-- BOTÃO -->
      <el-form-item>
        <MiranteSocialButton type="primary" class="submit-button" @click="sendDataCreateSocialProject">
          Salvar Projeto Social
        </MiranteSocialButton>
      </el-form-item>
    </el-form>
  </div>
</template>

<style scoped>
.company-card {
  max-width: 500px;
  margin: 4rem auto;
  padding: 2rem;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.title {
  text-align: center;
  margin-bottom: 1rem;
  color: #10B981;
  font-weight: bold;
}

.submit-button {
  width: 100%;
}
</style>

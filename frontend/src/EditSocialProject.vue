<script setup lang="ts">
import { ref, reactive, watch, onMounted } from "vue";
import { ElMessage, ElForm } from "element-plus";
import type { FormInstance } from "element-plus";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";

// Props recebidas do componente pai
const props = defineProps<{
  modelValue: boolean,
  projectId: number | null
}>();

const emit = defineEmits(["update:modelValue", "updated"]);

// Presenter
const presenter = new PresenterSocialProject();

// Controle do modal
const isOpen = ref(props.modelValue);

// Sincroniza o modal com o pai
watch(
  () => props.modelValue,
  v => { isOpen.value = v }
);
watch(isOpen, v => emit("update:modelValue", v));

// Formulário
const formRef = ref<FormInstance>();

const form = reactive({
  name: "",
  description: "",
  address: "",
  district: "",
  city: "",
  state: "",
  zipCode: "",
  phone: "",
  websiteUrl: "",
  visualColor: "",
  activityArea: "",
  targetAudiences: [] as string[],
  image: null as File | null,
  image_path: null as string | null
});

const fileList = ref<any[]>([]);

// Busca os dados do projeto quando abrir
watch(
  () => props.projectId,
  async (id) => {
    if (!id) return;

    const response = await presenter.GetProjectById(id);
    if (!response?.success) {
      ElMessage.error("Erro ao carregar projeto.");
      return;
    }

    const p = response.data;

    form.name = p.name;
    form.description = p.description;
    form.address = p.address;
    form.district = p.district;
    form.city = p.city;
    form.state = p.state;
    form.zipCode = p.zip_code;
    form.phone = p.phone;
    form.websiteUrl = p.website_url;
    form.visualColor = p.visual_color;
    form.activityArea = p.activity_area;
    form.targetAudiences = JSON.parse(p.target_audiences ?? "[]");
    form.image_path = p.image_path;

    // Para exibir a imagem atual no preview
    if (p.image_path) {
      fileList.value = [
        {
          name: "imagem atual",
          url: `${import.meta.env.VITE_API_URL}/storage/${p.image_path}`
        }
      ];
    }
  },
  { immediate: true }
);

// Atualiza imagem
const handleImageChange = (file: any) => {
  fileList.value = [file];
  form.image = file.raw;
};

// Envio de atualização
const submit = async () => {
  if (!formRef.value || !props.projectId) return;

  const valid = await formRef.value.validate();
  if (!valid) return;

  const result = await presenter.UpdateSocialProject(
    props.projectId,
    form.name,
    form.description,
    form.address,
    form.district,
    form.city,
    form.state,
    form.zipCode,
    form.phone,
    form.websiteUrl,
    form.visualColor,
    form.activityArea,
    form.targetAudiences,
    form.image
  );
    // const result = true;

  if (result){//result?.success) {
    ElMessage.success("Projeto atualizado com sucesso!");
    emit("updated"); // informa o pai para recarregar
    isOpen.value = false; // fecha modal
  } else {
    ElMessage.error("Erro ao atualizar projeto.");
  }
};
</script>

<template>
  <el-dialog
    v-model="isOpen"
    title="Editar Projeto Social"
    width="600px"
    destroy-on-close
  >
    
    <!-- Formulário -->
    <el-form
      ref="formRef"
      :model="form"
      label-position="top"
      status-icon
    >

      <el-form-item label="Nome" prop="name">
        <el-input v-model="form.name" />
      </el-form-item>

      <el-form-item label="Descrição" prop="description">
        <el-input type="textarea" v-model="form.description" :rows="3" />
      </el-form-item>

      <el-form-item label="Imagem">
        <el-upload
          class="upload-demo"
          action=""
          :auto-upload="false"
          :limit="1"
          list-type="picture-card"
          accept="image/*"
          :file-list="fileList"
          :on-change="handleImageChange"
        >
          <i class="el-icon-plus"></i>
        </el-upload>
      </el-form-item>

      <el-form-item label="Endereço">
        <el-input v-model="form.address" />
      </el-form-item>

      <el-form-item label="Bairro">
        <el-input v-model="form.district" />
      </el-form-item>

      <el-form-item label="Cidade">
        <el-input v-model="form.city" />
      </el-form-item>

      <el-form-item label="Estado (UF)">
        <el-input v-model="form.state" maxlength="2" />
      </el-form-item>

      <el-form-item label="CEP">
        <el-input v-model="form.zipCode" />
      </el-form-item>

      <el-form-item label="Telefone">
        <el-input v-model="form.phone" />
      </el-form-item>

      <el-form-item label="Website">
        <el-input v-model="form.websiteUrl" />
      </el-form-item>

      <el-form-item label="Cor Visual">
        <el-color-picker v-model="form.visualColor" />
      </el-form-item>

      <el-form-item label="Área de Atuação">
        <el-input v-model="form.activityArea" />
      </el-form-item>

      <el-form-item label="Públicos-Alvo">
        <el-select
          v-model="form.targetAudiences"
          multiple
          filterable
          allow-create
        >
          <el-option value="Crianças" label="Crianças" />
          <el-option value="Adolescentes" label="Adolescentes" />
          <el-option value="Adultos" label="Adultos" />
          <el-option value="Idosos" label="Idosos" />
        </el-select>
      </el-form-item>

    </el-form>

    <!-- Botões -->
    <template #footer>
      <el-button @click="isOpen = false">Cancelar</el-button>
      <el-button type="primary" @click="submit">Salvar</el-button>
    </template>

  </el-dialog>
</template>

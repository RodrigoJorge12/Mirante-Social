<script setup lang="ts">
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import { defineProps, defineEmits, computed, ref, watch } from "vue";
import { ElMessage } from "element-plus";
import { PresenterPersonalizedPage } from "./Presenters/PresenterPersonalizedPage";

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  project: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue"]);

// Montagem da imagem
const imageUrl = (path: string | null) =>
  path ? `${import.meta.env.VITE_API_URL}/storage/${path}` : "";

// Fechar modal
function closeModal() {
  emit("update:modelValue", false);
}

// Público-alvo tratado
const audiences = computed(() => {
  if (!props.project) return [];
  try {
    if (typeof props.project.target_audiences === "string") {
      return JSON.parse(props.project.target_audiences);
    }
    return props.project.target_audiences || [];
  } catch {
    return [];
  }
});

// “Ver no mapa” ainda não implementado
function comingSoon() {
  ElMessage.info("Funcionalidade de mapa será implementada em breve!");
}
const personalizedPageUrl = ref<string | null>(null);
// buscar pagina personalizada
watch(
  () => props.project,
  async (newProject) => {
    if (!newProject) return;

    const presenterPersonalizedPage = new PresenterPersonalizedPage();
    const url = await presenterPersonalizedPage.getUrlByProjectId(newProject.id);

    personalizedPageUrl.value = url;
  },
  { immediate: true }
);
</script>

<template>
  <el-dialog
    :model-value="modelValue"
    width="700px"
    @close="closeModal"
    center
  >
    <template #header>
      <h2 style="color:#10B981; margin:0; font-size:24px;">
        {{ project?.name }}
      </h2>
    </template>

    <div v-if="project">
      
      <!-- IMAGEM -->
      <el-image
        :src="imageUrl(project.image_path)"
        fit="cover"
        style="width: 100%; height: 260px; border-radius: 6px; margin-bottom: 15px"
      />

      <!-- DESCRIÇÃO -->
      <h3>Descrição</h3>
      <p style="line-height:1.5; margin-bottom:20px;">
        {{ project.description }}
      </p>

      <!-- LOCALIZAÇÃO -->
      <h3>Localização</h3>
      <p>
        <strong>Endereço:</strong> {{ project.address }} <br>
        <strong>Bairro:</strong> {{ project.district }} <br>
        <strong>Cidade:</strong> {{ project.city }} - {{ project.state }} <br>
        <strong>CEP:</strong> {{ project.zip_code }}
      </p>

      <!-- CONTATO -->
      <h3>Contato</h3>
      <p>
        <strong>Telefone:</strong> {{ project.phone }} <br>
        <strong>Site:</strong>
        <a
          v-if="project.website_url"
          :href="project.website_url"
          target="_blank"
        >
          {{ project.website_url }}
        </a>
        <span v-else>Não informado</span>
      </p>

      <!-- ÁREA DE ATUAÇÃO -->
      <h3>Área de Atuação</h3>
      <el-tag type="success" size="large" round>
        {{ project.activity_area || "Não informado" }}
      </el-tag>

      <!-- PÚBLICO ALVO -->
      <h3 style="margin-top:20px;">Público-Alvo</h3>
      <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:15px;">
        <el-tag
          v-for="(aud, index) in audiences"
          :key="index"
          type="info"
          round
        >
          {{ aud }}
        </el-tag>
      </div>

      <!-- 🔥 NOVO: PÁGINA PERSONALIZADA -->
        <h3>Página Personalizada</h3>
        <div>
        <template v-if="personalizedPageUrl">
            <a
            :href="personalizedPageUrl"
            target="_blank"
            style="color:#10B981; font-weight:bold; text-decoration:underline"
            >
            Acessar página personalizada
            </a>
        </template>

        <template v-else>
            <span style="color:#777;">Não possui página personalizada</span>
        </template>
        </div>

      <!-- 🔥 NOVO: VER NO MAPA -->
      <h3 style="margin-top:20px;">Mapa</h3>
      <MiranteSocialButton @click="comingSoon">
        Ver no mapa
      </MiranteSocialButton>

    </div>

    <template #footer>
      <MiranteSocialButton @click="closeModal">Fechar</MiranteSocialButton>
    </template>
  </el-dialog>
</template>

<style scoped>
h3 {
  margin: 10px 0 5px 0;
  color: #333;
}
</style>

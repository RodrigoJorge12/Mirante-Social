<script setup lang="ts">
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import { defineProps, defineEmits, computed, ref, watch } from "vue";
import { ElMessage } from "element-plus";
import { PresenterPersonalizedPage } from "./Presenters/PresenterPersonalizedPage";
import VerifiedBadge from "./components/VerifiedBadge.vue";
import { PresenterReport } from "./Presenters/PresenterReport";
import { PresenterProjectRating } from "./Presenters/PresenterProjectRating";

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  project: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue", "updated"]);

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
const reportModalVisible = ref(false);
const reportCategory = ref<string>("");
const reportReason = ref<string>("");
const reportSubmitting = ref(false);
const categories = [
  { value: "spam", label: "Spam" },
  { value: "fraude", label: "Fraude" },
  { value: "discurso_de_odio", label: "Discurso de ódio" },
  { value: "violencia", label: "Violência" },
  { value: "enganoso", label: "Enganoso" },
  { value: "outro", label: "Outro" },
];
const ratingPresenter = new PresenterProjectRating();
const myRating = ref<number | null>(null);
const myFeedback = ref<string>("");
const savingRating = ref(false);
const summaryAvg = ref<number>(0);
const summaryCount = ref<number>(0);
async function loadMyRating() {
  if (!props.project) return;
  const res = await ratingPresenter.getMine(props.project.id);
  if (res?.success && res.data) {
    myRating.value = res.data.rating ?? null;
    myFeedback.value = res.data.feedback_text ?? "";
  }
}
async function loadSummary(){
  if (!props.project) return;
  const s = await ratingPresenter.getSummary(props.project.id);
  if (s?.success && s.data){
    summaryAvg.value = Number(s.data.avg || 0);
    summaryCount.value = Number(s.data.count || 0);
  }
}
async function saveRating() {
  if (!props.project) return;
  if (myRating.value == null) {
    ElMessage.error("Selecione uma nota");
    return;
  }
  savingRating.value = true;
  const ok = await ratingPresenter.postRating(props.project.id, myRating.value, myFeedback.value || undefined);
  savingRating.value = false;
  if (ok?.success) {
    ElMessage.success("Avaliação salva");
    await loadSummary();
    emit("updated");
  } else {
    ElMessage.error("Não foi possível salvar");
  }
}
async function submitReport() {
  if (!props.project) return;
  if (!reportCategory.value || !reportReason.value || reportReason.value.length < 10) {
    ElMessage.error("Informe categoria e motivo (mín. 10 caracteres)");
    return;
  }
  reportSubmitting.value = true;
  const presenter = new PresenterReport();
  const ok = await presenter.createReport(props.project.id, reportCategory.value, reportReason.value);
  reportSubmitting.value = false;
  if (ok) {
    ElMessage.success("Denúncia enviada");
    reportModalVisible.value = false;
    reportCategory.value = "";
    reportReason.value = "";
  } else {
    ElMessage.error("Não foi possível enviar a denúncia");
  }
}
// buscar pagina personalizada
watch(
  () => props.project,
  async (newProject) => {
    if (!newProject) return;

    const presenterPersonalizedPage = new PresenterPersonalizedPage();
    const url = await presenterPersonalizedPage.getUrlByProjectId(newProject.id);

    personalizedPageUrl.value = url;
    await loadMyRating();
    await loadSummary();
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
      <h2 style="margin:0; font-size:24px; display:flex; align-items:center; gap:8px;">
        <span>{{ project?.name }}</span>
        <VerifiedBadge :verified="project?.verified" />
      </h2>
    </template>

    <div v-if="project">
      
      <!-- IMAGEM -->
      <el-image
        :src="project.image"
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

      <h3 style="margin-top:20px;">Avaliação</h3>
      <div style="display:flex; align-items:center; gap:8px;">
        <el-rate :model-value="summaryAvg" disabled allow-half />
        <small>({{ summaryCount }})</small>
      </div>

      <div style="margin-top:12px;">
        <h4 style="margin:0 0 6px 0;">Sua avaliação</h4>
        <el-rate v-model="myRating" />
        <el-input type="textarea" v-model="myFeedback" :rows="3" placeholder="Compartilhe um feedback (opcional)" />
        <MiranteSocialButton :disabled="savingRating" style="margin-top:8px;" @click="saveRating">Salvar</MiranteSocialButton>
      </div>

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
      <MiranteSocialButton style="margin-right:8px;" @click="() => (reportModalVisible = true)">Denunciar</MiranteSocialButton>
      <MiranteSocialButton @click="closeModal">Fechar</MiranteSocialButton>
    </template>
  </el-dialog>

  <el-dialog :model-value="reportModalVisible" width="500px" @close="() => (reportModalVisible = false)" center>
    <template #header>
      <h3 style="margin:0;">Denunciar projeto</h3>
    </template>
    <div>
      <el-form label-position="top">
        <el-form-item label="Categoria">
          <el-select v-model="reportCategory" placeholder="Selecione">
            <el-option v-for="c in categories" :key="c.value" :label="c.label" :value="c.value" />
          </el-select>
        </el-form-item>
        <el-form-item label="Motivo">
          <el-input type="textarea" v-model="reportReason" :rows="4" maxlength="1000" show-word-limit />
        </el-form-item>
      </el-form>
    </div>
    <template #footer>
      <MiranteSocialButton :disabled="reportSubmitting" @click="submitReport">Enviar denúncia</MiranteSocialButton>
    </template>
  </el-dialog>
</template>

<style scoped>
h3 {
  margin: 10px 0 5px 0;
  color: #333;
}
</style>

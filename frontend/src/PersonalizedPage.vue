<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { PresenterPersonalizedPage } from "./Presenters/PresenterPersonalizedPage";
import TemplateModel1 from "./TemplateModel1.vue";
import TemplateModel2 from "./TemplateModel2.vue";

const route = useRoute();
const presenter = new PresenterPersonalizedPage();

// dados reativos
const project = ref<any | null>(null);
const personalizedPageData = ref<any | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

onMounted(async () => {
  try {
    const slug = route.params.slug as string;

    const result = await presenter.getPageData(slug);

    project.value = result.data.project;
    personalizedPageData.value = result.data.page;
  } catch (err: any) {
    console.error(err);
    error.value = "Não foi possível carregar a página personalizada.";
  } finally {
    loading.value = false;
  }
});

// decide qual template usar com base no campo "template"
const templateComponent = computed(() => {
  if (!personalizedPageData.value) return null;

  const template = personalizedPageData.value.template;

  if (template === 1) {
    return TemplateModel1;
  }

  return TemplateModel2;
});
</script>

<template>
  <!-- LOADING -->
  <div v-if="loading" class="pp-state pp-loading">
    <div class="pp-spinner"></div>
    <p class="pp-state-text">Carregando página...</p>
  </div>

  <!-- ERRO -->
  <div v-else-if="error" class="pp-state pp-error">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#059669" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p class="pp-state-text">{{ error }}</p>
    <p class="pp-state-hint">Tente novamente em instantes.</p>
    <a href="https://mirantesocial.com.br" class="pp-back-link">Voltar ao início</a>
  </div>

  <!-- TEMPLATE DINÂMICO -->
  <div
    v-else-if="project && personalizedPageData && templateComponent"
    class="page-wrapper"
    :style="{ '--theme-color': project.visual_color }"
  >
    <component
      :is="templateComponent"
      :project="project"
      :personalized-page-data="personalizedPageData"
    />
  </div>

  <!-- NÃO ENCONTRADO -->
  <div v-else class="pp-state pp-error">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#059669" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p class="pp-state-text">Página personalizada não encontrada.</p>
    <a href="https://mirantesocial.com.br" class="pp-back-link">Voltar ao início</a>
  </div>
</template>

<style scoped>
.page-wrapper {
  width: 100%;
  min-height: 100vh;
  font-family: 'Nunito', sans-serif;
}

/* Estados genéricos */
.pp-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  font-family: 'Nunito', sans-serif;
  gap: 1rem;
  padding: 2rem;
}

.pp-loading {
  background: #F0FDF4;
}

.pp-error {
  background: #F0FDF4;
}

.pp-back-link {
  margin-top: 0.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  color: #059669;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.pp-state-text {
  font-size: 1.2rem;
  font-weight: 700;
  color: #064E3B;
  margin: 0;
  text-align: center;
}

.pp-state-hint {
  font-size: 1rem;
  font-weight: 600;
  color: #6B7280;
  margin: 0;
  text-align: center;
}

.pp-error-icon {
  font-size: 3rem;
}

/* Spinner */
.pp-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #A7F3D0;
  border-top-color: #059669;
  border-radius: 50%;
  animation: pp-spin 0.8s linear infinite;
}

@keyframes pp-spin {
  to { transform: rotate(360deg); }
}
</style>

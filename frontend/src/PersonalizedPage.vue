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
  <div v-if="loading" class="loading">
    Carregando página personalizada...
  </div>

  <div v-else-if="error" class="loading">
    {{ error }}
  </div>

  <div
    v-else-if="project && personalizedPageData && templateComponent"
    class="page-wrapper"
    :style="{ '--theme-color': project.color }"
  >
    <!-- Template dinâmico -->
    <component
      :is="templateComponent"
      :project="project"
      :personalized-page-data="personalizedPageData"
    />
  </div>

  <div v-else class="loading">
    Página personalizada não encontrada.
  </div>
</template>

<style scoped>
.page-wrapper {
  padding: 25px;
}

.loading {
  text-align: center;
  padding-top: 120px;
  font-size: 20px;
}
</style>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";
import ProjectDetailsModal from "@/ProjectDetailsModal.vue";
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import VerifiedBadge from "./components/VerifiedBadge.vue";

const presenter = new PresenterSocialProject();
const projects = ref<any[]>([]);
const search = ref("");

onMounted(async () => {
  const result = await presenter.GetAllProjects();
  projects.value = result.data ?? [];
});


// 🔥 FILTRO PRINCIPAL
const filteredProjects = computed(() => {
  if (!search.value.trim()) return projects.value;

  const term = search.value.toLowerCase();

  return projects.value.filter((p) => {
    const name = p.name?.toLowerCase() || "";
    const desc = p.description?.toLowerCase() || "";
    const area = p.activity_area?.toLowerCase() || "";
    const city = p.city?.toLowerCase() || ""; 
    const state = p.state?.toLowerCase() || "";
    const district = p.district?.toLowerCase() || "";

    // público-alvo pode ser array OU string JSON
    let audiences: string[] = [];
    try {
      if (typeof p.target_audiences === "string") {
        audiences = JSON.parse(p.target_audiences || "[]");
      } else if (Array.isArray(p.target_audiences)) {
        audiences = p.target_audiences;
      }
    } catch {
      audiences = [];
    }

    const audiencesText = audiences.join(" ").toLowerCase();

    return (
      name.includes(term) ||
      desc.includes(term) ||
      area.includes(term) ||
      city.includes(term) ||
      state.includes(term) ||
      district.includes(term) ||
      audiencesText.includes(term)
    );
  });
});

//modal de detalhes
const detailsVisible = ref(false);
const selectedProject = ref<any | null>(null);

function openDetails(project: any) {
  selectedProject.value = project;
  detailsVisible.value = true;
}
</script>

<template>
  <div class="home-root">
    <!-- Busca -->
    <div class="search-box">
      <el-input
        v-model="search"
        placeholder="Buscar projetos por nome, descrição, área, cidade..."
        size="large"
        clearable
      />
    </div>

    <!-- Carrossel -->
    <el-carousel
      v-if="filteredProjects.length"
      height="650px"
      type="card"
      indicator-position="outside"
      autoplay
    >
      <el-carousel-item
        v-for="project in filteredProjects"
        :key="project.id"
        class="item"
      >
        <div class="slide-wrapper">
          <el-card class="card" shadow="always">
            <!-- Imagem com altura fixa -->
            <el-image
              :src="project.image"
              fit="cover"
              class="img"
            />

            <!-- Conteúdo -->
            <div class="content-area">
              <h2 class="title">
                {{ project.name }}
                <VerifiedBadge :verified="project.verified" />
              </h2>
              <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <el-rate :model-value="Number(project.rating_avg || 0)" disabled allow-half />
                <small>({{ project.rating_count || 0 }})</small>
              </div>

              <!-- SÓ a descrição tem scroll -->
              <div class="desc-box">
                <p class="desc">
                  {{ project.description }}
                </p>
              </div>

              <el-tag type="success" class="tag">
                {{ project.activity_area || "Área não informada" }}
              </el-tag>
              <MiranteSocialButton
                type="primary"
                class="btn"
                @click="openDetails(project)"
              >
                Ver detalhes
              </MiranteSocialButton>
            </div>
          </el-card>
        </div>
      </el-carousel-item>
    </el-carousel>

    <p v-else class="no-results">Nenhum projeto encontrado.</p>
  </div>
  <ProjectDetailsModal
    v-model="detailsVisible"
    :project="selectedProject"
    @updated="async () => { const result = await presenter.GetAllProjects(); projects.value = result.data ?? projects.value; }"
  />
</template>


<style scoped>
.home-root {
  width: 100vw;
  min-height: 100vh;
  padding-top: 100px;        /* espaço pro header fixo */
  overflow-x: hidden;
}

/* Busca */
.search-box {
  width: 90vw;
  max-width: 900px;
  margin: 0 auto 30px auto;
}

.no-results {
  text-align: center;
  color: #777;
  margin-top: 40px;
  font-size: 18px;
}

/* Centraliza o item do carrossel */
.item {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Wrapper ocupa o slide inteiro */
.slide-wrapper {
  width: 90%;
  max-width: 1400px;
  height: 100%;           /* 100% dos 650px do carrossel */
  display: flex;
}

/* Card ocupa tudo dentro do wrapper */
.card {
  flex: 1;
  height: 100%;           /* ESSENCIAL pra % funcionar dentro */
  display: flex;
  flex-direction: column;
}

/* Imagem com altura fixa em px (aqui não tem milagre) */
.img {
  width: 100%;
  height: 260px;          /* sempre o mesmo tamanho */
  object-fit: cover;
  border-radius: 6px 6px 0 0;
  flex-shrink: 0;
}

/* Área de conteúdo ocupa o resto */
.content-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 16px;
  overflow: hidden;
}

.title {
  font-size: 24px;
  margin-bottom: 8px;
}

/* Caixa da descrição – tem scroll se passar do espaço */
.desc-box {
  flex: 1;
  overflow-y: auto;
  padding-right: 4px;
  max-height: 5em;        /* = ~2 linhas */
}

/* Texto da descrição */
.desc {
  line-height: 1.4;
  color: #555;
  white-space: pre-wrap;
}

/* Tag e botão sempre visíveis embaixo */
.tag {
  width: max-content;
  margin: 10px 0;
}

.btn {
  width: max-content;
}
</style>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";

const presenter = new PresenterSocialProject();
const projects = ref<any[]>([]);
const search = ref("");

onMounted(async () => {
  const result = await presenter.GetAllProjects();
  projects.value = result.data ?? [];
});

// Monta URL da imagem
const imageUrl = (path: string | null) =>
  path ? `${import.meta.env.VITE_API_URL}/storage/${path}` : "";

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
</script>

<template>
  <div class="home-root">

    <!-- 🔍 CAMPO DE BUSCA -->
    <div class="search-box">
      <el-input
        v-model="search"
        placeholder="Buscar projetos por nome, descrição, área ou público-alvo..."
        size="large"
        clearable
      />
    </div>

    <!-- CARROSSEL -->
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
        <el-card class="card" shadow="always">

          <!-- IMAGEM -->
          <el-image
            :src="imageUrl(project.image_path)"
            fit="cover"
            class="img"
          />

          <!-- CONTEÚDO -->
          <div class="content">
            <h2 class="title">{{ project.name }}</h2>

            <div class="scroll">
              <p class="desc">{{ project.description }}</p>
            </div>

            <el-tag type="success" class="tag">
              {{ project.activity_area || "Área não informada" }}
            </el-tag>

            <el-button type="primary" class="btn">
              Ver detalhes
            </el-button>
          </div>

        </el-card>
      </el-carousel-item>
    </el-carousel>

    <!-- Caso não encontre nada -->
    <p v-else class="no-results">Nenhum projeto encontrado.</p>

  </div>
</template>

<style scoped>
.home-root {
  width: 100vw;
  min-height: 100vh;
  padding-top: 100px;
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

/* Carrossel centralizado */
.item {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Card grande e responsivo */
.card {
  width: 85vw;
  max-width: 1400px;
  height: 90%;
  display: flex;
  flex-direction: column;
}

/* Imagem */
.img {
  width: 100%;
  height: 45%;
  object-fit: cover;
  border-radius: 6px;
}

/* Conteúdo */
.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 16px;
}

.title {
  font-size: 26px;
  margin-bottom: 10px;
}

.scroll {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 12px;
}

.desc {
  color: #555;
  line-height: 1.4;
}

.tag {
  width: max-content;
  margin-bottom: 12px;
}

.btn {
  width: max-content;
}
</style>

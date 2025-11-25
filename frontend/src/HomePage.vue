<script setup lang="ts">
import { ref, onMounted } from "vue";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";

const presenter = new PresenterSocialProject();
const projects = ref<any[]>([]);

onMounted(async () => {
  const result = await presenter.GetAllProjects();
  projects.value = result.data ?? [];
});

const imageUrl = (path: string | null) =>
  path ? `${import.meta.env.VITE_API_URL}/storage/${path}` : "";
</script>

<template>
  <div class="home-root">

    <el-carousel
      v-if="projects.length"
      height="650px"
      type="card"
      indicator-position="outside"
      autoplay
    >
      <el-carousel-item
        v-for="project in projects"
        :key="project.id"
        class="item"
      >
        <el-card class="card" shadow="always">

          <!-- Imagem -->
          <el-image
            :src="imageUrl(project.image_path)"
            fit="cover"
            class="img"
          />

          <!-- Conteúdo -->
          <div class="content">

            <h2 class="title">{{ project.name }}</h2>

            <div class="scroll">
              <p class="desc">
                {{ project.description }}
              </p>
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

  </div>
</template>

<style scoped>
/* A raiz ocupa 100% da tela REAL */
.home-root {
  width: 100vw;
  min-height: 100vh;
  padding-top: 90px; /* espaço pro header fixo */
  overflow-x: hidden;
}

/* O item do carrossel sempre centraliza */
.item {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* CARD RESPONSIVO E GRANDE SEMPRE */
.card {
  width: 85vw;             /* ocupa 85% da tela */
  max-width: 1400px;       /* limite em telas gigantes */
  height: 90%;             /* quase toda a altura */
  display: flex;
  flex-direction: column;
}

/* Imagem ocupa metade do card */
.img {
  width: 100%;
  height: 45%;
  object-fit: cover;
  border-radius: 6px;
}

/* Conteúdo */
.content {
  flex: 1;
  padding: 16px;
  display: flex;
  flex-direction: column;
}

/* Título */
.title {
  font-size: 26px;
  font-weight: bold;
  margin-bottom: 10px;
}

/* Scroll da descrição */
.scroll {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 12px;
}

/* Texto */
.desc {
  line-height: 1.4;
  color: #555;
}

/* Tag */
.tag {
  margin-bottom: 12px;
  width: max-content;
}

/* Botão */
.btn {
  width: max-content;
}
</style>

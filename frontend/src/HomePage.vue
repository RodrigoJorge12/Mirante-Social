<script setup lang="ts">
import { ref, onMounted } from "vue";
import Header from "@/Header.vue";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";

const presenter = new PresenterSocialProject();
const projects = ref<any[]>([]);

onMounted(async () => {
  const result = await presenter.GetAllProjects();
  projects.value = result.data ?? [];
});

const imageUrl = (path: string | null) => {
  if (!path) return "";
  return `${import.meta.env.VITE_API_URL}/storage/${path}`;
};
</script>

<template>
  <Header />

  <div class="page">

    <el-carousel
      height="520px"
      indicator-position="outside"
      arrow="always"
      autoplay
    >
      <el-carousel-item
        v-for="project in projects"
        :key="project.id"
      >
        <div class="carousel-center">

          <el-card class="card" shadow="hover" body-style="padding: 0">
            
            <el-image
              :src="imageUrl(project.image_path)"
              fit="cover"
              class="img"
            >
              <template #error>
                <div class="img-error">Imagem não disponível</div>
              </template>
            </el-image>

            <div class="content">

              <h2 class="title">{{ project.name }}</h2>

              <!-- Descrição com scroll -->
              <div class="scroll">
                <p>{{ project.description }}</p>
              </div>

              <el-tag
                v-if="project.activity_area"
                type="success"
                round
                class="tag"
              >
                {{ project.activity_area }}
              </el-tag>

              <el-button type="primary" round class="btn">
                Ver detalhes
              </el-button>

            </div>

          </el-card>

        </div>
      </el-carousel-item>
    </el-carousel>

  </div>
</template>

<style scoped>
.page {
  width: 100%;
  padding: 10px;
}

/* Centraliza o card dentro do slide */
.carousel-center {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}

/* 🔥 Card gigante ocupando praticamente a tela toda */
.card {
  width: 92%;        /* ocupa quase a tela inteira */
  max-width: 1500px; /* limite para monitores muito grandes */
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* Imagem ocupa ~45% da altura */
.img {
  width: 100%;
  height: 45%;
  object-fit: cover;
}

.img-error {
  width: 100%;
  height: 45%;
  background: #eee;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Conteúdo */
.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 14px;
}

/* Título */
.title {
  margin: 0 0 8px 0;
}

/* Área scrollável */
.scroll {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 10px;
}

/* Tag */
.tag {
  margin-bottom: 10px;
}

/* Botão */
.btn {
  width: fit-content;
}
</style>

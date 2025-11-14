<script setup lang="ts">
import { ref } from "vue";

const props = defineProps<{ project: any }>();

const contentRef = ref<HTMLElement | null>(null);

function scrollToContent() {
  contentRef.value?.scrollIntoView({ behavior: "smooth" });
}
</script>

<template>
  <div class="page">

    <!-- HERO ANIMADO -->
    <section class="hero" :style="{ '--theme': props.project.color }">
      <div class="hero-content">
        <h1 class="title">{{ props.project.name }}</h1>
        <p class="subtitle">Conheça o projeto {{ props.project.name }}</p>

        <el-button
          type="success"
          round
          size="large"
          class="cta-btn"
          @click="scrollToContent"
        >
          Saiba mais
        </el-button>
      </div>
    </section>

    <div ref="contentRef"></div>

    <!-- GRID DO CONTEÚDO -->
    <el-row :gutter="20" class="content">
      <el-col :xs="24" :md="14">
        <el-card class="fade-card" shadow="hover">
          <h2>Sobre o Projeto</h2>
          <p>{{ props.project.description }}</p>
        </el-card>
      </el-col>

      <el-col :xs="24" :md="10" class="right-col">

        <el-card class="info-card fade-card" shadow="never">
          <h3>Localização</h3>
          <p>{{ props.project.address }}</p>
          <p>
            {{ props.project.district }} —
            {{ props.project.city }} / {{ props.project.state }}
          </p>
          <p>CEP: {{ props.project.zip }}</p>
        </el-card>

        <el-card class="info-card fade-card" shadow="never">
          <h3>Contato</h3>
          <p><b>Email:</b> {{ props.project.email }}</p>
          <p><b>Telefone:</b> {{ props.project.phone }}</p>
        </el-card>

        <el-card class="info-card fade-card" shadow="never">
          <h3>Site Oficial</h3>
          <el-link :href="props.project.site" target="_blank" :style="{ color: props.project.color }">
            Acessar site
          </el-link>
        </el-card>

        <el-card class="info-card fade-card" shadow="never">
          <h3>Necessidades</h3>
          <p>{{ props.project.needs }}</p>
        </el-card>

      </el-col>
    </el-row>

  </div>
</template>

<style scoped>
.page {
  width: 100%;
  padding-bottom: 3rem;
}

/* HERO */
.hero {
  width: 100%;
  padding: 4rem 1rem;
  background: linear-gradient(135deg, var(--theme) 0%, #ffffff 80%);
  border-radius: 1rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: center;
  animation: fadeIn 1s ease;
}

.hero-content {
  max-width: 850px;
  text-align: center;
}

.title {
  font-size: 2.6rem;
  font-weight: 900;
  margin-bottom: 0.5rem;
  animation: slideDown 0.8s ease;
}

.subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  animation: fadeIn 1.4s ease;
}

.cta-btn {
  margin-top: 1rem;
  animation: fadeIn 1.8s ease;
}

/* Conteúdo */
.content {
  padding: 0 1rem;
}

.right-col .info-card {
  margin-bottom: 1rem;
}

/* Animações */
.fade-card {
  animation: fadeUp 0.8s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideDown {
  from { transform: translateY(-12px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>

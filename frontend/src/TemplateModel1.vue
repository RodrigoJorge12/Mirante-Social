<script setup lang="ts">
import { ref } from "vue";
import VerifiedBadge from "./components/VerifiedBadge.vue";
const props = defineProps<{ project: any }>();
const contentRef = ref<HTMLElement | null>(null);
function scrollToContent() { contentRef.value?.scrollIntoView({ behavior: "smooth" }); }
</script>

<template>
  <div class="page" :style="{ '--accent': props.project.color }">
    <header class="header">
      <div class="brand">
        <span class="dot" :style="{ background: 'var(--accent)' }"></span>
        <span class="name">{{ props.project.name }}</span>
        <VerifiedBadge :verified="props.project.verified" />
      </div>
    </header>

    <div ref="contentRef"></div>

    <section class="content">
      <el-card class="card accent">
        <h2 class="title">Sobre</h2>
        <p class="text">{{ props.project.description }}</p>
      </el-card>
      <div style="display:flex; align-items:center; gap:8px; margin-top:8px;">
        <el-rate :model-value="Number(props.project.rating_avg || 0)" disabled allow-half />
        <small>({{ props.project.rating_count || 0 }})</small>
      </div>
      <el-card class="card accent">
        <h2 class="title">Necessidades</h2>
        <p class="text">{{ props.project.needs }}</p>
      </el-card>
    </section>

    <section class="info">
      <el-card class="card">
        <h3 class="subtitle">Contato</h3>
        <div class="row"><span class="label">Email</span><span class="value">{{ props.project.email }}</span></div>
        <div class="row"><span class="label">Telefone</span><span class="value">{{ props.project.phone }}</span></div>
      </el-card>

      <el-card class="card">
        <h3 class="subtitle">Localização</h3>
        <div class="row"><span class="label">Endereço</span><span class="value">{{ props.project.address }}</span></div>
        <div class="row"><span class="label">Bairro</span><span class="value">{{ props.project.district }}</span></div>
        <div class="row"><span class="label">Cidade/UF</span><span class="value">{{ props.project.city }} / {{ props.project.state }}</span></div>
        <div class="row"><span class="label">CEP</span><span class="value">{{ props.project.zip }}</span></div>
      </el-card>
    </section>
  </div>
</template>

<style scoped>
.page { padding: 72px 24px 24px; }
.header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 8px 0; }
.brand { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.name { font-size: 18px; }
.content { display: block; margin-top: 24px; }
.content .card + .card { margin-top: 16px; }
.info { display: flex; flex-direction: column; gap: 16px; margin-top: 24px; }
.card { border-radius: 16px; padding: 12px 16px; box-shadow: 0 6px 20px rgba(0,0,0,.06); }
.accent { border-left: 4px solid var(--accent); }
.title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.subtitle { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.text { line-height: 1.65; }
.row { display: flex; align-items: center; justify-content: space-between; }
.label { opacity: .7; }
.value { font-weight: 600; }
</style>

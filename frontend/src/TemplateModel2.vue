<script setup lang="ts">
const props = defineProps<{ project: any }>();
import VerifiedBadge from "./components/VerifiedBadge.vue";
</script>

<template>
  <div class="page">
    <section class="mast" :style="{ '--accent': props.project.color }">
      <div class="mast-inner">
        <h1 class="title">
          {{ props.project.name }}
          <VerifiedBadge :verified="props.project.verified" />
        </h1>
        <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
          <el-rate :model-value="Number(props.project.rating_avg || 0)" disabled allow-half />
          <small>({{ props.project.rating_count || 0 }})</small>
        </div>
        <p class="lead">{{ props.project.description }}</p>
        <div class="cta">
          <el-button type="success" round>Entrar em contato</el-button>
          <el-link :href="props.project.site" target="_blank">Site oficial</el-link>
        </div>
      </div>
    </section>
    <div class="layout">
      <aside class="sidebar">
        <el-card class="side-card">
          <div class="side-title">Contato</div>
          <div class="side-item">Email: {{ props.project.email }}</div>
          <div class="side-item">Telefone: {{ props.project.phone }}</div>
          <el-divider></el-divider>
          <el-link :href="props.project.site" target="_blank">Site oficial</el-link>
        </el-card>
        <el-card class="side-card">
          <div class="side-title">Localização</div>
          <div class="side-item">{{ props.project.address }}</div>
          <div class="side-item">{{ props.project.district }} — {{ props.project.city }} / {{ props.project.state }}</div>
          <div class="side-item">CEP: {{ props.project.zip }}</div>
        </el-card>
      </aside>
      <main class="content">
        <el-card class="content-card">
          <div class="content-title">Sobre</div>
          <p class="content-text">{{ props.project.description }}</p>
        </el-card>
        <el-card class="content-card">
          <div class="content-title">Necessidades</div>
          <p class="content-text">{{ props.project.needs }}</p>
        </el-card>
      </main>
    </div>
  </div>
</template>

<style scoped>
.page { padding: 72px 24px 24px; }
.mast { border-radius: 20px; padding: 40px 24px; position: relative; overflow: hidden; }
.mast::before { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, var(--accent) 0%, transparent 70%); opacity: .18; }
.mast-inner { max-width: 1100px; margin: 0 auto; display: grid; gap: 10px; }
.title { font-size: 30px; font-weight: 800; }
.lead { font-size: 18px; }
.cta { display: flex; align-items: center; gap: 16px; margin-top: 8px; }
.layout { display: grid; grid-template-columns: 320px 1fr; gap: 24px; margin-top: 28px; }
.sidebar { display: grid; gap: 16px; }
.side-card { border-radius: 16px; padding: 12px 16px; box-shadow: 0 8px 30px rgba(0,0,0,.08); }
.side-title { font-weight: 700; }
.side-item { line-height: 1.65; }
.content { display: grid; gap: 16px; }
.content-card { border-radius: 16px; padding: 12px 16px; box-shadow: 0 6px 20px rgba(0,0,0,.06); }
.content-title { font-weight: 700; }
.content-text { line-height: 1.65; }
</style>

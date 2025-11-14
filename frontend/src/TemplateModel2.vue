<script setup lang="ts">
defineProps<{ project: any }>();
</script>

<template>
  <div class="page">

    <!-- Cabeçalho institucional -->
    <el-card class="header" shadow="always">
      <h1 :style="{ color: project.color }">
        {{ project.name }}
      </h1>
      <p class="desc">
        {{ project.description }}
      </p>
    </el-card>

    <!-- Sessão institucional -->
    <el-row :gutter="20" class="grid">

      <el-col :xs="24" :md="8">
        <el-card class="section-card" shadow="hover">
          <h3>Localização</h3>
          <p>{{ project.address }}</p>
          <p>{{ project.district }} — {{ project.city }} / {{ project.state }}</p>
          <p>CEP: {{ project.zip }}</p>
        </el-card>
      </el-col>

      <el-col :xs="24" :md="8">
        <el-card class="section-card" shadow="hover">
          <h3>Contato</h3>
          <p><b>Email:</b> {{ project.email }}</p>
          <p><b>Telefone:</b> {{ project.phone }}</p>
        </el-card>
      </el-col>

      <el-col :xs="24" :md="8">
        <el-card class="section-card" shadow="hover">
          <h3>Site Oficial</h3>
          <el-link
            :href="project.site"
            target="_blank"
            :style="{ color: project.color }"
          >
            Acessar site
          </el-link>
        </el-card>
      </el-col>

      <el-col :xs="24">
        <el-card class="section-card" shadow="hover">
          <h3>Necessidades do Projeto</h3>
          <p>{{ project.needs }}</p>
        </el-card>
      </el-col>

    </el-row>
  </div>
</template>

<style scoped>
.page {
  width: 100%;
  padding: 2rem 1rem;
}

.header {
  margin-bottom: 2rem;
  padding: 2rem;
}

.desc {
  margin-top: 0.5rem;
  font-size: 1.1rem;
  color: #444;
}

.grid {
  width: 100%;
}

.section-card {
  min-height: 10rem;
  padding: 1.5rem;
}
</style>

<script setup lang="ts">
import TemplateModel2 from "./TemplateModel2.vue";

const project = {
  name: "Instituto Viver Bem",
  description:
    "Programa de saúde preventiva e bem-estar com atividades físicas e atendimento básico.",
  color: "#2563EB",
  address: "Av. Paulista, 1000",
  district: "Bela Vista",
  city: "São Paulo",
  state: "SP",
  zip: "01310-100",
  email: "info@viverbem.org",
  phone: "(11) 98888-8888",
  site: "https://viverbem.org",
  needs:
    "Equipamentos médicos, profissionais voluntários (educação física e enfermagem) e kits de higiene.",
  verified: true,
};
</script>

<template>
  <div style="padding: 24px;">
    <TemplateModel2 :project="project" />
  </div>
</template>

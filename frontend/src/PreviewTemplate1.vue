<script setup lang="ts">
import TemplateModel1 from "./TemplateModel1.vue";

const project = {
  name: "Projeto Esperança",
  description:
    "Iniciativa comunitária focada em educação e apoio social para famílias em situação de vulnerabilidade.",
  color: "#10B981",
  address: "Rua das Flores, 123",
  district: "Centro",
  city: "Rio de Janeiro",
  state: "RJ",
  zip: "20000-000",
  email: "contato@projetoesperanca.org",
  phone: "(21) 99999-9999",
  site: "https://projetoesperanca.org",
  needs:
    "Doações de materiais escolares, voluntários para reforço escolar e apoio psicológico.",
  verified: true,
};
</script>

<template>
  <div style="padding: 24px;">
    <TemplateModel1 :project="project" />
  </div>
  </template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { ElMessageBox, ElMessage } from "element-plus";
import { useRouter } from "vue-router";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";
import EditSocialProject from "@/EditSocialProject.vue";

const router = useRouter();
const presenter = new PresenterSocialProject();

const loading = ref(true);
const projects = ref<any[]>([]);

const showModal = ref(false);
const editId = ref<number | null>(null);
const editHasPage = ref(false);

function editProject(project: any) {
  editId.value = project.id;
  editHasPage.value = !!project.has_personalized_page;
  showModal.value = true;
}

const reloadProjects = async () => {
  const response = await presenter.GetProjectsByLoggedUser();
  if (response?.success) projects.value = response.data;
};

const imageUrl = (path: string | null) => {
  if (!path) return null;
  return `${import.meta.env.VITE_API_URL}/storage/${path}`;
};

onMounted(async () => {
  const response = await presenter.GetProjectsByLoggedUser();
  if (response?.success) projects.value = response.data;
  loading.value = false;
});

async function deleteProject(id: number) {
  try {
    await ElMessageBox.confirm(
      "Tem certeza que deseja apagar este projeto? Essa ação não pode ser desfeita.",
      "Atenção",
      {
        type: "warning",
        confirmButtonText: "Apagar",
        cancelButtonText: "Cancelar",
      }
    );
    const result = await presenter.DeleteProject(id);
    if (result?.success) {
      projects.value = projects.value.filter(p => p.id !== id);
      ElMessage.success("Projeto removido com sucesso.");
    } else {
      ElMessage.error("Não foi possível remover o projeto. Tente novamente.");
    }
  } catch {}
}
</script>

<template>
  <div class="page">
    <EditSocialProject
      v-model="showModal"
      :project-id="editId"
      :has-personalized-page="editHasPage"
      @updated="reloadProjects"
    />

    <!-- Cabeçalho -->
    <div class="page-header">
      <div class="page-header-text">
        <h1 class="page-title">Meus Projetos</h1>
        <p class="page-subtitle">Gerencie e acompanhe os projetos sociais cadastrados</p>
      </div>
      <button class="btn-new" @click="router.push('/createSocialProject')">
        ＋ Criar Novo Projeto
      </button>
    </div>

    <!-- Loading -->
    <el-skeleton v-if="loading" animated :count="3" />

    <!-- Estado vazio -->
    <div v-else-if="projects.length === 0" class="empty-state">
      <img src="/MiranteSocial.png" alt="Mirante Social" class="empty-logo" />
      <p class="empty-title">Nenhum projeto cadastrado ainda</p>
      <p class="empty-sub">Clique em "Criar Novo Projeto" para começar!</p>
    </div>

    <!-- Cards -->
    <div v-else class="cards-grid">
      <div
        v-for="project in projects"
        :key="project.id"
        class="myp-card"
      >
        <!-- Imagem topo -->
        <img
          v-if="imageUrl(project.image_path)"
          :src="imageUrl(project.image_path) as string"
          class="myp-card-top"
        />
        <div v-else class="myp-card-top myp-card-no-image">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>

        <!-- Corpo do card -->
        <div class="myp-card-body">
          <div class="myp-card-top-row">
            <span class="myp-badge">{{ project.activity_area || "Área não informada" }}</span>
            <span v-if="project.has_personalized_page" class="myp-badge-page">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
              Página ativa
            </span>
          </div>
          <p class="myp-card-name">{{ project.name }}</p>

          <div class="myp-card-actions">
            <button class="btn-edit" @click="editProject(project)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Editar
            </button>
            <button class="btn-danger" @click="deleteProject(project.id)">Apagar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page {
  padding: 100px 32px 60px;
  min-height: 100vh;
  background: #F0FDF4;
  font-family: 'Nunito', sans-serif;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  max-width: 1200px;
  margin: 0 auto 36px;
}

.page-title {
  font-size: 28px;
  font-weight: 900;
  color: #022C22;
  margin: 0 0 4px;
}

.page-subtitle {
  font-size: 15px;
  color: #065F46;
  margin: 0;
}

.btn-new {
  background: #059669;
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 24px;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(5, 150, 105, 0.30);
  transition: background 0.2s, transform 0.15s;
  font-family: 'Nunito', sans-serif;
}

.btn-new:hover {
  background: #047857;
  transform: translateY(-1px);
}

.empty-logo {
  height: 72px;
  width: auto;
  border-radius: 14px;
  opacity: 0.5;
  margin-bottom: 8px;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 900px) {
  .cards-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 580px) {
  .cards-grid { grid-template-columns: 1fr; }
}

.myp-card {
  background: #fff;
  border: 2px solid #F0FDF4;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(16, 185, 129, 0.08);
  transition: box-shadow 0.2s, transform 0.2s;
  display: flex;
  flex-direction: column;
}

.myp-card:hover {
  box-shadow: 0 8px 32px rgba(16, 185, 129, 0.16);
  transform: translateY(-2px);
}

.myp-card-top {
  width: 100%;
  height: 116px;
  object-fit: cover;
  display: block;
}

.myp-card-no-image {
  background: #D1FAE5;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #059669;
}

.myp-card-body {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}

.myp-card-top-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.myp-badge {
  display: inline-block;
  background: #D1FAE5;
  color: #065F46;
  font-size: 12px;
  font-weight: 800;
  border-radius: 20px;
  padding: 3px 10px;
}

.myp-badge-page {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #EFF6FF;
  color: #2563EB;
  font-size: 11px;
  font-weight: 800;
  border-radius: 20px;
  padding: 3px 9px;
}

.myp-card-name {
  font-size: 15px;
  font-weight: 800;
  color: #022C22;
  margin: 0;
  line-height: 1.4;
  flex: 1;
}

.myp-card-actions {
  display: flex;
  gap: 8px;
  margin-top: auto;
}

.btn-edit {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 9px;
  border-radius: 9px;
  border: none;
  background: #F0FDF4;
  color: #065F46;
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  transition: background 0.2s, color 0.2s;
}

.btn-edit:hover {
  background: #059669;
  color: #fff;
}

.btn-danger {
  padding: 9px 14px;
  border-radius: 9px;
  background: rgba(239, 68, 68, 0.08);
  color: #DC2626;
  font-size: 13px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  transition: background 0.2s;
}

.btn-danger:hover {
  background: rgba(239, 68, 68, 0.18);
}

.empty-state {
  text-align: center;
  padding: 80px 24px;
}

.empty-title {
  font-size: 20px;
  font-weight: 800;
  color: #022C22;
  margin: 0 0 8px;
}

.empty-sub {
  font-size: 15px;
  color: #065F46;
  margin: 0;
}
</style>

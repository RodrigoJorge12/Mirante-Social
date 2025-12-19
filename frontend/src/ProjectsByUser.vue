<script setup lang="ts">
import { ref, onMounted } from "vue";
import { ElMessageBox, ElMessage } from "element-plus";
import { useRouter } from "vue-router";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject"; // AJUSTADO
import EditSocialProject from "@/EditSocialProject.vue";
import VerifiedBadge from "@/components/VerifiedBadge.vue";

const router = useRouter();
const presenter = new PresenterSocialProject(); // AJUSTADO

const loading = ref(true);
const projects = ref<any[]>([]);

const showModal = ref(false);
const editId = ref<number | null>(null);

const sendingVerification = ref(false);

function editProject(id: number) {
  editId.value = id;
  showModal.value = true;
}

const reloadProjects = async () => {
  const response = await presenter.GetProjectsByLoggedUser();
  projects.value = response.data;
};



onMounted(async () => {
  const response = await presenter.GetProjectsByLoggedUser(); // AJUSTADO
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
      ElMessage.success("Projeto apagado com sucesso!");
    } else {
      ElMessage.error("Erro ao apagar projeto.");
    }
  } catch {}
}

</script>

<template>
  <div class="page">
    <EditSocialProject
        v-model="showModal"
        :project-id="editId"
        @updated="reloadProjects"
    />

    <!-- Loading -->
    <el-skeleton v-if="loading" animated :count="3" />

    <!-- Cards -->
    <div v-else class="cards-container">

      <el-card
        v-for="project in projects"
        :key="project.id"
        shadow="hover"
        class="card"
      >
        <!-- Imagem -->
        <img
          v-if="project.image"
          :src="project.image"
          class="image"
        />

        <div v-else class="no-image">Sem imagem</div>

        <h3 class="title">
          {{ project.name }}
          <VerifiedBadge :verified="project.verified" />
        </h3>

        <!-- Conteúdo com scroll -->
        <div class="scroll">
          <p class="description">{{ project.description }}</p>
        </div>
          <el-tag
            size="small"
            type="info"
            effect="dark"
            style="margin-top: 6px"
          >
            {{ project.activity_area || "Área não informada" }}
          </el-tag>

        

        <div class="actions">
          <el-button type="primary" size="small" @click="editProject(project.id)">
            Editar
          </el-button>

          <el-button type="danger" size="small" @click="deleteProject(project.id)">
            Apagar
          </el-button>
          <template v-if="!project.verified">
            <el-tag type="info" size="small">Adicione telefone e e-mail</el-tag>
          </template>
          <el-tag v-else type="success" size="small">Verificado</el-tag>
        </div>
      </el-card>

    </div>
  </div>

  

</template>

<style scoped>
.page {
  padding: 20px;
}
.description{
    max-height: 5em;
}
/* CONTÊINER FLEX — resolve o problema dos cards apertados */
.cards-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
}

/* CARD COM TAMANHO IDEAL */
.card {
  width: 320px;          /* ocupa bom espaço sem cortar */
  min-height: 420px;     /* altura mínima */
  display: flex;
  flex-direction: column;
}

/* Imagem */
.image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 6px;
  margin-bottom: 10px;
}

.no-image {
  width: 100%;
  height: 180px;
  background: #f2f2f2;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #777;
  margin-bottom: 10px;
}

.title {
  margin-bottom: 6px;
  font-size: 18px;
  font-weight: bold;
}

/* Scroll interno */
.scroll {
  flex: 1;
  overflow-y: auto;
  padding-right: 6px;
}

/* Botões */
.actions {
  margin-top: 12px;
  display: flex;
  justify-content: space-between;
}
</style>

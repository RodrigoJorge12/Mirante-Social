<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import MiranteSocialButton from "./components/MiranteSocialButton.vue";
import { PresenterUsuario } from "./Presenters/PresenterUsuario"
import { ArrowDown } from "@element-plus/icons-vue"; // ícone do Element Plus
import { ElMessage } from "element-plus";

const router = useRouter();
const presenter = new PresenterUsuario()

function goToLoginPage() {
  router.push("/login"); 
}
function goToInitialPage(){
  router.push("/");
} 
function goToMapPage(){
  router.push("/socialProjectsNear");
}
async function logout() {
  try {
    const response = await presenter.logout();

    if (response?.success) {
      isLogged.value = false;
      user.value = null;
      router.push("/login");
    }
  } catch (e) {
    console.error("Erro no logout", e);
  }
}

const isLogged = ref(false);
const user = ref<string | null>(null);

onMounted(async () => {
  try {
    const result = await presenter.verifyIfIsLogged();
    isLogged.value = !!(result && result.authenticated);
    user.value = result?.data?.name ?? null;
  } catch {
    isLogged.value = false;
  }
});

function handleCommand(cmd: string) {
  if (cmd == "logout") {
      logout();
  }
}
function goToCreateSocialProjectPage(){
  if(!isLogged.value){
    router.push("/login");
  } else {
    router.push("/createSocialProject");
  }
}
function goToMySocialProjectsPage(){
  if(!isLogged.value){
    router.push("/login");
  } else {
    router.push("/mySocialProjects");
  }
}
</script>

<template>
  <header class="header">
    <h2 class="title">Mirante Social</h2>

    <ul class="list">
      <li><a @click="goToInitialPage">Inicio</a></li>
      <li><a @click="goToMapPage">Mapa</a></li>
      <li><a @click="goToMySocialProjectsPage">Meus Projetos</a></li>
      <li><a @click="goToCreateSocialProjectPage">Cadastrar Projeto</a></li>
      <li><a>Sobre</a></li>
    </ul>

    <MiranteSocialButton v-if="!isLogged" @click="goToLoginPage">
      Login
    </MiranteSocialButton>

    <el-dropdown v-else @command="handleCommand">
      <span class="el-dropdown-link" style="cursor: pointer; font-weight: bold;">
        {{ "Olá, " + user }}
        <el-icon><arrow-down /></el-icon>
      </span>

      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item disabled>
            Logado como {{ user }}
          </el-dropdown-item>
          <el-dropdown-item divided command="logout">
            Sair
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>

  </header>
</template>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-around;
  background: #fff;
  border-bottom: 1px solid #eee;
  padding: 1rem;
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 10;
}

.title {
  color: #10B981;
  font-weight: bold;
}

.list {
  display: flex;
  gap: 1rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.list li a {
  font-weight: bold;
  color: black;
}
</style>

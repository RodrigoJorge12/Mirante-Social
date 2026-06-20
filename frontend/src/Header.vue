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
  router.push("/map");
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
  <header class="hdr">

    <!-- Logo -->
    <div class="hdr-logo" @click="goToInitialPage">
      <img src="/MiranteSocial.png" alt="Mirante Social" class="hdr-logo-img" />
    </div>

    <!-- Nav links -->
    <nav class="hdr-nav">
      <a class="hdr-link" @click="goToInitialPage">Início</a>
      <a class="hdr-link" @click="goToMapPage">Mapa</a>
      <a class="hdr-link" @click="goToMySocialProjectsPage">Meus Projetos</a>
      <a class="hdr-link" @click="goToCreateSocialProjectPage">Cadastrar Projeto</a>
      <a class="hdr-link" @click="router.push('/about')">Sobre</a>
    </nav>

    <!-- Auth area -->
    <div class="hdr-auth">
      <MiranteSocialButton v-if="!isLogged" @click="goToLoginPage" class="hdr-btn-login">
        Entrar
      </MiranteSocialButton>

      <el-dropdown v-else @command="handleCommand">
        <span class="hdr-user-trigger">
          <div class="hdr-avatar">{{ user ? user.charAt(0).toUpperCase() : '?' }}</div>
          <span class="hdr-user-name">{{ "Olá, " + user }}</span>
          <el-icon class="hdr-chevron"><arrow-down /></el-icon>
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
    </div>

  </header>
</template>

<style scoped>
.hdr {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 2px solid var(--g10);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  height: 68px;
  font-family: 'Nunito', sans-serif;
}

/* Logo */
.hdr-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  text-decoration: none;
  flex-shrink: 0;
}

.hdr-logo-img {
  height: 44px;
  width: auto;
  border-radius: 10px;
  display: block;
  flex-shrink: 0;
}

/* Nav */
.hdr-nav {
  display: flex;
  align-items: center;
  gap: 4px;
}

.hdr-link {
  padding: 9px 13px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  color: #4a7a4a;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
  white-space: nowrap;
}

.hdr-link:hover {
  background: var(--g10);
  color: #065F46;
}

/* Auth area */
.hdr-auth {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* Override MiranteSocialButton styles for the header login button */
.hdr-auth :deep(.hdr-btn-login),
.hdr-auth :deep(button) {
  padding: 10px 22px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 800;
  background: #059669;
  color: #fff;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
  transition: background 0.15s, box-shadow 0.15s;
}

.hdr-auth :deep(button):hover {
  background: #047857;
  box-shadow: 0 6px 18px rgba(5, 150, 105, 0.4);
}

/* Logged-in user trigger */
.hdr-user-trigger {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 6px 12px 6px 6px;
  border-radius: 12px;
  transition: background 0.15s;
}

.hdr-user-trigger:hover {
  background: var(--g10);
}

.hdr-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #065F46, #34D399);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  font-weight: 800;
  color: #fff;
  flex-shrink: 0;
}

.hdr-user-name {
  font-size: 15px;
  font-weight: 700;
  color: #064E3B;
  white-space: nowrap;
}

.hdr-chevron {
  color: #059669;
  font-size: 14px;
}
</style>

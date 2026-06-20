<script setup>
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';

const router = useRouter();

function goTo(path) {
  router.push(path).then(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function goToMap() {
  router.push('/map').then(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
}
</script>

<template>
  <footer class="ftr">

    <div class="ftr-glow ftr-glow--left" aria-hidden="true"></div>
    <div class="ftr-glow ftr-glow--right" aria-hidden="true"></div>

    <div class="ftr-body">

      <!-- Bloco esquerdo: identidade -->
      <div class="ftr-col ftr-col--identity">
        <div class="ftr-brand" @click="goTo('/')">
          <img src="/MiranteSocial.png" alt="Mirante Social" class="ftr-logo" />
          <span class="ftr-brand-name">Mirante Social</span>
        </div>
        <p class="ftr-tagline">
          Conectando pessoas a projetos sociais<br />em Nova Friburgo
        </p>
      </div>

      <!-- Divisor decorativo vertical -->
      <div class="ftr-divider" aria-hidden="true"></div>

      <!-- Bloco central: navegação -->
      <div class="ftr-col ftr-col--nav">
        <h3 class="ftr-col-title">Navegação</h3>
        <nav class="ftr-nav">
          <a class="ftr-link" @click="goTo('/')">
            <span class="ftr-link-dot"></span>Início
          </a>
          <a class="ftr-link" @click="goToMap">
            <span class="ftr-link-dot"></span>Mapa
          </a>
          <a class="ftr-link" @click="goTo('/about')">
            <span class="ftr-link-dot"></span>Sobre
          </a>
          <a class="ftr-link" @click="goTo('/createSocialProject')">
            <span class="ftr-link-dot"></span>Cadastrar Projeto
          </a>
        </nav>
      </div>

      <!-- Divisor decorativo vertical -->
      <div class="ftr-divider" aria-hidden="true"></div>

      <!-- Bloco direito: sobre o projeto -->
      <div class="ftr-col ftr-col--about">
        <h3 class="ftr-col-title">Sobre o projeto</h3>
        <p class="ftr-about-text">
          Uma iniciativa para fortalecer a comunidade de Nova Friburgo, RJ.
          Juntos, transformamos cuidado em ação.
        </p>
        <a class="ftr-email" href="mailto:contato@mirantesocial.com.br">
          <span class="ftr-email-icon">✉</span>
          contato@mirantesocial.com.br
        </a>
      </div>

    </div>

    <!-- Barra inferior -->
    <div class="ftr-bar">
      <span class="ftr-bar-text">
        © 2025 Mirante Social · Nova Friburgo, RJ · Feito com carinho para a comunidade
      </span>
    </div>

  </footer>
</template>

<style scoped>
.ftr {
  position: relative;
  background: linear-gradient(160deg, #022C22 0%, #064E3B 100%);
  font-family: 'Nunito', sans-serif;
  overflow: hidden;
}

/* Brilhos decorativos de fundo */
.ftr-glow {
  position: absolute;
  width: 420px;
  height: 420px;
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
  opacity: 0.07;
}

.ftr-glow--left {
  background: radial-gradient(circle, #34D399, transparent 70%);
  top: -120px;
  left: -100px;
}

.ftr-glow--right {
  background: radial-gradient(circle, #10B981, transparent 70%);
  bottom: -100px;
  right: -80px;
}

/* Corpo principal do footer */
.ftr-body {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: 0;
  padding: 56px 48px 48px;
  max-width: 1100px;
  margin: 0 auto;
}

/* Colunas */
.ftr-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.ftr-col--identity {
  gap: 16px;
  min-width: 240px;
}

.ftr-col--nav {
  padding: 0 32px;
  gap: 0;
}

.ftr-col--about {
  gap: 16px;
  min-width: 240px;
  padding-left: 32px;
}

/* Divisores verticais */
.ftr-divider {
  width: 1px;
  align-self: stretch;
  background: linear-gradient(to bottom, transparent, rgba(167, 243, 208, 0.18) 25%, rgba(167, 243, 208, 0.18) 75%, transparent);
  flex-shrink: 0;
}

/* Identidade — logo + nome */
.ftr-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  width: fit-content;
  transition: opacity 0.2s;
}

.ftr-brand:hover {
  opacity: 0.85;
}

.ftr-logo {
  height: 44px;
  width: auto;
  border-radius: 10px;
  display: block;
  flex-shrink: 0;
}

.ftr-brand-name {
  font-size: 20px;
  font-weight: 800;
  color: #D1FAE5;
  letter-spacing: -0.3px;
}

.ftr-tagline {
  font-size: 15px;
  font-weight: 600;
  color: #6EE7B7;
  line-height: 1.65;
  margin: 0;
  opacity: 0.9;
}

.ftr-leaf-row {
  display: flex;
  gap: 6px;
  margin-top: 4px;
  font-size: 18px;
  opacity: 0.55;
}

/* Títulos das colunas */
.ftr-col-title {
  font-size: 13px;
  font-weight: 900;
  letter-spacing: 1.4px;
  text-transform: uppercase;
  color: #34D399;
  margin: 0 0 22px;
}

/* Navegação */
.ftr-nav {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.ftr-link {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  font-weight: 700;
  color: #A7F3D0;
  cursor: pointer;
  text-decoration: none;
  padding: 8px 10px 8px 0;
  border-radius: 8px;
  transition: color 0.18s, padding-left 0.18s;
}

.ftr-link:hover {
  color: #D1FAE5;
  padding-left: 6px;
}

.ftr-link-dot {
  display: inline-block;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #34D399;
  flex-shrink: 0;
  opacity: 0.7;
  transition: opacity 0.18s, transform 0.18s;
}

.ftr-link:hover .ftr-link-dot {
  opacity: 1;
  transform: scale(1.4);
}

/* Bloco sobre */
.ftr-about-text {
  font-size: 15px;
  font-weight: 600;
  color: #6EE7B7;
  line-height: 1.7;
  margin: 0;
  opacity: 0.9;
}

.ftr-email {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  font-size: 15px;
  font-weight: 700;
  color: #A7F3D0;
  text-decoration: none;
  margin-top: 4px;
  padding: 10px 16px;
  background: rgba(167, 243, 208, 0.07);
  border: 1px solid rgba(167, 243, 208, 0.14);
  border-radius: 10px;
  width: fit-content;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
}

.ftr-email:hover {
  background: rgba(167, 243, 208, 0.13);
  border-color: rgba(167, 243, 208, 0.3);
  color: #D1FAE5;
}

.ftr-email-icon {
  font-size: 16px;
  opacity: 0.8;
  flex-shrink: 0;
}

/* Barra inferior */
.ftr-bar {
  position: relative;
  z-index: 1;
  background: #022C22;
  border-top: 1px solid rgba(167, 243, 208, 0.1);
  padding: 18px 48px;
  text-align: center;
}

.ftr-bar-text {
  font-size: 13px;
  font-weight: 600;
  color: #A7F3D0;
  opacity: 0.75;
  letter-spacing: 0.2px;
}

/* Responsividade */
@media (max-width: 860px) {
  .ftr-body {
    flex-direction: column;
    padding: 40px 28px 36px;
    gap: 32px;
  }

  .ftr-divider {
    width: 100%;
    height: 1px;
    align-self: auto;
    background: linear-gradient(to right, transparent, rgba(167, 243, 208, 0.18) 25%, rgba(167, 243, 208, 0.18) 75%, transparent);
  }

  .ftr-col--nav {
    padding: 0;
  }

  .ftr-bar {
    padding: 16px 24px;
  }

  .ftr-bar-text {
    font-size: 12px;
  }
}
</style>

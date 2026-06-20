<script setup>
import { useRouter } from 'vue-router'
import { Search, Map, ClipboardPen } from 'lucide-vue-next'

const router = useRouter()

function goToCreateUser() {
  router.push('/createUser')
}

const stats = [
  { number: '127', label: 'Projetos cadastrados' },
  { number: '34',  label: 'Bairros cobertos'     },
  { number: '48',  label: 'Projetos verificados'  },
  { number: '9',   label: 'Áreas de atuação'      },
]

const features = [
  {
    iconComponent: Search,
    title: 'Descubra Projetos',
    description: 'Explore iniciativas sociais por área de atuação, bairro ou palavra-chave. Tudo em um só lugar, fácil de encontrar.',
  },
  {
    iconComponent: Map,
    title: 'Veja no Mapa',
    description: 'Encontre projetos próximos de você de forma visual e intuitiva. Basta olhar o mapa e clicar.',
  },
  {
    iconComponent: ClipboardPen,
    title: 'Cadastre o Seu',
    description: 'Crie uma conta gratuitamente e registre sua iniciativa social para que mais pessoas possam conhecer e apoiar.',
  },
]
</script>

<template>
  <div class="about-root">

    <!-- ── Hero ─────────────────────────────────────────── -->
    <section class="hero">
      <div class="hero-glow"></div>
      <div class="hero-content">
        <img src="/MiranteSocial.png" alt="Mirante Social" class="hero-logo" />
        <h1 class="hero-title">O que é o<br><span class="green">Mirante Social?</span></h1>
        <p class="hero-subtitle">
          Uma plataforma criada para conectar pessoas às iniciativas sociais de Nova Friburgo
          — simples, acessível e feita com carinho para toda a comunidade.
        </p>
      </div>
    </section>

    <!-- ── Números de impacto ────────────────────────────── -->
    <section class="stats-section">
      <div class="stats-grid">
        <div v-for="stat in stats" :key="stat.label" class="stat-card">
          <span class="stat-number">{{ stat.number }}</span>
          <span class="stat-label">{{ stat.label }}</span>
        </div>
      </div>
    </section>

    <!-- ── Cards de funcionalidades ─────────────────────── -->
    <section class="features-section">
      <h2 class="section-title">Como funciona?</h2>
      <p class="section-subtitle">Tudo pensado para ser fácil e acolhedor.</p>
      <div class="features-grid">
        <div v-for="f in features" :key="f.title" class="feature-card">
          <div class="feature-icon">
            <component :is="f.iconComponent" :size="28" :stroke-width="1.5" />
          </div>
          <h3 class="feature-title">{{ f.title }}</h3>
          <p class="feature-desc">{{ f.description }}</p>
        </div>
      </div>
    </section>

    <!-- ── CTA banner ────────────────────────────────────── -->
    <section class="cta-section">
      <div class="cta-inner">
        <h2 class="cta-title">Quer cadastrar seu projeto?</h2>
        <p class="cta-sub">
          Junte-se a dezenas de iniciativas que já fazem a diferença em Nova Friburgo.
          É gratuito e leva apenas alguns minutos.
        </p>
        <button class="cta-btn" @click="goToCreateUser">
          Criar conta grátis →
        </button>
      </div>
    </section>

  </div>
</template>

<style scoped>
/* ── Raiz ────────────────────────────────────────────────── */
.about-root {
  width: 100%;
  min-height: 100vh;
  padding-top: 88px;
  background: #F0FDF4;
  font-family: 'Nunito', sans-serif;
  overflow-x: hidden;
}

/* ── Hero ────────────────────────────────────────────────── */
.hero {
  position: relative;
  background: linear-gradient(160deg, #ECFDF5 0%, #D1FAE5 55%, #F0FDF4 100%);
  padding: 80px 24px 80px;
  text-align: center;
  overflow: hidden;
}

.hero-glow {
  position: absolute;
  top: -100px;
  left: 50%;
  transform: translateX(-50%);
  width: 700px;
  height: 500px;
  background: radial-gradient(ellipse at center, rgba(16, 185, 129, 0.18) 0%, transparent 68%);
  pointer-events: none;
}

.hero-content {
  position: relative;
  max-width: 700px;
  margin: 0 auto;
}

.hero-logo {
  height: 100px;
  width: auto;
  border-radius: 24px;
  display: block;
  margin: 0 auto 32px;
  box-shadow: 0 10px 40px rgba(16, 185, 129, 0.32);
}

.hero-title {
  font-size: 52px;
  font-weight: 900;
  color: #022C22;
  line-height: 1.15;
  letter-spacing: -1px;
  margin: 0 0 24px;
}

.hero-title .green {
  color: #059669;
}

.hero-subtitle {
  font-size: 21px;
  color: #065F46;
  line-height: 1.7;
  max-width: 560px;
  margin: 0 auto;
}

/* ── Stats ───────────────────────────────────────────────── */
.stats-section {
  padding: 64px 24px;
  background: #fff;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 960px;
  margin: 0 auto;
}

.stat-card {
  background: #F0FDF4;
  border: 2px solid #A7F3D0;
  border-radius: 20px;
  padding: 36px 24px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 14px 40px rgba(16, 185, 129, 0.15);
  border-color: #059669;
}

.stat-number {
  font-size: 52px;
  font-weight: 900;
  color: #059669;
  line-height: 1;
  letter-spacing: -2px;
}

.stat-label {
  font-size: 17px;
  font-weight: 700;
  color: #064E3B;
  line-height: 1.3;
}

/* ── Features ────────────────────────────────────────────── */
.features-section {
  padding: 80px 24px;
  text-align: center;
}

.section-title {
  font-size: 40px;
  font-weight: 900;
  color: #022C22;
  margin: 0 0 12px;
  letter-spacing: -0.5px;
}

.section-subtitle {
  font-size: 19px;
  color: #065F46;
  margin: 0 0 56px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  max-width: 1040px;
  margin: 0 auto;
}

.feature-card {
  background: #fff;
  border: 2px solid #D1FAE5;
  border-radius: 20px;
  padding: 44px 32px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 18px;
  box-shadow: 0 4px 16px rgba(16, 185, 129, 0.07);
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
}

.feature-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 48px rgba(16, 185, 129, 0.15);
  border-color: #6EE7B7;
}

.feature-icon {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  color: #059669;
}

.feature-title {
  font-size: 22px;
  font-weight: 800;
  color: #022C22;
  margin: 0;
}

.feature-desc {
  font-size: 17px;
  color: #065F46;
  line-height: 1.65;
  margin: 0;
}

/* ── CTA ─────────────────────────────────────────────────── */
.cta-section {
  padding: 0 24px 80px;
}

.cta-inner {
  background: linear-gradient(135deg, #047857 0%, #022C22 100%);
  border-radius: 24px;
  max-width: 860px;
  margin: 0 auto;
  padding: 72px 48px;
  text-align: center;
  box-shadow: 0 20px 64px rgba(2, 44, 34, 0.28);
}

.cta-title {
  font-size: 40px;
  font-weight: 900;
  color: #fff;
  margin: 0 0 16px;
  letter-spacing: -0.5px;
}

.cta-sub {
  font-size: 19px;
  color: #A7F3D0;
  line-height: 1.65;
  max-width: 520px;
  margin: 0 auto 40px;
}

.cta-btn {
  display: inline-block;
  background: #fff;
  color: #047857;
  border: none;
  border-radius: 16px;
  padding: 18px 48px;
  font-family: 'Nunito', sans-serif;
  font-size: 19px;
  font-weight: 900;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
}

.cta-btn:hover {
  transform: scale(1.04) translateY(-2px);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.22);
  background: #ECFDF5;
}

/* ── Responsivo ──────────────────────────────────────────── */
@media (max-width: 960px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .features-grid {
    grid-template-columns: 1fr;
    max-width: 520px;
  }
}

@media (max-width: 640px) {
  .hero-title {
    font-size: 38px;
  }

  .hero-subtitle {
    font-size: 18px;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  .stat-number {
    font-size: 42px;
  }

  .section-title {
    font-size: 30px;
  }

  .cta-inner {
    padding: 48px 24px;
  }

  .cta-title {
    font-size: 30px;
  }

  .cta-btn {
    width: 100%;
    font-size: 18px;
  }
}
</style>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";
import ProjectDetailsModal from "@/ProjectDetailsModal.vue";
import { GraduationCap, HeartPulse, Palette, Dumbbell, Leaf, HandHeart, LayoutGrid, Baby, Users, User, UserCog, Search } from 'lucide-vue-next'

const presenter = new PresenterSocialProject();
const projects = ref<any[]>([]);
const search = ref("");
const selectedArea = ref("Todos");
const selectedPublic = ref("");

onMounted(async () => {
  const result = await presenter.GetAllProjects();
  projects.value = result.data ?? [];
});

const imageUrl = (path: string | null) =>
  path ? `${import.meta.env.VITE_API_URL}/storage/${path}` : "";

function normalize(str: string): string {
  return str.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase();
}

const AREAS = [
  { label: 'Todos',             icon: LayoutGrid  },
  { label: 'Educação',          icon: GraduationCap },
  { label: 'Saúde',             icon: HeartPulse  },
  { label: 'Cultura',           icon: Palette     },
  { label: 'Esporte',           icon: Dumbbell    },
  { label: 'Meio Ambiente',     icon: Leaf        },
  { label: 'Assistência Social',icon: HandHeart   },
]

const PUBLICOS = [
  { label: 'Crianças',     icon: Baby    },
  { label: 'Adolescentes', icon: Users   },
  { label: 'Adultos',      icon: User    },
  { label: 'Idosos',       icon: UserCog },
]

function togglePublic(label: string) {
  selectedPublic.value = selectedPublic.value === label ? "" : label
}

const filteredProjects = computed(() => {
  return projects.value.filter((p) => {
    const term = normalize(search.value.trim())

    // filtro de texto
    if (term) {
      const fields = [p.name, p.description, p.activity_area, p.city, p.state, p.district]
        .map(f => normalize(f || ""))
      let audiences: string[] = []
      try { audiences = typeof p.target_audiences === "string" ? JSON.parse(p.target_audiences) : (p.target_audiences ?? []) } catch {}
      const audienceText = normalize(audiences.join(" "))
      const matchText = fields.some(f => f.includes(term)) || audienceText.includes(term)
      if (!matchText) return false
    }

    // filtro de área
    if (selectedArea.value && selectedArea.value !== 'Todos') {
      if (normalize(p.activity_area || "") !== normalize(selectedArea.value)) return false
    }

    // filtro de público
    if (selectedPublic.value) {
      let audiences: string[] = []
      try { audiences = typeof p.target_audiences === "string" ? JSON.parse(p.target_audiences) : (p.target_audiences ?? []) } catch {}
      if (!audiences.map(a => normalize(a)).includes(normalize(selectedPublic.value))) return false
    }

    return true
  })
})

const detailsVisible = ref(false);
const selectedProject = ref<any | null>(null);

function openDetails(project: any) {
  selectedProject.value = project;
  detailsVisible.value = true;
}
</script>

<template>
  <div class="home-root">

    <!-- ── Hero ── -->
    <div class="hero">
      <div class="hero-photo"></div>
      <div class="hero-overlay"></div>
      <div class="hero-vignette"></div>

      <div class="hero-content">
        <div class="hero-eyebrow">
          <span class="hero-eyebrow-dot"></span>
          Nova Friburgo, RJ — Plataforma Social
        </div>
        <h1 class="hero-title">
          Projetos que <span class="hero-green">transformam</span><br>
          a sua cidade.
        </h1>
        <p class="hero-sub">
          Descubra iniciativas sociais reais — educação, saúde, cultura e muito mais.<br>
          Encontre onde participar ou cadastre o seu gratuitamente.
        </p>
        <div class="hero-actions">
          <button class="hero-btn-p" @click="document.querySelector('.body-wrap')?.scrollIntoView({ behavior: 'smooth' })">
            Explorar projetos →
          </button>
          <RouterLink to="/criar-projeto" class="hero-btn-s">Cadastrar o meu</RouterLink>
        </div>
      </div>

      <div class="hero-metrics">
        <div class="hero-metric">
          <span class="hero-m-num">127</span>
          <span class="hero-m-lbl">Projetos cadastrados</span>
        </div>
        <div class="hero-metric-div"></div>
        <div class="hero-metric">
          <span class="hero-m-num">34</span>
          <span class="hero-m-lbl">Bairros cobertos</span>
        </div>
        <div class="hero-metric-div"></div>
        <div class="hero-metric">
          <span class="hero-m-num">48</span>
          <span class="hero-m-lbl">Verificados</span>
        </div>
        <div class="hero-metric-div"></div>
        <div class="hero-metric">
          <span class="hero-m-num">6</span>
          <span class="hero-m-lbl">Áreas de atuação</span>
        </div>
      </div>
    </div>

    <!-- ── Corpo: sidebar + conteúdo ── -->
    <div class="body-wrap">

      <!-- Sidebar de filtros -->
      <aside class="filter-sidebar">

        <!-- Busca -->
        <div class="sb-search">
          <Search :size="16" :stroke-width="2" class="sb-search-icon" />
          <input
            v-model="search"
            class="sb-search-input"
            type="text"
            placeholder="Buscar projetos..."
          />
        </div>

        <div class="sb-divider"></div>

        <!-- Área -->
        <div class="sb-section-label">Área de atuação</div>
        <div class="sb-area-list">
          <button
            v-for="a in AREAS"
            :key="a.label"
            :class="['sb-area-btn', { 'sb-area-btn--on': selectedArea === a.label }]"
            @click="selectedArea = a.label"
          >
            <component :is="a.icon" :size="16" :stroke-width="2" class="sb-area-icon" />
            {{ a.label }}
          </button>
        </div>

        <div class="sb-divider"></div>

        <!-- Público -->
        <div class="sb-section-label">Público-alvo</div>
        <div class="sb-pub-chips">
          <button
            v-for="p in PUBLICOS"
            :key="p.label"
            :class="['sb-pub-chip', { 'sb-pub-chip--on': selectedPublic === p.label }]"
            @click="togglePublic(p.label)"
          >
            <component :is="p.icon" :size="13" :stroke-width="2" />
            {{ p.label }}
          </button>
        </div>

      </aside>

      <!-- Grid de projetos -->
      <div class="content-col">
        <div v-if="filteredProjects.length" class="projects-grid">
          <div
            v-for="project in filteredProjects"
            :key="project.id"
            class="proj-card"
            @click="openDetails(project)"
          >
            <img :src="imageUrl(project.image_path)" class="proj-img" :alt="project.name" />
            <div class="proj-body">
              <span class="proj-area">{{ project.activity_area || "Geral" }}</span>
              <h2 class="proj-title">{{ project.name }}</h2>
              <p class="proj-desc">{{ project.description }}</p>
            </div>
            <div class="proj-footer">
              <span class="proj-location">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:3px;">
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#10B981"/>
                </svg>
                {{ project.city || "Nova Friburgo" }}
              </span>
              <button class="proj-btn" @click.stop="openDetails(project)">Ver detalhes</button>
            </div>
          </div>
        </div>

        <div v-else class="no-results">
          <div class="no-results-icon">🔍</div>
          <p>Nenhum projeto encontrado.</p>
          <span>Tente outros filtros ou termos de busca.</span>
        </div>
      </div>

    </div>

  </div>

  <ProjectDetailsModal v-model="detailsVisible" :project="selectedProject" />
</template>

<style scoped>
.home-root {
  width: 100%;
  min-height: 100vh;
  overflow-x: hidden;
  background: #F0FDF4;
  font-family: 'Nunito', sans-serif;
}

/* ── Hero ── */
.hero {
  position: relative;
  display: flex;
  flex-direction: column;
  min-height: 862px;
  overflow: hidden;
  margin-top: -80px;
  padding-top: 80px;
}

.hero-photo {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1593113598332-cd288d649433?w=1600&q=80');
  background-size: cover;
  background-position: center 30%;
  filter: brightness(.55) saturate(.75);
}

/* overlay verde suave — usa as cores médias do sistema, não o escuro total */
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    150deg,
    rgba(5, 150, 105, 0.55) 0%,
    rgba(16, 185, 129, 0.25) 50%,
    rgba(4, 120, 87, 0.35) 100%
  );
}

/* vinheta suave à esquerda para legibilidade do texto */
.hero-vignette {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(4,120,87,.5) 0%, transparent 55%);
}

.hero-content {
  position: relative;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-self: stretch;
  padding: 80px 80px 64px;
  gap: 20px;
}

.hero-content > * {
  max-width: 860px;
}

.hero-eyebrow {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 900;
  color: #A7F3D0;
  text-transform: uppercase;
  letter-spacing: .13em;
}

.hero-eyebrow-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #34D399;
  box-shadow: 0 0 8px rgba(52,211,153,.8);
  flex-shrink: 0;
  animation: hero-pulse 2s ease-in-out infinite;
}
@keyframes hero-pulse {
  0%,100% { transform: scale(1); opacity: 1; }
  50%      { transform: scale(1.5); opacity: .5; }
}

.hero-title {
  font-size: 66px;
  font-weight: 900;
  color: #fff;
  line-height: 1.08;
  letter-spacing: -1.5px;
  margin: 0;
}
.hero-green { color: #34D399; }

.hero-sub {
  font-size: 16px;
  color: rgba(255,255,255,.82);
  line-height: 1.65;
  margin: 0;
  font-weight: 600;
}

.hero-actions {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-top: 4px;
}

.hero-btn-p {
  background: linear-gradient(135deg, #059669, #047857);
  color: #fff;
  border: none;
  border-radius: 14px;
  padding: 14px 32px;
  font-family: 'Nunito', sans-serif;
  font-size: 15px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(5,150,105,.4);
  transition: transform .2s, box-shadow .2s;
}
.hero-btn-p:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(5,150,105,.5); }

.hero-btn-s {
  background: rgba(255,255,255,.15);
  color: #fff;
  border: 1.5px solid rgba(255,255,255,.35);
  border-radius: 14px;
  padding: 14px 24px;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  text-decoration: none;
  backdrop-filter: blur(4px);
  transition: background .2s;
  display: inline-block;
}
.hero-btn-s:hover { background: rgba(255,255,255,.25); }

/* Faixa de métricas */
.hero-metrics {
  position: relative;
  background: rgba(4, 120, 87, 0.55);
  backdrop-filter: blur(12px);
  border-top: 1px solid rgba(167,243,208,.25);
  padding: 22px 80px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0;
}

.hero-metric {
  text-align: center;
  padding: 0 48px;
  position: relative;
}
.hero-metric + .hero-metric::before {
  content: '';
  position: absolute;
  left: 0; top: 50%;
  transform: translateY(-50%);
  height: 36px; width: 1px;
  background: rgba(255,255,255,.18);
}

.hero-m-num {
  display: block;
  font-size: 32px;
  font-weight: 900;
  color: #fff;
  line-height: 1;
  letter-spacing: -1px;
}
.hero-m-lbl {
  display: block;
  font-size: 10px;
  font-weight: 700;
  color: #A7F3D0;
  text-transform: uppercase;
  letter-spacing: .09em;
  margin-top: 4px;
  opacity: .9;
}
.hero-metric-div { display: none; }

/* ── Layout corpo ── */
.body-wrap {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px 48px 64px;
  display: grid;
  grid-template-columns: 240px 1fr;
  gap: 28px;
  align-items: start;
}

/* ── Sidebar ── */
.filter-sidebar {
  background: #fff;
  border-radius: 20px;
  border: 2px solid #D1FAE5;
  padding: 22px 18px;
  box-shadow: 0 4px 20px rgba(16,185,129,0.07);
  position: sticky;
  top: 96px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* Busca na sidebar */
.sb-search {
  display: flex;
  align-items: center;
  gap: 9px;
  background: #F0FDF4;
  border: 1.5px solid #D1FAE5;
  border-radius: 12px;
  padding: 9px 13px;
  transition: border-color .2s;
}
.sb-search:focus-within { border-color: #059669; }
.sb-search-icon { color: #10B981; flex-shrink: 0; }
.sb-search-input {
  border: none; outline: none; background: transparent;
  font-family: 'Nunito', sans-serif; font-size: 13px;
  color: #022C22; width: 100%;
}
.sb-search-input::placeholder { color: #A7F3D0; }

.sb-divider { height: 1px; background: #F0FDF4; }

.sb-section-label {
  font-size: 10px;
  font-weight: 900;
  color: #10B981;
  text-transform: uppercase;
  letter-spacing: .1em;
}

/* Área — lista vertical */
.sb-area-list { display: flex; flex-direction: column; gap: 2px; }

.sb-area-btn {
  display: flex;
  align-items: center;
  gap: 9px;
  text-align: left;
  padding: 9px 11px;
  border-radius: 11px;
  border: none;
  background: transparent;
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  font-weight: 700;
  color: #065F46;
  cursor: pointer;
  transition: background .15s, color .15s;
  width: 100%;
}
.sb-area-btn:hover { background: #F0FDF4; }
.sb-area-btn--on   { background: #059669 !important; color: #fff !important; }
.sb-area-btn--on .sb-area-icon { color: #fff !important; }
.sb-area-icon { color: #10B981; flex-shrink: 0; }

/* Público — chips */
.sb-pub-chips { display: flex; flex-wrap: wrap; gap: 6px; }

.sb-pub-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 12px;
  border-radius: 50px;
  border: 1.5px solid #A7F3D0;
  background: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 12px;
  font-weight: 700;
  color: #065F46;
  cursor: pointer;
  transition: all .18s;
}
.sb-pub-chip:hover { background: #ECFDF5; border-color: #059669; }
.sb-pub-chip--on   { background: #059669; border-color: #059669; color: #fff; }

/* ── Grid de projetos ── */
.content-col { min-width: 0; }

.projects-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
}

@media (max-width: 1000px) {
  .projects-grid { grid-template-columns: repeat(2, 1fr); }
}

/* ── Card ── */
.proj-card {
  background: #fff;
  border: 2px solid #F0FDF4;
  border-radius: 20px;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  transition: transform .25s, box-shadow .25s, border-color .25s;
  box-shadow: 0 2px 8px rgba(16,185,129,0.06);
}
.proj-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 40px rgba(16,185,129,0.15);
  border-color: #A7F3D0;
}

.proj-img {
  height: 148px;
  width: 100%;
  object-fit: cover;
  display: block;
  flex-shrink: 0;
  background: #F0FDF4;
}

.proj-body {
  padding: 14px 16px 8px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.proj-area {
  display: inline-block;
  font-size: 10px;
  font-weight: 800;
  color: #047857;
  background: #F0FDF4;
  padding: 3px 9px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: .06em;
  width: fit-content;
}

.proj-title {
  font-size: 15px;
  font-weight: 800;
  color: #022C22;
  line-height: 1.3;
  margin: 0;
}

.proj-desc {
  font-size: 12px;
  color: #5a9a7a;
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.proj-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px 14px;
  border-top: 1px solid #F0FDF4;
  gap: 8px;
}

.proj-location {
  font-size: 11px;
  color: #059669;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.proj-btn {
  flex-shrink: 0;
  background: linear-gradient(135deg, #059669, #047857);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 8px 16px;
  font-family: 'Nunito', sans-serif;
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  transition: opacity .2s, transform .15s;
}
.proj-btn:hover { opacity: 0.88; transform: scale(1.04); }

/* ── Sem resultados ── */
.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 24px;
  text-align: center;
  gap: 8px;
}
.no-results-icon { font-size: 40px; margin-bottom: 8px; }
.no-results p { font-size: 18px; font-weight: 800; color: #022C22; margin: 0; }
.no-results span { font-size: 14px; color: #065F46; font-weight: 600; }
</style>

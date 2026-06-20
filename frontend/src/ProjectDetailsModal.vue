<script setup lang="ts">
import { defineProps, defineEmits, computed, ref, watch, nextTick } from "vue";
import { PresenterPersonalizedPage } from "./Presenters/PresenterPersonalizedPage";
import { X, MapPin, Phone, Globe, Sparkles, Building2, ExternalLink } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  project:    { type: Object, default: null },
});
const emit = defineEmits(["update:modelValue"]);

const activeTab = ref<'sobre' | 'local' | 'contato'>('sobre');
const mapContainer = ref<HTMLElement | null>(null);
const mapCoords = ref<{ lat: number; lng: number } | null>(null);
let mapInstance: any = null;

const imageUrl = (path: string | null) =>
  path ? `${import.meta.env.VITE_API_URL}/storage/${path}` : "";

function closeModal() {
  emit("update:modelValue", false);
  activeTab.value = 'sobre';
}

const audiences = computed(() => {
  if (!props.project) return [];
  try {
    const raw = props.project.target_audiences;
    return typeof raw === "string" ? JSON.parse(raw) : (raw ?? []);
  } catch { return []; }
});

const personalizedPageUrl = ref<string | null>(null);
watch(
  () => props.project,
  async (newProject) => {
    personalizedPageUrl.value = null;
    mapCoords.value = null;
    activeTab.value = 'sobre';
    if (!newProject) return;
    const presenter = new PresenterPersonalizedPage();
    personalizedPageUrl.value = await presenter.getUrlByProjectId(newProject.id);
  },
  { immediate: true }
);

async function selectTab(tab: 'sobre' | 'local' | 'contato') {
  activeTab.value = tab;
  if (tab === 'local') {
    await nextTick();
    await nextTick();
    initMap();
  }
}

async function initMap() {
  if (!mapContainer.value) return;

  const L = (await import('leaflet')).default;
  await import('leaflet/dist/leaflet.css');

  if (mapInstance) {
    mapInstance.remove();
    mapInstance = null;
  }

  const zip   = props.project?.zip_code || '';
  const addr  = props.project?.address  || '';
  const city  = props.project?.city     || 'Nova Friburgo';
  const state = props.project?.state    || 'RJ';
  const query = `${addr} ${city} ${state} Brasil`.trim();

  let lat = -22.2824, lng = -42.5311;

  try {
    const res = await fetch(
      `https://photon.komoot.io/api/?q=${encodeURIComponent(zip || query)}&limit=1`
    );
    const data = await res.json();
    if (data.features?.length) {
      const [lo, la] = data.features[0].geometry.coordinates;
      lat = la; lng = lo;
    }
  } catch { /* usa coordenadas padrão */ }

  mapCoords.value = { lat, lng };

  mapInstance = L.map(mapContainer.value).setView([lat, lng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(mapInstance);

  const icon = L.divIcon({
    className: '',
    html: `<div style="
      width:36px;height:36px;border-radius:50% 50% 50% 0;
      background:linear-gradient(135deg,#059669,#34D399);
      transform:rotate(-45deg);border:3px solid #fff;
      box-shadow:0 4px 12px rgba(5,150,105,.5)"></div>`,
    iconSize: [36, 36],
    iconAnchor: [18, 36],
  });

  L.marker([lat, lng], { icon })
    .addTo(mapInstance)
    .bindPopup(`<b>${props.project?.name}</b><br>${addr}`)
    .openPopup();
}

function openInMaps() {
  if (!mapCoords.value) return;
  const { lat, lng } = mapCoords.value;
  const label = encodeURIComponent(props.project?.name || 'Projeto');
  window.open(`https://www.google.com/maps?q=${lat},${lng}(${label})`, '_blank');
}

watch(() => props.modelValue, (open) => {
  if (!open && mapInstance) {
    mapInstance.remove();
    mapInstance = null;
  }
});
</script>

<template>
  <el-dialog
    :model-value="modelValue"
    width="780px"
    @close="closeModal"
    :show-close="false"
    class="modal-details-dialog"
    align-center
  >
    <template #header>
      <span style="display:none"></span>
    </template>

    <div v-if="project" class="md-root">

      <!-- ── HERO FOTO ── -->
      <div class="md-hero">
        <el-image
          v-if="project.image_path"
          :src="imageUrl(project.image_path)"
          fit="cover"
          class="md-hero-img"
        >
          <template #error>
            <div class="md-hero-fallback">
              <Building2 :size="52" color="#6EE7B7" />
            </div>
          </template>
        </el-image>
        <div v-else class="md-hero-fallback">
          <Building2 :size="52" color="#6EE7B7" />
        </div>

        <div class="md-hero-overlay"></div>

        <button class="md-close-btn" @click="closeModal">
          <X :size="16" />
        </button>

        <div class="md-hero-content">
          <span class="md-area-pill">{{ project.activity_area || 'Projeto Social' }}</span>
          <h2 class="md-title">{{ project.name }}</h2>
        </div>
      </div>

      <!-- ── TABS ── -->
      <div class="md-tabs">
        <button :class="['md-tab', { 'md-tab--on': activeTab === 'sobre' }]"   @click="selectTab('sobre')">Sobre</button>
        <button :class="['md-tab', { 'md-tab--on': activeTab === 'local' }]"   @click="selectTab('local')">Localização</button>
        <button :class="['md-tab', { 'md-tab--on': activeTab === 'contato' }]" @click="selectTab('contato')">Contato</button>
      </div>

      <!-- ── ABA: SOBRE ── -->
      <div v-show="activeTab === 'sobre'" class="md-panel">

        <p class="md-desc">{{ project.description }}</p>

        <div class="md-row-group">
          <div class="md-group-label">Área de atuação</div>
          <span class="md-area-tag">{{ project.activity_area || 'Não informado' }}</span>
        </div>

        <div class="md-row-group">
          <div class="md-group-label">Público-alvo</div>
          <div class="md-chips">
            <span v-for="(aud, i) in audiences" :key="i" class="md-chip">{{ aud }}</span>
            <span v-if="audiences.length === 0" class="md-empty">Não informado</span>
          </div>
        </div>

        <div v-if="personalizedPageUrl" class="md-page-card">
          <div class="md-page-card-icon">
            <Sparkles :size="18" color="#fff" />
          </div>
          <div class="md-page-card-text">
            <div class="md-page-card-title">Página personalizada</div>
            <div class="md-page-card-sub">Este projeto tem uma página própria</div>
          </div>
          <a :href="personalizedPageUrl" target="_blank" class="md-page-card-btn">
            Acessar <ExternalLink :size="13" style="vertical-align:middle;margin-left:4px;" />
          </a>
        </div>

      </div>

      <!-- ── ABA: LOCALIZAÇÃO ── -->
      <div v-show="activeTab === 'local'" class="md-panel md-panel--local">

        <div class="md-loc-grid">
          <div class="md-loc-item">
            <div class="md-loc-k">Endereço</div>
            <div class="md-loc-v">{{ project.address || 'Não informado' }}</div>
          </div>
          <div class="md-loc-item">
            <div class="md-loc-k">Bairro</div>
            <div class="md-loc-v">{{ project.district || 'Não informado' }}</div>
          </div>
          <div class="md-loc-item">
            <div class="md-loc-k">Cidade / UF</div>
            <div class="md-loc-v">{{ project.city }} — {{ project.state }}</div>
          </div>
          <div class="md-loc-item">
            <div class="md-loc-k">CEP</div>
            <div class="md-loc-v">{{ project.zip_code || 'Não informado' }}</div>
          </div>
        </div>

        <div ref="mapContainer" class="md-map"></div>

        <button v-if="mapCoords" class="md-maps-btn" @click="openInMaps">
          <MapPin :size="15" />
          Abrir no Google Maps
          <ExternalLink :size="13" style="margin-left:auto;opacity:.7" />
        </button>

      </div>

      <!-- ── ABA: CONTATO ── -->
      <div v-show="activeTab === 'contato'" class="md-panel">

        <div class="md-contact-list">
          <div class="md-contact-row">
            <div class="md-contact-icon">
              <Phone :size="18" color="#fff" />
            </div>
            <div>
              <div class="md-contact-k">Telefone</div>
              <div class="md-contact-v">{{ project.phone || 'Não informado' }}</div>
            </div>
          </div>
          <div class="md-contact-row">
            <div class="md-contact-icon">
              <Globe :size="18" color="#fff" />
            </div>
            <div>
              <div class="md-contact-k">Website</div>
              <a
                v-if="project.website_url"
                :href="project.website_url"
                target="_blank"
                class="md-contact-link"
              >{{ project.website_url }}</a>
              <div v-else class="md-empty">Não informado</div>
            </div>
          </div>
        </div>

        <div v-if="personalizedPageUrl" class="md-page-card">
          <div class="md-page-card-icon">
            <Sparkles :size="18" color="#fff" />
          </div>
          <div class="md-page-card-text">
            <div class="md-page-card-title">Página personalizada</div>
            <div class="md-page-card-sub">Acesse a página completa do projeto</div>
          </div>
          <a :href="personalizedPageUrl" target="_blank" class="md-page-card-btn">
            Acessar <ExternalLink :size="13" style="vertical-align:middle;margin-left:4px;" />
          </a>
        </div>

      </div>

      <!-- ── FOOTER ── -->
      <div class="md-footer">
        <button class="md-footer-close" @click="closeModal">Fechar</button>
      </div>

    </div>
  </el-dialog>
</template>

<style>
.modal-details-dialog .el-dialog {
  border-radius: 24px !important;
  overflow: hidden !important;
  padding: 0 !important;
  font-family: 'Nunito', sans-serif !important;
  box-shadow: 0 32px 80px rgba(2,44,34,.18) !important;
}
.modal-details-dialog .el-dialog__header { display: none !important; }
.modal-details-dialog .el-dialog__body   { padding: 0 !important; }
.modal-details-dialog .el-dialog__footer { display: none !important; }
</style>

<style scoped>
.md-root {
  display: flex;
  flex-direction: column;
  font-family: 'Nunito', sans-serif;
  background: #fff;
  border-radius: 24px;
  overflow: hidden;
}

/* ── Hero ── */
.md-hero {
  position: relative;
  height: 240px;
  overflow: hidden;
  background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
  flex-shrink: 0;
}
.md-hero-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.md-hero-fallback {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
}
.md-hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(0deg, rgba(2,44,34,.75) 0%, rgba(5,150,105,.2) 55%, transparent 100%);
}
.md-close-btn {
  position: absolute; top: 14px; right: 14px;
  width: 34px; height: 34px; border-radius: 50%;
  background: rgba(0,0,0,.35); backdrop-filter: blur(6px);
  border: none; color: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s; z-index: 2;
}
.md-close-btn:hover { background: rgba(0,0,0,.55); }
.md-hero-content { position: absolute; bottom: 20px; left: 24px; right: 24px; }
.md-area-pill {
  display: inline-block;
  background: rgba(52,211,153,.25); backdrop-filter: blur(6px);
  border: 1px solid rgba(52,211,153,.45); color: #34D399;
  font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .11em;
  padding: 4px 11px; border-radius: 20px; margin-bottom: 8px;
}
.md-title {
  font-size: 26px; font-weight: 900; color: #fff;
  line-height: 1.2; margin: 0; text-shadow: 0 2px 12px rgba(0,0,0,.3);
}

/* ── Tabs ── */
.md-tabs {
  display: flex; border-bottom: 2px solid #F0FDF4;
  background: #fff; flex-shrink: 0;
}
.md-tab {
  flex: 1; padding: 14px 12px; text-align: center;
  font-family: 'Nunito', sans-serif; font-size: 13px; font-weight: 800;
  color: #A7F3D0; background: none; border: none;
  border-bottom: 2px solid transparent; margin-bottom: -2px;
  cursor: pointer; transition: color .15s, border-color .15s;
}
.md-tab:hover { color: #059669; }
.md-tab--on   { color: #059669; border-bottom-color: #059669; }

/* ── Painéis ── */
.md-panel {
  padding: 24px 28px; display: flex; flex-direction: column;
  gap: 22px; min-height: 320px;
}
.md-panel--local { gap: 16px; }

/* Sobre */
.md-desc { font-size: 15px; color: #065F46; line-height: 1.7; margin: 0; }
.md-row-group { display: flex; flex-direction: column; gap: 8px; }
.md-group-label {
  font-size: 10px; font-weight: 900; color: #10B981;
  text-transform: uppercase; letter-spacing: .1em;
}
.md-area-tag {
  display: inline-block; font-size: 13px; font-weight: 800; color: #047857;
  background: #ECFDF5; border: 1.5px solid #A7F3D0;
  padding: 6px 16px; border-radius: 50px; width: fit-content;
}
.md-chips { display: flex; flex-wrap: wrap; gap: 7px; }
.md-chip {
  background: #D1FAE5; color: #047857;
  font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 50px;
}
.md-empty { font-size: 13px; color: #A7F3D0; font-style: italic; }

/* Página personalizada */
.md-page-card {
  display: flex; align-items: center; gap: 14px;
  background: linear-gradient(135deg, #F0FDF4, #ECFDF5);
  border: 2px solid #D1FAE5; border-radius: 16px; padding: 16px 20px;
}
.md-page-card-icon {
  width: 40px; height: 40px; border-radius: 12px;
  background: linear-gradient(135deg, #059669, #34D399);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.md-page-card-text { flex: 1; }
.md-page-card-title { font-size: 14px; font-weight: 800; color: #022C22; }
.md-page-card-sub   { font-size: 12px; font-weight: 600; color: #059669; margin-top: 2px; }
.md-page-card-btn {
  background: linear-gradient(135deg, #059669, #047857);
  color: #fff; text-decoration: none; font-size: 13px; font-weight: 900;
  padding: 9px 18px; border-radius: 10px; white-space: nowrap; flex-shrink: 0;
  display: inline-flex; align-items: center;
}

/* Localização */
.md-loc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
.md-loc-item { background: #F0FDF4; border-radius: 12px; padding: 12px 14px; }
.md-loc-k {
  font-size: 10px; font-weight: 800; color: #10B981;
  text-transform: uppercase; letter-spacing: .07em; margin-bottom: 3px;
}
.md-loc-v { font-size: 14px; font-weight: 700; color: #022C22; }

.md-map {
  height: 280px; border-radius: 16px; overflow: hidden;
  border: 2px solid #D1FAE5; z-index: 0;
}

.md-maps-btn {
  display: flex; align-items: center; gap: 8px;
  width: 100%; padding: 13px 16px;
  background: #F0FDF4; border: 2px solid #D1FAE5; border-radius: 14px;
  font-family: 'Nunito', sans-serif; font-size: 14px; font-weight: 800;
  color: #047857; cursor: pointer; transition: background .15s, border-color .15s;
}
.md-maps-btn:hover { background: #ECFDF5; border-color: #059669; color: #059669; }

/* Contato */
.md-contact-list { display: flex; flex-direction: column; gap: 10px; }
.md-contact-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px; background: #F0FDF4; border-radius: 14px;
}
.md-contact-icon {
  width: 40px; height: 40px; border-radius: 12px;
  background: linear-gradient(135deg, #059669, #34D399);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.md-contact-k {
  font-size: 10px; font-weight: 800; color: #10B981;
  text-transform: uppercase; letter-spacing: .07em; margin-bottom: 2px;
}
.md-contact-v { font-size: 14px; font-weight: 700; color: #022C22; }
.md-contact-link {
  font-size: 14px; font-weight: 700; color: #047857;
  text-decoration: underline; word-break: break-all;
}
.md-contact-link:hover { color: #059669; }

/* ── Footer ── */
.md-footer {
  padding: 16px 28px 24px;
  border-top: 2px solid #F0FDF4; flex-shrink: 0;
}
.md-footer-close {
  width: 100%; background: linear-gradient(135deg, #059669, #047857);
  color: #fff; border: none; border-radius: 14px; padding: 15px;
  font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 900;
  cursor: pointer; transition: opacity .2s, transform .15s;
}
.md-footer-close:hover { opacity: .9; transform: scale(1.01); }
</style>

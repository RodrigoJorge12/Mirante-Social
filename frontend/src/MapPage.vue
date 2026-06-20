<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { PresenterSocialProject } from '@/Presenters/PresenterSocialProject'
import { MapPinIcon, FlameIcon, SearchIcon, ChevronRightIcon, GraduationCap, HeartPulse, Palette, Dumbbell, Leaf, HandHeart, LayoutGrid } from 'lucide-vue-next'

// ─── Dados por bairro ────────────────────────────────────────────────────────
const BAIRRO_COORDS: Record<string, [number, number]> = {
  'Centro': [-22.2817, -42.5311],
  'Conselheiro Paulino': [-22.2750, -42.5280],
  'Olaria': [-22.2900, -42.5350],
  'Cascatinha': [-22.2650, -42.5200],
  'São Geraldo': [-22.2950, -42.5150],
  'Alto': [-22.2700, -42.5400],
  'Mury': [-22.2600, -42.5450],
  'Córrego Dantas': [-22.3000, -42.5100],
}
const CENTER: [number, number] = [-22.2817, -42.5311]

function getCoords(project: any): [number, number] {
  if (project.lat && project.lng) return [project.lat, project.lng]
  const coords = BAIRRO_COORDS[project.district]
  if (coords) return [coords[0] + (Math.random() - 0.5) * 0.005, coords[1] + (Math.random() - 0.5) * 0.005]
  return [CENTER[0] + (Math.random() - 0.5) * 0.02, CENTER[1] + (Math.random() - 0.5) * 0.02]
}

// ─── Cores e ícones por área ──────────────────────────────────────────────────
const AREA_CORES: Record<string, { bg: string; text: string; marker: string; border: string }> = {
  'Educação':           { bg: '#EFF6FF', text: '#1D4ED8', marker: '#2563EB', border: '#BFDBFE' },
  'Cultura':            { bg: '#F5F3FF', text: '#6D28D9', marker: '#7C3AED', border: '#DDD6FE' },
  'Saúde':              { bg: '#FFF1F2', text: '#BE123C', marker: '#E11D48', border: '#FECDD3' },
  'Esporte':            { bg: '#FFFBEB', text: '#B45309', marker: '#D97706', border: '#FDE68A' },
  'Meio Ambiente':      { bg: '#F0FDF4', text: '#065F46', marker: '#059669', border: '#A7F3D0' },
  'Assistência Social': { bg: '#FFF7ED', text: '#C2410C', marker: '#EA580C', border: '#FDBA74' },
}

// SVG paths inline dos ícones Lucide para uso no Leaflet divIcon
const AREA_SVG_PATHS: Record<string, string> = {
  'Educação':
    '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
  'Cultura':
    '<circle cx="13.5" cy="6.5" r=".5" fill="white"/><circle cx="17.5" cy="10.5" r=".5" fill="white"/><circle cx="8.5" cy="7.5" r=".5" fill="white"/><circle cx="6.5" cy="12.5" r=".5" fill="white"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>',
  'Saúde':
    '<path d="M11 2a2 2 0 0 0-2 2v5H4a2 2 0 0 0-2 2v2c0 1.1.9 2 2 2h5v5c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2v-5h5a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-5V4a2 2 0 0 0-2-2h-2z"/>',
  'Esporte':
    '<circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/>',
  'Meio Ambiente':
    '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
  'Assistência Social':
    '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
}

const AREA_ICON_COMPONENTS: Record<string, any> = {
  'Todos': LayoutGrid,
  'Educação': GraduationCap,
  'Cultura': Palette,
  'Saúde': HeartPulse,
  'Esporte': Dumbbell,
  'Meio Ambiente': Leaf,
  'Assistência Social': HandHeart,
}

function getAreaColor(area: string) {
  return AREA_CORES[area] ?? { bg: '#F3F4F6', text: '#374151', marker: '#6B7280', border: '#E5E7EB' }
}

// ─── Carregamento dinâmico do Leaflet via CDN ────────────────────────────────
async function loadLeaflet(): Promise<any> {
  return new Promise((resolve) => {
    if ((window as any).L) { resolve((window as any).L); return }

    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
    document.head.appendChild(link)

    const script = document.createElement('script')
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = () => {
      const heatScript = document.createElement('script')
      heatScript.src = 'https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js'
      heatScript.onload = () => resolve((window as any).L)
      document.head.appendChild(heatScript)
    }
    document.head.appendChild(script)
  })
}

// ─── Ícone de marker SVG ─────────────────────────────────────────────────────
function makeIcon(L: any, area: string) {
  const color = getAreaColor(area).marker
  const svgPaths = AREA_SVG_PATHS[area] || '<circle cx="12" cy="12" r="6"/>'
  const svgHtml = `
    <div style="
      width: 40px; height: 52px;
      display: flex; flex-direction: column; align-items: center;
      cursor: pointer; filter: drop-shadow(0 3px 6px rgba(0,0,0,0.28));
    ">
      <div style="
        width: 38px; height: 38px;
        background: ${color};
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 2.5px solid rgba(255,255,255,0.9);
        display: flex; align-items: center; justify-content: center;
      ">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          width="17" height="17"
          fill="none"
          stroke="white"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          style="transform: rotate(45deg); display: block;"
        >${svgPaths}</svg>
      </div>
      <div style="
        width: 5px; height: 10px;
        background: ${color};
        border-radius: 0 0 4px 4px;
        margin-top: -2px;
        opacity: 0.9;
      "></div>
    </div>
  `
  return L.divIcon({
    html: svgHtml,
    className: '',
    iconSize: [40, 52],
    iconAnchor: [20, 52],
    popupAnchor: [0, -54],
  })
}

// ─── HTML do Popup ───────────────────────────────────────────────────────────
function makePopupHTML(project: any): string {
  const color = getAreaColor(project.activity_area)
  const imgUrl = project.image_path
    ? `${import.meta.env.VITE_API_URL}/storage/${project.image_path}`
    : null

  let audiences: string[] = []
  try {
    audiences = typeof project.target_audiences === 'string'
      ? JSON.parse(project.target_audiences)
      : (project.target_audiences || [])
  } catch { audiences = [] }

  const whatsappNum = project.phone ? project.phone.replace(/\D/g, '') : ''
  const whatsappHref = whatsappNum
    ? `https://wa.me/55${whatsappNum}?text=Olá! Vi o projeto ${encodeURIComponent(project.name)} no Mirante Social e tenho interesse.`
    : '#'
  const mapsHref = project.address
    ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(project.address + ', ' + (project.district || '') + ', ' + (project.city || ''))}`
    : '#'

  const svgPaths = AREA_SVG_PATHS[project.activity_area] || '<circle cx="12" cy="12" r="6"/>'
  const areaIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="${color.text}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:4px;flex-shrink:0;">${svgPaths}</svg>`

  return `
    <div style="font-family: 'Nunito', sans-serif; width: 280px;">
      ${imgUrl ? `
        <div style="
          width: 100%; height: 120px;
          border-radius: 8px 8px 0 0;
          overflow: hidden;
          margin: -1px -1px 12px -1px;
        ">
          <img src="${imgUrl}" style="width:100%;height:100%;object-fit:cover;" />
        </div>
      ` : ''}

      <div style="padding: ${imgUrl ? '0' : '4px'} 0 0 0;">
        <span style="
          display: inline-flex;
          align-items: center;
          background: ${color.bg};
          color: ${color.text};
          font-size: 11px;
          font-weight: 800;
          padding: 3px 10px;
          border-radius: 20px;
          margin-bottom: 8px;
          border: 1px solid ${color.border};
        ">${areaIconSvg}${project.activity_area}</span>

        <h3 style="
          margin: 0 0 6px 0;
          font-size: 15px;
          font-weight: 900;
          color: #022C22;
          line-height: 1.3;
        ">${project.name}</h3>

        ${project.description ? `
          <p style="
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #4B5563;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
          ">${project.description}</p>
        ` : ''}

        <div style="
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 6px;
          margin-bottom: 10px;
        ">
          ${project.district ? `
            <div style="
              background: #F0FDF4;
              border-radius: 8px;
              padding: 7px 10px;
            ">
              <div style="font-size: 10px; color: #059669; font-weight: 800; margin-bottom: 2px;">BAIRRO</div>
              <div style="font-size: 12px; color: #022C22; font-weight: 700;">${project.district}</div>
            </div>
          ` : ''}
          ${audiences.length > 0 ? `
            <div style="
              background: #F0FDF4;
              border-radius: 8px;
              padding: 7px 10px;
            ">
              <div style="font-size: 10px; color: #059669; font-weight: 800; margin-bottom: 2px;">PÚBLICO</div>
              <div style="font-size: 12px; color: #022C22; font-weight: 700;">${audiences.slice(0, 2).join(', ')}</div>
            </div>
          ` : ''}
        </div>

        ${project.phone ? `
          <div style="
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #374151;
            font-weight: 600;
          ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            ${project.phone}
          </div>
        ` : ''}

        <div style="display: flex; gap: 8px;">
          ${whatsappNum ? `
            <a href="${whatsappHref}" target="_blank" style="
              flex: 1;
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 5px;
              padding: 8px 12px;
              background: #10B981;
              color: white;
              border-radius: 8px;
              text-decoration: none;
              font-size: 12px;
              font-weight: 800;
              font-family: 'Nunito', sans-serif;
            ">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              WhatsApp
            </a>
          ` : ''}
          <a href="${mapsHref}" target="_blank" style="
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px 12px;
            background: #F0FDF4;
            color: #047857;
            border: 1.5px solid #D1FAE5;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
          ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#047857" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Como Chegar
          </a>
        </div>
      </div>
    </div>
  `
}

// ─── Estado reativo ───────────────────────────────────────────────────────────
const projects = ref<any[]>([])
const search = ref('')
const selectedArea = ref('Todos')
const selectedId = ref<number | null>(null)
const mode = ref<'pins' | 'heat'>('pins')
const loading = ref(true)
const mapInstance = ref<any>(null)
const markersMap = ref<Map<number, any>>(new Map())
const heatLayer = ref<any>(null)

// ─── Computeds ────────────────────────────────────────────────────────────────
const areas = computed(() => ['Todos', ...Object.keys(AREA_CORES)])

const filteredProjects = computed(() => {
  const term = search.value.toLowerCase()
  return projects.value.filter(p => {
    const matchArea = selectedArea.value === 'Todos' || p.activity_area === selectedArea.value
    const matchTerm = !term ||
      p.name?.toLowerCase().includes(term) ||
      p.district?.toLowerCase().includes(term) ||
      p.city?.toLowerCase().includes(term)
    return matchArea && matchTerm
  })
})

// ─── Aplicar filtros no mapa ──────────────────────────────────────────────────
function applyFilter() {
  if (!mapInstance.value) return
  const visibleIds = new Set(filteredProjects.value.map(p => p.id))

  markersMap.value.forEach((marker, id) => {
    if (mode.value === 'pins') {
      if (visibleIds.has(id)) {
        if (!mapInstance.value.hasLayer(marker)) mapInstance.value.addLayer(marker)
      } else {
        if (mapInstance.value.hasLayer(marker)) mapInstance.value.removeLayer(marker)
      }
    } else {
      if (mapInstance.value.hasLayer(marker)) mapInstance.value.removeLayer(marker)
    }
  })

  if (mode.value === 'heat' && heatLayer.value) {
    const points = filteredProjects.value.map(p => {
      const [lat, lng] = getCoords(p)
      return [lat, lng, 1.0]
    })
    heatLayer.value.setLatLngs(points)
  }
}

watch([search, selectedArea], () => applyFilter())

// ─── Mudar modo pins / calor ──────────────────────────────────────────────────
function setMode(m: 'pins' | 'heat') {
  mode.value = m
  applyFilter()
  if (m === 'heat') {
    if (heatLayer.value && !mapInstance.value.hasLayer(heatLayer.value)) {
      heatLayer.value.addTo(mapInstance.value)
    }
  } else {
    if (heatLayer.value && mapInstance.value.hasLayer(heatLayer.value)) {
      mapInstance.value.removeLayer(heatLayer.value)
    }
  }
}

// ─── Selecionar área (chip) ───────────────────────────────────────────────────
function selectArea(area: string) {
  selectedArea.value = area
}

// ─── Focar projeto no mapa ────────────────────────────────────────────────────
function focusProject(project: any) {
  selectedId.value = project.id
  if (!mapInstance.value) return
  const [lat, lng] = getCoords(project)
  if (mode.value !== 'pins') setMode('pins')
  mapInstance.value.setView([lat, lng], 15, { animate: true })
  setTimeout(() => {
    const marker = markersMap.value.get(project.id)
    if (marker) marker.openPopup()
  }, 400)
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(async () => {
  // 1. Carregar projetos da API
  const presenter = new PresenterSocialProject()
  try {
    const result = await presenter.GetAllProjects()
    projects.value = result?.data ?? []
  } catch {
    projects.value = []
  }
  loading.value = false

  // 2. Carregar Leaflet via CDN
  await loadLeaflet()
  const L = (window as any).L

  // 3. Inicializar mapa
  const map = L.map('map-container', { zoomControl: false }).setView(CENTER, 13)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map)
  L.control.zoom({ position: 'bottomright' }).addTo(map)
  mapInstance.value = map

  // 4. Adicionar markers para cada projeto
  projects.value.forEach(p => {
    const [lat, lng] = getCoords(p)
    const marker = L.marker([lat, lng], { icon: makeIcon(L, p.activity_area) })
      .addTo(map)
      .bindPopup(makePopupHTML(p), { maxWidth: 300, minWidth: 280 })
    marker.on('click', () => {
      selectedId.value = p.id
    })
    markersMap.value.set(p.id, marker)
  })

  // 5. Criar heatLayer (inicia sem adicionar ao mapa)
  const points = projects.value.map(p => {
    const [lat, lng] = getCoords(p)
    return [lat, lng, 1.0]
  })
  if (L.heatLayer) {
    heatLayer.value = L.heatLayer(points, {
      radius: 45,
      blur: 30,
      maxZoom: 17,
      gradient: { 0.4: '#3B82F6', 0.6: '#10B981', 0.8: '#FBBF24', 1: '#EF4444' },
    })
  }

  setTimeout(() => map.invalidateSize(), 200)
})

onUnmounted(() => {
  if (mapInstance.value) {
    mapInstance.value.remove()
    mapInstance.value = null
  }
})
</script>

<template>
  <div class="map-page">
    <!-- ── Sidebar ─────────────────────────────────────────────────────────── -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-title">Projetos Sociais</h2>
        <span class="sidebar-count">
          {{ filteredProjects.length }} projeto{{ filteredProjects.length !== 1 ? 's' : '' }}
        </span>
      </div>

      <!-- Busca -->
      <div class="sidebar-search-wrap">
        <SearchIcon class="search-icon" :size="15" :stroke-width="2.5" />
        <input
          v-model="search"
          class="sidebar-search"
          placeholder="Buscar projeto ou bairro..."
        />
      </div>

      <!-- Chips de área -->
      <div class="chips-wrap">
        <button
          v-for="area in areas"
          :key="area"
          :class="['chip', selectedArea === area ? 'chip-on' : '']"
          :style="selectedArea === area && area !== 'Todos'
            ? { background: getAreaColor(area).bg, color: getAreaColor(area).text, borderColor: getAreaColor(area).text + '40' }
            : {}"
          @click="selectArea(area)"
        >
          <component :is="AREA_ICON_COMPONENTS[area]" :size="12" :stroke-width="2.5" />
          {{ area }}
        </button>
      </div>

      <!-- Estado de carregamento -->
      <div v-if="loading" class="sidebar-loading">
        <div class="loading-spinner"></div>
        <span>Carregando projetos...</span>
      </div>

      <!-- Lista de projetos -->
      <div class="sidebar-list" v-else>
        <div v-if="filteredProjects.length === 0" class="empty-list">
          <SearchIcon class="empty-icon" :size="36" :stroke-width="1.5" />
          <p>Nenhum projeto encontrado</p>
          <span class="empty-hint">Tente ajustar os filtros</span>
        </div>

        <div
          v-for="project in filteredProjects"
          :key="project.id"
          :class="['list-item', selectedId === project.id ? 'list-item-sel' : '']"
          :style="{ borderLeft: `3px solid ${getAreaColor(project.activity_area).marker}` }"
          @click="focusProject(project)"
        >
          <div
            class="list-icon"
            :style="{
              background: getAreaColor(project.activity_area).bg,
              color: getAreaColor(project.activity_area).text,
              border: `1.5px solid ${getAreaColor(project.activity_area).border}`,
            }"
          >
            <component
              :is="AREA_ICON_COMPONENTS[project.activity_area] || MapPinIcon"
              :size="17"
              :stroke-width="2"
            />
          </div>
          <div class="list-info">
            <div class="list-name">{{ project.name }}</div>
            <div class="list-meta">
              <MapPinIcon :size="11" :stroke-width="2.5" style="display:inline;vertical-align:middle;margin-right:2px" />
              {{ project.district || project.city }}
            </div>
            <span
              class="list-badge"
              :style="{
                background: getAreaColor(project.activity_area).bg,
                color: getAreaColor(project.activity_area).text,
                border: `1px solid ${getAreaColor(project.activity_area).border}`,
              }"
            >
              {{ project.activity_area }}
            </span>
          </div>
          <ChevronRightIcon class="list-arrow" :size="16" :stroke-width="2.5" />
        </div>
      </div>
    </aside>

    <!-- ── Mapa ───────────────────────────────────────────────────────────── -->
    <div class="map-wrap">
      <div id="map-container" class="leaflet-container"></div>

      <!-- Toggle Pins / Calor -->
      <div class="map-toggle">
        <button
          :class="['toggle-btn', mode === 'pins' ? 'toggle-btn-on' : '']"
          @click="setMode('pins')"
        >
          <MapPinIcon :size="14" :stroke-width="2.5" /> Pins
        </button>
        <button
          :class="['toggle-btn', mode === 'heat' ? 'toggle-btn-on' : '']"
          @click="setMode('heat')"
        >
          <FlameIcon :size="14" :stroke-width="2.5" /> Calor
        </button>
      </div>

      <!-- Info de modo calor -->
      <div v-if="mode === 'heat'" class="heat-legend">
        <span class="heat-legend-title">Concentração de projetos</span>
        <div class="heat-legend-bar"></div>
        <div class="heat-legend-labels">
          <span>Baixa</span>
          <span>Alta</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ── Layout geral ────────────────────────────────────────────────────────────── */
.map-page {
  display: flex;
  height: calc(100vh - 68px);
  margin-top: 68px;
  font-family: 'Nunito', sans-serif;
  overflow: hidden;
}

/* ── Sidebar ─────────────────────────────────────────────────────────────────── */
.sidebar {
  width: 320px;
  flex-shrink: 0;
  border-right: 1.5px solid #D1FAE5;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: #ffffff;
  box-shadow: 2px 0 12px rgba(16, 185, 129, 0.06);
}

.sidebar-header {
  padding: 20px 16px 14px;
  border-bottom: 1.5px solid #F0FDF4;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
  background: linear-gradient(135deg, #F0FDF4 0%, #ffffff 100%);
}

.sidebar-title {
  font-size: 17px;
  font-weight: 900;
  color: #022C22;
  margin: 0;
  letter-spacing: -0.3px;
}

.sidebar-count {
  font-size: 11px;
  font-weight: 800;
  color: #059669;
  background: #D1FAE5;
  padding: 4px 10px;
  border-radius: 20px;
  letter-spacing: 0.2px;
}

/* ── Busca ────────────────────────────────────────────────────────────────────── */
.sidebar-search-wrap {
  padding: 12px 12px 8px;
  flex-shrink: 0;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 22px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  margin-top: 6px;
  color: #9CA3AF;
}

.sidebar-search {
  width: 100%;
  box-sizing: border-box;
  padding: 10px 12px 10px 36px;
  border: 1.5px solid #D1FAE5;
  border-radius: 10px;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #022C22;
  background: #F0FDF4;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.sidebar-search::placeholder {
  color: #9CA3AF;
  font-weight: 600;
}

.sidebar-search:focus {
  border-color: #10B981;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* ── Chips de área ────────────────────────────────────────────────────────────── */
.chips-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  padding: 4px 12px 12px;
  flex-shrink: 0;
  border-bottom: 1.5px solid #F0FDF4;
}

.chip {
  padding: 5px 11px;
  border-radius: 20px;
  border: 1.5px solid #D1FAE5;
  background: #ffffff;
  color: #374151;
  font-family: 'Nunito', sans-serif;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
  white-space: nowrap;
}

.chip:hover {
  border-color: #10B981;
  background: #F0FDF4;
  color: #065F46;
}

.chip-on {
  background: #10B981;
  border-color: #10B981;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.chip-on:hover {
  background: #059669;
  border-color: #059669;
  color: #ffffff;
}

/* ── Estado de carregamento ──────────────────────────────────────────────────── */
.sidebar-loading {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  color: #059669;
  font-size: 14px;
  font-weight: 700;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #D1FAE5;
  border-top-color: #10B981;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ── Lista de projetos ────────────────────────────────────────────────────────── */
.sidebar-list {
  flex: 1;
  overflow-y: auto;
  padding: 8px 0;
}

.sidebar-list::-webkit-scrollbar {
  width: 4px;
}

.sidebar-list::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-list::-webkit-scrollbar-thumb {
  background: #D1FAE5;
  border-radius: 4px;
}

.sidebar-list::-webkit-scrollbar-thumb:hover {
  background: #10B981;
}

/* ── Estado vazio ─────────────────────────────────────────────────────────────── */
.empty-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
  gap: 8px;
}

.empty-icon {
  color: #D1FAE5;
  opacity: 0.8;
}

.empty-list p {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  color: #374151;
}

.empty-hint {
  font-size: 13px;
  color: #9CA3AF;
  font-weight: 600;
}

/* ── Item da lista ────────────────────────────────────────────────────────────── */
.list-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 11px 12px 11px;
  cursor: pointer;
  transition: background 0.15s;
  border-bottom: 1px solid #F9FAFB;
  position: relative;
}

.list-item:hover {
  background: #F0FDF4;
}

.list-item-sel {
  background: #ECFDF5;
}

.list-item-sel:hover {
  background: #ECFDF5;
}

.list-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.list-info {
  flex: 1;
  min-width: 0;
}

.list-name {
  font-size: 13px;
  font-weight: 800;
  color: #022C22;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 3px;
}

.list-meta {
  font-size: 11px;
  font-weight: 600;
  color: #6B7280;
  margin-bottom: 5px;
}

.list-badge {
  display: inline-block;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 20px;
}

.list-arrow {
  color: #D1FAE5;
  flex-shrink: 0;
  transition: color 0.15s;
}

.list-item:hover .list-arrow,
.list-item-sel .list-arrow {
  color: #10B981;
}

/* ── Área do mapa ─────────────────────────────────────────────────────────────── */
.map-wrap {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.leaflet-container {
  width: 100%;
  height: 100%;
}

/* ── Toggle Pins / Calor ──────────────────────────────────────────────────────── */
.map-toggle {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 1000;
  display: flex;
  gap: 0;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  border: 1.5px solid #E5E7EB;
  overflow: hidden;
}

.toggle-btn {
  padding: 9px 16px;
  background: white;
  border: none;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  font-size: 13px;
  font-weight: 800;
  color: #6B7280;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.toggle-btn:first-child {
  border-right: 1.5px solid #E5E7EB;
}

.toggle-btn:hover {
  background: #F0FDF4;
  color: #059669;
}

.toggle-btn-on {
  background: #10B981 !important;
  color: white !important;
}

.toggle-btn-on:hover {
  background: #059669 !important;
}

/* ── Legenda do heatmap ───────────────────────────────────────────────────────── */
.heat-legend {
  position: absolute;
  bottom: 32px;
  right: 16px;
  z-index: 1000;
  background: white;
  border-radius: 10px;
  padding: 10px 14px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  border: 1.5px solid #E5E7EB;
  min-width: 160px;
}

.heat-legend-title {
  display: block;
  font-size: 11px;
  font-weight: 800;
  color: #374151;
  margin-bottom: 8px;
  text-align: center;
}

.heat-legend-bar {
  height: 8px;
  border-radius: 4px;
  background: linear-gradient(to right, #3B82F6, #10B981, #FBBF24, #EF4444);
  margin-bottom: 4px;
}

.heat-legend-labels {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  font-weight: 700;
  color: #6B7280;
}

/* ── Overrides do Leaflet para o popup ───────────────────────────────────────── */
:deep(.leaflet-popup-content-wrapper) {
  border-radius: 12px !important;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
  border: 1.5px solid #E5E7EB !important;
  padding: 0 !important;
  overflow: hidden;
}

:deep(.leaflet-popup-content) {
  margin: 14px !important;
  width: auto !important;
}

:deep(.leaflet-popup-tip) {
  background: white !important;
}

:deep(.leaflet-popup-close-button) {
  color: #374151 !important;
  font-size: 18px !important;
  padding: 8px 10px !important;
  font-weight: 900 !important;
}
</style>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from "vue"
import { ElForm, ElFormItem, ElInput, ElMessage } from "element-plus"
import type { FormInstance, FormRules } from "element-plus"
import MiranteSocialButton from "./components/MiranteSocialButton.vue"
import { PresenterSocialProject } from "./Presenters/PresenterSocialProject"
import router from "./router"
import { ImageIcon, MapPinIcon, CheckIcon } from "lucide-vue-next"

// ── Upload de imagem ──
const previewUrl = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
function triggerUpload() { fileInput.value?.click() }
function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  form.image = file
  previewUrl.value = URL.createObjectURL(file)
}

// ── Mapa ──
const pinLat = ref<number | null>(null)
const pinLng = ref<number | null>(null)
const mapInstance = ref<any>(null)
const pinMarker = ref<any>(null)

async function loadLeaflet(): Promise<void> {
  if ((window as any).L) return
  await new Promise<void>((resolve) => {
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
    document.head.appendChild(link)
    const script = document.createElement('script')
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = () => resolve()
    document.head.appendChild(script)
  })
}

async function initMap() {
  await loadLeaflet()
  const L = (window as any).L
  if (mapInstance.value) return
  const map = L.map('mini-map', { zoomControl: true }).setView([-22.2817, -42.5311], 13)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map)
  mapInstance.value = map
  map.on('click', (e: any) => {
    pinLat.value = e.latlng.lat
    pinLng.value = e.latlng.lng
    if (pinMarker.value) {
      pinMarker.value.setLatLng(e.latlng)
    } else {
      pinMarker.value = L.marker(e.latlng, { draggable: true }).addTo(map)
      pinMarker.value.on('dragend', () => {
        const pos = pinMarker.value.getLatLng()
        pinLat.value = pos.lat
        pinLng.value = pos.lng
      })
    }
  })
  setTimeout(() => map.invalidateSize(), 150)
}

onUnmounted(() => {
  if (mapInstance.value) { mapInstance.value.remove(); mapInstance.value = null }
})

// ── CEP ──
const cepLoading = ref(false)

async function geocodeAndCenter(district: string, city: string, state: string) {
  const parts = [district, city, state, 'Brasil'].filter(Boolean)
  const query = parts.join(', ')
  const encoded = encodeURIComponent(query).replace(/%2C/g, ',')
  try {
    const resp = await fetch(`https://photon.komoot.io/api/?q=${encoded}&limit=1&lang=default`)
    const data = await resp.json()
    const f = data.features?.[0]
    if (f && mapInstance.value) {
      const lat = f.geometry.coordinates[1]
      const lng = f.geometry.coordinates[0]
      pinLat.value = lat
      pinLng.value = lng
      mapInstance.value.setView([lat, lng], 16, { animate: true })
      const L = (window as any).L
      if (pinMarker.value) {
        pinMarker.value.setLatLng([lat, lng])
      } else {
        pinMarker.value = L.marker([lat, lng], { draggable: true }).addTo(mapInstance.value)
        pinMarker.value.on('dragend', () => {
          const pos = pinMarker.value.getLatLng()
          pinLat.value = pos.lat
          pinLng.value = pos.lng
        })
      }
    }
  } catch {}
}

async function fetchCep(raw: string) {
  const cep = raw.replace(/\D/g, '')
  if (cep.length !== 8) return
  cepLoading.value = true
  try {
    const resp = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
    const data = await resp.json()
    if (data.erro) return
    form.address  = data.logradouro || form.address
    form.district = data.bairro     || form.district
    form.city     = data.localidade || form.city
    form.state    = data.uf         || form.state
    await geocodeAndCenter(data.bairro, data.localidade, data.uf)
  } catch {}
  finally { cepLoading.value = false }
}

// ── Etapas ──
const currentStep = ref(0)

const steps = [
  { label: 'Identidade',  sub: 'Nome, descrição e imagem' },
  { label: 'Localização', sub: 'CEP, endereço e mapa' },
  { label: 'Contato',     sub: 'Telefone, redes e público' },
  { label: 'Publicação',  sub: 'Cor visual e página pública' },
]

// Campos obrigatórios por etapa — usados para validação parcial
const stepFields: Record<number, string[]> = {
  0: ['name', 'description', 'image'],
  1: ['zipCode', 'address', 'city', 'state'],
  2: ['phone', 'activityArea', 'targetAudiences'],
  3: ['visualColor'],
}

async function goNext() {
  if (!formRef.value) return
  const fields = stepFields[currentStep.value]
  try {
    await formRef.value.validateField(fields)
  } catch {
    return // tem erro — não avança
  }
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
    if (currentStep.value === 1) {
      setTimeout(() => initMap(), 50)
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

function goBack() {
  if (currentStep.value > 0) {
    currentStep.value--
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const isLastStep = computed(() => currentStep.value === steps.length - 1)

// ── Página personalizada ──
const wantsPage = ref(false)
const selectedTemplate = ref("")

// ── Galeria (upload no cadastro) ──
const galleryFiles = ref<File[]>([])
const galleryPreviews = ref<string[]>([])
const galleryInput = ref<HTMLInputElement | null>(null)

function triggerGalleryUpload() { galleryInput.value?.click() }

function handleGalleryChange(e: Event) {
  const target = e.target as HTMLInputElement
  const files = Array.from(target.files || [])
  const remaining = 6 - galleryFiles.value.length
  const toAdd = files.slice(0, remaining)
  toAdd.forEach(f => {
    galleryFiles.value.push(f)
    galleryPreviews.value.push(URL.createObjectURL(f))
  })
  target.value = ""
}

function removeGalleryImage(i: number) {
  URL.revokeObjectURL(galleryPreviews.value[i])
  galleryFiles.value.splice(i, 1)
  galleryPreviews.value.splice(i, 1)
}

// ── Formulário ──
interface CompanyForm {
  name: string; description: string; address: string; district: string
  city: string; state: string; zipCode: string; phone: string
  websiteUrl: string; visualColor: string; activityArea: string
  targetAudiences: string[]; image: File | null
  instagramUrl: string; facebookUrl: string; operatingHours: string; mission: string
}

const formRef = ref<FormInstance>()
const form = reactive<CompanyForm>({
  name: "", description: "", address: "", district: "",
  city: "", state: "", zipCode: "", phone: "",
  websiteUrl: "", visualColor: "#059669", activityArea: "",
  targetAudiences: [], image: null,
  instagramUrl: "", facebookUrl: "", operatingHours: "", mission: "",
})

const rules: FormRules<CompanyForm> = {
  name:            [{ required: true, message: "Nome obrigatório",              trigger: "blur" }],
  description:     [{ required: true, message: "Descrição obrigatória",         trigger: "blur" }],
  address:         [{ required: true, message: "Endereço obrigatório",          trigger: "blur" }],
  city:            [{ required: true, message: "Cidade obrigatória",            trigger: "blur" }],
  zipCode:         [{ required: true, message: "CEP obrigatório",               trigger: "blur" }],
  phone:           [{ required: true, message: "Telefone obrigatório",          trigger: "blur" }],
  visualColor:     [{ required: true, message: "Cor principal obrigatória",     trigger: "blur" }],
  activityArea:    [{ required: true, message: "Área de atividade obrigatória", trigger: "blur" }],
  targetAudiences: [{ required: true, message: "Público-Alvo obrigatório",      trigger: "blur" }],
  image:           [{ required: true, message: "A imagem é obrigatória",        trigger: "change" }],
  state:           [{ required: true, min: 2, max: 2, message: "UF deve ter 2 caracteres", trigger: "blur" }],
}

const sendDataCreateSocialProject = async () => {
  if (!formRef.value) return
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  try {
    const presenter = new PresenterSocialProject()
    const status = await presenter.CreateSocialProject(
      form.name, form.description, form.address, form.district,
      form.city, form.state, form.zipCode, form.phone, form.websiteUrl,
      form.visualColor, form.activityArea, form.targetAudiences,
      form.image, wantsPage.value, selectedTemplate.value,
      pinLat.value, pinLng.value,
      form.instagramUrl, form.facebookUrl, form.operatingHours, form.mission,
      galleryFiles.value.length ? galleryFiles.value : undefined
    )
    if (status && status.success) {
      ElMessage.success("Projeto cadastrado com sucesso!")
      router.push("/")
      formRef.value.resetFields()
    } else {
      ElMessage.error("Não foi possível cadastrar o projeto.")
    }
  } catch {
    ElMessage.error("Ocorreu um erro inesperado. Tente novamente.")
  }
}
</script>

<template>
  <div class="page-bg">
    <div class="page-wrap">

      <!-- ── SIDEBAR ── -->
      <aside class="sidebar">
        <div class="sb-inner">
          <div class="sb-logo">Mirante Social</div>
          <div class="sb-title">Cadastrar Novo Projeto</div>
          <p class="sb-sub">Compartilhe sua iniciativa com a comunidade de Nova Friburgo</p>

          <div class="sb-divider"></div>

          <div class="sb-steps">
            <div
              v-for="(s, i) in steps"
              :key="s.label"
              :class="['sb-step', {
                'sb-step--done':   i < currentStep,
                'sb-step--active': i === currentStep,
                'sb-step--future': i > currentStep
              }]"
            >
              <div class="sb-step-dot">
                <CheckIcon v-if="i < currentStep" :size="11" :stroke-width="3" />
                <span v-else>{{ i + 1 }}</span>
              </div>
              <div class="sb-step-text">
                <span class="sb-step-name">{{ s.label }}</span>
                <span class="sb-step-sub">{{ s.sub }}</span>
              </div>
            </div>
          </div>

          <div class="sb-divider"></div>
          <p class="sb-note">Os dados ficam visíveis na plataforma imediatamente após o cadastro.</p>
        </div>
      </aside>

      <!-- ── FORMULÁRIO ── -->
      <div class="form-col">
        <el-form ref="formRef" :model="form" :rules="rules" label-position="top" status-icon>

          <!-- ETAPA 1: Identidade -->
          <div v-show="currentStep === 0" class="step-body">
            <h3 class="step-title">Identidade do Projeto</h3>

            <el-form-item prop="name">
              <template #label><span class="field-label">Nome do Projeto</span></template>
              <el-input v-model="form.name" placeholder="Como o projeto é chamado?" class="ja-input" />
            </el-form-item>

            <el-form-item prop="description">
              <template #label><span class="field-label">Descrição</span></template>
              <el-input type="textarea" v-model="form.description"
                placeholder="Conte sobre o projeto, seus objetivos e o impacto que gera..."
                :rows="4" :resize="'none'" class="ja-input" />
            </el-form-item>

            <el-form-item>
              <template #label><span class="field-label">Frase de Missão <span class="badge-optional">Opcional</span></span></template>
              <el-input v-model="form.mission" placeholder="Em uma frase, qual é o propósito do projeto?" class="ja-input" maxlength="300" show-word-limit />
            </el-form-item>

            <el-form-item prop="image">
              <template #label><span class="field-label">Imagem do Projeto</span></template>
              <div class="img-upload" :class="{ 'img-upload--has': previewUrl }" @click="triggerUpload">
                <input ref="fileInput" type="file" accept="image/*" style="display:none" @change="handleFileChange" />
                <img v-if="previewUrl" :src="previewUrl" class="img-preview" />
                <div v-else class="img-placeholder">
                  <ImageIcon class="img-icon" :size="32" :stroke-width="1.5" />
                  <span class="img-label">Clique para adicionar uma imagem</span>
                  <span class="img-hint">JPG, PNG ou WEBP · máx. 5MB</span>
                </div>
              </div>
            </el-form-item>
          </div>

          <!-- ETAPA 2: Localização -->
          <div v-show="currentStep === 1" class="step-body">
            <h3 class="step-title">Localização</h3>

            <el-form-item prop="zipCode">
              <template #label>
                <span class="field-label">
                  CEP
                  <span v-if="cepLoading" class="badge-loading">buscando...</span>
                </span>
              </template>
              <el-input v-model="form.zipCode" placeholder="00000-000" class="ja-input" @input="fetchCep(form.zipCode)" />
            </el-form-item>

            <div class="f-row">
              <el-form-item prop="address">
                <template #label><span class="field-label">Endereço</span></template>
                <el-input v-model="form.address" placeholder="Rua e número" class="ja-input" />
              </el-form-item>
              <el-form-item>
                <template #label><span class="field-label">Bairro <span class="badge-optional">Opcional</span></span></template>
                <el-input v-model="form.district" placeholder="Bairro" class="ja-input" />
              </el-form-item>
            </div>

            <div class="f-row">
              <el-form-item prop="city">
                <template #label><span class="field-label">Cidade</span></template>
                <el-input v-model="form.city" placeholder="Cidade" class="ja-input" />
              </el-form-item>
              <el-form-item prop="state">
                <template #label><span class="field-label">UF</span></template>
                <el-input v-model="form.state" placeholder="RJ" maxlength="2" class="ja-input" />
              </el-form-item>
            </div>

            <div class="map-hint" :class="{ 'map-hint--pinned': pinLat }">
              <MapPinIcon v-if="!pinLat" class="map-hint-icon" :size="16" :stroke-width="2" />
              <CheckIcon v-else class="map-hint-icon map-hint-icon--ok" :size="16" :stroke-width="2.5" />
              <span v-if="!pinLat">Após preencher o CEP, o mapa centraliza. Clique no local exato.</span>
              <span v-else><strong>Localização marcada.</strong> Arraste o pin para ajustar.</span>
            </div>
            <div id="mini-map" class="mini-map"></div>
          </div>

          <!-- ETAPA 3: Contato & Redes -->
          <div v-show="currentStep === 2" class="step-body">
            <h3 class="step-title">Contato &amp; Redes</h3>

            <div class="f-row">
              <el-form-item prop="phone">
                <template #label><span class="field-label">Telefone</span></template>
                <el-input v-model="form.phone" placeholder="(22) 99999-0000" class="ja-input" />
              </el-form-item>
              <el-form-item>
                <template #label><span class="field-label">Website <span class="badge-optional">Opcional</span></span></template>
                <el-input v-model="form.websiteUrl" placeholder="https://site.com" class="ja-input" />
              </el-form-item>
            </div>

            <div class="f-row">
              <el-form-item>
                <template #label><span class="field-label">Instagram <span class="badge-optional">Opcional</span></span></template>
                <el-input v-model="form.instagramUrl" placeholder="https://instagram.com/seuprojeto" class="ja-input" />
              </el-form-item>
              <el-form-item>
                <template #label><span class="field-label">Facebook <span class="badge-optional">Opcional</span></span></template>
                <el-input v-model="form.facebookUrl" placeholder="https://facebook.com/seuprojeto" class="ja-input" />
              </el-form-item>
            </div>

            <el-form-item>
              <template #label><span class="field-label">Horários de Funcionamento <span class="badge-optional">Opcional</span></span></template>
              <el-input type="textarea" v-model="form.operatingHours" placeholder="Ex: Segunda, quarta e sexta das 14h às 17h" :rows="2" :resize="'none'" class="ja-input" />
            </el-form-item>

            <el-form-item prop="activityArea">
              <template #label><span class="field-label">Área de Atuação</span></template>
              <div class="audience-chips">
                <button
                  v-for="area in ['Educação', 'Saúde', 'Cultura', 'Esporte', 'Meio Ambiente', 'Assistência Social']"
                  :key="area" type="button"
                  :class="['audience-chip', { 'audience-chip--on': form.activityArea === area }]"
                  @click="form.activityArea = form.activityArea === area ? '' : area"
                >{{ area }}</button>
              </div>
            </el-form-item>

            <el-form-item prop="targetAudiences">
              <template #label><span class="field-label">Públicos-Alvo</span></template>
              <div class="audience-chips">
                <button
                  v-for="opt in ['Crianças', 'Adolescentes', 'Adultos', 'Idosos']"
                  :key="opt" type="button"
                  :class="['audience-chip', { 'audience-chip--on': form.targetAudiences.includes(opt) }]"
                  @click="form.targetAudiences.includes(opt)
                    ? form.targetAudiences.splice(form.targetAudiences.indexOf(opt), 1)
                    : form.targetAudiences.push(opt)"
                >{{ opt }}</button>
              </div>
            </el-form-item>
          </div>

          <!-- ETAPA 4: Publicação -->
          <div v-show="currentStep === 3" class="step-body">
            <h3 class="step-title">Identidade Visual &amp; Publicação</h3>

            <div class="page-toggle" :class="{ 'page-toggle--active': wantsPage }" @click="wantsPage = !wantsPage">
              <div class="page-toggle-info">
                <span class="page-toggle-title">Deseja uma página pública exclusiva?</span>
                <span class="page-toggle-desc">Crie uma vitrine digital completa para o seu projeto</span>
              </div>
              <div class="page-toggle-switch" :class="{ 'page-toggle-switch--on': wantsPage }">
                <div class="page-toggle-knob"></div>
              </div>
            </div>

            <div v-if="wantsPage" class="template-section">

              <!-- Cor principal -->
              <el-form-item prop="visualColor" style="margin-bottom: 20px">
                <template #label><span class="field-label">Cor Principal do Projeto</span></template>
                <p class="field-hint">Define a identidade visual de toda a sua página pública.</p>
                <div class="color-field">
                  <div class="color-swatch" :style="{ background: form.visualColor }"></div>
                  <input type="color" v-model="form.visualColor" class="color-native" title="Escolha a cor do projeto" />
                  <span class="color-value">{{ form.visualColor }}</span>
                </div>
              </el-form-item>

              <p class="template-label">Escolha o layout da sua página:</p>
              <div class="template-cards">

                <!-- TEMPLATE 1 -->
                <div class="tmpl-card" :class="{ active: selectedTemplate === 'template1' }" @click="selectedTemplate = 'template1'">
                  <div class="tmpl-check">✓</div>
                  <!-- Preview fiel ao Template 1 -->
                  <div class="tmpl-preview tmpl-preview--1">
                    <!-- Nav bar -->
                    <div class="tp-nav" :style="{ borderBottomColor: form.visualColor + '22' }">
                      <div class="tp-nav-dot" :style="{ background: form.visualColor }"></div>
                      <div class="tp-nav-pill" :style="{ background: form.visualColor }"></div>
                    </div>
                    <!-- Hero fullbleed -->
                    <div class="tp1-hero" :style="{ background: `linear-gradient(160deg, ${form.visualColor}dd, ${form.visualColor}77)` }">
                      <div class="tp1-hero-badge"></div>
                      <div class="tp1-hero-title"></div>
                      <div class="tp1-hero-sub"></div>
                      <div class="tp1-hero-btns">
                        <div class="tp1-btn-main" :style="{ background: '#fff' }"></div>
                        <div class="tp1-btn-ghost"></div>
                      </div>
                    </div>
                    <!-- Sobre (2 colunas) -->
                    <div class="tp1-sobre">
                      <div class="tp1-sobre-left">
                        <div class="tp-eyebrow" :style="{ background: form.visualColor }"></div>
                        <div class="tp-line tp-line--title"></div>
                        <div class="tp-line"></div>
                        <div class="tp-line tp-line--short"></div>
                      </div>
                      <div class="tp1-contact-card" :style="{ borderColor: form.visualColor + '44' }">
                        <div class="tp1-cc-header" :style="{ background: form.visualColor }"></div>
                        <div class="tp1-cc-row"></div>
                        <div class="tp1-cc-row"></div>
                        <div class="tp1-cc-btn" :style="{ background: form.visualColor + '22' }"></div>
                      </div>
                    </div>
                    <!-- Faixa de impacto -->
                    <div class="tp1-impact" :style="{ background: form.visualColor }">
                      <div class="tp-line tp-line--white tp-line--short"></div>
                      <div class="tp1-impact-btn"></div>
                    </div>
                    <!-- Galeria -->
                    <div class="tp1-gallery">
                      <div class="tp1-gal-featured" :style="{ background: form.visualColor + '33' }"></div>
                      <div class="tp1-gal-small">
                        <div class="tp1-gal-item" :style="{ background: form.visualColor + '22' }"></div>
                        <div class="tp1-gal-item" :style="{ background: form.visualColor + '22' }"></div>
                        <div class="tp1-gal-item" :style="{ background: form.visualColor + '22' }"></div>
                        <div class="tp1-gal-item" :style="{ background: form.visualColor + '22' }"></div>
                      </div>
                    </div>
                    <!-- Info cards -->
                    <div class="tp1-info-grid">
                      <div class="tp1-info-card" v-for="i in 4" :key="i">
                        <div class="tp1-info-icon" :style="{ background: form.visualColor + '22' }"></div>
                        <div class="tp-lines">
                          <div class="tp-line tp-line--xs"></div>
                          <div class="tp-line tp-line--short"></div>
                        </div>
                      </div>
                    </div>
                    <!-- Footer -->
                    <div class="tp1-footer" :style="{ background: form.visualColor + 'cc' }"></div>
                  </div>
                  <div class="tmpl-info">
                    <span class="tmpl-name">Template 1 — Imersivo</span>
                    <span class="tmpl-desc">Hero fullbleed · Contato lateral · Galeria destacada</span>
                  </div>
                </div>

                <!-- TEMPLATE 2 -->
                <div class="tmpl-card" :class="{ active: selectedTemplate === 'template2' }" @click="selectedTemplate = 'template2'">
                  <div class="tmpl-check">✓</div>
                  <!-- Preview fiel ao Template 2 -->
                  <div class="tmpl-preview tmpl-preview--2">
                    <!-- Header hero com fundo -->
                    <div class="tp2-header" :style="{ background: `linear-gradient(160deg, ${form.visualColor}ee, ${form.visualColor}88)` }">
                      <div class="tp2-nav">
                        <div class="tp2-nav-dot"></div>
                        <div class="tp2-nav-pill"></div>
                      </div>
                      <div class="tp2-hcontent">
                        <div class="tp2-htext">
                          <div class="tp2-badge"></div>
                          <div class="tp-line tp-line--white tp-line--title"></div>
                          <div class="tp-line tp-line--white tp-line--short"></div>
                          <div class="tp2-hpills">
                            <div class="tp2-pill"></div>
                            <div class="tp2-pill"></div>
                          </div>
                        </div>
                        <div class="tp2-hbtns">
                          <div class="tp2-hbtn-main"></div>
                          <div class="tp2-hbtn-sec"></div>
                        </div>
                      </div>
                    </div>
                    <!-- Grid 2 colunas -->
                    <div class="tp2-body">
                      <!-- Esquerda -->
                      <div class="tp2-left">
                        <div class="tp2-card">
                          <div class="tp2-card-bar" :style="{ background: form.visualColor }"></div>
                          <div class="tp-line tp-line--short"></div>
                          <div class="tp-line"></div>
                          <div class="tp-line tp-line--xs"></div>
                        </div>
                        <div class="tp2-card tp2-card-mission" :style="{ background: form.visualColor + '11', borderColor: form.visualColor + '33' }">
                          <div class="tp-line tp-line--short"></div>
                          <div class="tp-line tp-line--xs"></div>
                        </div>
                        <div class="tp2-gallery-mini">
                          <div class="tp2-gal-wide" :style="{ background: form.visualColor + '33' }"></div>
                          <div class="tp2-gal-sm" :style="{ background: form.visualColor + '22' }"></div>
                          <div class="tp2-gal-sm" :style="{ background: form.visualColor + '22' }"></div>
                          <div class="tp2-gal-sm" :style="{ background: form.visualColor + '22' }"></div>
                          <div class="tp2-gal-sm" :style="{ background: form.visualColor + '22' }"></div>
                        </div>
                      </div>
                      <!-- Direita -->
                      <div class="tp2-right">
                        <div class="tp2-card tp2-card-contact">
                          <div class="tp2-cc-head" :style="{ background: form.visualColor }"></div>
                          <div class="tp2-cc-row" v-for="i in 3" :key="i">
                            <div class="tp2-cc-icon" :style="{ background: form.visualColor + '22' }"></div>
                            <div class="tp-lines">
                              <div class="tp-line tp-line--xs"></div>
                              <div class="tp-line tp-line--short"></div>
                            </div>
                          </div>
                          <div class="tp2-cc-btn" :style="{ background: form.visualColor }"></div>
                        </div>
                        <div class="tp2-card tp2-card-map">
                          <div class="tp2-map-ph"></div>
                          <div class="tp-line tp-line--short"></div>
                          <div class="tp2-map-btn" :style="{ background: form.visualColor }"></div>
                        </div>
                        <div class="tp2-card tp2-area-card" :style="{ background: form.visualColor }">
                          <div class="tp-line tp-line--white tp-line--short"></div>
                          <div class="tp-line tp-line--white tp-line--xs"></div>
                        </div>
                      </div>
                    </div>
                    <!-- Action bar -->
                    <div class="tp2-actionbar" :style="{ background: form.visualColor + 'cc' }">
                      <div class="tp-line tp-line--white tp-line--short"></div>
                      <div class="tp2-ab-btns">
                        <div class="tp2-ab-btn"></div>
                        <div class="tp2-ab-btn tp2-ab-btn--outline"></div>
                      </div>
                    </div>
                    <!-- Footer -->
                    <div class="tp2-footer" :style="{ background: form.visualColor + 'aa' }"></div>
                  </div>
                  <div class="tmpl-info">
                    <span class="tmpl-name">Template 2 — Institucional</span>
                    <span class="tmpl-desc">Header com fundo · Ficha de contato · Mapa</span>
                  </div>
                </div>

              </div>

              <!-- Galeria -->
              <div class="gallery-section">
                <p class="template-label" style="margin-top: 20px">Fotos do projeto <span class="badge-optional">Opcional</span></p>
                <p class="field-hint">Adicione até 6 fotos que aparecerão na galeria da sua página.</p>
                <input ref="galleryInput" type="file" accept="image/*" multiple style="display:none" @change="handleGalleryChange" />
                <div class="gallery-grid">
                  <div
                    v-for="(preview, i) in galleryPreviews"
                    :key="i"
                    class="gallery-item"
                  >
                    <img :src="preview" class="gallery-item-img" />
                    <button type="button" class="gallery-item-remove" @click="removeGalleryImage(i)">✕</button>
                  </div>
                  <button
                    v-if="galleryPreviews.length < 6"
                    type="button"
                    class="gallery-add"
                    @click="triggerGalleryUpload"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span>Adicionar foto</span>
                    <span class="gallery-count">{{ galleryPreviews.length }}/6</span>
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Rodapé de navegação -->
          <div class="step-footer">
            <button v-if="currentStep > 0" type="button" class="btn-back" @click="goBack">
              ← Voltar
            </button>
            <span v-else></span>

            <button v-if="!isLastStep" type="button" class="btn-next" @click="goNext">
              Próxima etapa →
            </button>
            <MiranteSocialButton v-else type="primary" class="btn-submit" @click="sendDataCreateSocialProject">
              Salvar Projeto Social
            </MiranteSocialButton>
          </div>

        </el-form>
      </div>

    </div>
  </div>
</template>

<style scoped>
.page-bg {
  min-height: 100vh;
  background: #F0FDF4;
  padding: 80px 24px 80px;
  font-family: 'Nunito', sans-serif;
}

.page-wrap {
  max-width: 1080px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 260px 1fr;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 12px 48px rgba(2,44,34,0.12);
}

/* ── Sidebar ── */
.sidebar {
  background: #ECFDF5;
  border-right: 2px solid #D1FAE5;
}

.sb-inner {
  padding: 36px 24px;
  position: sticky;
  top: 88px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sb-logo  { font-size: 13px; font-weight: 900; color: #059669; letter-spacing: 0.05em; text-transform: uppercase; }
.sb-title { font-size: 20px; font-weight: 900; color: #022C22; line-height: 1.25; }
.sb-sub   { font-size: 12px; color: #065F46; font-weight: 600; line-height: 1.65; margin: 0; }

.sb-divider { height: 1px; background: #D1FAE5; }

.sb-note { font-size: 11px; color: #059669; font-weight: 600; line-height: 1.65; margin: 0; }

/* Steps */
.sb-steps { display: flex; flex-direction: column; gap: 4px; }

.sb-step {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 9px 10px;
  border-radius: 12px;
  transition: background 0.2s;
}

.sb-step--active { background: #fff; box-shadow: 0 2px 10px rgba(5,150,105,0.10); }

.sb-step-dot {
  width: 28px; height: 28px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 900;
  flex-shrink: 0;
  transition: all 0.2s;
  border: 2px solid #A7F3D0;
  background: #fff;
  color: #A7F3D0;
}

.sb-step--active .sb-step-dot {
  border-color: #059669;
  background: #059669;
  color: #fff;
}

.sb-step--done .sb-step-dot {
  border-color: #D1FAE5;
  background: #D1FAE5;
  color: #059669;
}

.sb-step-text { display: flex; flex-direction: column; gap: 1px; }

.sb-step-name {
  font-size: 13px; font-weight: 800;
  color: #A7F3D0;
  transition: color 0.2s;
}
.sb-step-sub {
  font-size: 10px; font-weight: 600;
  color: #A7F3D0;
  transition: color 0.2s;
}

.sb-step--active .sb-step-name { color: #022C22; }
.sb-step--active .sb-step-sub  { color: #065F46; }
.sb-step--done .sb-step-name   { color: #059669; }
.sb-step--done .sb-step-sub    { color: #6EE7B7; }
.sb-step--future .sb-step-name { color: #A7F3D0; }
.sb-step--future .sb-step-sub  { color: #A7F3D0; }

/* ── Form col ── */
.form-col {
  background: #fff;
  padding: 40px 44px 40px;
  display: flex;
  flex-direction: column;
}

.step-body { flex: 1; }

.step-title {
  font-size: 20px;
  font-weight: 900;
  color: #022C22;
  margin: 0 0 24px;
  padding-bottom: 16px;
  border-bottom: 1.5px solid #F0FDF4;
}

/* ── Layout ── */
.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* ── Labels & badges ── */
.field-label    { display: inline-flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 900; color: #065F46; text-transform: uppercase; letter-spacing: .05em; }
.badge-optional { font-size: 10px; font-weight: 700; color: #059669; background: #D1FAE5; padding: 2px 7px; border-radius: 20px; text-transform: none; letter-spacing: 0; }
.badge-loading  { font-size: 10px; font-weight: 700; color: #059669; background: #D1FAE5; padding: 2px 7px; border-radius: 20px; text-transform: none; letter-spacing: 0; animation: pulse 1s ease-in-out infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }

/* ── Upload ── */
.img-upload     { width: 100%; border: 2px dashed #D1FAE5; border-radius: 14px; cursor: pointer; transition: border-color .2s, background .2s; overflow: hidden; min-height: 110px; display: flex; align-items: center; justify-content: center; background: #FAFFFE; }
.img-upload:hover { border-color: #10B981; background: #F0FDF4; }
.img-upload--has  { border-style: solid; border-color: #10B981; }
.img-preview      { width: 100%; max-height: 180px; object-fit: cover; display: block; }
.img-placeholder  { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 20px; text-align: center; }
.img-icon  { color: #10B981; opacity: .7; }
.img-label { font-size: 13px; font-weight: 700; color: #065F46; }
.img-hint  { font-size: 11px; color: #10B981; font-weight: 600; }

/* ── Field hint ── */
.field-hint { font-size: 12px; font-weight: 600; color: #065F46; margin: -4px 0 10px; opacity: 0.8; }

/* ── Color ── */
.color-field  { display: flex; align-items: center; gap: 10px; padding: 8px 14px; border: 1.5px solid #D1FAE5; border-radius: 12px; background: #FAFFFE; cursor: pointer; width: 100%; box-sizing: border-box; position: relative; height: 44px; }
.color-field:hover { border-color: #10B981; }
.color-swatch { width: 26px; height: 26px; border-radius: 7px; border: 2px solid rgba(0,0,0,.08); flex-shrink: 0; }
.color-native { position: absolute; opacity: 0; width: 26px; height: 26px; left: 14px; cursor: pointer; border: none; padding: 0; }
.color-value  { font-size: 13px; font-weight: 700; color: #065F46; font-family: monospace; text-transform: uppercase; }

/* ── Chips ── */
.audience-chips { display: flex; flex-wrap: wrap; gap: 10px; width: 100%; }
.audience-chip  { padding: 9px 18px; border-radius: 12px; border: 1.5px solid #D1FAE5; background: #FAFFFE; font-family: 'Nunito', sans-serif; font-size: 14px; font-weight: 700; color: #065F46; cursor: pointer; transition: border-color .2s, background .2s, color .2s; user-select: none; }
.audience-chip:hover { border-color: #10B981; background: #F0FDF4; }
.audience-chip--on   { border-color: #059669; background: #D1FAE5; color: #022C22; }

/* ── Mapa ── */
.mini-map { width: 100%; height: 240px; border-radius: 14px; overflow: hidden; border: 2px solid #D1FAE5; cursor: crosshair; transition: border-color .2s; }
.mini-map:hover { border-color: #10B981; }
.map-hint { display: flex; align-items: flex-start; gap: 8px; background: #F0FDF4; border: 1.5px solid #D1FAE5; border-radius: 10px; padding: 10px 14px; margin-bottom: 10px; font-size: 12px; font-weight: 600; color: #065F46; line-height: 1.5; transition: border-color .2s, background .2s; }
.map-hint--pinned  { background: #ECFDF5; border-color: #10B981; }
.map-hint-icon     { flex-shrink: 0; margin-top: 1px; color: #059669; }
.map-hint-icon--ok { color: #059669; }

/* ── Toggle ── */
.page-toggle     { display: flex; align-items: center; justify-content: space-between; gap: 16px; background: #F0FDF4; border: 2px solid #D1FAE5; border-radius: 14px; padding: 16px 18px; cursor: pointer; transition: all .2s; user-select: none; margin-top: 8px; }
.page-toggle:hover   { border-color: #10B981; background: #ECFDF5; }
.page-toggle--active { border-color: #059669; background: linear-gradient(135deg, #ECFDF5, #D1FAE5); box-shadow: 0 4px 16px rgba(5,150,105,.12); }
.page-toggle-info    { display: flex; flex-direction: column; gap: 3px; }
.page-toggle-title   { font-size: 14px; font-weight: 800; color: #022C22; }
.page-toggle-desc    { font-size: 12px; color: #065F46; font-weight: 600; }
.page-toggle-switch  { width: 46px; height: 26px; background: #D1FAE5; border-radius: 100px; position: relative; flex-shrink: 0; transition: background .25s; }
.page-toggle-switch--on { background: #059669; }
.page-toggle-knob    { width: 20px; height: 20px; background: #fff; border-radius: 50%; position: absolute; top: 3px; left: 3px; transition: transform .25s cubic-bezier(0.34,1.56,0.64,1); box-shadow: 0 2px 6px rgba(0,0,0,.15); }
.page-toggle-switch--on .page-toggle-knob { transform: translateX(20px); }

/* ── Templates ── */
.template-section { margin-top: 16px; }
.template-label   { font-size: 11px; font-weight: 700; color: #065F46; text-transform: uppercase; letter-spacing: .05em; margin: 0 0 12px; }
.template-cards   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.tmpl-card        { border: 2px solid #D1FAE5; border-radius: 16px; padding: 10px 10px 14px; cursor: pointer; transition: all .2s; background: #FAFFFE; position: relative; overflow: hidden; }
.tmpl-card:hover  { border-color: #10B981; box-shadow: 0 4px 20px rgba(16,185,129,.14); }
.tmpl-card.active { border-color: #059669; background: #F0FDF4; box-shadow: 0 6px 28px rgba(5,150,105,.18); }
.tmpl-info        { display: flex; flex-direction: column; gap: 3px; margin-top: 10px; padding: 0 2px; }
.tmpl-name        { font-size: 12px; font-weight: 800; color: #022C22; }
.tmpl-desc        { font-size: 10px; color: #065F46; font-weight: 600; }
.tmpl-check       { position: absolute; top: 10px; right: 10px; width: 20px; height: 20px; background: #059669; color: #fff; border-radius: 50%; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(.6); transition: opacity .2s, transform .2s cubic-bezier(0.34,1.56,0.64,1); z-index: 10; }
.tmpl-card.active .tmpl-check { opacity: 1; transform: scale(1); }

/* ── Preview base ── */
.tmpl-preview {
  width: 100%;
  height: 240px;
  border-radius: 10px;
  overflow: hidden;
  background: #F8FAFC;
  border: 1px solid #E5E7EB;
  display: flex;
  flex-direction: column;
  font-size: 0;
}

/* shared micro elements */
.tp-nav { display: flex; align-items: center; justify-content: space-between; padding: 3px 6px; background: rgba(255,255,255,0.95); border-bottom: 1px solid; flex-shrink: 0; }
.tp-nav-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.tp-nav-pill { width: 24px; height: 5px; border-radius: 99px; background: currentColor; opacity: 0.7; }
.tp-eyebrow { height: 3px; width: 28px; border-radius: 99px; margin-bottom: 3px; }
.tp-line { height: 3px; background: #D1D5DB; border-radius: 99px; margin-bottom: 2px; }
.tp-line--title { height: 5px; background: #9CA3AF; width: 80%; }
.tp-line--short { width: 60%; }
.tp-line--xs { width: 40%; }
.tp-line--white { background: rgba(255,255,255,0.6); }
.tp-line--white.tp-line--title { background: rgba(255,255,255,0.9); }
.tp-lines { display: flex; flex-direction: column; gap: 2px; flex: 1; min-width: 0; }

/* ── Template 1 preview ── */
.tmpl-preview--1 { gap: 0; }
.tp1-hero { flex-shrink: 0; height: 68px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; padding: 6px; }
.tp1-hero-badge { width: 28px; height: 4px; background: rgba(255,255,255,0.4); border-radius: 99px; margin-bottom: 2px; }
.tp1-hero-title { width: 70px; height: 6px; background: rgba(255,255,255,0.9); border-radius: 99px; }
.tp1-hero-sub { width: 50px; height: 3px; background: rgba(255,255,255,0.55); border-radius: 99px; }
.tp1-hero-btns { display: flex; gap: 4px; margin-top: 3px; }
.tp1-btn-main { width: 34px; height: 7px; border-radius: 99px; }
.tp1-btn-ghost { width: 26px; height: 7px; background: rgba(255,255,255,0.25); border-radius: 99px; border: 1px solid rgba(255,255,255,0.4); }

.tp1-sobre { display: flex; gap: 4px; padding: 5px 5px 4px; background: #fff; flex-shrink: 0; }
.tp1-sobre-left { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.tp1-contact-card { width: 42px; border: 1px solid; border-radius: 4px; overflow: hidden; flex-shrink: 0; }
.tp1-cc-header { height: 7px; }
.tp1-cc-row { height: 5px; background: #F3F4F6; margin: 2px 3px; border-radius: 2px; }
.tp1-cc-btn { height: 5px; margin: 2px 3px; border-radius: 2px; }

.tp1-impact { flex-shrink: 0; height: 16px; display: flex; align-items: center; justify-content: space-between; padding: 0 6px; gap: 4px; }
.tp1-impact-btn { width: 20px; height: 6px; background: rgba(255,255,255,0.35); border-radius: 99px; flex-shrink: 0; }

.tp1-gallery { display: flex; gap: 3px; padding: 4px 5px; background: #fff; flex-shrink: 0; height: 38px; }
.tp1-gal-featured { width: 44px; border-radius: 3px; flex-shrink: 0; }
.tp1-gal-small { flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 2px; }
.tp1-gal-item { border-radius: 2px; }

.tp1-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3px; padding: 4px 5px; background: #F9FAFB; flex: 1; }
.tp1-info-card { background: #fff; border-radius: 4px; padding: 3px 4px; display: flex; align-items: flex-start; gap: 3px; border: 1px solid #E9EDF2; }
.tp1-info-icon { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }

.tp1-footer { height: 10px; flex-shrink: 0; }

/* ── Template 2 preview ── */
.tmpl-preview--2 { gap: 0; }
.tp2-header { flex-shrink: 0; height: 70px; display: flex; flex-direction: column; }
.tp2-nav { display: flex; align-items: center; justify-content: space-between; padding: 3px 6px; border-bottom: 1px solid rgba(255,255,255,0.15); flex-shrink: 0; }
.tp2-nav-dot { width: 5px; height: 5px; border-radius: 50%; background: rgba(255,255,255,0.7); }
.tp2-nav-pill { width: 18px; height: 5px; border-radius: 99px; background: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.35); }
.tp2-hcontent { flex: 1; display: flex; align-items: flex-end; justify-content: space-between; padding: 0 6px 5px; gap: 4px; }
.tp2-htext { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.tp2-badge { width: 20px; height: 4px; background: rgba(255,255,255,0.3); border-radius: 99px; }
.tp2-hpills { display: flex; gap: 2px; margin-top: 1px; }
.tp2-pill { width: 16px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; }
.tp2-hbtns { display: flex; flex-direction: column; gap: 2px; flex-shrink: 0; }
.tp2-hbtn-main { width: 30px; height: 7px; background: rgba(255,255,255,0.95); border-radius: 99px; }
.tp2-hbtn-sec { width: 24px; height: 5px; background: rgba(255,255,255,0.2); border-radius: 99px; border: 1px solid rgba(255,255,255,0.3); }

.tp2-body { flex: 1; display: flex; gap: 3px; padding: 4px 5px; background: #F8FAFC; min-height: 0; overflow: hidden; }
.tp2-left { flex: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0; }
.tp2-right { width: 46px; display: flex; flex-direction: column; gap: 3px; flex-shrink: 0; }
.tp2-card { background: #fff; border-radius: 4px; padding: 4px 4px 3px; border: 1px solid #E9EDF2; display: flex; flex-direction: column; gap: 2px; }
.tp2-card-bar { width: 3px; height: 8px; border-radius: 2px; flex-shrink: 0; align-self: flex-start; margin-bottom: 1px; }
.tp2-card-mission { }
.tp2-gallery-mini { display: grid; grid-template-columns: repeat(5, 1fr); gap: 2px; height: 16px; }
.tp2-gal-wide { grid-column: span 2; border-radius: 2px; }
.tp2-gal-sm { border-radius: 2px; }
.tp2-card-contact { padding: 0; overflow: hidden; gap: 0; }
.tp2-cc-head { height: 8px; width: 100%; }
.tp2-cc-row { display: flex; align-items: center; gap: 2px; padding: 2px 3px; }
.tp2-cc-icon { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.tp2-cc-btn { height: 6px; background: currentColor; border-radius: 99px; margin: 2px 3px 3px; opacity: 0.85; }
.tp2-card-map { gap: 2px; }
.tp2-map-ph { height: 20px; background: #E5E7EB; border-radius: 3px; }
.tp2-map-btn { height: 5px; border-radius: 99px; opacity: 0.9; }
.tp2-area-card { border: none !important; }

.tp2-actionbar { flex-shrink: 0; height: 18px; display: flex; align-items: center; justify-content: space-between; padding: 0 6px; }
.tp2-ab-btns { display: flex; gap: 3px; }
.tp2-ab-btn { width: 18px; height: 6px; background: rgba(255,255,255,0.9); border-radius: 99px; }
.tp2-ab-btn--outline { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); }
.tp2-footer { height: 9px; flex-shrink: 0; }

/* ── Galeria no cadastro ── */
.gallery-section { margin-top: 4px; }
.gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 8px; }
.gallery-item { position: relative; aspect-ratio: 1; border-radius: 10px; overflow: hidden; border: 1.5px solid #D1FAE5; }
.gallery-item-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.gallery-item-remove { position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border-radius: 50%; background: rgba(0,0,0,0.55); color: #fff; border: none; font-size: 10px; font-weight: 900; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; transition: background 0.15s; }
.gallery-item-remove:hover { background: rgba(220,38,38,0.85); }
.gallery-add { aspect-ratio: 1; border-radius: 10px; border: 2px dashed #D1FAE5; background: #FAFFFE; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; cursor: pointer; color: #059669; transition: border-color 0.2s, background 0.2s; font-family: 'Nunito', sans-serif; }
.gallery-add:hover { border-color: #10B981; background: #F0FDF4; }
.gallery-add span:first-of-type { font-size: 12px; font-weight: 700; color: #065F46; }
.gallery-count { font-size: 10px; font-weight: 700; color: #10B981; background: #D1FAE5; padding: 1px 7px; border-radius: 20px; }

/* ── Rodapé de navegação ── */
.step-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1.5px solid #F0FDF4;
}

.btn-back {
  padding: 11px 24px;
  border-radius: 12px;
  background: transparent;
  color: #065F46;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 700;
  border: 1.5px solid #D1FAE5;
  cursor: pointer;
  transition: border-color .2s, background .2s;
}
.btn-back:hover { border-color: #10B981; background: #F0FDF4; }

.btn-next {
  padding: 11px 32px;
  border-radius: 12px;
  background: linear-gradient(135deg, #059669, #047857);
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: opacity .2s, transform .1s;
}
.btn-next:hover  { opacity: 0.92; }
.btn-next:active { transform: scale(0.99); }

.btn-submit {
  padding: 11px 32px !important;
  height: auto !important;
  font-size: 14px !important;
  font-weight: 800 !important;
  border-radius: 12px !important;
  font-family: 'Nunito', sans-serif !important;
}

/* ── Element Plus overrides ── */
:deep(.el-input__wrapper) { border-radius: 12px !important; border: 1.5px solid #D1FAE5 !important; box-shadow: none !important; padding: 4px 14px !important; background: #FAFFFE !important; transition: border-color .2s, box-shadow .2s !important; }
:deep(.el-input__wrapper:hover)    { border-color: #10B981 !important; }
:deep(.el-input__wrapper.is-focus) { border-color: #059669 !important; box-shadow: 0 0 0 3px rgba(5,150,105,.10) !important; background: #fff !important; }
:deep(.el-input__inner) { font-family: 'Nunito', sans-serif !important; font-size: 14px !important; font-weight: 600 !important; color: #022C22 !important; height: 36px !important; }
:deep(.el-input__inner::placeholder) { color: #a0c4a0 !important; font-weight: 500 !important; }
:deep(.el-textarea__inner) { border-radius: 12px !important; border: 1.5px solid #D1FAE5 !important; box-shadow: none !important; padding: 10px 14px !important; font-family: 'Nunito', sans-serif !important; font-size: 14px !important; font-weight: 600 !important; color: #022C22 !important; background: #FAFFFE !important; resize: none; transition: border-color .2s, box-shadow .2s !important; }
:deep(.el-textarea__inner::placeholder) { color: #a0c4a0 !important; font-weight: 500 !important; }
:deep(.el-textarea__inner:focus) { border-color: #059669 !important; box-shadow: 0 0 0 3px rgba(5,150,105,.10) !important; outline: none !important; background: #fff !important; }
:deep(.el-form-item__label) { font-family: 'Nunito', sans-serif !important; font-weight: 700 !important; font-size: 11px !important; color: #065F46 !important; text-transform: uppercase !important; letter-spacing: .05em !important; padding-bottom: 6px !important; }
:deep(.el-form-item__error) { font-family: 'Nunito', sans-serif; font-size: 12px; color: #DC2626; font-weight: 600; }
:deep(.el-form-item.is-required .el-form-item__label::before) { display: none !important; }
:deep(.el-form-item.is-required .el-form-item__label::after) {
  content: ' *';
  color: #059669;
  font-weight: 900;
  font-size: 13px;
  margin-left: 2px;
}
</style>

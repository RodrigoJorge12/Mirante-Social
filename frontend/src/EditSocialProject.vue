<script setup lang="ts">
import { ref, reactive, watch, computed } from "vue";
import { ElMessage } from "element-plus";
import type { FormInstance } from "element-plus";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";
import { ServicePersonalizedPage } from "@/Services/ServicePersonalizedPage";

const props = defineProps<{
  modelValue: boolean;
  projectId: number | null;
  hasPersonalizedPage?: boolean;
}>();

const emit = defineEmits(["update:modelValue", "updated"]);

const presenter = new PresenterSocialProject();
const ppService = new ServicePersonalizedPage();

const isOpen = ref(props.modelValue);
watch(() => props.modelValue, v => { isOpen.value = v; });
watch(isOpen, v => emit("update:modelValue", v));

const activeTab = ref("dados");

// ── Formulário de dados ──────────────────────────────────────────────────────
const formRef = ref<FormInstance>();
const form = reactive({
  name: "",
  description: "",
  address: "",
  district: "",
  city: "",
  state: "",
  zipCode: "",
  phone: "",
  websiteUrl: "",
  visualColor: "",
  activityArea: "",
  targetAudiences: [] as string[],
  image: null as File | null,
  image_path: null as string | null,
  instagramUrl: "",
  facebookUrl: "",
  operatingHours: "",
  mission: "",
});
const fileList = ref<any[]>([]);

// ── Página personalizada ─────────────────────────────────────────────────────
const pageId = ref<number | null>(null);
const pageSlug = ref<string | null>(null);
const galleryFiles = ref<File[]>([]);
const galleryPreviews = ref<string[]>([]);
const galleryExisting = ref<string[]>([]);
const galleryUploading = ref(false);
const loadingPage = ref(false);

const apiUrl = import.meta.env.VITE_API_URL as string;

function existingImageUrl(path: string) {
  if (path.startsWith("http")) return path;
  return `${apiUrl}/storage/${path}`;
}

const totalGalleryCount = computed(() => galleryExisting.value.length + galleryFiles.value.length);

// ── Carregar dados quando abre ───────────────────────────────────────────────
watch(
  () => props.projectId,
  async (id) => {
    if (!id) return;
    activeTab.value = "dados";
    galleryFiles.value = [];
    galleryPreviews.value = [];
    galleryExisting.value = [];
    pageId.value = null;
    pageSlug.value = null;

    const response = await presenter.GetProjectById(id);
    if (!response?.success) {
      ElMessage.error("Não foi possível carregar os dados do projeto.");
      return;
    }

    const p = response.data;
    form.name = p.name;
    form.description = p.description;
    form.address = p.address;
    form.district = p.district;
    form.city = p.city;
    form.state = p.state;
    form.zipCode = p.zip_code;
    form.phone = p.phone;
    form.websiteUrl = p.website_url;
    form.visualColor = p.visual_color;
    form.activityArea = p.activity_area;
    form.targetAudiences = JSON.parse(p.target_audiences ?? "[]");
    form.image_path = p.image_path;
    form.instagramUrl = p.instagram_url ?? "";
    form.facebookUrl = p.facebook_url ?? "";
    form.operatingHours = p.operating_hours ?? "";
    form.mission = p.mission ?? "";

    if (p.image_path) {
      fileList.value = [{
        name: "imagem atual",
        url: `${apiUrl}/storage/${p.image_path}`,
      }];
    }

    // Carrega dados da página personalizada se existir
    if (props.hasPersonalizedPage) {
      loadingPage.value = true;
      const slug = await ppService.getSlugByProjectId(id);
      if (slug) {
        pageSlug.value = slug;
        const pageData = await ppService.getPageData(slug);
        if (pageData?.data?.page) {
          pageId.value = pageData.data.page.id;
          const raw = pageData.data.page.gallery_images;
          if (raw) {
            try {
              const parsed = typeof raw === "string" ? JSON.parse(raw) : raw;
              if (Array.isArray(parsed)) galleryExisting.value = parsed;
            } catch {}
          }
        }
      }
      loadingPage.value = false;
    }
  },
  { immediate: true }
);

// ── Imagem de capa ───────────────────────────────────────────────────────────
const handleImageChange = (file: any) => {
  fileList.value = [file];
  form.image = file.raw;
};

// ── Galeria ──────────────────────────────────────────────────────────────────
function onGalleryFileChange(e: Event) {
  const input = e.target as HTMLInputElement;
  if (!input.files) return;
  const slots = 6 - totalGalleryCount.value;
  const newFiles = Array.from(input.files).slice(0, slots);
  newFiles.forEach(f => {
    galleryFiles.value.push(f);
    galleryPreviews.value.push(URL.createObjectURL(f));
  });
  input.value = "";
}

function removeNewImage(i: number) {
  URL.revokeObjectURL(galleryPreviews.value[i]);
  galleryFiles.value.splice(i, 1);
  galleryPreviews.value.splice(i, 1);
}

function removeExistingImage(i: number) {
  galleryExisting.value.splice(i, 1);
}

// ── Salvar dados ─────────────────────────────────────────────────────────────
const submit = async () => {
  if (!formRef.value || !props.projectId) return;
  const valid = await formRef.value.validate();
  if (!valid) return;

  const result = await presenter.UpdateSocialProject(
    props.projectId,
    form.name, form.description, form.address, form.district,
    form.city, form.state, form.zipCode, form.phone,
    form.websiteUrl, form.visualColor, form.activityArea,
    form.targetAudiences, form.image,
    form.instagramUrl, form.facebookUrl, form.operatingHours, form.mission
  );

  if (result) {
    ElMessage.success("Projeto atualizado com sucesso!");
    emit("updated");
    isOpen.value = false;
  } else {
    ElMessage.error("Não foi possível salvar as alterações. Tente novamente.");
  }
};

// ── Salvar galeria ────────────────────────────────────────────────────────────
const saveGallery = async () => {
  if (!pageId.value) return;
  galleryUploading.value = true;

  // Envia apenas arquivos novos (os existentes que foram removidos somem automaticamente
  // pois o backend substitui tudo — ideal seria merge, mas por simplicidade substitui)
  const result = await ppService.updateGallery(pageId.value, galleryFiles.value);
  galleryUploading.value = false;

  if (result?.success) {
    ElMessage.success("Galeria salva com sucesso!");
    // Atualiza lista: existentes + novos viram "existentes"
    const newPaths = result.data?.gallery_images ?? [];
    galleryExisting.value = newPaths;
    galleryFiles.value.forEach(f => URL.revokeObjectURL(URL.createObjectURL(f)));
    galleryFiles.value = [];
    galleryPreviews.value = [];
    emit("updated");
  } else {
    ElMessage.error("Erro ao salvar galeria.");
  }
};

const pageUrl = computed(() =>
  pageSlug.value ? `${window.location.origin}/personalizedPages/${pageSlug.value}` : null
);
</script>

<template>
  <el-dialog
    v-model="isOpen"
    width="700px"
    destroy-on-close
    class="edit-dialog"
    :show-close="true"
  >
    <template #header>
      <div class="dialog-hd">
        <div class="dialog-hd-icon-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
        </div>
        <div>
          <p class="dialog-hd-title">Editar Projeto</p>
          <p class="dialog-hd-sub">{{ form.name || 'Carregando...' }}</p>
        </div>
      </div>
    </template>

    <!-- Abas -->
    <div class="tabs">
      <button
        class="tab-btn"
        :class="{ active: activeTab === 'dados' }"
        @click="activeTab = 'dados'"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Dados do Projeto
      </button>
      <button
        v-if="hasPersonalizedPage"
        class="tab-btn"
        :class="{ active: activeTab === 'pagina' }"
        @click="activeTab = 'pagina'"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Página Personalizada
      </button>
    </div>

    <!-- ABA: Dados do Projeto -->
    <el-form
      v-show="activeTab === 'dados'"
      ref="formRef"
      :model="form"
      label-position="top"
      class="edit-form"
    >
      <el-form-item label="Nome" prop="name">
        <el-input v-model="form.name" />
      </el-form-item>

      <el-form-item label="Descrição" prop="description">
        <el-input type="textarea" v-model="form.description" :rows="3" />
      </el-form-item>

      <el-form-item label="Frase de Missão">
        <el-input v-model="form.mission" placeholder="Em uma frase, qual é o propósito do projeto?" maxlength="300" show-word-limit />
      </el-form-item>

      <el-form-item label="Imagem de Capa">
        <el-upload
          class="upload-demo"
          action=""
          :auto-upload="false"
          :limit="1"
          list-type="picture-card"
          accept="image/*"
          :file-list="fileList"
          :on-change="handleImageChange"
        >
          <i class="el-icon-plus"></i>
        </el-upload>
      </el-form-item>

      <div class="f-row">
        <el-form-item label="Endereço">
          <el-input v-model="form.address" />
        </el-form-item>
        <el-form-item label="Bairro">
          <el-input v-model="form.district" />
        </el-form-item>
      </div>

      <div class="f-row-3">
        <el-form-item label="Cidade">
          <el-input v-model="form.city" />
        </el-form-item>
        <el-form-item label="Estado (UF)">
          <el-input v-model="form.state" maxlength="2" />
        </el-form-item>
        <el-form-item label="CEP">
          <el-input v-model="form.zipCode" />
        </el-form-item>
      </div>

      <div class="f-row">
        <el-form-item label="Telefone">
          <el-input v-model="form.phone" />
        </el-form-item>
        <el-form-item label="Website">
          <el-input v-model="form.websiteUrl" />
        </el-form-item>
      </div>

      <div class="f-row">
        <el-form-item label="Instagram">
          <el-input v-model="form.instagramUrl" placeholder="https://instagram.com/..." />
        </el-form-item>
        <el-form-item label="Facebook">
          <el-input v-model="form.facebookUrl" placeholder="https://facebook.com/..." />
        </el-form-item>
      </div>

      <el-form-item label="Horários de Funcionamento">
        <el-input type="textarea" v-model="form.operatingHours" :rows="2" placeholder="Ex: Seg, Qua e Sex das 14h às 17h" />
      </el-form-item>

      <div class="f-row">
        <el-form-item label="Área de Atuação">
          <el-input v-model="form.activityArea" />
        </el-form-item>
        <el-form-item label="Cor Visual">
          <div class="color-row">
            <el-color-picker v-model="form.visualColor" />
            <span class="color-hint">{{ form.visualColor || 'Escolha uma cor' }}</span>
          </div>
        </el-form-item>
      </div>

      <el-form-item label="Públicos-Alvo">
        <el-select v-model="form.targetAudiences" multiple filterable allow-create style="width:100%">
          <el-option value="Crianças" label="Crianças" />
          <el-option value="Adolescentes" label="Adolescentes" />
          <el-option value="Adultos" label="Adultos" />
          <el-option value="Idosos" label="Idosos" />
        </el-select>
      </el-form-item>
    </el-form>

    <!-- ABA: Página Personalizada -->
    <div v-if="activeTab === 'pagina'" class="page-tab">

      <div v-if="loadingPage" class="page-tab-loading">
        <div class="pt-spinner"></div>
        <p>Carregando dados da página...</p>
      </div>

      <template v-else>
        <!-- Link da página -->
        <div v-if="pageUrl" class="page-link-box">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          <a :href="pageUrl" target="_blank" class="page-link-url">{{ pageUrl }}</a>
        </div>

        <!-- Galeria -->
        <div class="gal-section">
          <p class="gal-section-title">Galeria de Fotos</p>
          <p class="gal-section-hint">Até 6 fotos exibidas na página personalizada do projeto.</p>

          <div class="gal-grid">
            <!-- Fotos existentes no servidor -->
            <div v-for="(img, i) in galleryExisting" :key="'ex-' + i" class="gal-item">
              <img :src="existingImageUrl(img)" class="gal-img" />
              <button class="gal-remove" @click="removeExistingImage(i)">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>

            <!-- Novas fotos (preview local) -->
            <div v-for="(preview, i) in galleryPreviews" :key="'new-' + i" class="gal-item gal-item--new">
              <img :src="preview" class="gal-img" />
              <button class="gal-remove" @click="removeNewImage(i)">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>

            <!-- Slot de adicionar -->
            <label v-if="totalGalleryCount < 6" class="gal-add">
              <input type="file" accept="image/*" multiple class="gal-input" @change="onGalleryFileChange" />
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>Adicionar</span>
            </label>
          </div>

          <div class="gal-footer">
            <span class="gal-count">{{ totalGalleryCount }}/6 fotos</span>
            <button
              class="btn-save-gallery"
              :disabled="galleryFiles.length === 0 || galleryUploading"
              @click="saveGallery"
            >
              <svg v-if="galleryUploading" class="gal-spin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
              {{ galleryUploading ? "Salvando..." : "Salvar galeria" }}
            </button>
          </div>
        </div>
      </template>
    </div>

    <template #footer>
      <div class="dialog-footer">
        <button class="btn-cancel" @click="isOpen = false">Fechar</button>
        <button v-if="activeTab === 'dados'" class="btn-save" @click="submit">Salvar Alterações</button>
      </div>
    </template>
  </el-dialog>
</template>

<style scoped>
.dialog-hd {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 4px 0;
}

.dialog-hd-icon-wrap {
  width: 40px;
  height: 40px;
  background: #F0FDF4;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dialog-hd-title {
  font-size: 18px;
  font-weight: 900;
  color: #022C22;
  margin: 0 0 2px;
  font-family: 'Nunito', sans-serif;
}

.dialog-hd-sub {
  font-size: 13px;
  color: #065F46;
  margin: 0;
  font-family: 'Nunito', sans-serif;
}

/* Abas */
.tabs {
  display: flex;
  gap: 4px;
  border-bottom: 2px solid #E5E7EB;
  margin-bottom: 24px;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 10px 18px;
  border: none;
  background: transparent;
  font-size: 14px;
  font-weight: 700;
  color: #6B7280;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: color 0.15s, border-color 0.15s;
}

.tab-btn.active {
  color: #059669;
  border-bottom-color: #059669;
}

.tab-btn:hover:not(.active) {
  color: #374151;
}

/* Formulário */
.edit-form {
  font-family: 'Nunito', sans-serif;
}

.f-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.f-row-3 {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 16px;
}

@media (max-width: 500px) {
  .f-row, .f-row-3 { grid-template-columns: 1fr; }
}

.color-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.color-hint {
  font-size: 13px;
  color: #065F46;
  font-weight: 700;
}

/* Footer */
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 4px 0;
}

.btn-cancel {
  padding: 12px 24px;
  border-radius: 12px;
  border: 2px solid #E5E7EB;
  background: #fff;
  color: #6B7280;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  transition: background 0.2s;
}

.btn-cancel:hover { background: #F9FAFB; }

.btn-save {
  padding: 12px 28px;
  border-radius: 14px;
  border: none;
  background: #059669;
  color: #fff;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  box-shadow: 0 4px 14px rgba(5, 150, 105, 0.30);
  transition: background 0.2s, transform 0.15s;
}

.btn-save:hover { background: #047857; transform: translateY(-1px); }

/* Aba Página Personalizada */
.page-tab {
  font-family: 'Nunito', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.page-tab-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 40px;
  color: #6B7280;
  font-size: 14px;
  font-weight: 600;
}

.pt-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #D1FAE5;
  border-top-color: #059669;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.page-link-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #F0FDF4;
  border: 1px solid #A7F3D0;
  border-radius: 10px;
  padding: 10px 14px;
  color: #059669;
}

.page-link-url {
  font-size: 13px;
  font-weight: 700;
  color: #059669;
  text-decoration: none;
  word-break: break-all;
}

.page-link-url:hover { text-decoration: underline; }

/* Galeria */
.gal-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.gal-section-title {
  font-size: 15px;
  font-weight: 900;
  color: #022C22;
  margin: 0;
}

.gal-section-hint {
  font-size: 13px;
  color: #6B7280;
  margin: 0;
}

.gal-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.gal-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 10px;
  overflow: hidden;
}

.gal-item--new::after {
  content: "Nova";
  position: absolute;
  bottom: 6px;
  left: 6px;
  background: #059669;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 999px;
  font-family: 'Nunito', sans-serif;
}

.gal-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.gal-remove {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(0,0,0,0.55);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.gal-remove:hover { background: rgba(220,38,38,0.85); }

.gal-add {
  aspect-ratio: 1;
  border-radius: 10px;
  border: 2px dashed #D1FAE5;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  cursor: pointer;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  font-family: 'Nunito', sans-serif;
  transition: border-color 0.2s, background 0.2s;
}

.gal-add:hover { border-color: #059669; background: #F0FDF4; }

.gal-input { display: none; }

.gal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.gal-count {
  font-size: 13px;
  font-weight: 700;
  color: #9CA3AF;
}

.btn-save-gallery {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  border-radius: 10px;
  border: none;
  background: #059669;
  color: #fff;
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  font-family: 'Nunito', sans-serif;
  transition: background 0.2s, opacity 0.2s;
}

.btn-save-gallery:hover:not(:disabled) { background: #047857; }
.btn-save-gallery:disabled { opacity: 0.45; cursor: not-allowed; }

@keyframes spin { to { transform: rotate(360deg); } }
.gal-spin { animation: spin 0.7s linear infinite; }
</style>

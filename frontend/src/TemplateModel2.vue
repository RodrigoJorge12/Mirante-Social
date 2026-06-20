<script setup lang="ts">
import { computed, ref } from "vue";

const props = defineProps<{
  project: {
    id?: number;
    name: string;
    description: string;
    color?: string;
    address?: string;
    district?: string;
    city?: string;
    state?: string;
    zip?: string;
    phone?: string;
    email?: string;
    site?: string;
    area?: string;
    targets?: string | string[];
    needs?: string;
    mission?: string;
    instagram?: string;
    facebook?: string;
    hours?: string;
    image?: string;
  };
  personalizedPageData?: {
    id?: number;
    url?: string;
    template?: number;
    caption?: string;
    gallery_images?: string;
  };
}>();

function hexToRgb(hex: string) {
  const c = hex.replace("#", "");
  return { r: parseInt(c.slice(0,2),16), g: parseInt(c.slice(2,4),16), b: parseInt(c.slice(4,6),16) };
}
function hexToRgba(hex: string, a: number) {
  const { r, g, b } = hexToRgb(hex);
  return `rgba(${r},${g},${b},${a})`;
}
function mixBlack(hex: string, amount: number) {
  const { r, g, b } = hexToRgb(hex);
  return `rgb(${Math.round(r*(1-amount))},${Math.round(g*(1-amount))},${Math.round(b*(1-amount))})`;
}
function mixWhite(hex: string, amount: number) {
  const { r, g, b } = hexToRgb(hex);
  return `rgb(${Math.round(r+(255-r)*amount)},${Math.round(g+(255-g)*amount)},${Math.round(b+(255-b)*amount)})`;
}

const accentColor = computed(() => props.project.color || "#059669");

const palette = computed(() => {
  const c = accentColor.value;
  return {
    "--accent":        c,
    "--accent-dark":   mixBlack(c, 0.25),
    "--accent-darker": mixBlack(c, 0.50),
    "--accent-light":  mixWhite(c, 0.75),
    "--accent-pale":   mixWhite(c, 0.92),
    "--accent-05":     hexToRgba(c, 0.05),
    "--accent-10":     hexToRgba(c, 0.10),
    "--accent-15":     hexToRgba(c, 0.15),
    "--accent-20":     hexToRgba(c, 0.20),
    "--accent-hero":   `linear-gradient(160deg, ${c} 0%, ${mixBlack(c, 0.35)} 100%)`,
  };
});

const galleryImages = computed<string[]>(() => {
  try {
    const raw = props.personalizedPageData?.gallery_images;
    if (!raw) return [];
    const parsed = typeof raw === "string" ? JSON.parse(raw) : raw;
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
});

const imageBase = `${import.meta.env.VITE_API_URL}/storage/`;

function imageUrl(path: string) {
  if (!path) return "";
  if (path.startsWith("http")) return path;
  return imageBase + path;
}

const mapSrc = computed<string | null>(() => {
  if (props.project.address && props.project.city) {
    const q = encodeURIComponent(
      `${props.project.address}, ${props.project.city}, ${props.project.state || ""}, Brasil`
    );
    return `https://maps.google.com/maps?q=${q}&output=embed`;
  }
  return null;
});

const googleMapsUrl = computed(() => {
  if (props.project.address && props.project.city) {
    const q = encodeURIComponent(
      `${props.project.address}, ${props.project.city}, ${props.project.state || ""}, Brasil`
    );
    return `https://www.google.com/maps/search/?api=1&query=${q}`;
  }
  return null;
});

const whatsappUrl = computed(() => {
  if (!props.project.phone) return null;
  const digits = props.project.phone.replace(/\D/g, "");
  const number = digits.startsWith("55") ? digits : `55${digits}`;
  const text = encodeURIComponent(`Olá! Vi a página do projeto "${props.project.name}" no Mirante Social e gostaria de saber mais.`);
  return `https://wa.me/${number}?text=${text}`;
});

const contactRef = ref<HTMLElement | null>(null);
function scrollToContact() {
  contactRef.value?.scrollIntoView({ behavior: "smooth" });
}

const formattedAddress = computed(() => {
  const parts: string[] = [];
  if (props.project.address) parts.push(props.project.address);
  if (props.project.district) parts.push(props.project.district);
  if (props.project.city && props.project.state) parts.push(`${props.project.city} / ${props.project.state}`);
  else if (props.project.city) parts.push(props.project.city);
  if (props.project.zip) parts.push(`CEP ${props.project.zip}`);
  return parts;
});

const targetsList = computed<string[]>(() => {
  if (!props.project.targets) return [];
  if (Array.isArray(props.project.targets)) return props.project.targets;
  try { return JSON.parse(props.project.targets as string); } catch { return []; }
});
</script>

<template>
  <div class="t2-page" :style="palette">

    <!-- ══════════════════════ HEADER HERO ══════════════════════ -->
    <header class="t2-header" :style="{ background: 'var(--accent-hero)' }">
      <!-- Imagem de capa como fundo do header -->
      <div v-if="project.image" class="t2-header-bg" :style="{ backgroundImage: `url(${imageUrl(project.image)})` }"></div>
      <div class="t2-header-overlay"></div>

      <nav class="t2-nav">
        <div class="t2-nav-inner">
          <span class="t2-nav-area">
            <span class="t2-nav-dot"></span>
            {{ project.area || 'Projeto Social' }}
          </span>
          <button class="t2-nav-cta" @click="scrollToContact">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Contato
          </button>
        </div>
      </nav>

      <div class="t2-header-content">
        <div class="t2-header-inner">
          <div class="t2-header-text">
            <span v-if="project.area" class="t2-area-badge">{{ project.area }}</span>
            <h1 class="t2-header-name">{{ project.name }}</h1>
            <p v-if="project.mission" class="t2-header-mission">{{ project.mission }}</p>
            <p v-else class="t2-header-mission">{{ project.description?.slice(0, 160) }}{{ (project.description?.length || 0) > 160 ? '…' : '' }}</p>

            <div class="t2-header-pills">
              <span v-if="project.city" class="t2-header-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ project.district ? project.district + ', ' : '' }}{{ project.city }}
              </span>
              <span v-if="project.hours" class="t2-header-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                {{ project.hours }}
              </span>
              <span v-for="t in targetsList.slice(0,2)" :key="t" class="t2-header-pill">{{ t }}</span>
            </div>
          </div>

          <div class="t2-header-actions">
            <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="t2-action-primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Falar pelo WhatsApp
            </a>
            <a v-if="googleMapsUrl" :href="googleMapsUrl" target="_blank" rel="noopener noreferrer" class="t2-action-secondary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Como chegar
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- ══════════════════════ CORPO PRINCIPAL ══════════════════════ -->
    <div class="t2-body">
      <div class="t2-main">

        <!-- COLUNA ESQUERDA -->
        <div class="t2-col-left">

          <!-- Sobre o Projeto -->
          <section class="t2-card">
            <div class="t2-card-header">
              <span class="t2-card-bar" :style="{ background: 'var(--accent)' }"></span>
              <h2 class="t2-card-title" :style="{ color: 'var(--accent-darker)' }">Sobre o Projeto</h2>
            </div>
            <p class="t2-card-body">{{ project.description }}</p>
            <div v-if="targetsList.length" class="t2-targets">
              <span class="t2-targets-label">Atende:</span>
              <div class="t2-targets-chips">
                <span v-for="t in targetsList" :key="t" class="t2-target-chip" :style="{ background: 'var(--accent-05)', borderColor: 'var(--accent-20)', color: 'var(--accent-darker)' }">{{ t }}</span>
              </div>
            </div>
          </section>

          <!-- Missão -->
          <section v-if="project.mission" class="t2-card t2-card-mission" :style="{ background: 'var(--accent-05)', borderColor: 'var(--accent-20)' }">
            <svg class="t2-mission-icon" viewBox="0 0 24 24" fill="currentColor" :style="{ color: 'var(--accent)' }">
              <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
            <blockquote class="t2-mission-text" :style="{ color: 'var(--accent-darker)' }">{{ project.mission }}</blockquote>
          </section>

          <!-- Como Ajudar -->
          <section v-if="project.needs" class="t2-card">
            <div class="t2-card-header">
              <span class="t2-card-bar" :style="{ background: 'var(--accent)' }"></span>
              <h2 class="t2-card-title" :style="{ color: 'var(--accent-darker)' }">Como Ajudar</h2>
            </div>
            <p class="t2-card-body">{{ project.needs }}</p>
            <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="t2-needs-btn" :style="{ background: 'var(--accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Entrar em contato
            </a>
          </section>

          <!-- Galeria -->
          <section v-if="galleryImages.length > 0" class="t2-card">
            <div class="t2-card-header">
              <span class="t2-card-bar" :style="{ background: 'var(--accent)' }"></span>
              <h2 class="t2-card-title" :style="{ color: 'var(--accent-darker)' }">Galeria de Fotos</h2>
            </div>
            <div class="t2-gallery" :class="`t2-gallery--${Math.min(galleryImages.length, 6)}`">
              <div v-for="(img, i) in galleryImages" :key="i" class="t2-gallery-item" :class="{ 't2-gallery-item--wide': i === 0 && galleryImages.length >= 3 }">
                <img :src="imageUrl(img)" :alt="`Foto ${i + 1} — ${project.name}`" class="t2-gallery-img" loading="lazy" />
              </div>
            </div>
          </section>

        </div>

        <!-- COLUNA DIREITA -->
        <div class="t2-col-right">

          <!-- Card de contato rápido -->
          <section ref="contactRef" class="t2-card t2-card-contact" id="t2-contato">
            <div class="t2-contact-header" :style="{ background: 'var(--accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Contato
            </div>
            <ul class="t2-contact-list">
              <li v-if="project.phone">
                <a :href="`tel:${project.phone}`" class="t2-contact-item">
                  <span class="t2-contact-icon" :style="{ background: 'var(--accent-10)', color: 'var(--accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  </span>
                  <div class="t2-contact-info">
                    <span class="t2-contact-label">Telefone</span>
                    <span class="t2-contact-val">{{ project.phone }}</span>
                  </div>
                </a>
              </li>
              <li v-if="project.email">
                <a :href="`mailto:${project.email}`" class="t2-contact-item">
                  <span class="t2-contact-icon" :style="{ background: 'var(--accent-10)', color: 'var(--accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  </span>
                  <div class="t2-contact-info">
                    <span class="t2-contact-label">E-mail</span>
                    <span class="t2-contact-val">{{ project.email }}</span>
                  </div>
                </a>
              </li>
              <li v-if="project.site">
                <a :href="project.site" target="_blank" rel="noopener noreferrer" class="t2-contact-item">
                  <span class="t2-contact-icon" :style="{ background: 'var(--accent-10)', color: 'var(--accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                  </span>
                  <div class="t2-contact-info">
                    <span class="t2-contact-label">Site</span>
                    <span class="t2-contact-val">{{ project.site }}</span>
                  </div>
                </a>
              </li>
              <li v-if="project.instagram">
                <a :href="project.instagram" target="_blank" rel="noopener noreferrer" class="t2-contact-item">
                  <span class="t2-contact-icon" :style="{ background: 'var(--accent-10)', color: 'var(--accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                  </span>
                  <div class="t2-contact-info">
                    <span class="t2-contact-label">Instagram</span>
                    <span class="t2-contact-val">Ver perfil</span>
                  </div>
                </a>
              </li>
              <li v-if="project.facebook">
                <a :href="project.facebook" target="_blank" rel="noopener noreferrer" class="t2-contact-item">
                  <span class="t2-contact-icon" :style="{ background: 'var(--accent-10)', color: 'var(--accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  </span>
                  <div class="t2-contact-info">
                    <span class="t2-contact-label">Facebook</span>
                    <span class="t2-contact-val">Ver página</span>
                  </div>
                </a>
              </li>
            </ul>
            <div v-if="whatsappUrl" class="t2-contact-footer">
              <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="t2-whatsapp-btn" :style="{ background: 'var(--accent)' }">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Falar pelo WhatsApp
              </a>
            </div>
          </section>

          <!-- Localização -->
          <section class="t2-card t2-card-map">
            <div class="t2-card-header">
              <span class="t2-card-bar" :style="{ background: 'var(--accent)' }"></span>
              <h2 class="t2-card-title" :style="{ color: 'var(--accent-darker)' }">Localização</h2>
            </div>

            <div v-if="mapSrc" class="t2-map-wrapper">
              <iframe :src="mapSrc" class="t2-map-iframe" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="Mapa de localização"></iframe>
            </div>

            <address class="t2-address">
              <div v-for="(line, i) in formattedAddress" :key="i" class="t2-address-line">
                <svg v-if="i === 0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13" :style="{ color: 'var(--accent)', flexShrink: 0, marginTop: '3px' }"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>{{ line }}</span>
              </div>
            </address>

            <a v-if="googleMapsUrl" :href="googleMapsUrl" target="_blank" rel="noopener noreferrer" class="t2-map-btn" :style="{ background: 'var(--accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              Ver no Google Maps
            </a>
          </section>

          <!-- Horários -->
          <section v-if="project.hours" class="t2-card">
            <div class="t2-card-header">
              <span class="t2-card-bar" :style="{ background: 'var(--accent)' }"></span>
              <h2 class="t2-card-title" :style="{ color: 'var(--accent-darker)' }">Horários de Atendimento</h2>
            </div>
            <div class="t2-hours-block" :style="{ background: 'var(--accent-05)', borderColor: 'var(--accent-20)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" :style="{ color: 'var(--accent)', flexShrink: 0 }"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <p class="t2-hours-text">{{ project.hours }}</p>
            </div>
          </section>

          <!-- Área de atuação -->
          <section v-if="project.area" class="t2-card t2-card-area" :style="{ background: 'var(--accent)', borderColor: 'transparent' }">
            <div class="t2-area-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
              <p class="t2-area-label">Área de atuação</p>
              <p class="t2-area-val">{{ project.area }}</p>
            </div>
          </section>

        </div>
      </div>
    </div>

    <!-- ══════════════════════ ACTION BAR ══════════════════════ -->
    <div v-if="project.phone || googleMapsUrl" class="t2-action-bar" :style="{ background: 'var(--accent-darker)' }">
      <div class="t2-action-bar-inner">
        <div class="t2-action-bar-text">
          <p class="t2-action-bar-headline">Quer fazer parte desta iniciativa?</p>
          <p class="t2-action-bar-sub">Entre em contato e saiba como contribuir com {{ project.name }}.</p>
        </div>
        <div class="t2-action-bar-btns">
          <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="t2-action-btn t2-action-btn--white">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            WhatsApp
          </a>
          <a v-if="googleMapsUrl" :href="googleMapsUrl" target="_blank" rel="noopener noreferrer" class="t2-action-btn t2-action-btn--outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Localização
          </a>
        </div>
      </div>
    </div>

    <!-- ══════════════════════ FOOTER ══════════════════════ -->
    <footer class="t2-footer" :style="{ background: 'var(--accent-darker)' }">
      <div class="t2-footer-inner">
        <div class="t2-footer-brand">
          <span class="t2-footer-name">{{ project.name }}</span>
          <span v-if="project.area" class="t2-footer-area">{{ project.area }}</span>
        </div>
        <p class="t2-footer-powered">
          Esta página faz parte do
          <a href="https://mirantesocial.com.br" target="_blank" rel="noopener noreferrer" class="t2-footer-link">Mirante Social</a>
        </p>
      </div>
    </footer>

  </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.t2-page {
  width: 100%;
  min-height: 100vh;
  font-family: 'Nunito', sans-serif;
  background: #F8FAFC;
  color: #1C1C1C;
}

/* ── HEADER HERO ── */
.t2-header {
  position: relative;
  width: 100%;
  min-height: 480px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.t2-header-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transform: scale(1.03);
  transition: transform 10s ease;
}
.t2-header:hover .t2-header-bg { transform: scale(1.06); }
.t2-header-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg, rgba(0,0,0,0.38) 0%, rgba(0,0,0,0.55) 100%);
}

/* nav */
.t2-nav {
  position: relative;
  z-index: 10;
  border-bottom: 1px solid rgba(255,255,255,0.10);
}
.t2-nav-inner {
  max-width: 1080px;
  margin: 0 auto;
  padding: 0 2rem;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.t2-nav-area {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  font-weight: 800;
  color: rgba(255,255,255,0.9);
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.t2-nav-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #fff;
  flex-shrink: 0;
}
.t2-nav-cta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.28);
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.82rem;
  font-weight: 800;
  padding: 6px 14px;
  border-radius: 999px;
  cursor: pointer;
  backdrop-filter: blur(6px);
  transition: background 0.15s;
}
.t2-nav-cta:hover { background: rgba(255,255,255,0.25); }

/* header content */
.t2-header-content {
  position: relative;
  z-index: 5;
  flex: 1;
  display: flex;
  align-items: flex-end;
  padding-bottom: 0;
}
.t2-header-inner {
  max-width: 1080px;
  margin: 0 auto;
  padding: 2.5rem 2rem 3rem;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 2rem;
  flex-wrap: wrap;
}
.t2-header-text { flex: 1; min-width: 260px; }
.t2-area-badge {
  display: inline-block;
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.35);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 4px 14px;
  border-radius: 999px;
  margin-bottom: 0.85rem;
  backdrop-filter: blur(6px);
}
.t2-header-name {
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 900;
  color: #fff;
  line-height: 1.08;
  text-shadow: 0 2px 16px rgba(0,0,0,0.25);
  margin-bottom: 0.75rem;
  letter-spacing: -0.02em;
}
.t2-header-mission {
  font-size: 1.05rem;
  color: rgba(255,255,255,0.88);
  font-weight: 600;
  line-height: 1.6;
  max-width: 520px;
  margin-bottom: 1.25rem;
}
.t2-header-pills {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.t2-header-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.24);
  color: rgba(255,255,255,0.88);
  font-size: 0.8rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 999px;
  backdrop-filter: blur(4px);
}
.t2-header-actions {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  align-self: flex-end;
  flex-shrink: 0;
}
.t2-action-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  color: var(--accent-darker);
  font-family: 'Nunito', sans-serif;
  font-size: 0.95rem;
  font-weight: 800;
  padding: 12px 22px;
  border-radius: 999px;
  text-decoration: none;
  white-space: nowrap;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  transition: transform 0.15s, box-shadow 0.15s;
}
.t2-action-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.28); }
.t2-action-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.12);
  border: 1.5px solid rgba(255,255,255,0.32);
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.88rem;
  font-weight: 800;
  padding: 10px 18px;
  border-radius: 999px;
  text-decoration: none;
  backdrop-filter: blur(6px);
  transition: background 0.15s;
}
.t2-action-secondary:hover { background: rgba(255,255,255,0.2); }

/* ── BODY ── */
.t2-body {
  background: #F8FAFC;
}
.t2-main {
  max-width: 1080px;
  margin: 0 auto;
  padding: 2.25rem 2rem 3rem;
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 1.5rem;
  align-items: start;
}

/* ── COLUMNS ── */
.t2-col-left, .t2-col-right {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

/* ── CARDS ── */
.t2-card {
  background: #fff;
  border-radius: 1.15rem;
  padding: 1.75rem;
  box-shadow: 0 2px 16px rgba(0,0,0,0.06);
  border: 1px solid #E9EDF2;
}
.t2-card-mission {
  border: 1.5px solid;
  padding: 1.75rem;
  position: relative;
  overflow: hidden;
}
.t2-card-area {
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-radius: 1.15rem;
}
.t2-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 1.15rem;
}
.t2-card-bar {
  display: block;
  width: 4px;
  height: 20px;
  border-radius: 3px;
  flex-shrink: 0;
}
.t2-card-title {
  font-size: 0.95rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0;
}
.t2-card-body {
  font-size: 1.02rem;
  color: #374151;
  line-height: 1.85;
}

/* targets */
.t2-targets { margin-top: 1.25rem; }
.t2-targets-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #9CA3AF; display: block; margin-bottom: 0.5rem; }
.t2-targets-chips { display: flex; gap: 0.45rem; flex-wrap: wrap; }
.t2-target-chip { display: inline-block; font-size: 0.82rem; font-weight: 700; padding: 4px 13px; border-radius: 999px; border: 1px solid; }

/* mission */
.t2-mission-icon { width: 32px; height: 32px; opacity: 0.25; margin-bottom: 0.75rem; }
.t2-mission-text { font-size: 1.1rem; font-style: italic; font-weight: 700; line-height: 1.7; }

/* needs btn */
.t2-needs-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.9rem;
  font-weight: 800;
  padding: 10px 20px;
  border-radius: 999px;
  text-decoration: none;
  margin-top: 1.25rem;
  transition: opacity 0.15s, transform 0.15s;
}
.t2-needs-btn:hover { opacity: 0.88; transform: translateY(-1px); }

/* gallery */
.t2-gallery {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.6rem;
  grid-auto-rows: 130px;
}
.t2-gallery-item {
  border-radius: 0.7rem;
  overflow: hidden;
  background: var(--accent-light);
  position: relative;
}
.t2-gallery-item--wide {
  grid-column: span 2;
  grid-row: span 2;
}
.t2-gallery-img {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.35s ease;
}
.t2-gallery-img:hover { transform: scale(1.05); }

/* contact card */
.t2-card-contact { padding: 0; overflow: hidden; }
.t2-contact-header {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.85rem 1.25rem;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.t2-contact-list { list-style: none; padding: 0.5rem 0; }
.t2-contact-item {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.65rem 1.25rem;
  text-decoration: none;
  transition: background 0.15s;
  cursor: pointer;
}
.t2-contact-item:hover { background: #F9FAFB; }
.t2-contact-icon {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.t2-contact-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.t2-contact-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #9CA3AF; }
.t2-contact-val { font-size: 0.9rem; font-weight: 700; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.t2-contact-footer { padding: 0.75rem 1.25rem 1.25rem; }
.t2-whatsapp-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 0.8rem;
  border-radius: 999px;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.9rem;
  font-weight: 800;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
}
.t2-whatsapp-btn:hover { opacity: 0.88; transform: translateY(-1px); }

/* map */
.t2-card-map { padding: 1.75rem 1.75rem 1.25rem; }
.t2-map-wrapper { border-radius: 0.75rem; overflow: hidden; margin-bottom: 1rem; line-height: 0; border: 1px solid #E9EDF2; }
.t2-map-iframe { width: 100%; height: 190px; border: none; display: block; }
.t2-address { font-style: normal; margin-bottom: 1.1rem; }
.t2-address-line { display: flex; align-items: flex-start; gap: 6px; font-size: 0.9rem; color: #374151; line-height: 1.6; font-weight: 600; }
.t2-map-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.88rem;
  font-weight: 800;
  padding: 9px 18px;
  border-radius: 999px;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
}
.t2-map-btn:hover { opacity: 0.88; transform: translateY(-1px); }

/* hours */
.t2-hours-block {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  border: 1px solid;
  border-radius: 0.75rem;
  padding: 1rem 1.15rem;
}
.t2-hours-text { font-size: 0.97rem; color: #374151; line-height: 1.7; font-weight: 600; }

/* area card */
.t2-area-icon { color: rgba(255,255,255,0.8); flex-shrink: 0; }
.t2-area-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.65); margin-bottom: 0.2rem; }
.t2-area-val { font-size: 1.1rem; font-weight: 900; color: #fff; }

/* ── ACTION BAR ── */
.t2-action-bar { padding: 2.5rem 2rem; }
.t2-action-bar-inner {
  max-width: 1080px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  flex-wrap: wrap;
}
.t2-action-bar-text { flex: 1; }
.t2-action-bar-headline { font-size: 1.3rem; font-weight: 900; color: #fff; margin-bottom: 0.3rem; }
.t2-action-bar-sub { font-size: 0.9rem; color: rgba(255,255,255,0.7); font-weight: 600; }
.t2-action-bar-btns { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.t2-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: 'Nunito', sans-serif;
  font-size: 0.95rem;
  font-weight: 800;
  padding: 12px 24px;
  border-radius: 999px;
  text-decoration: none;
  white-space: nowrap;
  transition: transform 0.15s, opacity 0.15s;
}
.t2-action-btn--white { background: #fff; color: var(--accent-darker); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.t2-action-btn--outline { border: 1.5px solid rgba(255,255,255,0.3); color: #fff; }
.t2-action-btn:hover { transform: translateY(-2px); opacity: 0.92; }

/* ── FOOTER ── */
.t2-footer {
  padding: 1.75rem 2rem;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.t2-footer-inner {
  max-width: 1080px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}
.t2-footer-brand { display: flex; flex-direction: column; gap: 2px; }
.t2-footer-name { font-size: 0.9rem; font-weight: 800; color: rgba(255,255,255,0.85); }
.t2-footer-area { font-size: 0.78rem; font-weight: 600; color: rgba(255,255,255,0.5); }
.t2-footer-powered { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.5); }
.t2-footer-link { color: rgba(255,255,255,0.8); font-weight: 800; text-decoration: none; }
.t2-footer-link:hover { color: #fff; text-decoration: underline; }

/* ── RESPONSIVE ── */
@media (max-width: 820px) {
  .t2-main { grid-template-columns: 1fr; padding: 1.75rem 1.25rem 2.5rem; }
  .t2-header-inner { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
  .t2-header-actions { flex-direction: row; align-self: flex-start; }
  .t2-action-bar-inner { flex-direction: column; align-items: flex-start; }
  .t2-footer-inner { flex-direction: column; gap: 0.5rem; }
}
@media (max-width: 540px) {
  .t2-header { min-height: 400px; }
  .t2-header-inner { padding: 2rem 1.25rem 2.5rem; }
  .t2-gallery { grid-template-columns: repeat(2, 1fr); }
  .t2-gallery-item--wide { grid-column: span 2; grid-row: span 1; }
  .t2-action-bar-btns { width: 100%; flex-direction: column; }
  .t2-action-btn { justify-content: center; }
}
</style>

<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  project: {
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
  personalizedPageData: {
    id: number;
    url?: string;
    template: number;
    caption?: string;
    gallery_images?: string;
  };
}>();

const apiUrl = import.meta.env.VITE_API_URL as string;

function imageUrl(path: string) {
  if (!path) return "";
  if (path.startsWith("http")) return path;
  return `${apiUrl}/storage/${path}`;
}

const themeColor = computed(() => props.project.color || "#059669");

function hexToRgb(hex: string): { r: number; g: number; b: number } {
  const clean = hex.replace("#", "");
  return {
    r: parseInt(clean.substring(0, 2), 16),
    g: parseInt(clean.substring(2, 4), 16),
    b: parseInt(clean.substring(4, 6), 16),
  };
}
function hexToRgba(hex: string, alpha: number): string {
  const { r, g, b } = hexToRgb(hex);
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
function mixWithWhite(hex: string, amount: number): string {
  const { r, g, b } = hexToRgb(hex);
  const mix = (c: number) => Math.round(c + (255 - c) * amount);
  return `rgb(${mix(r)}, ${mix(g)}, ${mix(b)})`;
}
function mixWithBlack(hex: string, amount: number): string {
  const { r, g, b } = hexToRgb(hex);
  const mix = (c: number) => Math.round(c * (1 - amount));
  return `rgb(${mix(r)}, ${mix(g)}, ${mix(b)})`;
}

const palette = computed(() => {
  const c = themeColor.value;
  return {
    "--t1-accent":        c,
    "--t1-accent-dark":   mixWithBlack(c, 0.25),
    "--t1-accent-darker": mixWithBlack(c, 0.45),
    "--t1-accent-light":  mixWithWhite(c, 0.80),
    "--t1-accent-pale":   mixWithWhite(c, 0.93),
    "--t1-accent-05":     hexToRgba(c, 0.05),
    "--t1-accent-10":     hexToRgba(c, 0.10),
    "--t1-accent-20":     hexToRgba(c, 0.20),
    "--t1-accent-50":     hexToRgba(c, 0.50),
    "--t1-accent-80":     hexToRgba(c, 0.80),
    "--t1-hero-overlay":  `linear-gradient(to bottom, ${hexToRgba(c, 0.30)} 0%, ${hexToRgba(c, 0.72)} 50%, ${mixWithBlack(c, 0.60)} 100%)`,
  };
});

const coverImage = computed(() =>
  props.project.image ? `${apiUrl}/storage/${props.project.image}` : null
);

const galleryImages = computed<string[]>(() => {
  const raw = props.personalizedPageData?.gallery_images;
  if (!raw) return [];
  try {
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) return parsed.slice(0, 6);
    return [];
  } catch {
    return [];
  }
});

const whatsappLink = computed(() => {
  if (!props.project.phone) return null;
  const digits = props.project.phone.replace(/\D/g, "");
  const text = encodeURIComponent(
    `Olá! Vi a página do projeto "${props.project.name}" no Mirante Social e gostaria de saber mais.`
  );
  return `https://wa.me/55${digits}?text=${text}`;
});

const mapsLink = computed(() => {
  const parts = [props.project.address, props.project.district, props.project.city, props.project.state].filter(Boolean).join(", ");
  return parts ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(parts)}` : null;
});

const locationFull = computed(() => {
  const parts = [props.project.address, props.project.district].filter(Boolean);
  return parts.join(", ");
});

const locationCity = computed(() =>
  [props.project.city, props.project.state].filter(Boolean).join(" / ")
);

const targetsList = computed<string[]>(() => {
  if (!props.project.targets) return [];
  if (Array.isArray(props.project.targets)) return props.project.targets;
  try { return JSON.parse(props.project.targets as string); } catch { return []; }
});
</script>

<template>
  <div class="t1-page" :style="palette">

    <!-- ═══════════════ NAVBAR ═══════════════ -->
    <nav class="t1-nav">
      <div class="t1-nav-inner">
        <span class="t1-nav-brand">
          <span class="t1-nav-dot" :style="{ background: 'var(--t1-accent)' }"></span>
          {{ project.area || 'Projeto Social' }}
        </span>
        <div class="t1-nav-actions">
          <a v-if="whatsappLink" :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="t1-nav-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            WhatsApp
          </a>
        </div>
      </div>
    </nav>

    <!-- ═══════════════ HERO FULLBLEED ═══════════════ -->
    <section class="t1-hero">
      <div v-if="coverImage" class="t1-hero-bg" :style="{ backgroundImage: `url(${coverImage})` }"></div>
      <div v-else class="t1-hero-bg t1-hero-bg--solid" :style="{ background: `linear-gradient(150deg, var(--t1-accent), var(--t1-accent-darker))` }"></div>
      <div class="t1-hero-overlay"></div>

      <div class="t1-hero-inner">
        <span v-if="project.area" class="t1-area-badge">{{ project.area }}</span>
        <h1 class="t1-hero-title">{{ project.name }}</h1>
        <p class="t1-hero-mission">{{ project.mission || project.description }}</p>

        <div class="t1-hero-ctas">
          <a v-if="whatsappLink" :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="t1-hero-btn t1-hero-btn--primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Falar pelo WhatsApp
          </a>
          <a v-if="mapsLink" :href="mapsLink" target="_blank" rel="noopener noreferrer" class="t1-hero-btn t1-hero-btn--ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Como chegar
          </a>
        </div>

        <div class="t1-chips">
          <span v-if="locationCity" class="t1-chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ locationCity }}
          </span>
          <span v-if="project.hours" class="t1-chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ project.hours }}
          </span>
          <span v-for="t in targetsList.slice(0, 3)" :key="t" class="t1-chip">{{ t }}</span>
        </div>
      </div>

      <div class="t1-scroll-hint">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </section>

    <!-- ═══════════════ SOBRE ═══════════════ -->
    <section class="t1-section t1-sobre">
      <div class="t1-container">
        <div class="t1-sobre-grid">
          <div class="t1-sobre-main">
            <div class="t1-eyebrow" :style="{ color: 'var(--t1-accent)' }">Sobre o Projeto</div>
            <h2 class="t1-section-title">Conheça nossa missão</h2>

            <blockquote v-if="project.mission" class="t1-mission-quote" :style="{ borderColor: 'var(--t1-accent)', background: 'var(--t1-accent-05)' }">
              <svg class="t1-quote-icon" viewBox="0 0 24 24" fill="currentColor" :style="{ color: 'var(--t1-accent)' }">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
              </svg>
              <p>{{ project.mission }}</p>
            </blockquote>

            <p class="t1-description">{{ project.description }}</p>

            <div v-if="targetsList.length" class="t1-audiences">
              <span class="t1-audiences-label">Quem atendemos:</span>
              <div class="t1-audience-chips">
                <span v-for="t in targetsList" :key="t" class="t1-audience-chip" :style="{ background: 'var(--t1-accent-05)', borderColor: 'var(--t1-accent-20)', color: 'var(--t1-accent)' }">{{ t }}</span>
              </div>
            </div>
          </div>

          <div class="t1-sobre-sidebar">
            <!-- Card de contato -->
            <div class="t1-contact-card" :style="{ borderColor: 'var(--t1-accent-20)' }">
              <div class="t1-contact-card-header" :style="{ background: 'var(--t1-accent)', color: '#fff' }">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Contato rápido
              </div>
              <div class="t1-contact-card-body">
                <a v-if="project.phone" :href="`tel:${project.phone}`" class="t1-contact-row">
                  <div class="t1-contact-icon-wrap" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  </div>
                  <span>{{ project.phone }}</span>
                </a>
                <a v-if="project.email" :href="`mailto:${project.email}`" class="t1-contact-row">
                  <div class="t1-contact-icon-wrap" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  </div>
                  <span>{{ project.email }}</span>
                </a>
                <a v-if="project.instagram" :href="project.instagram" target="_blank" rel="noopener noreferrer" class="t1-contact-row">
                  <div class="t1-contact-icon-wrap" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
                  </div>
                  <span>Instagram</span>
                </a>
                <a v-if="project.facebook" :href="project.facebook" target="_blank" rel="noopener noreferrer" class="t1-contact-row">
                  <div class="t1-contact-icon-wrap" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  </div>
                  <span>Facebook</span>
                </a>
                <div v-if="project.hours" class="t1-contact-row t1-contact-row--static">
                  <div class="t1-contact-icon-wrap" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  </div>
                  <span>{{ project.hours }}</span>
                </div>
              </div>
              <div v-if="whatsappLink" class="t1-contact-card-footer">
                <a :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="t1-cta-whatsapp" :style="{ background: 'var(--t1-accent)' }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  Falar pelo WhatsApp
                </a>
              </div>
            </div>

            <!-- Localização -->
            <div v-if="locationFull || locationCity" class="t1-loc-card" :style="{ borderColor: 'var(--t1-accent-20)', background: 'var(--t1-accent-05)' }">
              <div class="t1-loc-icon" :style="{ color: 'var(--t1-accent)' }">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <p class="t1-loc-label">Localização</p>
                <p class="t1-loc-val" v-if="locationFull">{{ locationFull }}</p>
                <p class="t1-loc-val" v-if="locationCity">{{ locationCity }}</p>
                <a v-if="mapsLink" :href="mapsLink" target="_blank" rel="noopener noreferrer" class="t1-loc-link" :style="{ color: 'var(--t1-accent)' }">Ver no mapa →</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ FAIXA DE IMPACTO ═══════════════ -->
    <section class="t1-impact" :style="{ background: 'var(--t1-accent)', color: '#fff' }">
      <div class="t1-container t1-impact-inner">
        <div class="t1-impact-text">
          <h2 class="t1-impact-headline">Faça parte desta transformação</h2>
          <p class="t1-impact-sub">Cada contribuição importa. Juntos fazemos a diferença na comunidade.</p>
        </div>
        <div class="t1-impact-ctas">
          <a v-if="whatsappLink" :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="t1-impact-btn t1-impact-btn--white">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Quero ajudar
          </a>
          <a v-if="mapsLink" :href="mapsLink" target="_blank" rel="noopener noreferrer" class="t1-impact-btn t1-impact-btn--outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Como chegar
          </a>
          <a v-if="project.site" :href="project.site" target="_blank" rel="noopener noreferrer" class="t1-impact-btn t1-impact-btn--outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Visitar site
          </a>
        </div>
      </div>
    </section>

    <!-- ═══════════════ GALERIA ═══════════════ -->
    <section v-if="galleryImages.length" class="t1-section t1-galeria">
      <div class="t1-container">
        <div class="t1-eyebrow" :style="{ color: 'var(--t1-accent)' }">Galeria de fotos</div>
        <h2 class="t1-section-title">Momentos do projeto</h2>
        <div class="t1-gallery-grid" :class="`t1-gallery-grid--${Math.min(galleryImages.length, 6)}`">
          <div v-for="(img, i) in galleryImages" :key="i" class="t1-gallery-item" :class="{ 't1-gallery-item--featured': i === 0 && galleryImages.length >= 3 }">
            <img :src="imageUrl(img)" :alt="`Foto ${i + 1} do projeto`" loading="lazy" />
            <div class="t1-gallery-overlay"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ COMO AJUDAR ═══════════════ -->
    <section v-if="project.needs" class="t1-section t1-needs-section">
      <div class="t1-container">
        <div class="t1-needs-wrap">
          <div class="t1-needs-left">
            <div class="t1-eyebrow" :style="{ color: 'var(--t1-accent)' }">Como ajudar</div>
            <h2 class="t1-section-title" style="margin-bottom: 1rem">Precisamos de você</h2>
            <p class="t1-needs-text">{{ project.needs }}</p>
            <a v-if="whatsappLink" :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="t1-needs-btn" :style="{ background: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Entrar em contato
            </a>
          </div>
          <div class="t1-needs-deco" :style="{ background: 'var(--t1-accent-05)', borderColor: 'var(--t1-accent-20)' }">
            <div class="t1-needs-icon-big" :style="{ color: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="64" height="64"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <p class="t1-needs-quote" :style="{ color: 'var(--t1-accent-darker)' }">
              "Sozinhos fazemos pouco, juntos fazemos muito."
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ INFORMAÇÕES ═══════════════ -->
    <section class="t1-section t1-info-section">
      <div class="t1-container">
        <div class="t1-eyebrow" :style="{ color: 'var(--t1-accent)' }">Informações</div>
        <h2 class="t1-section-title">Saiba mais sobre nós</h2>

        <div class="t1-info-grid">
          <div v-if="locationFull || locationCity" class="t1-info-card" :style="{ '--card-accent': 'var(--t1-accent)' }">
            <div class="t1-info-icon" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="t1-info-content">
              <span class="t1-info-label">Localização</span>
              <p class="t1-info-value">
                <span v-if="locationFull">{{ locationFull }}<br></span>
                <span v-if="locationCity">{{ locationCity }}<br></span>
                <span v-if="project.zip" style="color: #9CA3AF">CEP {{ project.zip }}</span>
              </p>
            </div>
          </div>

          <div v-if="project.hours" class="t1-info-card">
            <div class="t1-info-icon" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="t1-info-content">
              <span class="t1-info-label">Horários de atendimento</span>
              <p class="t1-info-value">{{ project.hours }}</p>
            </div>
          </div>

          <div v-if="project.phone || project.email" class="t1-info-card">
            <div class="t1-info-icon" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.85a16 16 0 0 0 6.29 6.29l1.85-1.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div class="t1-info-content">
              <span class="t1-info-label">Contato</span>
              <p class="t1-info-value">
                <a v-if="project.phone" :href="`tel:${project.phone}`" class="t1-contact-link" :style="{ color: 'var(--t1-accent)' }">{{ project.phone }}</a>
                <br v-if="project.phone && project.email">
                <a v-if="project.email" :href="`mailto:${project.email}`" class="t1-contact-link" :style="{ color: 'var(--t1-accent)' }">{{ project.email }}</a>
              </p>
            </div>
          </div>

          <div v-if="project.instagram || project.facebook" class="t1-info-card">
            <div class="t1-info-icon" :style="{ background: 'var(--t1-accent-10)', color: 'var(--t1-accent)' }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </div>
            <div class="t1-info-content">
              <span class="t1-info-label">Redes sociais</span>
              <div class="t1-social-links">
                <a v-if="project.instagram" :href="project.instagram" target="_blank" rel="noopener noreferrer" class="t1-social-btn" :style="{ color: 'var(--t1-accent)', borderColor: 'var(--t1-accent-20)' }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
                  Instagram
                </a>
                <a v-if="project.facebook" :href="project.facebook" target="_blank" rel="noopener noreferrer" class="t1-social-btn" :style="{ color: 'var(--t1-accent)', borderColor: 'var(--t1-accent-20)' }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  Facebook
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="t1-footer" :style="{ background: 'var(--t1-accent-darker)' }">
      <div class="t1-footer-inner">
        <div class="t1-footer-brand">
          <span class="t1-footer-org">{{ project.name }}</span>
          <span class="t1-footer-sep">·</span>
          <span class="t1-footer-area">{{ project.area }}</span>
        </div>
        <div class="t1-footer-powered">
          <span>Esta página faz parte do</span>
          <a href="https://mirantesocial.com.br" target="_blank" rel="noopener noreferrer" class="t1-footer-link">Mirante Social</a>
        </div>
      </div>
    </footer>

  </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }

.t1-page {
  width: 100%;
  min-height: 100vh;
  font-family: 'Nunito', sans-serif;
  background: var(--t1-accent-pale);
  color: #1C1C1C;
}

.t1-container {
  max-width: 1040px;
  margin: 0 auto;
  padding: 0 1.75rem;
}

/* ── NAVBAR ── */
.t1-nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  background: rgba(255,255,255,0.94);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(0,0,0,0.06);
}
.t1-nav-inner {
  max-width: 1040px;
  margin: 0 auto;
  padding: 0 1.75rem;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.t1-nav-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  font-weight: 800;
  color: #1C1C1C;
  letter-spacing: -0.01em;
}
.t1-nav-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}
.t1-nav-cta {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.82rem;
  font-weight: 800;
  font-family: 'Nunito', sans-serif;
  padding: 7px 16px;
  border-radius: 999px;
  background: var(--t1-accent);
  color: #fff;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
}
.t1-nav-cta:hover { opacity: 0.88; transform: translateY(-1px); }
.t1-nav-cta svg { width: 14px; height: 14px; }

/* ── HERO ── */
.t1-hero {
  position: relative;
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow: hidden;
}
.t1-hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transform: scale(1.04);
  transition: transform 10s ease;
}
.t1-hero:hover .t1-hero-bg { transform: scale(1.08); }
.t1-hero-bg--solid { transform: none; }
.t1-hero-overlay {
  position: absolute;
  inset: 0;
  background: var(--t1-hero-overlay);
}
.t1-hero-inner {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
  padding: 6rem 2rem 4rem;
  max-width: 780px;
}
.t1-area-badge {
  display: inline-block;
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.38);
  backdrop-filter: blur(8px);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 5px 18px;
  border-radius: 999px;
}
.t1-hero-title {
  font-size: clamp(2.6rem, 7vw, 4.8rem);
  font-weight: 900;
  color: #fff;
  margin: 0;
  line-height: 1.05;
  text-shadow: 0 4px 32px rgba(0,0,0,0.35);
  letter-spacing: -0.03em;
}
.t1-hero-mission {
  font-size: clamp(1.05rem, 2.5vw, 1.3rem);
  color: rgba(255,255,255,0.92);
  margin: 0;
  line-height: 1.65;
  max-width: 580px;
  text-shadow: 0 1px 8px rgba(0,0,0,0.2);
}
.t1-hero-ctas {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 0.25rem;
}
.t1-hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Nunito', sans-serif;
  font-size: 0.97rem;
  font-weight: 800;
  padding: 0.85rem 1.75rem;
  border-radius: 999px;
  text-decoration: none;
  transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
  white-space: nowrap;
}
.t1-hero-btn svg { width: 17px; height: 17px; flex-shrink: 0; }
.t1-hero-btn--primary {
  background: #fff;
  color: var(--t1-accent-darker);
  box-shadow: 0 4px 24px rgba(0,0,0,0.2);
}
.t1-hero-btn--ghost {
  background: rgba(255,255,255,0.14);
  border: 1.5px solid rgba(255,255,255,0.36);
  color: #fff;
  backdrop-filter: blur(6px);
}
.t1-hero-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.28); }
.t1-chips {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 0.5rem;
}
.t1-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.28);
  backdrop-filter: blur(6px);
  color: rgba(255,255,255,0.9);
  font-size: 0.82rem;
  font-weight: 700;
  padding: 4px 13px;
  border-radius: 999px;
}
.t1-chip svg { width: 13px; height: 13px; }
.t1-scroll-hint {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  color: rgba(255,255,255,0.55);
  animation: t1-bounce 2.2s ease-in-out infinite;
}
.t1-scroll-hint svg { width: 28px; height: 28px; }
@keyframes t1-bounce {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(8px); }
}

/* ── SECTIONS ── */
.t1-section { padding: 5.5rem 0; }
.t1-eyebrow {
  font-size: 0.72rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  margin-bottom: 0.6rem;
}
.t1-section-title {
  font-size: clamp(1.8rem, 4vw, 2.6rem);
  font-weight: 900;
  color: var(--t1-accent-darker);
  margin: 0 0 2.5rem;
  line-height: 1.12;
  letter-spacing: -0.02em;
}

/* ── SOBRE ── */
.t1-sobre { background: #fff; }
.t1-sobre-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 3.5rem;
  align-items: start;
}
.t1-mission-quote {
  position: relative;
  border-left: 4px solid;
  border-radius: 0 1rem 1rem 0;
  padding: 1.5rem 1.75rem;
  margin: 0 0 2rem;
  font-size: 1.15rem;
  font-style: italic;
  color: #1a1a1a;
  font-weight: 700;
  line-height: 1.7;
}
.t1-mission-quote p { margin: 0; position: relative; z-index: 1; }
.t1-quote-icon {
  width: 36px; height: 36px;
  opacity: 0.15;
  position: absolute;
  top: 1rem; right: 1rem;
}
.t1-description {
  font-size: 1.06rem;
  color: #374151;
  line-height: 1.85;
  margin: 0 0 2rem;
}
.t1-audiences { display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
.t1-audiences-label { font-size: 0.8rem; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.06em; padding-top: 4px; white-space: nowrap; }
.t1-audience-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.t1-audience-chip { display: inline-block; font-size: 0.82rem; font-weight: 700; padding: 4px 14px; border-radius: 999px; border: 1px solid; }

/* Contact card */
.t1-contact-card {
  border: 1.5px solid;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.t1-contact-card-header {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.85rem 1.25rem;
  font-size: 0.82rem;
  font-weight: 800;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.t1-contact-card-body { padding: 0.5rem 0; }
.t1-contact-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 1.25rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: #374151;
  text-decoration: none;
  transition: background 0.15s;
}
.t1-contact-row--static { cursor: default; }
.t1-contact-row:not(.t1-contact-row--static):hover { background: #F9FAFB; }
.t1-contact-icon-wrap {
  width: 30px; height: 30px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.t1-contact-card-footer { padding: 0.75rem 1.25rem 1.25rem; }
.t1-cta-whatsapp {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 0.75rem;
  border-radius: 999px;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.9rem;
  font-weight: 800;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
}
.t1-cta-whatsapp:hover { opacity: 0.88; transform: translateY(-1px); }

/* Loc card */
.t1-loc-card {
  display: flex;
  gap: 0.75rem;
  border: 1.5px solid;
  border-radius: 1.25rem;
  padding: 1.25rem;
  margin-top: 1rem;
}
.t1-loc-icon { flex-shrink: 0; margin-top: 2px; }
.t1-loc-label { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #9CA3AF; margin-bottom: 0.35rem; }
.t1-loc-val { font-size: 0.9rem; color: #374151; font-weight: 600; line-height: 1.55; margin: 0; }
.t1-loc-link { display: inline-block; margin-top: 0.5rem; font-size: 0.82rem; font-weight: 800; text-decoration: none; }
.t1-loc-link:hover { text-decoration: underline; }

.t1-contact-link { text-decoration: none; transition: opacity 0.15s; }
.t1-contact-link:hover { text-decoration: underline; }

/* ── IMPACT ── */
.t1-impact { padding: 3.5rem 1.75rem; }
.t1-impact-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2.5rem;
  flex-wrap: wrap;
}
.t1-impact-text { flex: 1; min-width: 220px; }
.t1-impact-headline { font-size: 1.6rem; font-weight: 900; margin: 0 0 0.4rem; line-height: 1.2; }
.t1-impact-sub { font-size: 0.97rem; opacity: 0.85; margin: 0; line-height: 1.6; }
.t1-impact-ctas { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.t1-impact-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  font-size: 0.92rem;
  padding: 0.8rem 1.5rem;
  border-radius: 999px;
  text-decoration: none;
  white-space: nowrap;
  transition: transform 0.15s, opacity 0.15s;
}
.t1-impact-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.t1-impact-btn--white { background: #fff; color: var(--t1-accent-darker); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.t1-impact-btn--outline { border: 1.5px solid rgba(255,255,255,0.35); color: #fff; }
.t1-impact-btn:hover { transform: translateY(-2px); opacity: 0.92; }

/* ── GALLERY ── */
.t1-galeria { background: #fff; }
.t1-gallery-grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(3, 1fr);
  grid-auto-rows: 220px;
}
.t1-gallery-item {
  border-radius: 1rem;
  overflow: hidden;
  background: var(--t1-accent-light);
  position: relative;
}
.t1-gallery-item--featured {
  grid-column: span 2;
  grid-row: span 2;
}
.t1-gallery-item img {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}
.t1-gallery-item:hover img { transform: scale(1.05); }
.t1-gallery-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.15), transparent 50%);
  opacity: 0;
  transition: opacity 0.3s;
}
.t1-gallery-item:hover .t1-gallery-overlay { opacity: 1; }

/* ── NEEDS ── */
.t1-needs-section { background: var(--t1-accent-pale); }
.t1-needs-wrap {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 3rem;
  align-items: center;
}
.t1-needs-text { font-size: 1.08rem; color: #374151; line-height: 1.85; margin-bottom: 2rem; }
.t1-needs-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 0.97rem;
  font-weight: 800;
  padding: 0.85rem 1.75rem;
  border-radius: 999px;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
  box-shadow: 0 4px 20px var(--t1-accent-20);
}
.t1-needs-btn:hover { opacity: 0.88; transform: translateY(-2px); }
.t1-needs-deco {
  border: 1.5px solid;
  border-radius: 1.5rem;
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.25rem;
  text-align: center;
}
.t1-needs-icon-big { opacity: 0.6; }
.t1-needs-quote { font-size: 1rem; font-style: italic; font-weight: 700; line-height: 1.6; margin: 0; }

/* ── INFO CARDS ── */
.t1-info-section { background: #fff; }
.t1-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
}
.t1-info-card {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  background: #F9FAFB;
  border: 1px solid #E5E7EB;
  border-radius: 1.25rem;
  padding: 1.5rem;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.t1-info-card:hover {
  box-shadow: 0 4px 24px var(--t1-accent-10);
  border-color: var(--t1-accent-light);
}
.t1-info-icon {
  width: 46px; height: 46px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.t1-info-icon svg { width: 20px; height: 20px; }
.t1-info-content { flex: 1; min-width: 0; }
.t1-info-label { display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #9CA3AF; margin-bottom: 0.45rem; }
.t1-info-value { font-size: 0.95rem; color: #374151; line-height: 1.65; margin: 0; }
.t1-social-links { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.25rem; }
.t1-social-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  font-weight: 700;
  font-family: 'Nunito', sans-serif;
  padding: 5px 13px;
  border-radius: 999px;
  border: 1.5px solid;
  text-decoration: none;
  transition: background 0.15s, transform 0.15s;
}
.t1-social-btn:hover { transform: translateY(-1px); background: rgba(0,0,0,0.04); }

/* ── FOOTER ── */
.t1-footer { padding: 2rem 1.75rem; }
.t1-footer-inner {
  max-width: 1040px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}
.t1-footer-brand { display: flex; align-items: center; gap: 0.5rem; }
.t1-footer-org { font-size: 0.9rem; font-weight: 800; color: rgba(255,255,255,0.9); }
.t1-footer-sep { color: rgba(255,255,255,0.3); }
.t1-footer-area { font-size: 0.85rem; color: rgba(255,255,255,0.55); font-weight: 600; }
.t1-footer-powered { display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; color: rgba(255,255,255,0.5); font-weight: 600; }
.t1-footer-link { font-size: 0.82rem; font-weight: 800; color: rgba(255,255,255,0.8); text-decoration: none; }
.t1-footer-link:hover { color: #fff; text-decoration: underline; }

/* ── RESPONSIVE ── */
@media (max-width: 860px) {
  .t1-sobre-grid { grid-template-columns: 1fr; }
  .t1-needs-wrap { grid-template-columns: 1fr; }
  .t1-needs-deco { display: none; }
  .t1-impact-inner { flex-direction: column; align-items: flex-start; }
  .t1-footer-inner { justify-content: center; text-align: center; flex-direction: column; gap: 0.5rem; }
}
@media (max-width: 640px) {
  .t1-hero { min-height: 90vh; }
  .t1-section { padding: 3.5rem 0; }
  .t1-info-grid { grid-template-columns: 1fr; }
  .t1-gallery-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 160px; }
  .t1-gallery-item--featured { grid-column: span 2; grid-row: span 1; }
  .t1-hero-ctas { flex-direction: column; align-items: center; }
  .t1-hero-btn { width: 100%; justify-content: center; max-width: 300px; }
}
@media (max-width: 420px) {
  .t1-gallery-grid { grid-template-columns: 1fr; grid-auto-rows: 200px; }
  .t1-gallery-item--featured { grid-column: span 1; }
}
</style>

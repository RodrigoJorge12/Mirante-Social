<script setup lang="ts">
import { computed } from "vue";
const props = defineProps<{ verified?: boolean, accent?: string, size?: number }>();
const accentColor = computed(() => props.accent || '#10B981');
</script>

<template>
  <span v-if="verified" class="badge" :style="{ '--accent': accentColor }" aria-label="Verificado">
    <svg :width="size || 28" :height="size || 28" viewBox="0 0 24 24" role="img">
      <defs>
        <linearGradient id="accentGrad" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" :style="{ stopColor: 'var(--accent)', stopOpacity: 0.9 }" />
          <stop offset="100%" :style="{ stopColor: 'var(--accent)', stopOpacity: 0.6 }" />
        </linearGradient>
        <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
          <feDropShadow dx="0" dy="1" stdDeviation="1.6" flood-color="#000" flood-opacity="0.12" />
        </filter>
      </defs>
      <polygon
        fill="url(#accentGrad)"
        filter="url(#shadow)"
        points="22,12 18.47,14.68 19.07,19.07 14.68,18.47 12,22 9.32,18.47 4.93,19.07 5.53,14.68 2,12 5.53,9.32 4.93,4.93 9.32,5.53 12,2 14.68,5.53 19.07,4.93 18.47,9.32"
      />
      <path d="M8.6 12.4l2.2 2.2 4.6-4.6" stroke="#fff" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
  </span>
</template>

<style scoped>
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  vertical-align: middle;
  margin: 0 0 4px 4px;
}
</style>

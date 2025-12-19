<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { ElMessage } from "element-plus";

// Props
const props = defineProps<{
  cep: string | null;
  city: string | null;
}>();

// Emits
const emit = defineEmits<{
  (e: "update:location", payload: { latitude: number; longitude: number }): void;
}>();

// State
const map = ref<L.Map | null>(null);
const marker = ref<L.Marker | null>(null);

const latitude = ref<number | null>(null);
const longitude = ref<number | null>(null);

function initMap(lat: number, lng: number) {
  if (map.value) return;

  map.value = L.map("location-map").setView([lat, lng], 15);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map.value);

  marker.value = L.marker([lat, lng], {
    draggable: true,
  }).addTo(map.value);

  marker.value.on("dragend", () => {
    const pos = marker.value!.getLatLng();
    latitude.value = pos.lat;
    longitude.value = pos.lng;

    emit("update:location", {
      latitude: pos.lat,
      longitude: pos.lng,
    });
  });
}

async function centerMapByData(cep: string) {
  const cepsToTry = [
    cep?.replace(/\D/g, ""),
    "28630000", // CEP geral de Nova Friburgo caso falhe (fallback)
  ].filter(Boolean);

  for (const currentCep of cepsToTry) {
    if (currentCep.length !== 8) continue;

    try {
      const url = `https://nominatim.openstreetmap.org/search?format=json&postalcode=${currentCep}&country=Brazil&limit=1`;

      const response = await fetch(url, {
        headers: {
          "User-Agent": "MiranteSocial/1.0",
        },
      });

      const data = await response.json();

      if (data && data.length) {
        const lat = parseFloat(data[0].lat);
        const lng = parseFloat(data[0].lon);

        updateMapPosition(lat, lng);
        return;
      }
    } catch {
      
    }
  }

  updateMapPosition(-22.2816, -42.5311);
}
function updateMapPosition(lat: number, lng: number) {
  latitude.value = lat;
  longitude.value = lng;

  if (!map.value) {
    initMap(lat, lng);
  } else {
    map.value.setView([lat, lng], 15);
    marker.value!.setLatLng([lat, lng]);
  }

  emit("update:location", {
    latitude: lat,
    longitude: lng,
  });
}

watch(
  () => props.cep,
  (newCep) => {
    if (newCep) {
      centerMapByData(newCep);
    }
  }
);
</script>

<template>
  <div class="location-picker">
    <p class="hint">
      Ajuste o marcador para o local exato do projeto
    </p>

    <div id="location-map"></div>

    <div class="coords">
      <small v-if="latitude && longitude">
        Latitude: {{ latitude.toFixed(6) }} |
        Longitude: {{ longitude.toFixed(6) }}
      </small>
    </div>
  </div>
</template>

<style scoped>
#location-map {
  width: 100%;
  height: 350px;
  border-radius: 8px;
  margin-top: 8px;
}

.hint {
  font-size: 13px;
  color: #666;
}

.coords {
  margin-top: 6px;
}
</style>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { ElMessage } from "element-plus";
import { PresenterSocialProject } from "@/Presenters/PresenterSocialProject";

// Presenter
const presenter = new PresenterSocialProject();

// Estado
const map = ref<L.Map | null>(null);
const markersLayer = ref<L.LayerGroup | null>(null);

const userLat = ref<number | null>(null);
const userLng = ref<number | null>(null);
const radius = ref<number>(20);
const loading = ref<boolean>(false);

const projects = ref<any[]>([]);

function getUserLocation() {
  if (!navigator.geolocation) {
    ElMessage.error("Seu navegador não suporta geolocalização.");
    return;
  }
  console.log("Obtendo localização do usuário...");
  navigator.geolocation.getCurrentPosition(
    (position) => {
      console.log("Localização obtida:", position.coords);
      userLat.value = position.coords.latitude;
      userLng.value = position.coords.longitude;

      initMap();
      fetchProjects();
    },
    () => {
      ElMessage.error("Não foi possível obter sua localização.");
    }
  );
}
const userIcon = L.icon({
  iconUrl: "https://cdn-icons-png.flaticon.com/512/149/149071.png",
  iconSize: [40, 40],
  iconAnchor: [20, 40],
  popupAnchor: [0, -40],
});

function initMap() {
  if (map.value) return; 

  map.value = L.map("map").setView(
    [userLat.value!, userLng.value!],
    13
  );

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map.value);

L.marker(
  [userLat.value!, userLng.value!],
  { icon: userIcon }
)
  .addTo(map.value)
  .bindPopup("👤 Você está aqui");

  markersLayer.value = L.layerGroup().addTo(map.value);
}


async function fetchProjects() {
  if (!userLat.value || !userLng.value) return;

  loading.value = true;

  try {
    const response = await presenter.getProjectsNear(
      userLat.value,
      userLng.value,
      radius.value
    );

    projects.value = response.data ?? response;
    plotProjects();
  } catch (error) {
    ElMessage.error("Erro ao buscar projetos próximos.");
  } finally {
    loading.value = false;
  }
}

function plotProjects() {
  if (!map.value || !markersLayer.value) return;

  markersLayer.value.clearLayers();

  projects.value.forEach((project) => {
    if (!project.latitude || !project.longitude) return;

    const lat = parseFloat(project.latitude);
    const lng = parseFloat(project.longitude);

    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

    const marker = L.marker([lat, lng]).bindPopup(`
      <strong>${project.name}</strong><br/>
      ${project.city} - ${project.state}
    `);

    markersLayer.value!.addLayer(marker);
  });
}

watch(radius, () => {
  fetchProjects();
});

onMounted(() => {
  getUserLocation();
});
</script>

<template>
  <div class="social-projects-map">

    <h2>Projetos sociais próximos de você</h2>

    <!-- Controle de raio -->
    <div class="radius-control">
      <span>
        Raio de busca: <strong>{{ radius }} km</strong>
      </span>

      <el-slider
        v-model="radius"
        :min="1"
        :max="50"
        :step="1"
        show-input
      />
    </div>

    <!-- Loading -->
    <el-skeleton v-if="loading" animated :rows="4" />

    <!-- Mapa -->
    <div id="map"></div>

  </div>
</template>

<style scoped>
.social-projects-map {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
}

#map {
  width: 100%;
  height: 500px;
  margin-top: 12px;
  border-radius: 8px;
}

.radius-control {
  margin-bottom: 12px;
}
</style>

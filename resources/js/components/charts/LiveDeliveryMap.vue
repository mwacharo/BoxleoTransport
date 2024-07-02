<template>
  <div id="map" style="height: 400px;"></div>
</template>

<script setup>
import { onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

onMounted(() => {
  const map = L.map('map').setView([51.505, -0.09], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // Example: Add markers for delivery vehicles
  L.marker([51.5, -0.09]).addTo(map)
    .bindPopup('Delivery Vehicle 1')
    .openPopup();

  L.marker([51.51, -0.1]).addTo(map)
    .bindPopup('Delivery Vehicle 2')
    .openPopup();
});
</script>

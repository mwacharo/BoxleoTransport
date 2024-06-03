<template>

     
        <div id="map-container">
          <div id="map"></div>
        </div>

</template>

<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  props: {
    orders: Array,
  },
  methods: {
    show() {
      console.log('Showing map...');
      // Implement your logic to show or focus on the map
    }
  },
  mounted() {
    const map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    // Uncomment and adjust the following lines to add markers based on orders
    this.orders.forEach(order => {
      if (order.latitude && order.longitude) {
        L.marker([order.latitude, order.longitude]).addTo(map)
          .bindPopup(`<b>${order.client}</b><br>${order.address}`);
      }
     });
  },
};
</script>

<style>
#map-container {
  width: 100%;
  height: 100%;
  max-width: 1200px; /* Adjust as needed */
  max-height: 800px; /* Adjust as needed */
  margin: 0 auto;
}

#map {
  width: 100%;
  height: 100%;
}
</style>

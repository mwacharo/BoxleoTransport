<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Track Order</v-card-title>
      <v-card-text>
        
        <div id="map" style="height: 400px; width: 100%;"></div>
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" text @click="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  mixins: [fetchDataMixin],
  props: {
    orderId: {
      type: Number,
      required: true
    },
    branchAddress: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      map: null,
      orderLocation: null
    };
  },
  methods: {
    async show() {
      await this.geocodeOrderAddress();
      this.dialog = true;
      this.$nextTick(() => {
        this.initMap();
      });
    },
    close() {
      this.dialog = false;
    },
    async geocodeOrderAddress() {
      try {
        const response = await axios.post('/api/v1/geocodeAddress', { orderId: this.orderId });
        this.orderLocation = response.data;
      } catch (error) {
        console.error('Error geocoding address:', error);
      }
    },
    initMap() {
      if (!this.orderLocation) {
        console.error('Order location not available');
        return;
      }

      this.map = new google.maps.Map(document.getElementById('map'), {
        center: this.branchAddress,
        zoom: 10
      });

      // Add marker for branch
      new google.maps.Marker({
        map: this.map,
        position: this.branchAddress,
        title: 'Branch'
      });

      // Add marker for order location
      new google.maps.Marker({
        map: this.map,
        position: this.orderLocation,
        title: 'Order Location'
      });

      // Draw line between branch and order location
      const line = new google.maps.Polyline({
        path: [this.branchAddress, this.orderLocation],
        map: this.map
      });

      // Fit map to show both markers
      const bounds = new google.maps.LatLngBounds();
      bounds.extend(this.branchAddress);
      bounds.extend(this.orderLocation);
      this.map.fitBounds(bounds);
    }
  }
}
</script>

<template>
  <v-dialog v-model="dialog" max-width="800" @click:outside="closeDialog">
    <v-card>
      <v-card-title>Route Map</v-card-title>
      <v-card-actions>
        <v-btn color="primary" @click="ViewRoute">Assign Route</v-btn>
        <v-btn color="secondary" @click="closeDialog">Close</v-btn>
      </v-card-actions>
      <v-card-text>
        <div ref="map" id="map" style="height: 500px; width: 100%;"></div>
      </v-card-text>
    </v-card>
  </v-dialog>


  <v-dialog v-model="dialogRoute" max-width="600" @click:outside="closeDialog">
     <v-card>
       <v-card-title>Optimized Route Map</v-card-title>
       <v-card-text>

        <v-dialog v-model="Vehicledialog" max-width="600" @click:outside="closeDialog">
         <v-select
           v-model="selectedVehicle"
           :items="vehicles"
           item-text="name"
           item-value="id"
           label="Select Vehicle"
           @change="updateRouteForVehicle"
         ></v-select>
         </v-dialog>

         <div v-if="selectedItems.length > 0" class="x-actions">
           <v-icon class="mx-1" color="primary" @click="showVehicledialog" title="Print">mdi-car</v-icon>
         </div>
         <v-data-table
           :headers="headers"
           :items="routeDetails"
           class="elevation-1"
           show-select
         >
         </v-data-table>
       </v-card-text>
       <v-card-actions>
         <v-btn color="primary" @click="assignRoute" :disabled="!selectedVehicle">Assign Route</v-btn>
         <v-btn color="secondary" @click="closeVehicleDialog">Close</v-btn>
       </v-card-actions>
     </v-card>
   </v-dialog>

</template>

<script>
import axios from 'axios';

export default {
  props: {
    selectedItems: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      map: null,
      dialog: false,
      dialogRoute:false,
      Vehicledialog:false,
      drawingManager: null,
      geofence: null,
      geofences: [],
      vehicles: [],
      agents: [],
      orders: [],
      googleMapsApiLoaded: false,
      orderMarkers: [],
    };
  },
  watch: {
    dialog(val) {
      if (val && this.googleMapsApiLoaded) {
        this.initMap();
        this.loadSelectedOrders();
      }
    },
    selectedItems: {
      handler() {
        if (this.map && this.dialog) {
          this.loadSelectedOrders();
        }
      },
      deep: true
    }
  },
  created() {
    this.loadVehicles();

  },
  methods: {

  showVehicledialog(){
  Vehicledialog=true;
  },
  closeVehicleDialog(){
    Vehicledialog=false;
  },
    show() {
      this.dialog = true;
      if (!this.googleMapsApiLoaded) {
        this.loadGoogleMapsApi();
      } else {
        this.initMap();
        this.loadSelectedOrders();
      }
    },
    closeDialog() {
      this.dialog = false;
      this.clearGeofence();
      this.clearOrderMarkers();
    },
    loadGoogleMapsApi() {
      if (this.googleMapsApiLoaded) {
        return;
      }
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCzbYZ78miZi3hAUmj_HCvpW0mG2VGgxD8&libraries=drawing,geometry`;
      script.onload = () => {
        this.googleMapsApiLoaded = true;
        if (this.dialog) {
          this.initMap();
          this.loadSelectedOrders();
        }
      };
      script.onerror = (error) => {
        console.error('Error loading Google Maps API:', error);
      };
      document.head.appendChild(script);
    },
    initMap() {
      if (this.map) return;

      this.map = new google.maps.Map(this.$refs.map, {
        center: { lat: -1.286389, lng: 36.817223 },
        zoom: 12
      });

      this.initDrawingManager();
      this.loadGeofences();

      google.maps.event.addListenerOnce(this.map, 'idle', () => {
        this.loadVehicles();
        this.loadAgents();
      });
    },
    initDrawingManager() {
      if (!google.maps.drawing) {
        console.error("Google Maps Drawing library not loaded.");
        return;
      }

      this.drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        },
        polygonOptions: {
          editable: true,
          draggable: true,
          fillColor: '#FF0000',
          fillOpacity: 0.2,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 2
        }
      });

      this.drawingManager.setMap(this.map);

      google.maps.event.addListener(this.drawingManager, 'overlaycomplete', (event) => {
        if (event.type === google.maps.drawing.OverlayType.POLYGON) {
          this.clearGeofence();
          this.geofence = event.overlay;
        }
      });
    },
    loadSelectedOrders() {
      console.log('Loading selected orders:', this.selectedItems);
      if (!this.selectedItems || this.selectedItems.length === 0) {
        console.warn('No selected items to display');
        return;
      }
      axios.post('/api/v1/orders/details', { orderIds: this.selectedItems })
        .then(response => {
          const orders = response.data;
          console.log('Fetched order details:', orders);

          orders.forEach(order => {
            if (order.address) {
              console.log('Geocoding address:', order.address);
              this.geocodeAddress(order.address, (latLng) => {
                const marker = new google.maps.Marker({
                  position: latLng,
                  map: this.map,
                  title: `Order: ${order.name}`
                });
                this.orderMarkers.push(marker);
              });
            }
          });
        })
        .catch(error => {
          console.error('Error loading order details:', error);
        });
    },
    loadVehicles() {
      axios.get('/api/v1/vehicles')
        .then(response => {
          this.vehicles = response.data;
          this.vehicles.forEach(vehicle => {
            this.geocodeAddress(vehicle.address, (latLng) => {
              const marker = new google.maps.Marker({
                position: latLng,
                map: this.map,
                icon: {
                  url: '/icons/truck.png',
                  scaledSize: new google.maps.Size(30, 30),
                  anchor: new google.maps.Point(15, 30)
                },
                title: `Vehicle: ${vehicle.name}`
              });
            });
          });
        })
        .catch(error => console.error('Error loading vehicles:', error));
    },

    ViewRoute(){
      this.dialogRoute = true;

    },

    clearGeofence() {
      if (this.geofence) {
        this.geofence.setMap(null);
        this.geofence = null;
      }
    },
    clearOrderMarkers() {
      this.orderMarkers.forEach(marker => marker.setMap(null));
      this.orderMarkers = [];
    },

  }
};
</script>

<style>
#map {
  height: 500px;
  width: 100%;
}
</style>

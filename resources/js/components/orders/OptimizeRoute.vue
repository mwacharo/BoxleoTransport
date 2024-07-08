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
      dialogRoute: false,
      selectedVehicle: null,
      vehicles: [],
      orderMarkers: [],
      routeDetails: [],
      directionsService: null,
      directionsRenderer: null,
      headers: [
        { text: 'Order Name', value: 'name' },
        { text: 'Address', value: 'address' },
        { text: 'Optimized Order', value: 'optimizedOrder' }
      ],
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
    show() {
      this.dialog = true;
      if (!this.googleMapsApiLoaded) {
        this.loadGoogleMapsApi();
      } else {
        this.initMap();
        this.loadSelectedOrders();
      }
    },
    ViewRoute(){
    this.dialogRoute =true;

    },

    closeDialog() {
      this.dialog = false;
      this.clearMarkers();
    },
    closeVehicleDialog() {
      this.dialogRoute = false;
    },
    loadGoogleMapsApi() {
      if (this.googleMapsApiLoaded) {
        return;
      }
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCzbYZ78miZi3hAUmj_HCvpW0mG2VGgxD8&libraries&libraries=places`;
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

      this.directionsService = new google.maps.DirectionsService();
      this.directionsRenderer = new google.maps.DirectionsRenderer();
      this.directionsRenderer.setMap(this.map);
    },
    loadSelectedOrders() {
      if (!this.selectedItems || this.selectedItems.length === 0) {
        return;
      }
      axios.post('/api/v1/orders/details', { orderIds: this.selectedItems })
        .then(response => {
          const orders = response.data;
          this.clearMarkers();
          orders.forEach(order => {
            if (order.address) {
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
        })
        .catch(error => console.error('Error loading vehicles:', error));
    },
    geocodeAddress(address, callback) {
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ address: address }, (results, status) => {
        if (status === 'OK') {
          callback(results[0].geometry.location);
        } else {
          console.error('Geocode was not successful for the following reason: ' + status);
        }
      });
    },
    optimizeRoute() {
      if (this.orderMarkers.length < 2) {
        return;
      }

      const waypoints = this.orderMarkers.slice(1, -1).map(marker => ({
        location: marker.getPosition(),
        stopover: true
      }));

      this.directionsService.route({
        origin: this.orderMarkers[0].getPosition(),
        destination: this.orderMarkers[this.orderMarkers.length - 1].getPosition(),
        waypoints: waypoints,
        optimizeWaypoints: true,
        travelMode: google.maps.TravelMode.DRIVING
      }, (response, status) => {
        if (status === 'OK') {
          this.directionsRenderer.setDirections(response);
          const route = response.routes[0];
          this.routeDetails = route.legs.map((leg, index) => ({
            name: this.selectedItems[index].name,
            address: leg.start_address,
            optimizedOrder: index + 1
          }));
          this.dialogRoute = true;
        } else {
          console.error('Directions request failed due to ' + status);
        }
      });
    },
    updateRouteForVehicle() {
      // Logic to update the route for the selected vehicle
    },
    assignRoute() {
      const payload = {
        vehicleId: this.selectedVehicle,
        routeDetails: this.routeDetails
      };
      axios.post('/api/v1/assignRoute', payload)
        .then(response => {
          console.log('Route assigned successfully:', response.data);
          this.closeVehicleDialog();
        })
        .catch(error => {
          console.error('Error assigning route:', error);
        });
    },
    clearMarkers() {
      this.orderMarkers.forEach(marker => marker.setMap(null));
      this.orderMarkers = [];
    }
  }
};

</script>

<style>
#map {
  height: 500px;
  width: 100%;
}
</style>

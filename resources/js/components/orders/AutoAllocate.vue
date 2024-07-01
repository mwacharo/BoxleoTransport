<template>
  <v-dialog v-model="dialog" max-width="800" @click:outside="closeDialog">
    <v-card>
      <v-card-title>Geofence Map</v-card-title>
      <v-card-actions>
        <v-btn color="primary" @click="saveGeofence">Save Geofence</v-btn>
        <v-btn color="primary" @click="autoAssign">Auto Assign</v-btn>
        <v-btn color="secondary" @click="closeDialog">Close</v-btn>
      </v-card-actions>
      <v-card-text>
        <div ref="map" id="map" style="height: 500px; width: 100%;"></div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      map: null,
      dialog: false,
      drawingManager: null,
      geofence: null,
      geofences: [],
      orders: [],
      vehicles: [],
      agents: [],
      googleMapsApiLoaded: false,
    };
  },
  watch: {
    dialog(val) {
      if (val && this.googleMapsApiLoaded) {
        this.initMap();
      }
    }
  },
  created() {
    this.loadGeofences();
    this.loadOrders();
    this.loadVehicles();
    this.loadAgents();
  },
  methods: {
    show() {
      this.dialog = true;
      if (!this.googleMapsApiLoaded) {
        this.loadGoogleMapsApi();
      } else {
        this.initMap();
      }
    },
    closeDialog() {
      this.dialog = false;
      this.clearGeofence();
    },
    loadGoogleMapsApi() {
      if (this.googleMapsApiLoaded) {
        return;
      }
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCzbYZ78miZi3hAUmj_HCvpW0mG2VGgxD8&libraries=drawing,geocoding`;
      script.onload = () => {
        this.googleMapsApiLoaded = true;
        if (this.dialog) {
          this.initMap();
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
      this.loadOrders();
      this.loadVehicles();
      this.loadAgents();
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
    saveGeofence() {
      if (this.geofence) {
        const path = this.geofence.getPath().getArray().map(latlng => ({
          lat: latlng.lat(),
          lng: latlng.lng()
        }));
        console.log('Geofence coordinates:', path);

        // Send the geofence coordinates to your backend
        axios.post('/api/v1/geofences', { path })
          .then(response => {
            console.log('Geofence saved:', response);
            this.$toastr.success(response.data.message);
          })
          .catch(error => console.error('Error saving geofence:', error));
      }
    },
    autoAssign() {
      // Implement your auto-assign logic here
    },
    loadGeofences() {
      axios.get('/api/v1/geofences')
        .then(response => {
          console.log('Geofences data:', response.data);
          this.geofences = response.data;
          this.geofences.forEach(geofence => {
            if (geofence.path && geofence.path.length) {
              const path = geofence.path.map(point => ({ lat: point.lat, lng: point.lng }));
              const polygon = new google.maps.Polygon({
                paths: path,
                editable: false,
                draggable: false,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2
              });
              polygon.setMap(this.map);
            } else {
              console.log(`Geofence with ID ${geofence.id} has an invalid path.`);
            }
          });
        })
        .catch(error => console.error('Error loading geofences:', error));
    },
    loadOrders() {
    axios.get('/api/v1/orders')
      .then(response => {
        this.orders = response.data;
        this.orders.forEach(order => {
          this.geocodeAddress(order.address, (latLng) => {
            const marker = new google.maps.Marker({
              position: latLng,
              map: this.map,
              title: `Order: ${order.order_no}`
            });
          });
        });
      })
      .catch(error => console.error('Error loading orders:', error));
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
                  scaledSize: new google.maps.Size(30, 30), // Resize icon here
                  anchor: new google.maps.Point(15, 30) // Adjust anchor point to position the icon correctly
                },
                title: `Vehicle: ${vehicle.name}`
              });
            });
          });
        })
        .catch(error => console.error('Error loading vehicles:', error));
    },
    loadAgents() {
      axios.get('/api/v1/riders')
        .then(response => {
          this.agents = response.data;
          this.agents.forEach(agent => {
            this.geocodeAddress(agent.address, (latLng) => {
              const marker = new google.maps.Marker({
                position: latLng,
                map: this.map,
                icon: {
                  url: '/icons/motorcycle.png',
                  scaledSize: new google.maps.Size(30, 30), // Resize icon here
                  anchor: new google.maps.Point(15, 30) // Adjust anchor point to position the icon correctly
                },
                title: `Agent: ${agent.name}`
              });
            });
          });
        })
        .catch(error => console.error('Error loading agents:', error));
    },
    geocodeAddress(address, callback) {
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ address: address }, (results, status) => {
        if (status === 'OK') {
          const latLng = results[0].geometry.location;
          callback(latLng);
        } else {
          console.error('Geocode was not successful for the following reason: ' + status);
        }
      });
    },
    clearGeofence() {
      if (this.geofence) {
        this.geofence.setMap(null);
        this.geofence = null;
      }
    },
    isLocationWithinGeofence(latitude, longitude) {
      if (!this.geofence) {
        return false;
      }

      const polygon = new google.maps.Polygon({
        paths: this.geofence.getPath().getArray()
      });

      const point = new google.maps.LatLng(latitude, longitude);
      return google.maps.geometry.poly.containsLocation(point, polygon);
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

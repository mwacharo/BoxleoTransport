<template>
  <v-dialog v-model="dialog" max-width="800" @click:outside="closeDialog">
    <v-card>
      <v-card-title>Geofence Map</v-card-title>
      <v-card-actions>
        <v-btn color="primary" @click="saveGeofence">Save Geofence</v-btn>
          <v-btn color="primary" @click="autoAssign">Assign</v-btn>
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
        this.resizeMap(); // Add this line to trigger map resize
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
    this.loadGeofences();
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
        this.loadSelectedOrders();
      this.resizeMap();
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
          this.resizeMap();
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
    saveGeofence() {
      if (this.geofence) {
        const path = this.geofence.getPath().getArray().map(latlng => ({
          lat: latlng.lat(),
          lng: latlng.lng()
        }));
        console.log('Geofence coordinates:', path);

        axios.post('/api/v1/geofences', { path })
          .then(response => {
            console.log('Geofence saved:', response);
            this.$toastr.success(response.data.message);
          })
          .catch(error => console.error('Error saving geofence:', error));
      }
    },
    resizeMap() {
    if (this.map) {
      google.maps.event.trigger(this.map, 'resize');
      const center = this.map.getCenter();
      google.maps.event.addListenerOnce(this.map, 'idle', () => {
        this.map.setCenter(center);
      });
    }
  },
    autoAssign() {
      if (!this.geofences || this.geofences.length === 0) {
        console.error('No geofences defined.');
        return;
      }

      this.fetchOrderDetails(this.selectedItems)
        .then(fetchedOrders => {
          if (!fetchedOrders || fetchedOrders.length === 0) {
            console.warn('No orders found.');
            return;
          }

          this.orders = fetchedOrders;

          const ordersByGeofence = this.groupOrdersByGeofence(this.orders);
          console.log('Orders grouped by geofence:', ordersByGeofence);

          Object.entries(ordersByGeofence).forEach(([geofenceId, ordersInGeofence]) => {
            if (ordersInGeofence.length === 0) return;

            if (geofenceId === "undefined") {
              console.error("Geofence ID is undefined.");
              return;
            }

            const agent = this.isAgentInGeofence(geofenceId);

            if (!agent) {
              console.error(`No agent found for geofence ${geofenceId}`);
              return;
            }

            const orderIds = ordersInGeofence.map(order => order.id);
            console.log(`Assigning orders ${orderIds} to agent ${agent.id}`);
            this.assignOrdersToAgent(orderIds, agent.id);
          });
        })
        .catch(error => console.error('Error fetching order details:', error));
    },
    fetchOrderDetails(orderIds) {
      return axios.post('/api/v1/orders/details', { orderIds })
        .then(response => response.data)
        .catch(error => {
          console.error('Error fetching order details:', error);
          throw error;
        });
    },
    groupOrdersByGeofence(orders) {
      const ordersByGeofence = {};

      this.geofences.forEach(geofence => {
        ordersByGeofence[geofence.id] = orders.filter(order =>
          this.isLocationWithinGeofence(order.latitude, order.longitude, geofence.getPath().getArray())
        );
      });

      // Add a debug statement to check the orders and geofences
      console.log('Geofences:', this.geofences);
      console.log('Orders:', orders);
      console.log('Orders grouped by geofence:', ordersByGeofence);

      return ordersByGeofence;
    },
    isAgentInGeofence(geofenceId) {
      // Ensure the geofenceId is a number
      const id = parseInt(geofenceId, 10);
      if (isNaN(id)) {
        console.error(`Invalid geofenceId provided: ${geofenceId}`);
        return null;
      }

      console.log(`Checking for agents in geofence ${id}`);

      // Ensure the agents array is valid
      if (!Array.isArray(this.agents)) {
        console.error('Agents data is not an array:', this.agents);
        return null;
      }

      // Find the agent associated with the given geofence ID
      const agent = this.agents.find(agent => {
        // Ensure agent.geofence_id is a number
        const agentGeofenceId = parseInt(agent.geofence_id, 10);
        console.log(`Comparing with agent ${agent.id} having geofence_id ${agentGeofenceId}`);
        return agentGeofenceId === id;
      });

      console.log(`Agent found for geofence ${id}:`, agent);
      return agent || null;  // Return null if no agent is found
    },
    assignOrdersToAgent(orderIds, agentId) {
      axios.post('/api/v1/orders/assignOrders', { order_ids: orderIds, agent_id: agentId })
        .then(response => {
          console.log('Orders assigned:', response.data);
          this.$toastr.success(response.data.message);
        })
        .catch(error => {
          console.error('Error assigning orders:', error);
        });
    },
    loadGeofences() {
      axios.get('/api/v1/geofences')
        .then(response => {
          console.log('Geofences data:', response.data);
          this.geofences = response.data.map(geofence => {
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
            polygon.id = geofence.id;  // Make sure each polygon has an id
            return polygon;
          });

          this.geofences.forEach(polygon => polygon.setMap(this.map));
        })
        .catch(error => console.error('Error loading geofences:', error));
    },
    loadSelectedOrders() {
      console.log('Loading selected orders:', this.selectedItems);
      if (!this.selectedItems || this.selectedItems.length === 0) {
        console.warn('No selected items to display');
        return;
      }
      axios.post('/api/v1/orders/details', { orderIds: this.selectedItems })
        .then(response => {
          const orders = this.sanitizeOrderData(response.data);
          console.log('Fetched and sanitized order details:', orders);

          orders.forEach(order => {
            if (this.isValidLatLng(order.latitude, order.longitude)) {
              const marker = new google.maps.Marker({
                position: { lat: order.latitude, lng: order.longitude },
                map: this.map,
                title: `Order: ${order.order_no || order.id}`
              });
              this.orderMarkers.push(marker);
            } else {
              console.warn(`Invalid or missing coordinates for order ${order.id}: ${order.latitude}, ${order.longitude}`);
            }
          });
        })
        .catch(error => {
          console.error('Error loading order details:', error);
          this.$toastr.error('Failed to load order details. Please try again.');
        });
    },
    sanitizeOrderData(orders) {
      if (!Array.isArray(orders)) {
        console.warn('Invalid orders data format. Expected array, got:', typeof orders);
        return [];
      }
      return orders.map(order => ({
        ...order,
        latitude: this.parseCoordinate(order.latitude),
        longitude: this.parseCoordinate(order.longitude)
      }));
    },
    parseCoordinate(value) {
      const parsed = parseFloat(value);
      return isNaN(parsed) ? null : parsed;
    },
    isValidLatLng(lat, lng) {
      return typeof lat === 'number' && typeof lng === 'number' &&
        isFinite(lat) && isFinite(lng) &&
        Math.abs(lat) <= 90 && Math.abs(lng) <= 180;
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
                  scaledSize: new google.maps.Size(30, 30),
                  anchor: new google.maps.Point(15, 30)
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
          console.error('Geocode was not successful for the following reason:', status);
          console.error('Address that failed:', address);
          callback(this.map.getCenter());
        }
      });
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
    isLocationWithinGeofence(latitude, longitude, geofencePath) {
      const polygon = new google.maps.Polygon({
        paths: geofencePath
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

<template>
  <v-container-fluid>
    <v-card class="elevation-2 report-card">
      <v-card-title class="headline">Reports</v-card-title>
      <v-form @submit.prevent="generateReport">
        <v-container>
          <v-row>

          <v-col cols="12" md="6">
            <v-select
              v-model="selectedReportType"
              :items="reportTypes"
              label="Select Report Type"
              outlined
              dense
              prepend-icon="mdi-file-chart"
            ></v-select>
          </v-col>


          <v-col cols="12" md="6">
            <v-select
              v-model="selectedOrderType"
              :items="OrderCategory"
                 item-value="id"
              item-title="name"
              label="Select Order Type"
              
              outlined
              dense
              prepend-icon="mdi-file-chart"
            ></v-select>
          </v-col>


          <v-col cols="12" md="6">
            <v-select
              v-model="selectedOrderStatus"
              :items="OrderStatus"
              label="Select Status"
              item-value="id"
              item-title="name"
              outlined
              dense
              prepend-icon="mdi-file-chart"
            ></v-select>
          </v-col>

            <v-col cols="12" md="6">
              <v-select
                v-model="selectedAgent"
                :items="agents"
                item-value="id"
                item-title="name"
                label="Select Agent"
                outlined
                dense
                multiple
                prepend-icon="mdi-account"
              ></v-select>
            </v-col>


            <v-col cols="12" md="6">
              <v-select
                v-model="selectedDriver"
                :items="drivers"
                item-value="id"
                item-title="name"
                label="Select Driver"
                outlined
                dense
                multiple
                prepend-icon="mdi-account"
              ></v-select>
            </v-col>



            <v-col cols="12" md="6">
              <v-select
                v-model="selectedVendor"
                :items="vendors"
                item-value="id"
                item-title="name"
                label="Select Vendor"
                outlined
                dense
                multiple
                prepend-icon="mdi-account"
              ></v-select>
            </v-col>

            <v-col cols="12" md="6">
              <v-select
                v-model="selectedVehicle"
                :items="vehicles"
                item-value="id"
                item-title="license_plate"
                label="Select Vehicle"
                outlined
                dense
                multiple
                prepend-icon="mdi-truck"
              ></v-select>
            </v-col>


            <v-col cols="12" md="6">
              <v-select
                v-model="selectedCity"
                :items="cities"
                item-value="id"
                item-title="name"
                label="Select Zone"
                outlined
                multiple
                dense
                prepend-icon="mdi-map-marker"
              ></v-select>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="startSelectedDate"
                label="Start Date"
                prepend-icon="mdi-calendar"
                type="date"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="endSelectedDate"
                label="End Date"
                prepend-icon="mdi-calendar"
                type="date"
              ></v-text-field>
            </v-col>

          </v-row>
        </v-container>
        <v-card-actions>
          <v-btn type="submit" color="primary" class="mr-4">
            <v-icon left>mdi-file-document</v-icon>
            Generate Report
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-container-fluid>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      selectedAgent: null,
      selectedDriver: null,
      selectedVendor: null,
      selectedVehicle: null,
      startSelectedDate: null,
      endSelectedDate: null,
      selectedReportType: null,
      selectedCity: null,
      selectedOrderStatus: null,
      selectedOrderCategory: null,
      agents: [], 
      OrderStatus:[],
      vehicles: [],
      vendors:[],
      drivers:[],
      OrderCategory:[],
      reportTypes: ['Order Report','Rider Clerance', 'Product Report','Merchant Report','Delivery Performance Report', 'Agent/Driver Report', 'Financial Report', 'Vehicle Report', 'Client Report', 'Zone Report'], 
      cities: [], 
    };
  },
  created() {
    this.fetchAgents();
    this.fetchVehicles();
    this.fetchCities();
    this.fetchVendors();
    this.fetchDrivers();
    this.fetchOrderStatus();
    this.fetchOrderCategory();
  },
  methods: {
    generateReport() {
      axios
        .post('/api/reports/generate', {
          agent_id: this.selectedAgent,
          vehicle_id: this.selectedVehicle,
          start_date: this.startSelectedDate,
          end_date: this.endSelectedDate,
          report_type: this.selectedReportType,
          city_id: this.selectedCity,
        })
        .then(response => {
          console.log(response);
          // Use the URL from the response to trigger a file download
          if (response.data.url) {
            window.location.href = response.data.url; // Redirect to trigger download
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    },
    fetchAgents() {
      const url = '/api/v1/riders';
      axios
        .get(url)
        .then(response => {
          this.agents = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    fetchVehicles() {
      const url = '/api/v1/vehicles';
      axios
        .get(url)
        .then(response => {
          this.vehicles = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    fetchCities() {
      const url = '/api/v1/geofences';
      axios
        .get(url)
        .then(response => {
          this.cities = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    fetchVendors() {
      const url = '/api/v1/vendors  ';
      axios
        .get(url)
        .then(response => {
          this.vendors = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    fetchDrivers() {
      const url = '/api/v1/drivers  ';
      axios
        .get(url)
        .then(response => {
          this.drivers = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },

    fetchOrderStatus() {
      const url = '/api/v1/orderstatus  ';
      axios
        .get(url)
        .then(response => {
          this.OrderStatus = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },

    fetchOrderCategory() {
      const url = '/api/v1/ordercategories ';
      axios
        .get(url)
        .then(response => {
          this.OrderCategory = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },

};
</script>

<style scoped>
.report-card {
  width: 100%;
  margin: 0 auto;
  padding: 24px;
}
</style>




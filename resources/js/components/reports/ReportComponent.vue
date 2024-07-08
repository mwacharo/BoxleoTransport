<template>
  <v-container-fluid>
    <v-card class="elevation-2 report-card">
      <v-card-title class="headline">Generate Courier Report</v-card-title>
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
                v-model="selectedAgent"
                :items="agents"
                item-value="id"
                item-title="name"
                label="Select Agent/Driver"
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
                item-title="name"
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
                label="Select City/Zone"
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
      selectedVehicle: null,
      startSelectedDate: null,
      endSelectedDate: null,
      selectedReportType: null,
      selectedCity: null,
      agents: [], // Populate this array with agent/driver data
      vehicles: [], // Populate this array with vehicle data
      reportTypes: ['Order Report', 'Delivery Performance Report', 'Agent/Driver Report', 'Financial Report', 'Vehicle Report', 'Client Report', 'Geographical Report'], // Add more report types if needed
      cities: [], // Populate this array with city/zone data
    };
  },
  created() {
    this.fetchAgents();
    this.fetchVehicles();
    this.fetchCities();
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
          this.agents = response.data.agents;
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
          this.vehicles = response.data.vehicles;
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
          this.cities = response.data.cities;
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

<template>
  <v-container-fluid>
    <v-card class="elevation-2 report-card">
      <v-card-title class="headline">Reports</v-card-title>
      <v-form @submit.prevent="generateReport">
        <v-container>
          <v-row>

            <v-col cols="12" md="3">
              <v-select v-model="selectedReportType" :items="reportTypes" label="Select Report Type" outlined dense
                prepend-icon="mdi-file-chart"></v-select>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Order Report'">
              <v-select v-model="selectedOrderType" :items="OrderCategory" item-value="id" item-title="name"
                label="Select Order Type" outlined dense prepend-icon="mdi-file-chart"></v-select>
            </v-col>


            <v-col cols="12" md="3">
              <v-select v-model="selectedOrderStatus" :items="OrderStatus" label="Select Status" item-value="name"
                item-title="name" outlined dense prepend-icon="mdi-file-chart"></v-select>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Rider Report' || 'POD Report'">
              <v-select v-model="selectedAgent" :items="agents" item-value="id" item-title="name" label="Select Rider"
                outlined dense multiple prepend-icon="mdi-account"></v-select>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Driver Report' || 'POD Report'">
              <v-select v-model="selectedDriver" :items="drivers" item-value="id" item-title="name"
                label="Select Driver" outlined dense multiple prepend-icon="mdi-account"></v-select>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Merchant Report' || 'POD Report'">
              <v-select v-model="selectedVendor" :items="vendors" item-value="id" item-title="name"
                label="Select Vendor" outlined dense multiple prepend-icon="mdi-account"></v-select>
            </v-col>

            <v-col cols="12" md="3" v-if="selectedReportType === 'Vehicle Report'">
              <v-select v-model="selectedVehicle" :items="vehicles" item-value="id" item-title="license_plate"
                label="Select Vehicle" outlined dense multiple prepend-icon="mdi-truck"></v-select>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Zone Report'">
              <v-select v-model="selectedCity" :items="cities" item-value="id" item-title="name" label="Select Zone"
                outlined multiple dense prepend-icon="mdi-map-marker"></v-select>
            </v-col>

            <v-col cols="12" md="3">
              <v-text-field v-model="startSelectedDate" label="Start Date" prepend-icon="mdi-calendar"
                type="date"></v-text-field>
            </v-col>

            <v-col cols="12" md="3">
              <v-text-field v-model="endSelectedDate" label="End Date" prepend-icon="mdi-calendar"
                type="date"></v-text-field>
            </v-col>


            <v-col cols="12" md="3" v-if="selectedReportType === 'Dispatch Report' ">
              <v-text-field v-model="dispatched_startDate" label=" Start Dispatched" prepend-icon="mdi-calendar"
                type="date"></v-text-field>
            </v-col>

            <v-col cols="12" md="3" v-if="selectedReportType === 'Dispatch Report' ">
              <v-text-field v-model="dispatched_endDate" label="End Dispatched" prepend-icon="mdi-calendar"
                type="date"></v-text-field>
            </v-col>


          </v-row>
        </v-container>
        <v-card-actions>

          <!-- Search Button with Tooltip -->
          <v-tooltip text="Search">
            <template v-slot:activator="{ props }">
              <v-btn color="primary" @click="generateReport" v-bind="props">
                <v-icon left>mdi-magnify</v-icon>

              </v-btn>
            </template>
          </v-tooltip>
          <!-- Generate Report Button with Tooltip -->
          <v-tooltip text="Email">
            <template v-slot:activator="{ props }">
              <v-btn type="searchReport" color="primary" class="mr-4" v-bind="props">
                <v-icon left>mdi-file-document</v-icon>
              </v-btn>
            </template>
          </v-tooltip>



          <!-- Generate Report Button with Tooltip -->
          <!-- <v-tooltip text="PDF">
            <template v-slot:activator="{ props }">
              <v-btn type="searchReport" color="red" class="mr-4" v-bind="props">
                <v-icon left>mdi-file-document</v-icon>
              </v-btn>
            </template>
          </v-tooltip> -->



          <!-- Generate Excel Report Button with Tooltip -->
          <v-tooltip text="Excel ">
            <template v-slot:activator="{ props }">
              <v-btn color="success" @click="downloadExcel('excel')" v-bind="props">
                <v-icon left>mdi-file-excel</v-icon>

              </v-btn>
            </template>
          </v-tooltip>

          <!-- Generate CSV Report Button with Tooltip -->
          <v-tooltip text="CSV">
            <template v-slot:activator="{ props }">
              <v-btn color="info" @click="exportReport('csv')" v-bind="props">
                <v-icon left>mdi-file-excel</v-icon>

              </v-btn>
            </template>
          </v-tooltip>

          <!-- Generate PDF Report Button with Tooltip -->
          <v-tooltip text="PDF">
            <template v-slot:activator="{ props }">
              <v-btn color="error" @click="exportReport('pdf')" v-bind="props">
                <v-icon left>mdi-file-pdf</v-icon>

              </v-btn>
            </template>
          </v-tooltip>
        </v-card-actions>

        <!-- Report Table -->
        <v-data-table :headers="headers" :items="reportData" class="mt-4" item-value="id" :items-per-page="10"
          show-select></v-data-table>


      </v-form>
    </v-card>
  </v-container-fluid>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      reportData: [],
      reportUrl: null,
      headers: [
        { title: 'Created On', value: 'created_at' },
        { title: 'Order No', value: 'order_no' },
        { title: 'Client Name', value: 'client_name' },
        { title: 'Client Address', value: 'address' },
        { title: 'Client Phone', value: 'phone' },
        { title: 'Delivery Status', value: 'status' },
        { title: 'Total', value: 'cod_amount' },
        { title: 'POD', value: 'pod' },

      ],
      selectedAgent: null,
      selectedDriver: null,
      selectedVendor: null,
      selectedVehicle: null,
      startSelectedDate: null,
      dispatched_startDate:null,
      dispatched_endDate:null,
      endSelectedDate: null,
      selectedReportType: null,
      selectedCity: null,
      selectedOrderStatus: null,
      name: null,
      selectedOrderCategory: null,
      agents: [],
      OrderStatus: [],
      vehicles: [],
      vendors: [],
      drivers: [],
      OrderCategory: [],
      reportTypes: ['Order Report', 'POD Report', 'Rider Report', 'Dispatch Report', 'Rider Clerance', 'Product Report', 'Merchant Report', 'Delivery Performance Report', 'Agent/Driver Report', 'Financial Report', 'Vehicle Report', 'Client Report', 'Zone Report'],
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
      this.loading = true;
      axios
        .post('/api/reports/generateReport', {
          agent_id: this.selectedAgent,
          vehicle_id: this.selectedVehicle,
          start_date: this.startSelectedDate,
          end_date: this.endSelectedDate,
          dispatched_startDate: this.dispatched_startDate,
          dispatched_endDate: this.dispatched_endDate,
          report_type: this.selectedReportType,
          city_id: this.selectedCity,
            status: this.selectedOrderStatus,
          // order_status_id: this.selectedOrderStatus,
          vendor_id: this.selectedVendor,
          driver_id: this.selectedDriver,
          order_type_id: this.selectedOrderType,
        })
        .then(response => {
          console.log(response);

          // Assign the reportData from the response to this.$reportData
          this.reportData = response.data.reportData;
          this.reportUrl = response.data.url;
          // this.$toastr.success('Report generated successfully');
          console.log('reportData:', this.reportData);
          // this.$toastr.error('Failed to generate report');

        })
        .catch(error => {
          console.error('Error:', error);
        })
        .finally(() => {
          this.loading = false;
        });
    },

    downloadExcel() {
      if (this.reportData.length > 0) {
        console.log('Attempting to download Excel');
        axios.post('api/v1/reports/downloadExcel', {
          reportData: this.reportData
        }, {
          responseType: 'blob' 
        })
          .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'report.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          })
          .catch(error => {
            console.error('Download error:', error);
            this.$toastr.error('Failed to download the Excel file');
          });
      } else {
        this.$toastr.error('No report data available for download');
      }
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

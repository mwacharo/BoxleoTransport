<template>
  <v-app>
    <v-main>
      <!-- Fleet Management Toolbar -->
      <v-toolbar flat>
        <v-toolbar-title>Fleet Management</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-btn color="primary" @click="addVehicle">
        maintenance-management
          <v-icon right>mdi-plus</v-icon>
        </v-btn>

        <v-btn color="primary" @click="addDriver">
          fuel-management
          <v-icon right>mdi-account-plus</v-icon>
        </v-btn>
        <v-btn color="primary" @click="addDriver">
        route-management
          <v-icon right>mdi-account-plus</v-icon>
        </v-btn>

        <v-btn color="primary" @click="addDriver">

        analytics-reporting
          <v-icon right>mdi-account-plus</v-icon>
        </v-btn>
      </v-toolbar>

      <!-- Search Field -->
      <v-text-field
        v-model="search"
        label="Search"
        clearable
        @input="filterFleet"
        dense
      ></v-text-field>

      <!-- Fleet Management Dashboard -->
      <v-row>
        <v-col cols="12" md="3" v-for="card in overviewCards" :key="card.title">
          <v-card>
            <v-card-title>{{ card.title }}</v-card-title>
            <v-card-subtitle>{{ card.value }}</v-card-subtitle>
          </v-card>
        </v-col>
      </v-row>

      <!-- Fleet Status Map -->
      <v-responsive>
        <div id="fleet-map" class="fleet-map"></div>
      </v-responsive>

      <!-- Vehicle List -->

      <!-- Driver List -->


      <!-- Add/Edit Vehicle Dialog -->
      <v-dialog v-model="vehicleDialog" max-width="800">
        <v-card>
          <v-card-title>
            <span class="text-h5">{{ vehicleFormTitle }}</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedVehicle.name" label="Vehicle Name" prepend-icon="mdi-truck"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedVehicle.model" label="Vehicle Model" prepend-icon="mdi-car"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedVehicle.licensePlate" label="License Plate" prepend-icon="mdi-numeric"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedVehicle.capacity" label="Capacity" prepend-icon="mdi-weight"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeVehicleDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveVehicle">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Add/Edit Driver Dialog -->
      <v-dialog v-model="driverDialog" max-width="800">
        <v-card>
          <v-card-title>
            <span class="text-h5">{{ driverFormTitle }}</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedDriver.name" label="Driver Name" prepend-icon="mdi-account"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedDriver.licenseNumber" label="License Number" prepend-icon="mdi-card"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedDriver.phone" label="Phone Number" prepend-icon="mdi-phone"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field v-model="editedDriver.email" label="Email" prepend-icon="mdi-email"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeDriverDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveDriver">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Delete Confirmation Dialog -->
      <v-dialog v-model="dialogDelete" max-width="290">
        <v-card>
          <v-card-title class="headline">Warning</v-card-title>
          <v-card-text>This will permanently delete the item. Continue?</v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
            <v-btn color="primary" text @click="deleteItemConfirm">OK</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-main>
  </v-app>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      search: '',
      vehicles: [],
      drivers: [],
      overviewCards: [
        { title: 'Total Vehicles', value: '50' },
        { title: 'Vehicles in Operation', value: '35' },
        { title: 'Vehicles Under Maintenance', value: '5' },
        { title: 'Available Vehicles', value: '10' },
      ],
      vehicleHeaders: [
        { text: 'Name', value: 'name' },
        { text: 'Model', value: 'model' },
        { text: 'License Plate', value: 'licensePlate' },
        { text: 'Capacity', value: 'capacity' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      driverHeaders: [
        { text: 'Name', value: 'name' },
        { text: 'License Number', value: 'licenseNumber' },
        { text: 'Phone', value: 'phone' },
        { text: 'Email', value: 'email' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      editedVehicle: {
        name: '',
        model: '',
        licensePlate: '',
        capacity: '',
      },
      editedDriver: {
        name: '',
        licenseNumber: '',
        phone: '',
        email: '',
      },
      vehicleDialog: false,
      driverDialog: false,
      dialogDelete: false,
      editedIndex: -1,
    };
  },
  computed: {
    filteredVehicles() {
      if (!this.search) return this.vehicles;
      return this.vehicles.filter(
        (vehicle) =>
          vehicle.name.toLowerCase().includes(this.search.toLowerCase()) ||
          vehicle.model.toLowerCase().includes(this.search.toLowerCase()) ||
          vehicle.licensePlate.toLowerCase().includes(this.search.toLowerCase()) ||
          vehicle.capacity.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    filteredDrivers() {
      if (!this.search) return this.drivers;
      return this.drivers.filter(
        (driver) =>
          driver.name.toLowerCase().includes(this.search.toLowerCase()) ||
          driver.licenseNumber.toLowerCase().includes(this.search.toLowerCase()) ||
          driver.phone.toLowerCase().includes(this.search.toLowerCase()) ||
          driver.email.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    vehicleFormTitle() {
      return this.editedIndex === -1 ? 'New Vehicle' : 'Edit Vehicle';
    },
    driverFormTitle() {
      return this.editedIndex === -1 ? 'New Driver' : 'Edit Driver';
    },
  },
  created() {
    this.fetchFleetData();
  },
  methods: {
    filterFleet() {
      // Logic to filter fleet data
    },
    fetchFleetData() {
      // Fetch vehicles and drivers data from API
      axios
        .get('/api/v1/vehicles')
        .then((response) => {
          this.vehicles = response.data;
        })
        .catch((error) => {
          console.error('Error fetching vehicles:', error);
        });

      axios
        .get('/api/v1/drivers')
        .then((response) => {
          this.drivers = response.data;
        })
        .catch((error) => {
          console.error('Error fetching drivers:', error);
        });
    },
    addVehicle() {
      this.editedVehicle = { name: '', model: '', licensePlate: '', capacity: '' };
      this.vehicleDialog = true;
    },
    addDriver() {
      this.editedDriver = { name: '', licenseNumber: '', phone: '', email: '' };
      this.driverDialog = true;
    },
    editVehicle(vehicle) {
      this.editedIndex = this.vehicles.indexOf(vehicle);
      this.editedVehicle = { ...vehicle };
      this.vehicleDialog = true;
    },
    editDriver(driver) {
      this.editedIndex = this.drivers.indexOf(driver);
      this.editedDriver = { ...driver };
      this.driverDialog = true;
    },
    deleteVehicle(vehicle) {
      this.editedIndex = this.vehicles.indexOf(vehicle);
      this.editedVehicle = { ...vehicle };
      this.dialogDelete = true;
    },
    deleteDriver(driver) {
      this.editedIndex = this.drivers.indexOf(driver);
      this.editedDriver = { ...driver };
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      if (this.editedVehicle.name) {
        // Delete vehicle
        axios
          .delete(`/api/v1/vehicles/${this.editedVehicle.id}`)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchFleetData();
            this.closeDelete();
          })
          .catch((error) => {
            console.error('Error deleting vehicle:', error);
            this.$toastr.error('Failed to delete the vehicle!');
          });
      } else {
        // Delete driver
        axios
          .delete(`/api/v1/drivers/${this.editedDriver.id}`)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchFleetData();
            this.closeDelete();
          })
          .catch((error) => {
            console.error('Error deleting driver:', error);
            this.$toastr.error('Failed to delete the driver!');
          });
      }
    },
    saveVehicle() {
      if (this.editedIndex > -1) {
        // Update vehicle
        axios
          .put(`/api/v1/vehicles/${this.editedVehicle.id}`, this.editedVehicle)
          .then((response) => {
            this.$toastr.success('Vehicle updated successfully');
            this.fetchFleetData();
            this.closeVehicleDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating vehicle');
            console.error('Error updating vehicle:', error);
          });
      } else {
        // Add new vehicle
        axios
          .post('/api/v1/vehicles', this.editedVehicle)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchFleetData();
            this.closeVehicleDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding vehicle');
            console.error('Error adding vehicle:', error);
          });
      }
    },
    saveDriver() {
      if (this.editedIndex > -1) {
        // Update driver
        axios
          .put(`/api/v1/drivers/${this.editedDriver.id}`, this.editedDriver)
          .then((response) => {
            this.$toastr.success('Driver updated successfully');
            this.fetchFleetData();
            this.closeDriverDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating driver');
            console.error('Error updating driver:', error);
          });
      } else {
        // Add new driver
        axios
          .post('/api/v1/drivers', this.editedDriver)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchFleetData();
            this.closeDriverDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding driver');
            console.error('Error adding driver:', error);
          });
      }
    },
    closeVehicleDialog() {
      this.vehicleDialog = false;
      this.editedVehicle = { name: '', model: '', licensePlate: '', capacity: '' };
      this.editedIndex = -1;
    },
    closeDriverDialog() {
      this.driverDialog = false;
      this.editedDriver = { name: '', licenseNumber: '', phone: '', email: '' };
      this.editedIndex = -1;
    },
    closeDelete() {
      this.dialogDelete = false;
      this.editedVehicle = { name: '', model: '', licensePlate: '', capacity: '' };
      this.editedDriver = { name: '', licenseNumber: '', phone: '', email: '' };
      this.editedIndex = -1;
    },
  },
};
</script>

<style>
.fleet-map {
  height: 500px;
}
</style>

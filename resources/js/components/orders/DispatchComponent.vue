<template>


  <v-card>
    <v-card-title class="text-h5">Dispatch</v-card-title>
    <v-card-subtitle>Input your dispatch details</v-card-subtitle>


    <!-- update status -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Update Status</span>
        </v-card-title>

        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-select v-model="selectedStatus" :items="statusOptions" label="Status" required></v-select>

          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeDialog">Close</v-btn>
          <v-btn color="primary" text @click="updateOrder">Update</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- end of update status -->

    <v-card-text>
      <v-row>
        <!-- Zone From -->
        <v-col cols="12" md="6">
          <v-select v-model="zoneFrom" :items="zones" item-title="name" item-value="id" label="Zone From"></v-select>
        </v-col>

        <!-- Zone To -->
        <v-col cols="12" md="6">
          <v-select v-model="zoneTo" :items="zones" item-title="name" item-value="id" label="Zone To"></v-select>
        </v-col>



        <v-radio-group v-model="assignTo" row required>
          <v-radio label="Assign to Rider" value="rider"></v-radio>
          <v-radio label="Assign to Driver" value="driver"></v-radio>
        </v-radio-group>
        <v-select v-if="assignTo === 'rider'" v-model="rider" item-title="name" :items="riders" item-value="id"
          label="Rider" required></v-select>
        <v-select v-if="assignTo === 'driver'" v-model="driver" item-title="name" :items="drivers" item-value="id"
          label="Driver" required></v-select>


        <!-- Rider -->
        <!-- <v-col cols="12" md="6">
          <v-select v-model="rider" item-title="name" :items="riders"   item-value="id" label="Rider"></v-select>
        </v-col>

        <v-col cols="12" md="6">
          <v-select v-model="driver" item-title="name" :items="drivers"   item-value="id" label="Driver"></v-select>
        </v-col> -->

        <!-- Manual Zone Location -->
        <!-- <v-col cols="12" md="6">
                <v-switch
                  v-model="manualZone"
                  label="Manual Zone Location"
                ></v-switch>
              </v-col> -->
      </v-row>

      <!-- Waybill Input -->
      <v-row>
        <v-radio-group v-model="waybillType" row>
          <v-radio label="Scan one by one" value="scan"></v-radio>
          <v-textarea v-if="waybillType === 'scan'" v-model="waybills"
            label="Input waybills separated by commas or a new line"></v-textarea>
          <v-radio label="Input waybills" value="input"></v-radio>
        </v-radio-group>
      </v-row>

      <v-textarea v-if="waybillType === 'input'" v-model="waybills"
        label="Input waybills separated by commas or a new line"></v-textarea>

      <!-- Search Button -->
      <v-btn color="primary" @click="onSearch">Search</v-btn>


      <div v-if="selectedItems.length > 0" class="x-actions">
        <v-icon class="mx-1" color="primary" @click="bulkUpdateStatus" title="Update Status">mdi-update</v-icon>
      </div>


      <!-- Dispatch Table -->
      <v-data-table :headers="headers" :items="dispatchData" class="mt-4" item-value="id" :items-per-page="10"
        v-model="selectedItems" show-select></v-data-table>
    </v-card-text>

    <!-- Actions -->
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" text @click="dialog = false">Close</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from 'axios';

export default {


  data() {
    return {
      assignTo: null,
      dialog: false,
      zoneFrom: null,
      zoneTo: null,
      rider: null,
      manualZone: false,
      waybillType: 'input',
      waybills: '',
      selectedItems: [],
      orders: [],
      drivers: [],
      driver: null,
      selectedStatus: null,
      statusOptions: ['Dispatched','In Transit'],

      zones: [],
      riders: [],
      headers: [
        { title: 'Created On', value: 'created_on' },
        { title: 'Order No', value: 'order_no' },
        { title: 'Client Name', value: 'client_name' },
        { title: 'Client Address', value: 'address' },
        { title: 'Client Phone', value: 'phone' },
        { title: 'Delivery Status', value: 'status' },
        { title: 'Total', value: 'cod_amount' },
        { title: 'Actions', value: 'actions' },
      ],
      dispatchData: [], // Populate this array with your data
    };
  },
  created() {
    this.fetchRiders();
    this.fetchZones();
    this.fetchOrders();
    this.fetchDrivers();

  },
  methods: {

    updateOrder() {

      if (
        !this.selectedItems.length ||
        !this.zoneTo ||
        !this.selectedStatus ||
        (!this.rider && !this.driver) ||  // Ensure at least one is selected
        (this.rider && this.driver)       // Prevent both from being selected
      ) {
        this.$toastr.error('Please fill in all fields and select either a Rider or a Driver, but not both.');
        return;
      }



      const payload = {
        order_ids: this.selectedItems,
        geofence_id: this.zoneTo,
        status: this.selectedStatus,
      };

      if (this.assignTo === 'rider') {
        payload.rider_id = this.rider;
      } else if (this.assignTo === 'driver') {
        payload.driver_id = this.driver;
      }

      axios.post('/api/v1/dispatchOrders', payload)
        .then(response => {
          this.$toastr.success('Orders updated successfully!');
          this.dialog = false;
          this.fetchOrders(); // Refresh the orders after update
        })
        .catch(error => {
          this.$toastr.error('Error updating orders.');
          console.error('Error updating orders:', error);
        });
    },
    closeDialog() {
      this.dialog = false;
    },
    bulkUpdateStatus() {

      console.log('clicked')
      this.dialog = true;
    },
    onSearch() {
      // Split the waybills input into an array of individual waybills, removing extra spaces
      const waybillArray = this.waybills.split(/[\s,]+/).map(waybill => waybill.trim());

      // Filter orders based on matching waybills
      this.dispatchData = this.orders.filter(order => waybillArray.includes(order.order_no));

      // Log the filtered dispatch data
      console.log('Filtered Orders:', this.dispatchData);

      // Handle cases where no orders are found
      if (this.dispatchData.length === 0) {
        this.$toastr.error('No orders found for the given waybills.');
      } else {
        this.$toastr.success('Orders found!');
      }
    },

    fetchRiders() {
      const url = `/api/v1/riders`;
      axios
        .get(url, {})
        .then(response => {
          this.riders = response.data;
        })
        .catch(error => {
          console.error('Error fetching riders:', error);
        });
    },
    fetchZones() {
      const url = `/api/v1/geofences`;
      axios
        .get(url, {})
        .then(response => {
          this.zones = response.data;
        })
        .catch(error => {
          console.error('Error fetching zones:', error);
        });
    },

    fetchOrders() {
      const url = `/api/v1/orders`;
      axios
        .get(url, {})
        .then(response => {
          this.orders = response.data;
        })
        .catch(error => {
          console.error('Error fetching zones:', error);
        });
    },
    fetchDrivers() {
      const url = `/api/v1/drivers`;
      axios
        .get(url, {})
        .then(response => {
          this.drivers = response.data;
        })
        .catch(error => {
          console.error('Error fetching zones:', error);
        });
    },

  },
};
</script>

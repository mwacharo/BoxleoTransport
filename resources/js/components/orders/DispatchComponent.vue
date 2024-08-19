<template>
   
      <!-- Dialog Component -->
        <v-card>
          <v-card-title class="text-h5">Dispatch</v-card-title>
          <v-card-subtitle>Input your dispatch details</v-card-subtitle>
  
          <v-card-text>
            <v-row>
              <!-- Zone From -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="zoneFrom"
                  :items="zones"
                  label="Zone From"
                ></v-select>
              </v-col>
  
              <!-- Zone To -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="zoneTo"
                  :items="zones"
                  label="Zone To"
                ></v-select>
              </v-col>
  
              <!-- Rider -->
              <v-col cols="12" md="6">
                <v-select
                  v-model="rider"
                  :items="riders"
                  label="Rider"
                ></v-select>
              </v-col>
  
              <!-- Manual Zone Location -->
              <v-col cols="12" md="6">
                <v-switch
                  v-model="manualZone"
                  label="Manual Zone Location"
                ></v-switch>
              </v-col>
            </v-row>
  
            <!-- Waybill Input -->
            <v-row>
              <v-radio-group v-model="waybillType" row>
                <v-radio label="Scan one by one" value="scan"></v-radio>
                <v-radio label="Input waybills" value="input"></v-radio>
              </v-radio-group>
            </v-row>
  
            <v-textarea
              v-if="waybillType === 'input'"
              v-model="waybills"
              label="Input waybills separated by commas or a new line"
            ></v-textarea>
  
            <!-- Search Button -->
            <v-btn color="primary" @click="onSearch">Search</v-btn>
  
            <!-- Dispatch Table -->
            <v-data-table
              :headers="headers"
              :items="dispatchData"
              class="mt-4"
              item-value="order_no"
              :items-per-page="10"
            ></v-data-table>
          </v-card-text>
  
          <!-- Actions -->
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="dialog = false">Close</v-btn>
          </v-card-actions>
        </v-card>
  </template>
  
  <script>
  export default {
    data() {
      return {
        dialog: false,
        zoneFrom: null,
        zoneTo: null,
        rider: null,
        manualZone: false,
        waybillType: 'input',
        waybills: '',
        zones: ['Zone 1', 'Zone 2', 'Zone 3'],
        riders: ['Rider 1', 'Rider 2', 'Rider 3'],
        headers: [
          { title: 'Created On', value: 'created_on' },
          { title: 'Order No', value: 'order_no' },
          { title: 'Client Name', value: 'client_name' },
          { title: 'Client Address', value: 'client_address' },
          { title: 'Client Phone', value: 'client_phone' },
          { title: 'Delivery Status', value: 'delivery_status' },
          { title: 'Total', value: 'total' },
          { title: 'Actions', value: 'actions' },
        ],
        dispatchData: [], // Populate this array with your data
      };
    },
    methods: {
      onSearch() {
        // Add your search functionality here
        console.log('Searching for waybills:', this.waybills);
      },
    },
  };
  </script>
  
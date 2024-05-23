<template>
  <v-container>
    <v-card class="mb-5">
      <v-card-title>Google Sheets</v-card-title>
      <v-card-subtitle>
        Integrating your googlesheets with Boxleo Transport and Logistics bridges the gap between your sales channel and inventory, giving you the ability to tackle <br> 
        any number of online orders with ease while keeping the stock in continuous sync in both platforms<br>
          boxleologistics-transport@boxleotransport.iam.gserviceaccount.com
      </v-card-subtitle>
      <v-card-actions>
        <v-btn @click="addSheet" color="primary">Add Sheet</v-btn>
      </v-card-actions>
    </v-card>

    <v-card>
      <v-card-title>Connected Stores</v-card-title>
      <v-tabs>
        <v-tab>Current</v-tab>
        <v-tab>All</v-tab>
      </v-tabs>
      <v-tab-item>
        <v-data-table :headers="headers" :items="connectedStores" item-key="name" class="elevation-1">
          <template v-slot:top>
            <v-text-field v-model="search" label="Search" class="mx-4"></v-text-field>
          </template>
        </v-data-table>
      </v-tab-item>
    </v-card>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      headers: [
        { text: 'Merchant', value: 'merchant' },
        { text: 'Sheet Name', value: 'sheetName' },
        { text: 'Status', value: 'status' },
        { text: 'Default', value: 'default' },
        { text: 'Last update', value: 'lastUpdate' },
        { text: 'Created On', value: 'createdOn' }
      ],
      connectedStores: [
        {
          merchant: 'PRUNUS',
          sheetName: 'AUGUST 2023',
          status: 'Active',
          default: true,
          lastUpdate: '2024-05-21 11:58:24',
          createdOn: 'Tue 01 Aug 2023'
        },
        {
          merchant: 'Kenya Originals',
          sheetName: '1ST-30THSEPTEMBER',
          status: 'Active',
          default: true,
          lastUpdate: '2024-05-21 12:02:26',
          createdOn: 'Fri 22 Sep 2023'
        }
        // Add more stores as needed
      ]
    };
  },
  methods: {
    addSheet() {
      // Function to add a new Google Sheet
    }
  }
};
</script>

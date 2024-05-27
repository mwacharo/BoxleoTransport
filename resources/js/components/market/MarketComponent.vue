<template>
  <AppLayout>
    <v-container>
      <v-card class="mb-5">
        <v-card-title>Google Sheets</v-card-title>
        <v-card-subtitle></v-card-subtitle>
        <v-card-actions>
          <v-btn @click="addSheet" color="primary">Add Sheet</v-btn>
        </v-card-actions>
      </v-card>

      <v-card>
        <v-card-title>Connected Stores</v-card-title>

        <v-data-table :headers="headers" :items="sheets" item-key="name" class="elevation-1">
          <template v-slot:top>
            <v-text-field v-model="search" label="Search" class="mx-4"></v-text-field>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" color="primary" @click="editSheet(item)"> mdi-pencil </v-icon>
            <v-icon small class="mr-2" color="error" @click="deleteSheet(item)"> mdi-delete </v-icon>
            <v-icon small class="mr-2" color="teal" @click="syncSheet(item)"> mdi-sync </v-icon>
            <v-icon small color="orange" @click="updateSheet(item)"> mdi-update </v-icon>
          </template>
        </v-data-table>

        <AddSheet ref="AddSheetComponent" />
      </v-card>
    </v-container>
  </AppLayout>
</template>

<script>
import AddSheet from '@/components/market/AddSheet.vue';
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  components: {
    AddSheet
  },
  mixins: [fetchDataMixin],
  data() {
    return {
      search: '',
      tab: null,

      headers: [
        { title: 'Merchant', value: 'name' },
        { title: 'Sheet Name', value: 'sheet_name' },
        { title: 'Status', value: 'status' },
        { title: 'Default', value: 'default' },
        { title: 'Last update', value: 'lastUpdate' },
        { title: 'Created On', value: 'created_at' },
        { title: 'Actions', value: 'actions', sortable: false }
      ],
      sheets: []
      // vendors: [],
    };
  },
  created() {
    this.loadAllData();
  },
  methods: {
    async loadAllData() {
      this.vendors = await this.fetchDataFromApi('/api/v1/vendors');
      this.sheets = await this.fetchDataFromApi('/api/v1/sheets');
    },
    addSheet() {
      this.$refs.AddSheetComponent.show();
    },
    editSheet(sheet) {
      // Implement the logic to edit the sheet
      console.log('Edit:', sheet);
    },
    deleteSheet(sheet) {
      // Implement the logic to delete the sheet
      console.log('Delete:', sheet);
    },
    // syncSheet(sheet) {
    //   // makes api call to read google sheet data and store in orders table use axios
    //   console.log('Sync:', sheet);
    // },

    async syncSheet(sheet) {
      try {
        const response = await axios.post(`/api/v1/sheets/${sheet.id}/sync`);
        console.log('Sync response:', response.data);
        this.loadAllData(); // Reload data after sync
      } catch (error) {
        console.error('Error syncing sheet:', error);
      }
    },
    updateSheet(sheet) {
      // Implement the logic to update the sheet
      console.log('Update:', sheet);
    }
  }
};
</script>

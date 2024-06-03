<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>{{ entityName }}</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="addEntity">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-btn color="primary" @click="toggleFilterDrawer">
              <v-icon>mdi-filter</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field
            v-model="search"
            label="Search by order_no , phone"
            clearable
            @input="filterEntities"
            dense
          ></v-text-field>
          <div v-if="selectedItems.length>0" class="x-actions">
            <v-icon class="mx-1" color="error" @click="bulkDelete" title="Delete">mdi-delete</v-icon>
            <v-icon class="mx-1" color="primary" @click="bulkAssignRider" title="Assign Rider">mdi-bicycle</v-icon>
            <v-icon class="mx-1" color="primary" @click="bulkAssignDriver" title="Assign Vendor">mdi-car</v-icon>
            <v-icon class="mx-1" color="primary" @click="bulkUpdateStatus" title="Update Status">mdi-update</v-icon>
            <v-icon class="mx-1" color="primary" @click="bulkCategorize" title="Categorize">mdi-label</v-icon>
            <v-icon class="mx-1" color="primary" @click="bulkAutoAllocate" title="Auto Allocate">mdi-auto-fix</v-icon>
          </div>
          <v-responsive>
            <v-data-table
              :headers="headers"
              :items="filteredEntities"
              item-value="id"
              items-per-page="5"
              show-select
              v-model="selectedItems"
            >
   
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editEntity(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteEntity(item)">mdi-delete</v-icon>
                </div>
              </template>
            </v-data-table>
          </v-responsive>

          <v-dialog v-model="dialog" max-width="800">
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="6" v-for="field in entityFields" :key="field.name">
                      <v-text-field
                        v-model="editedItem[field.name]"
                        :label="field.label"
                        :prepend-icon="field.icon"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
                <v-btn color="blue darken-1" text @click.prevent="saveEntity">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>
              <v-card-text>This will permanently delete the {{ entityName }}. Continue?</v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="primary" text @click="deleteEntityConfirm">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="bulkDialog" max-width="500">
            <v-card>
              <v-card-title class="headline">Bulk Action</v-card-title>
              <v-card-text>
                <v-select v-model="bulkAction" :items="bulkActionOptions" label="Select Action" clearable></v-select>
                <!-- Add any additional input fields required for bulk actions here -->
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeBulkDialog">Cancel</v-btn>
                <v-btn color="primary" text @click="performBulkAction">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-navigation-drawer v-model="filterDrawer" location="right" temporary :width="400">
            <v-card>
              <v-card-title>
                <span class="text-h5">Filter Options</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="toggleFilterDrawer">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>

              <v-card-text>
                <v-select
                  v-model="filterType"
                  :items="typeOptions"
                  item-title="name"
                  item-value="id"
                  label="Filter by Order Type"
                  clearable
                ></v-select>
                <v-select
                  v-model="filterVendor"
                  :items="vendorOptions"
                  item-title="name"
                  item-value="id"
                  label="Filter by Vendor"
                  clearable
                ></v-select>
                <v-select
                  v-model="filterAgent"
                  :items="agentOptions"
                  item-title="name"
                  item-value="id"
                  label="Filter by Agent"
                  clearable
                ></v-select>
                <v-select
                  v-model="filterStatus"
                  :items="statusOptions"
                  item-title="name"
                  item-value="id"
                  label="Filter by Status"
                  clearable
                ></v-select>
                <v-text-field v-model="filterAddress" label="Filter by Address" clearable></v-text-field>
                <v-text-field v-model="startSelectedDate" label="Start Date" type="date"></v-text-field>
                <v-text-field v-model="endSelectedDate" label="End Date" type="date"></v-text-field>

                <v-btn color="primary" @click="applyFilters">Search</v-btn>
              </v-card-text>
            </v-card>
          </v-navigation-drawer>
        </v-main>
      </v-app>
    </v-layout>
  </v-card>
</template>

<script>
import axios from 'axios';
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  mixins: [fetchDataMixin],
  props: {
    entityName: {
      type: String,
      default: 'Order'
    },
    apiEndpoint: {
      type: String,
      default: '/api/v1/orders'
    },
    entityFields: {
      type: Array,
      default: () => [
        // { name: 'vendorOptions.name', label: 'vendor', icon: 'mdi-store' },
        { name: 'created_at', label: 'Created', icon: 'mdi-calendar' },
        { name: 'order_no', label: 'Order', icon: 'mdi-file-document' },
        { name: 'client_name', label: 'Client', icon: 'mdi-account' },
        { name: 'address', label: 'Address', icon: 'mdi-home' },
        { name: 'city', label: 'City', icon: 'mdi-city' },
        { name: 'phone', label: 'Contact', icon: 'mdi-phone' },
        { name: 'alt_phone', label: 'Other', icon: 'mdi-phone-in-talk' },
        { name: 'product_name', label: 'Product', icon: 'mdi-package-variant' },
        { name: 'quantity', label: 'Quantity', icon: 'mdi-format-list-numbered' },
        { name: 'status', label: 'Status', icon: 'mdi-information' }
      ]
    }
  },
  data() {
    return {
      dialog: false,
      selected: false,
      dialogDelete: false,
      bulkDialog: false,
      search: '',
      entities: [],
      headers: [
        { title: '#', value: 'index' },
        ...this.entityFields.map(field => ({ title: field.label, value: field.name })),
        { title: 'Actions', value: 'actions', sortable: false }
      ],
      editedIndex: -1,
      editedItem: this.getDefaultItem(),
      defaultItem: this.getDefaultItem(),
      filterDrawer: false,
      filterType: null,
      filterVendor: null,
      filterAgent: null,
      filterStatus: null,
      filterAddress: '',
      startSelectedDate: '',
      endSelectedDate: '',
      typeOptions: [],
      vendorOptions: [],
      agentOptions: [],
      statusOptions: [],
      selectedItems: [],
      // selected: [],
      bulkAction: null
      // bulkActionOptions: [
      //   { name: 'Delete', id: 'bulkDelete' },
      //   { name: 'Assign Rider', id: 'bulkAssignRider' },
      //   { name: 'Assign Vendor', id: 'bulkAssignDriver' },
      //   { name: 'Update Status', id: 'bulkUpdateStatus' },
      //   { name: 'Categorize', id: 'bulkCategorize' },
      //   { name: 'Auto Allocate', id: 'bulkAutoAllocate' }
      // ]
    };
  },
  computed: {
    filteredEntities() {
      let filtered = this.entities;

      if (this.search) {
        const searchLower = this.search.toLowerCase();
        filtered = filtered.filter(entity =>
          ['order_no', 'phone', 'alt_phone'].some(field => entity[field]?.toLowerCase().includes(searchLower))
        );
      }

      if (this.filterType) {
        filtered = filtered.filter(entity => entity.type === this.filterType);
      }

      if (this.filterVendor) {
        filtered = filtered.filter(entity => entity.vendor === this.filterVendor);
      }

      if (this.filterAgent) {
        filtered = filtered.filter(entity => entity.agent === this.filterAgent);
      }

      if (this.filterStatus) {
        filtered = filtered.filter(entity => entity.status === this.filterStatus);
      }

      if (this.filterAddress) {
        filtered = filtered.filter(entity => entity.address.toLowerCase().includes(this.filterAddress.toLowerCase()));
      }

      if (this.startSelectedDate) {
        filtered = filtered.filter(entity => new Date(entity.created_at) >= new Date(this.startSelectedDate));
      }

      if (this.endSelectedDate) {
        filtered = filtered.filter(entity => new Date(entity.created_at) <= new Date(this.endSelectedDate));
      }

      return filtered;
    },
    formTitle() {
      return this.editedIndex === -1 ? `New ${this.entityName}` : `Edit ${this.entityName}`;
    }
    // hasSelectedItems() {
    //   return this.selectedItems.length > 0;
    // }
  },
  created() {
    this.fetchEntities();
    this.fetchFilterData();
  },
  methods: {
    getDefaultItem() {
      const item = {};
      this.entityFields.forEach(field => {
        item[field.name] = '';
      });
      return item;
    },
    // enterSelect(event) {
    // if (event.length > 0) {
    //   this.select = true;
    // } else {
    //   this.select = false;
    // }
    //   enterSelect(selectedItems) {
    //     this.selectedItems = selectedItems;
    //     console.log(this.selectedItems); // This will log the selected items to the console

    // },
    addEntity() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },
    editEntity(entity) {
      this.editedIndex = this.entities.indexOf(entity);
      this.editedItem = { ...entity };
      this.dialog = true;
    },
    deleteEntity(entity) {
      this.editedIndex = this.entities.indexOf(entity);
      this.editedItem = { ...entity };
      this.dialogDelete = true;
    },
    async deleteEntityConfirm() {
      try {
        await axios.delete(`${this.apiEndpoint}/${this.editedItem.id}`);
        this.fetchEntities();
        this.closeDelete();
      } catch (error) {
        console.error(`Error deleting ${this.entityName}:`, error);
      }
    },
    async saveEntity() {
      try {
        if (this.editedIndex > -1) {
          await axios.put(`${this.apiEndpoint}/${this.editedItem.id}`, this.editedItem);
        } else {selected
          await axios.post(this.apiEndpoint, this.editedItem);
        }
        this.fetchEntities();
        this.closeDialog();
      } catch (error) {
        console.error(`Error saving ${this.entityName}:`, error);
      }
    },
    async fetchEntities() {
      try {
        const response = await axios.get(this.apiEndpoint);
        this.entities = response.data;
      } catch (error) {
        console.error(`Error fetching ${this.entityName}:`, error);
      }
    },
    async fetchFilterData() {
      this.typeOptions = await this.fetchDataFromApi('/api/v1/ordercategories');
      this.vendorOptions = await this.fetchDataFromApi('/api/v1/vendors');
      this.agentOptions = await this.fetchDataFromApi('/api/v1/riders');
      this.statusOptions = await this.fetchDataFromApi('/api/v1/orderstatus');
    },
    closeDialog() {
      this.dialog = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
    closeDelete() {
      this.dialogDelete = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
    closeBulkDialog() {
      this.bulkDialog = false;
      this.bulkAction = null;
    },
    toggleFilterDrawer() {
      this.filterDrawer = !this.filterDrawer;
    },
    applyFilters() {
      this.filterEntities();
      this.toggleFilterDrawer();
    },
    filterEntities() {
      // Filtering handled by the computed property 'filteredEntities'
    },
    refreshEntities() {
      this.fetchEntities();
    },
    async bulkDelete() {
      try {
        await Promise.all(this.selectedItems.map(item => axios.delete(`${this.apiEndpoint}/${item.id}`)));
        this.refreshEntities();
      } catch (error) {
        console.error('Error performing bulk delete:', error);
      }
    },
    bulkAssignRider() {
      this.bulkAction = 'bulkAssignRider';
      this.bulkDialog = true;
    },
    bulkAssignDriver() {
      this.bulkAction = 'bulkAssignDriver';
      this.bulkDialog = true;
    },
    bulkUpdateStatus() {
      this.bulkAction = 'bulkUpdateStatus';
      this.bulkDialog = true;
    },
    bulkCategorize() {
      this.bulkAction = 'bulkCategorize';
      this.bulkDialog = true;
    },
    bulkAutoAllocate() {
      this.bulkAction = 'bulkAutoAllocate';
      this.bulkDialog = true;
    },
    performBulkAction() {
      switch (this.bulkAction) {
        case 'bulkAssignRider':
          // Implement bulk assign rider logic here
          console.log('Performing bulk assign rider');
          break;
        case 'bulkAssignDriver':
          // Implement bulk assign vendor logic here
          console.log('Performing bulk assign vendor');
          break;
        case 'bulkUpdateStatus':
          // Implement bulk update status logic here
          console.log('Performing bulk update status');
          break;
        case 'bulkCategorize':
          // Implement bulk categorize logic here
          console.log('Performing bulk categorize');
          break;
        case 'bulkAutoAllocate':
          // Implement bulk auto allocate logic here
          console.log('Performing bulk auto allocate');
          break;
      }
      this.closeBulkDialog();
    }
  }
};
</script>




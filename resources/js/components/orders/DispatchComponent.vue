<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>{{ entityName }}</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="toggleFilterDrawer">
              <v-icon>mdi-filter</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field v-model="search" label="Search by order_no, phone" clearable @input="filterEntities"
            dense></v-text-field>

          <v-responsive>
            <v-data-table :headers="headers" :items="filteredEntities" item-value="id" items-per-page="5" show-select
              :loading="isLoading" v-model="selectedItems">



              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="OpenDispatchDialog(item)">mdi-package-variant</v-icon>

                  <v-icon class="mx-1" color="blue" @click="editEntity(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteEntity(item)">mdi-delete</v-icon>
                </div>
              </template>

              <template v-slot:progress>
                <v-progress-linear color="blue" indeterminate></v-progress-linear>
              </template>
            </v-data-table>
          </v-responsive>

          <!-- attach order items -->
          <v-dialog v-model="productInstanceDialog" max-width="600">
            <v-card>
              <v-card-title>
                <span class="text-h5">Product Instances</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="closeProductInstanceDialog">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="product_name" label="Product Name"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-combobox v-model="selectedBarcode" :items="barcodes" label="Barcode"
                        allow-overflow></v-combobox>
                    </v-col>
                  </v-row>
                  <v-btn color="primary" @click="saveProductInstance">Save</v-btn>
                </v-container>
              </v-card-text>
            </v-card>
          </v-dialog>


          <!-- Edit Dialog -->
          <v-dialog v-model="dialog" max-width="800">
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="6" v-for="field in entityFields" :key="field.name">
                      <v-text-field v-model="editedItem[field.name]" :label="field.label"
                        :prepend-icon="field.icon"></v-text-field>
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

          <!-- Delete Confirmation Dialog -->
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

          <!-- Filter Drawer -->
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
                <v-select v-model="filterType" :items="typeOptions" item-title="name" item-value="id"
                  label="Filter by Order Type" clearable></v-select>
                <v-select v-model="filterVendor" :items="vendorOptions" item-title="name" item-value="id"
                  label="Filter by Vendor" clearable></v-select>
                <v-select v-model="filterAgent" :items="agentOptions" item-title="name" item-value="id"
                  label="Filter by Agent" clearable></v-select>
                <v-select v-model="filterStatus" :items="statusOptions" item-title="name" item-value="id"
                  label="Filter by Status" clearable></v-select>
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
      default: 'Dispatch'
    },
    apiEndpoint: {
      type: String,
      default: '/api/v1/orders'
    },
    entityFields: {
      type: Array,
      default: () => [
        { name: 'created_at', label: 'Created', icon: 'mdi-calendar' },
        { name: 'order_no', label: 'Order', icon: 'mdi-file-document' },
        { name: 'product_name', label: 'Product Details', icon: 'mdi-package-variant' },
        { name: 'quantity', label: 'quantity', icon: 'mdi-format-list-numbered' },
        { name: 'order_items', label: 'Order Items', icon: 'mdi-package-variant' },
        { name: 'status', label: 'Status', icon: 'mdi-information' }
      ]
    }
  },
  data() {
    return {
      dialog: false,
      dialogDelete: false,
      productInstanceDialog: false,
      search: '',
      entities: [],
      product_name: '',
      productId: '',
      selectedBarcode: '',
      barcodes: [],
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
      isLoading: false,
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
  },
  created() {
    this.fetchEntities();
    this.fetchFilterData();
  },
  methods: {

    
    OpenDispatchDialog(item) {
      this.productInstanceDialog = true;
      const orderId = item.id;
      
      axios.get(`/api/v1/orders/${orderId}`).then(response => {
        // extract product_id  from fetched orderdetails
        this.barcodes = response.data.instances.map(instance => instance.barcode);
        this.product_name = item.product_name;
      })
        .catch(error => {
          console.error('There was an error fetching the product instances:', error);
        });
    }
    ,
    closeProductInstanceDialog() {
      this.productInstanceDialog = false;
      this.product_name = '';
      this.selectedBarcode = null;
      this.barcodes = [];
    },
  
    getDefaultItem() {
      const item = {};
      this.entityFields.forEach(field => {
        item[field.name] = '';
      });
      return item;
    },
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
        } else {
          await axios.post(this.apiEndpoint, this.editedItem);
        }
        this.fetchEntities();
        this.closeDialog();
      } catch (error) {
        console.error(`Error saving ${this.entityName}:`, error);
      }
    },
    async fetchEntities() {
      this.isLoading = true;
      try {
        const response = await axios.get(this.apiEndpoint);
        this.entities = response.data;
      } catch (error) {
        console.error(`Error fetching ${this.entityName}:`, error);
      } finally {
        this.isLoading = false;
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
    toggleFilterDrawer() {
      this.filterDrawer = !this.filterDrawer;
    },
    async fetchEntities() {
      this.isLoading = true
      try {
        await new Promise(resolve => setTimeout(resolve, 2000))
        const response = await axios.get(this.apiEndpoint);

        this.entities = response.data;
      } catch (error) {
        console.error(`Error fetching ${this.entityName}:`, error);
      } finally {
        this.isLoading = false
      }
    },
    applyFilters() {
      this.filterEntities();
      this.toggleFilterDrawer();
    },

  }
};
</script>

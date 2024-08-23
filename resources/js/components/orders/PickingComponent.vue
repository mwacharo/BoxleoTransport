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



              <template v-slot:item.order_products="{ item }">
                <v-chip v-for="(product, index) in item.order_products" :key="index" class="mr-2">
                  {{ formatProductDetails(item, product) }}
                  <!-- <v-icon class="mx-1" color="blue">mdi-pencil</v-icon> -->
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="OpenDispatchDialog(item)">mdi-package-variant</v-icon>

                  <!-- <v-icon class="mx-1" color="blue" @click="editEntity(item)">mdi-pencil</v-icon> -->
                  <!-- <v-icon class="mx-1" color="red" @click="deleteEntity(item)">mdi-delete</v-icon> -->
                </div>
              </template>

              <template v-slot:progress>
                <v-progress-linear color="blue" indeterminate></v-progress-linear>
              </template>
            </v-data-table>
          </v-responsive>

          <!--dialog to show details of product  -->

          <v-dialog v-model="showProductDetails" max-width="600px">
            <v-card>
              <v-card-title>
                <span class="headline">Order Products</span>
              </v-card-title>
              <v-card-text>
                <v-list>
                  <v-list-item v-for="product in order_products" :key="product.id">
                    {{ product.name }} {{ product.quantity }}
                  </v-list-item>
                </v-list>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeProductInstanceDialog">Close</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- attach order items -->

          <v-dialog v-model="productInstanceDialog" max-width="800px">
            <v-card>
              <v-card-title>
                <span class="text-h5">Order Items</span>
              </v-card-title>
              <v-card-text>
                <v-form>
                  <v-table>
                    <thead>
                      <tr>
                        <th class="text-left">Product Name</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Barcode</th>
                        <th class="text-left">Product Instances</th> -
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in orderItems" :key="index">
                        <td>{{ item.product_name }}</td>
                        <!-- <td>{{ formatProductDetails(item.product) }}</td> -->
                        <td>{{ item.quantity }}</td>

                        <td>
                          <v-combobox v-model="item.barcodes" multiple chips deletable-chips label="Enter barcodes"
                            placeholder="Enter Key" :delimiter="[' ', ',']"></v-combobox>
                        </td>
                        <td>
                    
                        <v-select v-if="item.selected_instances.length < item.quantity"
                            v-model="item.selected_instances" :items="item.available_instances" item-title="barcode"
                            item-value="id" multiple chips hide-details>
                          </v-select>



                        </td>
                      </tr>
                    </tbody>

                  </v-table>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDispatchDialog">Close</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="updateDispatchDetails">Pick</v-btn>
              </v-card-actions>
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
      default: 'Picking'
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
        { name: 'order_products', label: 'Order Details', icon: 'mdi-package-variant' },
        { name: 'status', label: 'Status', icon: 'mdi-information' }
      ]
    }
  },
  data() {
    return {



      orderItems: [],
      selectedOrder: null,

      dialog: false,
      dialogDelete: false,
      productInstanceDialog: false,
      search: '',
      entities: [],
      products: [],
      product_instances: [],
      available_instances: [],
      selected_instances: [],
      selected_instance: [],
      order_products: [],
      selectedItems: [],
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

      this.selectedOrder = item;
      this.orderItems = item.order_products.map(orderProduct => {
        const product = item.products.find(p => p.id === orderProduct.product_id);

        return {
          ...orderProduct,
          product_name: product ? product.name : '',
          selected_instances: [],
          available_instances: product ? product.product_instances : [],
          selectedInstance: null,
        };
      });
    },
    addInstance(item) {
      if (item.selectedInstance && item.selected_instances.length < item.quantity) {
        const instance = item.available_instances.find(i => i.id === item.selectedInstance);
        item.selected_instances.push(instance);
        item.available_instances = item.available_instances.filter(i => i.id !== instance.id);
        item.selectedInstance = null;
      }
    },
    removeInstance(item, index) {
      const removedInstance = item.selected_instances.splice(index, 1)[0];
      item.available_instances.push(removedInstance);
    },
    closeDispatchDialog() {
      this.productInstanceDialog = false;
      this.orderItems = [];
      this.selectedOrder = null;
    },
    async updateDispatchDetails() {
      // Validate that all items have the correct number of instances selected
      const isValid = this.orderItems.every(item => {
        console.log(`Product ID: ${item.product_id}`);
        console.log(`Selected Instances: ${item.selected_instances.length}`);
        console.log(`Required Quantity: ${item.quantity}`);
        return item.selected_instances.length === item.quantity;
      });


      if (!isValid) {
        this.$toastr.error('Please select the correct number of instances for each product.');
        return;
      }

      // Prepare data for API call
      const itemsPicked = {
        order_id: this.selectedOrder.id,
        picked_items: this.orderItems.map(item => ({
          product_id: item.product_id,
          instance_ids: item.selected_instances,
        })),
      };

      try {
        // Make API call to update dispatch details
        await axios.post('/api/v1/pickOrderitems', itemsPicked);
        this.$toastr.success('Order dispatched successfully!');
        this.closeDispatchDialog();
      } catch (error) {
        console.error('Error dispatching order:', error);
        this.$toastr.error('An error occurred while dispatching the order. Please try again.');
      }
    },
    // For future barcode scanning feature
    handleBarcodeScanned(item) {
      const scannedInstance = item.available_instances.find(i => i.barcode === item.scannedBarcode);
      if (scannedInstance) {
        item.selectedInstance = scannedInstance.id;
        this.addInstance(item);
      } else {
        this.$toastr.errror('Invalid barcode or product instance not available.');
      }
      item.scannedBarcode = '';
    },

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

    formatProductDetails(order, orderProduct) {
      const product = order.products.find(p => p.id === orderProduct.product_id);
      return product ? `${product.name} (${orderProduct.quantity})` : `Unknown Product (${orderProduct.quantity})`;
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

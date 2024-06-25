<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Product Instances</v-card-title>

      <div v-if="selectedItems.length > 0" class="x-actions">
        <v-icon class="mx-1" color="error" @click="confirmDeleteDialog" title="Delete">mdi-delete</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkAssignBin" title="Assign Bin">mdi-package-variant-closed</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkPickItem" title="Pick Item">mdi-hand-pointing-right</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkTransferItem" title="Transfer Item">mdi-truck</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkAssignToOrder" title="Assign to Order">mdi-clipboard-check</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkReturnItem" title="Return Item">mdi-undo</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkUpdateStatus" title="Update Status">mdi-update</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkPrint" title="Print">mdi-printer</v-icon>
      </div>

      <v-data-table :headers="headers" :items="productInstances" v-model="selectedItems" item-value="id" show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editProduct(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
          </div>
        </template>
      </v-data-table>

      <!-- Bulk Action Dialog -->
      <v-dialog v-model="bulkDialog" max-width="500">
        <v-card>
          <v-card-title class="headline">{{ bulkActionTitle }}</v-card-title>
          <v-card-text>
            <v-select v-if="bulkAction === 'bulkAssignBin'" :items="bins" label="Select Bin" clearable v-model="selectedBin" item-title="name" item-value="id"></v-select>
            <v-select v-if="bulkAction === 'bulkUpdateStatus'" v-model="selectedStatus" :items="statuses" label="Select Status" item-title="name" item-value="name" clearable></v-select>

            <div v-if="bulkAction === 'bulkTransferItem'">
              <v-select v-model="transferType" :items="transferTypes" label="Transfer Type" required></v-select>
              <v-select v-model="sourceLocation" :items="sourceOptions" label="From" item-title="name" item-value="id" required></v-select>
              <v-select v-model="destinationLocation" :items="destinationOptions" label="To" item-title="name" item-value="id" required></v-select>
            </div>
            <v-select v-if="bulkAction === 'bulkAssignToOrder'" v-model="selectedOrder" :items="orders" label="Select Order" item-title="order_no" item-value="id" clearable></v-select>

            <div v-if="bulkAction === 'bulkPickItem'">
              <v-alert type="info">Are you sure you want to pick the selected items?</v-alert>
            </div>

            <div v-if="bulkAction === 'bulkReturnItem'">
              <v-select v-model="returnReason" :items="returnReasons" label="Select Reason for Return" item-title="reason" item-value="id" clearable></v-select>
              <v-alert type="info">Are you sure you want to return the selected items?</v-alert>
            </div>

            <div v-if="bulkAction === 'bulkPrint'">
              <v-select v-model="printFormat" :items="printFormats" label="Select Print Format" item-title="name" item-value="id" clearable></v-select>
              <v-alert type="info">Are you sure you want to print the selected items?</v-alert>
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="closeBulkDialog">Cancel</v-btn>
            <v-btn color="primary" text @click="performBulkAction">OK</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>


      <!-- Delete Bulk Confirmation Dialog -->
<v-dialog v-model="confirmDeleteDialog" max-width="500">
  <v-card>
    <v-card-title class="headline">Confirm Delete</v-card-title>
    <v-card-text> Are you sure you want to delete the selected items? </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" text @click="closeConfirmDeleteDialog">Cancel</v-btn>
      <v-btn color="error" text @click="confirmDelete">Delete</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  mixins: [fetchDataMixin],
  props: {
    productId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      confirmDeleteDialog: false,
      bulkDialog: false,
      bulkAction: '',
      bulkActionTitle: '',
      selectedBin: null,
      selectedStatus: null,
      selectedOrder: null,
      transferType: '',
      sourceLocation: null,
      destinationLocation: null,
      returnReason: null,
      printFormat: null,
      bins: [],
      statuses: [],
      orders: [],
      transferTypes: ['Warehouse to Warehouse', 'Merchant to Merchant', 'Bin to Bin'],
      returnReasons: [{ id: 1, reason: 'Defective' }, { id: 2, reason: 'Customer Return' }, { id: 3, reason: 'Other' }],
      printFormats: [{ id: 1, name: 'PDF' }, { id: 2, name: 'Excel' }],
      productInstances: [],
      selectedItems: [],
      headers: [
        { title: 'Barcode', value: 'barcode' },
        { title: 'Status', value: 'status' },
        { title: 'Bin', value: 'bin.code' },
        { title: 'Price', value: 'price' },
        { title: 'Actions', value: 'actions', sortable: false }
      ]
    };
  },
  created() {
    this.fetchBulkData();
  },
  computed: {
   sourceOptions() {
     if (this.transferType === 'Warehouse to Warehouse') {
       return this.warehouses;
     } else if (this.transferType === 'Merchant to Merchant') {
       return this.merchants;
     } else if (this.transferType === 'Bin to Bin') {
       return this.bins;
     }
     return [];
   },
   destinationOptions() {
     if (this.transferType === 'Warehouse to Warehouse') {
       return this.warehouses;
     } else if (this.transferType === 'Merchant to Merchant') {
       return this.merchants;
     } else if (this.transferType === 'Bin to Bin') {
       return this.bins;
     }
     return [];
   }
 },
  methods: {
  closeConfirmDeleteDialog() {
  this.confirmDeleteDialog = false;
},
confirmDeleteDialog() {
  // Implement your delete logic here
  console.log('Deleting selected items');
  this.confirmDeleteDialog = true;
},
    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    },
    async fetchBulkData() {
      this.bins = await this.fetchDataFromApi('/api/v1/bins');
      this.statuses = await this.fetchDataFromApi('/api/v1/orderstatus');
      this.orders = await this.fetchDataFromApi('/api/v1/orders');
      this.warehouses = await this.fetchDataFromApi('/api/v1/warehouses');
  this.merchants = await this.fetchDataFromApi('/api/v1/vendors');
    },
    bulkAssignBin() {
      this.bulkAction = 'bulkAssignBin';
      this.bulkActionTitle = 'Assign Bin';
      this.bulkDialog = true;
    },
    bulkPickItem() {
      this.bulkAction = 'bulkPickItem';
      this.bulkActionTitle = 'Pick Item';
      this.bulkDialog = true;
    },
    bulkTransferItem() {
      this.bulkAction = 'bulkTransferItem';
      this.bulkActionTitle = 'Transfer Item';
      this.bulkDialog = true;
    },
    bulkAssignToOrder() {
      this.bulkAction = 'bulkAssignToOrder';
      this.bulkActionTitle = 'Assign to Order';
      this.bulkDialog = true;
    },
    bulkReturnItem() {
      this.bulkAction = 'bulkReturnItem';
      this.bulkActionTitle = 'Return Item';
      this.bulkDialog = true;
    },
    bulkUpdateStatus() {
      this.bulkAction = 'bulkUpdateStatus';
      this.bulkActionTitle = 'Update Status';
      this.bulkDialog = true;
    },
    bulkPrint() {
      this.bulkAction = 'bulkPrint';
      this.bulkActionTitle = 'Print';
      this.bulkDialog = true;
    },
    closeBulkDialog() {
      this.bulkDialog = false;
      this.bulkAction = '';
      this.bulkActionTitle = '';
      this.selectedBin = null;
      this.selectedStatus = null;
      this.selectedOrder = null;
      this.transferType = '';
      this.sourceLocation = null;
      this.destinationLocation = null;
      this.returnReason = null;
      this.printFormat = null;
    },
    // ... other methods
async performBulkAction() {
  const payload = {
    product_instance_ids: this.selectedItems,
  };

  if (this.bulkAction === 'bulkAssignBin') {
    payload.bin_id = this.selectedBin;
  } else if (this.bulkAction === 'bulkUpdateStatus') {
    payload.status = this.selectedStatus;
  } else if (this.bulkAction === 'bulkTransferItem') {
    payload.transferType = this.transferType;
    payload.sourceLocation = this.sourceLocation;
    payload.destinationLocation = this.destinationLocation;
  } else if (this.bulkAction === 'bulkAssignToOrder') {
    payload.order = this.selectedOrder;
  } else if (this.bulkAction === 'bulkReturnItem') {
    payload.reason = this.returnReason;
  } else if (this.bulkAction === 'bulkPrint') {
    payload.format = this.printFormat;
  }

  try {
    const response = await axios.post(`/api/v1/${this.bulkAction}`, payload); // Adjust the endpoint URL
    console.log('Bulk action response:', response.data);
    this.fetchProductInstances(); // Refresh the product instances
  } catch (error) {
    console.error('Error performing bulk action:', error);
  }

  this.closeBulkDialog();
},
async performBulkDelete() {
  try {
    const response = await axios.post('/api/bulk-delete', { items: this.selectedItems });
    console.log('Bulk delete response:', response.data);
    this.fetchProductInstances(); // Refresh the product instances
  } catch (error) {
    console.error('Error performing bulk delete:', error);
  }
  this.closeBulkDeleteDialog();
},
    fetchProductInstances() {
      const url = `/api/v1/products/${this.productId}/instances`;
      axios
        .get(url, {})
        .then(response => {
          this.productInstances = response.data.instances;
        })
        .catch(error => {
          console.error('Error fetching product instances:', error);
        });
    }
  },
  watch: {
    productId() {
      if (this.dialog) {
        this.fetchProductInstances();
      }
    }
  }
};
</script>

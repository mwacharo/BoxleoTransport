<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Product Instances</v-card-title>


      <div v-if="selectedItems.length > 0" class="x-actions">
        <v-icon class="mx-1" color="error" @click="confirmBulkDeleteDialog" title="Delete">mdi-delete</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkAssignBin" title="Assign Bin">mdi-package-variant-closed</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkPickItem" title="Pick Item">mdi-hand-pointing-right</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkTransferItem" title="Transfer Item">mdi-truck</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkAssignToOrder" title="Assign to Order">mdi-clipboard-check</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkReturnItem" title="Return Item">mdi-undo</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkUpdateStatus" title="Update Status">mdi-update</v-icon>
        <v-icon class="mx-1" color="primary" @click="bulkPrint" title="Print">mdi-printer</v-icon>
      </div>

      <v-data-table :headers="headers" :items="productInstances"
          v-model="selectedItems"
            item-value="id"
      show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editProduct(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
          </div>
        </template>
      </v-data-table>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    productId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      dialog: false,

      productInstances: [],
      selectedItems: [],

      headers: [
        { title: 'Barcode', value: 'barcode' },
        { title: 'Status', value: 'status' },
        { title: 'Actions', value: 'actions', sortable: false }
      ]
    };
  },
  methods: {
    show() {
      // console.log(item);
      this.dialog = true;

    },
    close() {
      this.dialog = false;
    },

    fetchProductInstances() {
      const url = `/api/v1/products/${this.productId}/instances`;
      axios
        .get(url, {})
        .then(response => {
          this.productInstances = response.data.instances;
        })
        .catch(error => {
          console.error('Error fetching product intances:', error);
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

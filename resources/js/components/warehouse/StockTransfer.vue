<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Stock Transfer</v-card-title>
      <v-stepper v-model="step" :items="items" show-actions>
        <template v-slot:item.1>
          <h3 class="text-h6">Select Type of Transfer</h3>
          <v-select
            v-model="transferType"
            :items="transferTypes"
            label="Transfer Type"
            required
          ></v-select>
        </template>

        <template v-slot:item.2>
          <h3 class="text-h6">Select From and Destination</h3>
          <v-form>
            <v-select
              v-model="sourceLocation"
              :items="getSourceLocations"
              label="From"
              item-title="name"
              item-value="id"
              required
            ></v-select>
            <v-select
              v-model="destinationLocation"
              :items="getDestinationLocations"
              label="To"
              item-title="name"
              item-value="id"
              required
            ></v-select>
            <v-select
              v-model="selectedProductIds"
              :items="products"
              label="Select Products"
              item-title="name"
              item-value="id"
              multiple
              required
            ></v-select>
          </v-form>
        </template>

        <template v-slot:item.3>
          <h3 class="text-h6">Confirm Transfer</h3>
          <v-data-table
            :headers="headers"
            :items="selectedProducts"
            :items-per-page="5"
            hide-default-footer
          >
            <template v-slot:item.quantity="{ item }">
              <v-text-field
                v-model.number="item.quantity"
                type="number"
                outlined
                hide-details
                dense
                required
              ></v-text-field>
            </template>
          </v-data-table>
        </template>
      </v-stepper>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
        <v-btn color="green darken-1" text @click="submitTransfer" :disabled="!canSubmit">Submit Transfer</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  mixins: [fetchDataMixin],
  data: () => ({
    dialog: false,
    step: 1,
    items: [
      'Select Type of Transfer',
      'Select From and Destination',
      'Confirm Transfer',
    ],
    transferType: '',
    transferTypes: [
      'Warehouse to Warehouse',
      'Merchant to Merchant',
      'Bin to Bin',
    ],
    sourceLocation: '',
    destinationLocation: '',
    selectedProductIds: [],
    products: [],
    warehouses: [],
    merchants: [],
    bins: [],
    headers: [
      { title: 'SKU', value: 'sku' },
      { title: 'Name', value: 'name' },
      { title: 'Quantity', value: 'quantity' },
    ],
  }),
  computed: {
    getSourceLocations() {
      switch (this.transferType) {
        case 'Warehouse to Warehouse':
          return this.warehouses;
        case 'Merchant to Merchant':
          return this.merchants;
        case 'Bin to Bin':
          return this.bins;
        default:
          return [];
      }
    },
    getDestinationLocations() {
      return this.getSourceLocations.filter(loc => loc.id !== this.sourceLocation);
    },
    selectedProducts() {
      return this.products.filter(product => this.selectedProductIds.includes(product.id));
    },
    canSubmit() {
      return this.transferType && this.sourceLocation && this.destinationLocation &&
             this.selectedProductIds.length > 0 && this.selectedProducts.every(p => p.quantity > 0);
    },
  },
  created() {
    this.fetchBulkData();
  },
  methods: {
    async fetchBulkData() {
      this.warehouses = await this.fetchDataFromApi('/api/v1/warehouses');
      this.merchants = await this.fetchDataFromApi('/api/v1/vendors');
      this.bins = await this.fetchDataFromApi('/api/v1/bins');
      this.products = await this.fetchDataFromApi('/api/v1/products');
    },
    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
      this.resetForm();
    },
    resetForm() {
      this.step = 1;
      this.transferType = '';
      this.sourceLocation = '';
      this.destinationLocation = '';
      this.selectedProductIds = [];
    },
    submitTransfer() {
      // Implement the API call to submit the transfer
      const transferData = {
        transferType: this.transferType,
        sourceLocation: this.sourceLocation,
        destinationLocation: this.destinationLocation,
        products: this.selectedProducts.map(p => ({ id: p.id, quantity: p.quantity })),
      };
      console.log('Submitting transfer:', transferData);
      // TODO: Make API call to submit transfer
      // After successful submission:
      this.$toastr.success('Stock transfer submitted successfully');
      this.close();
    },
  },
};
</script>

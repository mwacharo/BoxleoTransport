<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Receiving</v-card-title>

      <v-stepper v-model="step" :items="items" show-actions @update:modelValue="stepUpdated">
        <template v-slot:item.1>
          <h3 class="text-h6">Select Warehouse, Vendor, Product</h3>
          <br>
          <v-form ref="form">
            <v-select
              v-model="selectedWarehouse"
              :items="warehouses"
              label="Select Warehouse"
              item-title="name"
              item-value="id"
              :rules="[v => !!v || 'Warehouse is required']"
            ></v-select>
            <v-select
              v-model="selectedVendor"
              :items="vendors"
              label="Select Vendor"
              item-title="name"
              item-value="id"
              :rules="[v => !!v || 'Vendor is required']"
            ></v-select>
            <v-select
              v-model="selectedProductIds"
              :items="products"
              label="Select Product"
              item-title="name"
              item-value="id"
              multiple
              :rules="[v => v.length > 0 || 'At least one product is required']"
            ></v-select>
          </v-form>
        </template>

        <template v-slot:item.2>
          <h3 class="text-h6">Product Tables</h3>
          <br>
          <v-data-table
            :headers="headers"
            :items="addedProducts"
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
                :rules="[v => v > 0 || 'Quantity must be greater than 0']"
              ></v-text-field>
            </template>
            <template v-slot:item.price="{ item }">
              <v-text-field
                v-model.number="item.price"
                type="number"
                outlined
                hide-details
                dense
                :rules="[v => v >= 0 || 'Price must be non-negative']"
              ></v-text-field>
            </template>
            <template v-slot:item.weight="{ item }">
              <v-text-field
                v-model.number="item.weight"
                type="number"
                outlined
                hide-details
                dense
                :rules="[v => v >= 0 || 'Weight must be non-negative']"
              ></v-text-field>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
            </template>
          </v-data-table>
        </template>

        <template v-slot:item.3>
          <h3 class="text-h6">Confirm</h3>
          <br>
          <v-data-table :headers="headers" :items="addedProducts">
            <template v-slot:item.actions="{ item }">
              <v-icon color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
            </template>
          </v-data-table>
        </template>
      </v-stepper>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="receiveBulk" :disabled="!isFormValid">Submit</v-btn>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { fetchDataMixin } from '@/mixins/fetchDataMixin';

export default {
  mixins: [fetchDataMixin],

  data: () => ({
    shipping: 0,
    step: 1,
    dialog: false,
    items: [
      'Select WH',
      'Insert Quantity',
      'Submit',
    ],
    headers: [
      { title: 'SKU', value: 'sku' },
      { title: 'Name', value: 'name' },
      { title: 'Quantity', value: 'quantity' },
      { title: 'Price', value: 'price' },
      { title: 'Weight', value: 'weight' },
      { title: 'Actions', value: 'actions', sortable: false },
    ],
    products: [],
    warehouses: [],
    vendors: [],
    selectedWarehouse: '',
    selectedVendor: '',
    selectedProductIds: [],
    addedProducts: [], // Added this line
  }),

  computed: {
    isFormValid() {
      return this.selectedWarehouse && this.selectedVendor && this.addedProducts.length > 0 &&
             this.addedProducts.every(p => p.quantity > 0 && p.price >= 0 && p.weight >= 0);
    }
  },

  created() {
    this.fetchBulkData();
  },

  methods: {
    async fetchBulkData() {
      try {
        const [vendors, warehouses, products] = await Promise.all([
          this.fetchDataFromApi('/api/v1/vendors'),
          this.fetchDataFromApi('/api/v1/warehouses'),
          this.fetchDataFromApi('/api/v1/products')
        ]);
        this.vendors = vendors;
        this.warehouses = warehouses;
        this.products = products;
      } catch (error) {
        console.error('Error fetching data:', error);
        // Handle error (e.g., show error message to user)
      }
    },

    stepUpdated() {
      if (this.step === 2) {
        this.addProduct();
      }
    },

    addProduct() {
      this.selectedProductIds.forEach((productId) => {
        const product = this.products.find(p => p.id === productId);
        if (product && !this.addedProducts.some(p => p.product_id === product.id)) {
          this.addedProducts.push({
            product_id: product.id,
            name: product.name,
            sku: product.sku,
            quantity: 0,
            price: Number(product.price) || 0,
            weight: 0
          });
        }
      });
      this.selectedProductIds = [];
    },

    deleteProduct(item) {
      const index = this.addedProducts.findIndex(p => p.product_id === item.product_id);
      if (index !== -1) {
        this.addedProducts.splice(index, 1);
      }
    },

    async receiveBulk() {
      if (!this.isFormValid) {
        // Show error message
        return;
      }

      try {
        const response = await axios.post('api/v1/receiveBulk', {
          products: this.addedProducts,
          warehouse: this.selectedWarehouse,
          vendor: this.selectedVendor,
        });

        this.$toastr.success(response.data.message);

        this.dialog = false;
      } catch (error) {
        console.error('There was an error receiving the items!', error);
        // Show error message to user
      }
    }
,

    show() {
      this.dialog = true;
    },

    close() {
      this.dialog = false;
    }
  }
};
</script>

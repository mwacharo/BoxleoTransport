<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>Product</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="toggleFilterDrawer">
              <v-icon>mdi-filter</v-icon>
            </v-btn>

            <v-btn color="primary" @click="addProduct">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field v-model="search" label="Search by name or sku" clearable @input="searchProducts" dense></v-text-field>
          <v-responsive>
            <v-data-table :headers="headers" :items="searchProducts">
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editProduct(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="blue" @click="viewProductInstances(item.id)">mdi-eye</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
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
                    <v-col cols="12" md="6">
                      <v-text-field v-model="editedItem.name" label="Name" prepend-icon="mdi-account"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="editedItem.sku" label="SKU" prepend-icon="sku"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="editedItem.description"
                        label="Description"
                        prepend-icon="mdi-web"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="editedItem.price" label="Price" prepend-icon="mdi-web"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select
                        :rules="vendorRules"
                        required
                        v-model="editedItem.vendor_id"
                        prepend-icon="mdi-web"
                        :items="vendors"
                        item-title="name"
                        item-value="id"
                        label="Vendor"
                      ></v-select>
                    </v-col>

                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="editedItem.quantity"
                        label="Quantity"
                        prepend-icon="mdi-phone"
                  
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
                <v-btn color="blue darken-1" text @click.prevent="saveProduct">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>
              <v-card-text>This will permanently delete the product. Continue?</v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="primary" text @click="deleteProductConfirm">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-main>
      </v-app>
    </v-layout>
    <instance ref="InstanceComponent" :productId="selectedProductId"/>
  </v-card>
</template>

<script>
import Instance from '@/components/products/Instance.vue';

export default {
  components: {
    Instance
  },
  props: {
    user_id: {
      type: Number,
      required: true

    },

  },

  data() {
    return {
      search: '',
      vendors: [],
      vendor: '',
      products: [],
      selectedProductId: '',
      id: '',
      headers: [
        // { title: '#', value: 'index' },
        { title: 'Name', value: 'name' },
        { title: 'SKU', value: 'sku' },
        { title: 'Description', value: 'description' },
        { title: 'Quantity Available', value: 'quantity' },
        // { title: 'Reserved', value: 'quantity' },
        // { title: 'Committed', value: 'quantity' },
        // { title: 'Alltime', value: 'quantity' },
        { title: 'Total price', value: 'total_price' },
        { title: 'Actions', value: 'actions', sortable: false }
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        description: '',
        user_id: this.user_id,
        price: '',
        quantity: '',
        sku: '',
        vendor: '',
        vendor_id: this.vendor,
      },
      defaultItem: {
        name: '',
        user_id: this.user_id,
        price: '',
        quantity: '',
        sku: '',
        vendor: this.vendor_id,
      },
      dialog: false,
      dialogDelete: false
    };
  },

  computed: {
    searchProducts() {
      if (!this.search) return this.products;
      return this.products.filter(
        product =>
          product.name.toLowerCase().includes(this.search.toLowerCase()) ||
          product.sku.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? 'New Product' : 'Edit Product';
    }
  },

  created() {
    this.fetchVendors();
    this.fetchProducts();
  },

  methods: {
    viewProductInstances(id) {
      this.selectedProductId = id;
      this.$refs.InstanceComponent.show();
    },
    addProduct() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },

    editProduct(product) {
      this.editedIndex = this.products.indexOf(product);
      this.editedItem = { ...product };
      this.dialog = true;
    },

    deleteProduct(product) {
      this.editedIndex = this.products.indexOf(product);
      this.editedItem = { ...product };
      this.dialogDelete = true;
    },

    deleteProductConfirm() {
      axios
        .delete(`/api/v1/products/${this.editedItem.id}`)
        .then(response => {
          this.$toastr.success(response.data.message);
          this.fetchProducts();
          this.closeDelete();
        })
        .catch(error => {
          console.error('Error deleting product:', error);
          this.$toastr.error('Failed to delete the product!');
        });
    },

    saveProduct() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/products/${this.editedItem.id}`, this.editedItem)
          .then(response => {
            this.$toastr.success('Product updated successfully');
            this.fetchProducts();
            this.closeDialog();
          })
          .catch(error => {
            this.$toastr.error('Error updating product');
            console.error('Error updating product:', error);
          });
      } else {
        axios
          .post('/api/v1/products', this.editedItem)
          .then(response => {
            this.$toastr.success(response.data.message);
            this.fetchProducts();
            this.closeDialog();
          })
          .catch(error => {
            this.$toastr.error('Error adding product');
            console.error('Error adding product:', error);
          });
      }
    },

    fetchProducts() {
      axios
        .get('/api/v1/products')
        .then(response => {
          this.products = response.data;
        })
        .catch(error => {
          console.error('Error fetching products:', error);
        });
    },
    fetchVendors() {
      axios
        .get('/api/v1/vendors')
        .then(response => {
          this.vendors = response.data;
        })
        .catch(error => {
          console.error('Error fetching products:', error);
        });
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
    }
  }
};
</script>

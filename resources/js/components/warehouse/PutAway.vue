<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Put Away</v-card-title>

      <v-stepper
    v-model="step"
    :items="items"
    show-actions
  >
    <template v-slot:item.1>
      <h3 class="text-h6">Order</h3>

      <br>

      <v-form>
      <v-select
        v-model="selectedWarehouse"
        :items="warehouses"
        label="Select Warehouse"
        item-title="name"
        item-value="id"
      ></v-select>
      <v-select
        v-model="selectedVendor"
          :items="vendors"
          label="Select Vendor"
          item-title="name"
        item-value="id"
          ></v-select>


          <v-select
            v-model="selectedProductIds"
              :items="products"
              label="Select Product"
              item-title="name"
            item-value="id"
            multiple
              ></v-select>

          </v-form>
    </template>

    <template v-slot:item.2>
      <h3 class="text-h6">Shipping</h3>

      <br>

      <v-radio-group v-model="putawayMode" row>
           <v-radio label="Assign bins by product" value="product"></v-radio>
           <v-radio label="Assign bins by product instance" value="instance"></v-radio>
         </v-radio-group>
    </template>

    <template v-slot:item.3>
      <h3 class="text-h6">Confirm</h3>



      <br>

      <v-responsive>

      <v-card>
      <v-data-table
    :headers="headers"
    :items="products"
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
     ></v-text-field>
    </template>




    <template v-slot:item.weight_kgs="{ item }">
     <span>{{ item.weight_kgs }}</span>
    </template>


    <template v-slot:item.actions="{ item }">
     <v-icon color="red" @click="deleteProduct(item)">mdi-delete</v-icon>
     <v-icon class="mx-1" color="blue" @click="viewProductInstances(item.id)">mdi-eye</v-icon>

    </template>
    </v-data-table>
    </v-card>



      </v-responsive>

    </template>
  </v-stepper>



      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
      </v-card-actions>

      <instance ref="InstanceComponent" :productId="selectedProductId"/>

    </v-card>
  </v-dialog>
</template>

<script>
import Instance from '@/components/products/Instance.vue';

import { fetchDataMixin } from '@/mixins/fetchDataMixin';

  export default {

  components: {
    Instance
  },
  mixins: [fetchDataMixin],

    data: () => ({
      shipping: 0,
      step: 1,
      dialog:false,
      items: [
        'Select Product',
        'Select Product or Instance ',
        'Allocate BIn',
      ],
      products: [

      ],
      headers: [
  { title: 'SKU', value: 'sku' },
  { title: 'Name', value: 'name' },
  { title: 'Quantity', value: 'quantity' },
  { title: 'Price', value: 'price' },
  { title: 'Weight', value: 'weight' },
  { title: 'Actions', value: 'actions', sortable: false },
],
      warehouses:[],
    vendors:[],
    selectedWarehouse:'',
    selectedVendor:'',
    selectedProductIds:'',
    }),

    computed: {

    },

    created() {

      // fetch vendors ,warehouses, products
      this.fetchBulkData();
    },
    methods:{

    async fetchBulkData() {
      this.vendors = await this.fetchDataFromApi('/api/v1/vendors');
      this.warehouses = await this.fetchDataFromApi('/api/v1/warehouses');
      this.products = await this.fetchDataFromApi('/api/v1/products');
    },


        viewProductInstances(id) {
          this.selectedProductId = id;
          this.$refs.InstanceComponent.show();
        },
    show() {
      // console.log(item);
      this.dialog = true;

    },
    close() {
      this.dialog = false;
    }

    }
  };
</script>

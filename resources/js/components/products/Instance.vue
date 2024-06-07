<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Product Instances</v-card-title>

      <v-data-table :headers="headers" :items="productInstances"
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



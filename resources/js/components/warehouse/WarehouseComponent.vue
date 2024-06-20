<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>Warehousing</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="receive">
    Receive
    <v-icon right>mdi-download</v-icon> <!-- Appropriate icon for receiving items -->
  </v-btn>

  <v-btn color="primary" @click="putAway">
    Put Away
    <v-icon right>mdi-archive</v-icon> <!-- Icon for putting items away -->
  </v-btn>

  <v-btn color="primary" @click="level">
    Level
    <v-icon right>mdi-layers-outline</v-icon> <!-- Icon representing levels or layers -->
  </v-btn>

  <v-btn color="primary" @click="bin">
    Bin
    <v-icon right>mdi-package-variant-closed</v-icon> <!-- Icon for a bin or box -->
  </v-btn>

  <v-btn color="primary" @click="bay">
    Bay
    <v-icon right>mdi-warehouse</v-icon> <!-- Icon for a warehouse bay -->
  </v-btn>

  <v-btn color="primary" @click="row">
    Row
    <v-icon right>mdi-view-sequential-outline</v-icon> <!-- Icon for rows -->
  </v-btn>

  <v-btn color="primary" @click="area">
    Area
    <v-icon right>mdi-map-outline</v-icon> <!-- Icon for an area or map -->
  </v-btn>

  <v-btn color="primary" @click="addWarehouse">
    <v-icon>mdi-plus</v-icon>
    </v-btn>

          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterWarehouse" dense></v-text-field>
          <v-responsive>
            <v-data-table :headers="headers" :items="searchWarehouses">
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editWarehouse(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteWarehouse(item)">mdi-delete</v-icon>
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
                      <v-text-field v-model="editedItem.email" label="Email" prepend-icon="mdi-email"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="editedItem.address" label="Address" prepend-icon="mdi-web"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="editedItem.phone" label="Phone (optional)" prepend-icon="mdi-phone"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
                <v-btn color="blue darken-1" text @click.prevent="saveWarehouse">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>
              <v-card-text>This will permanently delete the warehouse. Continue?</v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="primary" text @click="deleteWarehouseConfirm">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-main>
      </v-app>
    </v-layout>
    <Bay ref="BayComponent"/>
    <Bin ref="BinComponent"/>
    <Level ref="LevelComponent"/>
    <Receive ref="ReceiveComponent"/>
    <PutAway ref="PutAwayComponent"/>
    <Row ref="RowComponent"/>
    <Area ref="AreaComponent"/>
  </v-card>
</template>

<script>

import { fetchDataMixin } from '@/mixins/fetchDataMixin';

import Bay from '@/components/warehouse/Bay.vue';
import Level from '@/components/warehouse/Level.vue';
import Receive from '@/components/warehouse/Receive.vue';
import Row from '@/components/warehouse/Row.vue';
import Bin from '@/components/warehouse/Bin.vue';
import Area from '@/components/warehouse/Area.vue';
import PutAway from '@/components/warehouse/PutAway.vue';


export default {
  mixins: [fetchDataMixin],
  components: {
    Area,
    Bay,
    Level,
    Receive,
    Row,
    Bin,
    PutAway,
  },
  props: {
    user_id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      search: '',
      warehouses: [],
      headers: [
        { title: '#', value: 'index' },
        { title: 'Name', value: 'name' },
        { title: 'Address', value: 'address' },
        { title: 'Email', value: 'email' },
        { title: 'Phone', value: 'phone' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        email: '',
        user_id: this.user_id,
        address: '',
        phone: '',
      },
      defaultItem: {
        name: '',
        email: '',
        address: '',
        user_id: this.user_id,
        phone: '',
      },
      dialog: false,
      dialogDelete: false,
    };
  },
  computed: {
    searchWarehouses() {
      if (!this.search) return this.warehouses;
      return this.warehouses.filter(
        (warehouse) =>
          warehouse.name.toLowerCase().includes(this.search.toLowerCase()) ||
          warehouse.email.toLowerCase().includes(this.search.toLowerCase()) ||
          warehouse.phone.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? 'New Warehouse' : 'Edit Warehouse';
    },
  },
  created() {
    this.fetchWarehouses();
  },
  methods: {
    putAway() {
      this.$refs.PutAwayComponent.show();
    },
    receive() {
      this.$refs.ReceiveComponent.show();
    },
    level() {
      this.$refs.LevelComponent.show();
    },
    bay() {
      this.$refs.BayComponent.show();
    },
    bin() {
      this.$refs.BinComponent.show();
    },
    area() {
      this.$refs.AreaComponent.show();
    },
    row() {
      this.$refs.RowComponent.show();
    },
    addWarehouse() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },
    editWarehouse(warehouse) {
      this.editedIndex = this.warehouses.indexOf(warehouse);
      this.editedItem = { ...warehouse };
      this.dialog = true;
    },
    deleteWarehouse(warehouse) {
      this.editedIndex = this.warehouses.indexOf(warehouse);
      this.editedItem = { ...warehouse };
      this.dialogDelete = true;
    },
    deleteWarehouseConfirm() {
      axios
        .delete(`/api/v1/warehouses/${this.editedItem.id}`)
        .then((response) => {
          this.$toastr.success(response.data.message);
          this.fetchWarehouses();
          this.closeDelete();
        })
        .catch((error) => {
          console.error('Error deleting warehouse:', error);
          this.$toastr.error('Failed to delete the warehouse!');
        });
    },
    saveWarehouse() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/warehouses/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Warehouse updated successfully');
            this.fetchWarehouses();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating warehouse');
            console.error('Error updating warehouse:', error);
          });
      } else {
        axios
          .post('/api/v1/warehouses', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchWarehouses();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding warehouse');
            console.error('Error adding warehouse:', error);
          });
      }
    },
    fetchWarehouses() {
      axios
        .get('/api/v1/warehouses')
        .then((response) => {
          this.warehouses = response.data;
        })
        .catch((error) => {
          console.error('Error fetching warehouse:', error);
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
    },
  },
};
</script>

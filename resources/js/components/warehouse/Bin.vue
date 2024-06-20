<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Bin</v-card-title>
      <v-toolbar flat>
        <v-btn color="primary" @click="addBin">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="bins" show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editBin(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteBin(item)">mdi-delete</v-icon>
          </div>
        </template>
      </v-data-table>

      <v-dialog v-model="dialog1" max-width="800">
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
                  <v-select :items="warehouses"
                  v-model="editedItem.warehouse_id"
                  label="Warehouse"
                  item-title="name"
                   item-value="id"
                   prepend-icon="mdi-warehouse"></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select :items="areas" v-model="editedItem.area_id" label="Area" item-title="name" item-value="id" prepend-icon="mdi-map-marker"></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select :items="rows" v-model="editedItem.row_id" label="Row" item-title="name" item-value="id" prepend-icon="mdi-table-row"></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select :items="bays" v-model="editedItem.bay_id" label="Bay" item-title="name" item-value="id" prepend-icon="mdi-rack"></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select :items="levels" v-model="editedItem.level_id" label="Level" item-title="name" item-value="id" prepend-icon="mdi-layers"></v-select>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveBin">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogDelete" max-width="290">
        <v-card>
          <v-card-title class="headline">Warning</v-card-title>
          <v-card-text>This will permanently delete the bin. Continue?</v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="closeDeleteDialog">Cancel</v-btn>
            <v-btn color="primary" text @click="deleteBinConfirm">OK</v-btn>
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

  data() {
    return {
      dialog: false,
      dialog1: false,
      dialogDelete: false,
      bins: [],
      warehouses: [],
      areas: [],
      rows: [],
      bays: [],
      levels: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        warehouse_id: '',
        area_id: '',
        row_id: '',
        bay_id: '',
        level_id: '',
        code: '',
      },
      defaultItem: {
        name: '',
        warehouse_id: '',
        area_id: '',
        row_id: '',
        bay_id: '',
        level_id: '',
        code: '',
      },
      headers: [
        { title: 'Code', value: 'code' },
        { title: 'Name', value: 'name' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
    };
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'New Bin' : 'Edit Bin';
    },
  },
  created() {
    this.fetchBins();
    this.fetchWarehouses();
    this.fetchAreas();
    this.fetchRows();
    this.fetchBays();
    this.fetchLevels();
  },
  methods: {
    generateCode(item) {
      return `${item.name}-${item.area_id}-${item.row_id}-${item.bay_id}-${item.level_id}-${item.name}`;
    },
    saveBin() {
      this.editedItem.code = this.generateCode(this.editedItem);

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/bins/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Bin updated successfully');
            this.fetchBins();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating bin');
            console.error('Error updating bin:', error);
          });
      } else {
        axios
          .post('/api/v1/bins', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchBins();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding bin');
            console.error('Error adding bin:', error);
          });
      }
    },
    fetchBins() {
      axios
        .get('/api/v1/bins')
        .then((response) => {
          this.bins = response.data;
        })
        .catch((error) => {
          console.error('Error fetching bins:', error);
        });
    },
    async fetchWarehouses() {
      this.warehouses = await this.fetchDataFromApi('/api/v1/warehouses');
    },
    async fetchAreas() {
      this.areas = await this.fetchDataFromApi('/api/v1/areas');
    },
    async fetchRows() {
      this.rows = await this.fetchDataFromApi('/api/v1/rows');
    },
    async fetchBays() {
      this.bays = await this.fetchDataFromApi('/api/v1/bays');
    },
    async fetchLevels() {
      this.levels = await this.fetchDataFromApi('/api/v1/levels');
    },
    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    },
    editBin(bin) {
      this.editedIndex = this.bins.indexOf(bin);
      this.editedItem = { ...bin };
      this.dialog1 = true;
    },
    deleteBin(bin) {
      this.editedIndex = this.bins.indexOf(bin);
      this.editedItem = { ...bin };
      this.dialogDelete = true;
    },
    addBin() {
      this.editedItem = { ...this.defaultItem };
      this.dialog1 = true;
    },
    closeDialog() {
      this.dialog1 = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
    async deleteBinConfirm() {
      try {
        const response = await axios.delete(`/api/v1/bins/${this.editedItem.id}`);
        this.$toastr.success(response.data.message);
        this.fetchBins();
        this.dialogDelete = false;
      } catch (error) {
        console.error('Error deleting bin:', error);
        this.$toastr.error('Error deleting bin');
      }
    },
    closeDeleteDialog() {
      this.dialogDelete = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
  },
};
</script>

<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>{{ entityName }}</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="addEntity">
              <v-icon>mdi-plus</v-icon>
            </v-btn>

            <v-btn color="primary" @click="toggleFilterDrawer">
              <v-icon>mdi-filter</v-icon>
            </v-btn>

            <v-btn color="primary" @click="refreshEntities">
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterEntities" dense></v-text-field>
          <v-responsive>
            <v-data-table show-select :headers="headers" :items="searchEntities">
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editEntity(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteEntity(item)">mdi-delete</v-icon>
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
                    <v-col cols="12" md="6" v-for="field in entityFields" :key="field.name">
                      <v-text-field
                        v-model="editedItem[field.name]"
                        :label="field.label"
                        :prepend-icon="field.icon"
                      ></v-text-field>
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
          <!-- navigation -->

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
                <v-select
                  v-model="filterStatus"
                  :items="statusOptions"
                  label="Filter by Status"
                  clearable
                ></v-select>
                <v-text-field
                  v-model="filterClientName"
                  label="Filter by Client Name"
                  clearable
                ></v-text-field>
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

export default {
  props: {
    entityName: {
      type: String,
      default: 'Order'
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
        { name: 'client_name', label: 'Client', icon: 'mdi-account' },
        { name: 'address', label: 'Address', icon: 'mdi-home' },
        { name: 'city', label: 'City', icon: 'mdi-city' },
        { name: 'phone', label: 'Contact', icon: 'mdi-phone' },
        { name: 'alt_phone', label: 'Other', icon: 'mdi-phone-in-talk' },
        { name: 'product_name', label: 'Product', icon: 'mdi-package-variant' },
        { name: 'quantity', label: 'Quantity', icon: 'mdi-format-list-numbered' },
        { name: 'status', label: 'Status', icon: 'mdi-information' }
      ]
    }
  },
  data() {
    return {
      search: '',
      entities: [],
      headers: [
        { title: '#', value: 'index' },
        ...this.entityFields.map(field => ({ title: field.label, value: field.name })),
        { title: 'Actions', value: 'actions', sortable: false }
      ],
      editedIndex: -1,
      editedItem: this.getDefaultItem(),
      defaultItem: this.getDefaultItem(),
      dialog: false,
      dialogDelete: false,
      filterDrawer: false,
      filterStatus: '',
      filterClientName: '',
      statusOptions: [
        { text: 'Pending', value: 'pending' },
        { text: 'Assigned', value: 'assigned' },
        { text: 'In Transit', value: 'in_transit' },
        { text: 'Completed', value: 'completed' }
      ]
    };
  },
  computed: {
    searchEntities() {
      if (!this.search) return this.entities;
      return this.entities.filter(entity =>
        this.entityFields.some(field => entity[field.name].toLowerCase().includes(this.search.toLowerCase()))
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? `New ${this.entityName}` : `Edit ${this.entityName}`;
    }
  },
  created() {
    this.fetchEntities();
  },
  methods: {
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
    async fetchEntities() {
      try {
        const response = await axios.get(this.apiEndpoint);
        this.entities = response.data;
      } catch (error) {
        console.error(`Error fetching ${this.entityName}:`, error);
      }
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
    applyFilters() {
      // Implement your filter logic here
      this.filterEntities();
      this.toggleFilterDrawer();
    },
    filterEntities() {
      // Apply filter logic based on filterStatus and filterClientName
      this.searchEntities = this.entities.filter(entity => {
        const matchesStatus = !this.filterStatus || entity.status === this.filterStatus;
        const matchesClientName = !this.filterClientName || entity.client_name.toLowerCase().includes(this.filterClientName.toLowerCase());
        return matchesStatus && matchesClientName;
      });
    },
    // closeDrawer() {
    //   this.drawer = false;
    // },
    refreshEntities() {
      this.fetchEntities();
    }
  }
};
</script>

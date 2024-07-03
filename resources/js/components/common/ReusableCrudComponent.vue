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

            <v-btn color="primary" @click="refreshEntities">
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterEntities" dense></v-text-field>
          <v-responsive>

            <v-data-table show-select
            :headers="headers" :items="searchEntities">
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
                    <v-col cols="12" md="6" v-for="field in entityFieldsRef" :key="field.name">
                      <component
                        :is="field.type === 'select' ? 'v-select' : 'v-text-field'"
                        v-model="editedItem[field.name]"
                        :label="field.label"
                        :prepend-icon="field.icon"
                        :items="field.options || []"
                        :item-title="field.itemText || 'title'"
                        :item-value="field.itemValue || 'value'"
                      ></component>
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
        </v-main>
      </v-app>
    </v-layout>
  </v-card>
</template>

<script>
import { toRef } from 'vue';
import axios from 'axios';

export default {
  props: {
    entityName: {
      type: String,
      required: true
    },
    apiEndpoint: {
      type: String,
      required: true
    },
    entityFields: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  setup(props) {
    const entityFieldsRef = toRef(props, 'entityFields');
    return { entityFieldsRef };
  },
  data() {
    return {
      search: '',
      entities: [],
      headers: [
        { title: '#', value: 'index' },
        ...this.entityFieldsRef.map(field => ({ title: field.label, value: field.name })),
        { title: 'Actions', value: 'actions', sortable: false }
      ],
      editedIndex: -1,
      editedItem: this.getDefaultItem(),
      defaultItem: this.getDefaultItem(),
      dialog: false,
      dialogDelete: false
    };
  },
  computed: {
    searchEntities() {
      if (!this.search) return this.entities;
      return this.entities.filter(entity =>
        this.entityFieldsRef.some(field =>
          String(entity[field.name]).toLowerCase().includes(this.search.toLowerCase())
        )
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? `New ${this.entityName}` : `Edit ${this.entityName}`;
    }
  },
  watch: {
    entityFieldsRef: {
      handler() {
        this.updateHeaders();
      },
      deep: true
    }
  },
  created() {
    this.fetchEntities();
  },
  methods: {
    updateHeaders() {
      this.headers = [
        { title: '#', value: 'index' },
        ...this.entityFieldsRef.map(field => ({ title: field.label, value: field.name })),
        { title: 'Actions', value: 'actions', sortable: false }
      ];
    },
    getDefaultItem() {
      const item = {};
      this.entityFieldsRef.forEach(field => {
        item[field.name] = '';
      });
      return item;
    },
    addEntity() {
      this.editedItem = this.getDefaultItem();
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
    const response = await axios.delete(`${this.apiEndpoint}/${this.editedItem.id}`);
    this.$toastr.success(response.data.message || `${this.entityName} deleted successfully`);
    this.fetchEntities();
    this.closeDelete();
  } catch (error) {
    console.error(`Error deleting ${this.entityName}:`, error);
    this.$toastr.error(`Failed to delete ${this.entityName}`);
  }
},

async saveEntity() {
  try {
    let response;
    if (this.editedIndex > -1) {
      response = await axios.put(`${this.apiEndpoint}/${this.editedItem.id}`, this.editedItem);
    } else {
      response = await axios.post(this.apiEndpoint, this.editedItem);
    }
    this.$toastr.success(response.data.message || `${this.entityName} ${this.editedIndex > -1 ? 'updated' : 'created'} successfully`);
    this.fetchEntities();
    this.closeDialog();
  } catch (error) {
    console.error(`Error saving ${this.entityName}:`, error);
    this.$toastr.error(`Failed to save ${this.entityName}`);
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
      this.$nextTick(() => {
        this.editedItem = this.getDefaultItem();
        this.editedIndex = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = this.getDefaultItem();
        this.editedIndex = -1;
      });
    },
    refreshEntities() {
      this.fetchEntities();
    },
    filterEntities() {
      // This method is called when the search input changes
      // The filtering is handled by the searchEntities computed property
    }
  }
};
</script>

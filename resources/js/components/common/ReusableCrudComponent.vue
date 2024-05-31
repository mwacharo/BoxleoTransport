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

            <!-- <v-btn color="primary" @click="FilterEntity">
              <v-icon>mdi-filter</v-icon>
            </v-btn> -->

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
          <!--   
            <v-snackbar v-model="snackbar.show" :timeout="snackbar.timeout" :color="snackbar.color">
              {{ snackbar.text }}
              <v-btn color="white" text @click="snackbar.show = false">Close</v-btn>
            </v-snackbar> -->
        </v-main>
      </v-app>
    </v-layout>
  </v-card>
</template>

<script>
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
      dialogDelete: false
      // snackbar: {
      //   show: false,
      //   text: '',
      //   color: '',
      //   timeout: 3000,
      // },
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
        const response = await axios.delete(`${this.apiEndpoint}/${this.editedItem.id}`);
        //   this.snackbar.text = response.data.message;
        //   this.snackbar.color = 'success';
        //   this.snackbar.show = true;
        this.fetchEntities();
        this.closeDelete();
      } catch (error) {
        //   this.snackbar.text = `Failed to delete the ${this.entityName}!`;
        //   this.snackbar.color = 'error';
        //   this.snackbar.show = true;
        console.error(`Error deleting ${this.entityName}:`, error);
      }
    },
    async saveEntity() {
      try {
        if (this.editedIndex > -1) {
          const response = await axios.put(`${this.apiEndpoint}/${this.editedItem.id}`, this.editedItem);
          // this.snackbar.text = `${this.entityName} updated successfully`;
          // this.snackbar.color = 'success';
        } else {
          const response = await axios.post(this.apiEndpoint, this.editedItem);
          // this.snackbar.text = response.data.message;
          // this.snackbar.color = 'success';
        }
        //   this.snackbar.show = true;
        this.fetchEntities();
        this.closeDialog();
      } catch (error) {
        //   this.snackbar.text = `Error saving ${this.entityName}`;
        //   this.snackbar.color = 'error';
        //   this.snackbar.show = true;
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
    }
  }
};
</script>

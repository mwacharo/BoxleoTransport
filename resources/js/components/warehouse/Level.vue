<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Level</v-card-title>
      <v-toolbar flat>
        <v-btn color="primary" @click="addLevel">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="levels" show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editLevel(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteLevel(item)">mdi-delete</v-icon>
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
                  <v-text-field v-model="editedItem.code" label="Code" prepend-icon="mdi-email"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveLevel">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>


      <v-dialog v-model="dialogDelete" max-width="290">
        <v-card>
          <v-card-title class="headline">Warning</v-card-title>
          <v-card-text>This will permanently delete the vendor. Continue?</v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="closeDeleteDialog">Cancel</v-btn>
            <v-btn color="primary" text @click="deleteLevelConfirm">OK</v-btn>
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

export default {
  data() {
    return {
      dialog: false,
      dialog1: false,
      levels: [],
      dialogDelete:false,
      editedIndex: -1,
      editedItem: {
        name: '',
        code: '',
        user_id: this.user_id,
      },
      defaultItem: {
        name: '',
        code: '',
        user_id: this.user_id,
      },
      headers: [
        { title: 'Code', value: 'code' },
        { title: 'Name', value: 'name' },
        { title: 'Bay', value: 'actions', sortable: false },
      ],
    };
  },
  computed: {

    formTitle() {
      return this.editedIndex === -1 ? 'New Level' : 'Edit Level';
    },
  },
  created() {
    this.fetchLevels();
  },

  methods: {
    saveLevel() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/levels/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Level updated successfully');
            this.fetchLevels();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating level');
            console.error('Error updating level:', error);
          });
      } else {
        axios
          .post('/api/v1/levels', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchLevels();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding lecwl');
            console.error('Error adding level:', error);
          });
      }
    },


    fetchLevels() {
      axios
        .get('/api/v1/levels')
        .then((response) => {
          this.levels = response.data;
        })
        .catch((error) => {
          console.error('Error fetching levels:', error);
        });
    },

    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    },

    editLevel(level) {
      this.editedIndex = this.levels.indexOf(level);
      this.editedItem = { ...level };
      this.dialog1 = true;
    },

    deleteLevel(level) {
      this.editedIndex = this.levels.indexOf(level);
      this.editedItem = { ...level };
      this.dialogDelete = true;
    },

    addLevel() {
      this.editedItem = { ...this.defaultItem };
      this.dialog1 = true;
    },

    closeDialog() {
      this.dialog1 = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
    async deleteLevelConfirm() {
    try {
      // Use the DELETE method to call the API endpoint with the level ID
      const response = await axios.delete(`/api/v1/levels/${this.editedItem.id}`);

      console.log('Items deleted:', response.data);
      this.$toastr.success(response.data.message);
      this.fetchLevels();
      this.dialogDelete = false;

      // Handle successful deletion, e.g., update UI, show success message
    } catch (error) {
      console.error('Error deleting items:', error);
      this.$toastr.error('Error deleting items');
      // Handle error, e.g., show error message
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

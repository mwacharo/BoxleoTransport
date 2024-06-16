<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Bay</v-card-title>
      <v-toolbar flat>
        <v-btn color="primary" @click="addBay">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="bays" show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editBay(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteBay(item)">mdi-delete</v-icon>
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
                <v-col cols="12" >
                  <v-text-field v-model="editedItem.name" label="Name" prepend-icon="mdi-account"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="editedItem.code" label="Code" prepend-icon="mdi-email"></v-text-field>
                </v-col>

                <v-col cols="12" >
                  <v-text-field v-model="editedItem.row_id" label="Row" prepend-icon="mdi-account"></v-text-field>
                </v-col>

              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveBay">Save</v-btn>
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
            <v-btn color="primary" text @click="deleteBayConfirm">OK</v-btn>
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
      bays: [],
      dialogDelete:false,
      editedIndex: -1,
      editedItem: {
        name: '',
        code: '',
        row_id:'',
        user_id: this.user_id,
      },
      defaultItem: {
        name: '',
        code: '',
        row_id:'',
        user_id: this.user_id,
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
      return this.editedIndex === -1 ? 'New Bay' : 'Edit Bay';
    },
  },
  created() {
    this.fetchBays();
  },

  methods: {
    saveBay() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/bays/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('bay updated successfully');
            this.fetchBays();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating bay');
            console.error('Error updating bay:', error);
          });
      } else {
        axios
          .post('/api/v1/bays', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchbays();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding bay');
            console.error('Error adding bay:', error);
          });
      }
    },


    fetchBays() {
      axios
        .get('/api/v1/bays')
        .then((response) => {
          this.bays = response.data;
        })
        .catch((error) => {
          console.error('Error fetching bays:', error);
        });
    },

    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    },

    editBay(bay) {
      this.editedIndex = this.bays.indexOf(level);
      this.editedItem = { ...bay };
      this.dialog1 = true;
    },

    deleteBay(bay) {
      this.editedIndex = this.bays.indexOf(level);
      this.editedItem = { ...bay };
      this.dialogDelete = true;
    },

    addBay() {
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
      const response = await axios.delete(`/api/v1/bay/${this.editedItem.id}`);

      console.log('Items deleted:', response.data);
      this.$toastr.success(response.data.message);
      this.fetchBays();
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

<template>
  <v-dialog v-model="dialog" max-width="800">
    <v-card>
      <v-card-title class="headline">Row</v-card-title>
      <v-toolbar flat>
        <v-btn color="primary" @click="addRow">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="rows" show-select>
        <template v-slot:item.actions="{ item }">
          <div class="d-flex align-center">
            <v-icon class="mx-1" color="blue" @click="editRow(item)">mdi-pencil</v-icon>
            <v-icon class="mx-1" color="red" @click="deleteRow(item)">mdi-delete</v-icon>
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
                <v-col cols="12">
                  <v-text-field v-model="editedItem.name" label="Name" prepend-icon="mdi-account"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="editedItem.code" label="Code" prepend-icon="mdi-email"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="editedItem.warehouse_id" label="Warehouse ID" prepend-icon="mdi-warehouse"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
            <v-btn color="blue darken-1" text @click.prevent="saveRow">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogDelete" max-width="290">
        <v-card>
          <v-card-title class="headline">Warning</v-card-title>
          <v-card-text>This will permanently delete the row. Continue?</v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="closeDeleteDialog">Cancel</v-btn>
            <v-btn color="primary" text @click="deleteRowConfirm">OK</v-btn>
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
      rows: [],
      dialogDelete: false,
      editedIndex: -1,
      editedItem: {
        name: '',
        code: '',
        warehouse_id: '',
        user_id: this.user_id,
      },
      defaultItem: {
        name: '',
        code: '',
        warehouse_id: '',
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
      return this.editedIndex === -1 ? 'New Row' : 'Edit Row';
    },
  },
  created() {
    this.fetchRows();
  },
  methods: {
    saveRow() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/rows/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Row updated successfully');
            this.fetchRows();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating row');
            console.error('Error updating row:', error);
          });
      } else {
        axios
          .post('/api/v1/rows', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchRows();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding row');
            console.error('Error adding row:', error);
          });
      }
    },
    fetchRows() {
      axios
        .get('/api/v1/rows')
        .then((response) => {
          this.rows = response.data;
        })
        .catch((error) => {
          console.error('Error fetching rows:', error);
        });
    },
    show() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    },
    editRow(row) {
      this.editedIndex = this.rows.indexOf(row);
      this.editedItem = { ...row };
      this.dialog1 = true;
    },
    deleteRow(row) {
      this.editedIndex = this.rows.indexOf(row);
      this.editedItem = { ...row };
      this.dialogDelete = true;
    },
    addRow() {
      this.editedItem = { ...this.defaultItem };
      this.dialog1 = true;
    },
    closeDialog() {
      this.dialog1 = false;
      this.editedItem = { ...this.defaultItem };
      this.editedIndex = -1;
    },
    async deleteRowConfirm() {
      try {
        const response = await axios.delete(`/api/v1/rows/${this.editedItem.id}`);
        console.log('Items deleted:', response.data);
        this.$toastr.success(response.data.message);
        this.fetchRows();
        this.dialogDelete = false;
      } catch (error) {
        console.error('Error deleting items:', error);
        this.$toastr.error('Error deleting items');
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

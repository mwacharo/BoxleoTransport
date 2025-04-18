<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>Vendors</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="addVendor">
              <v-icon>mdi-plus</v-icon>
            </v-btn>

            <!-- <v-btn color="primary" @click="refreshVendors">
              <v-icon>mdi-refresh</v-icon>
            </v-btn> -->
          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterVendors" dense></v-text-field>
          <v-responsive>
            <v-data-table :headers="headers" :items="searchVendors">
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editVendor(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="success" @click="showServicesModal(item)">mdi-cog</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteVendor(item)">mdi-delete</v-icon>
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
                      <v-text-field
                        v-model="editedItem.phone"
                        label="Phone (optional)"
                        prepend-icon="mdi-phone"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn color="red darken-1" text @click.prevent="closeDialog">Close</v-btn>
                <v-btn color="blue darken-1" text @click.prevent="saveVendor">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>
              <v-card-text>This will permanently delete the vendor. Continue?</v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="primary" text @click="deleteVendorConfirm">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-main>
      </v-app>
    </v-layout>
    <Service ref="ServiceComponent"/>
  </v-card>

</template>

<script>
import Service from '@/components/vendors/Service.vue';
export default {

  components: {
    Service
,
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
      vendors: [],
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
    searchVendors() {
      if (!this.search) return this.vendors;
      return this.vendors.filter(
        (vendor) =>
          vendor.name.toLowerCase().includes(this.search.toLowerCase()) ||
          vendor.email.toLowerCase().includes(this.search.toLowerCase()) ||
          vendor.phone.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? 'New Vendor' : 'Edit Vendor';
    },
  },

  created() {
    this.fetchVendors();
  },

  methods: {
    showServicesModal(item){
this.$refs.ServiceComponent.show(item);
    },
    addVendor() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },

    editVendor(vendor) {
      this.editedIndex = this.vendors.indexOf(vendor);
      this.editedItem = { ...vendor };
      this.dialog = true;
    },

    deleteVendor(vendor) {
      this.editedIndex = this.vendors.indexOf(vendor);
      this.editedItem = { ...vendor };
      this.dialogDelete = true;
    },

    deleteVendorConfirm() {
      axios
        .delete(`/api/v1/vendors/${this.editedItem.id}`)
        .then((response) => {
          this.$toastr.success(response.data.message);
          this.fetchVendors();
          this.closeDelete();
        })
        .catch((error) => {
          console.error('Error deleting vendor:', error);
          this.$toastr.error('Failed to delete the vendor!');
        });
    },

    saveVendor() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/vendors/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Vendor updated successfully');
            this.fetchVendors();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating vendor');
            console.error('Error updating vendor:', error);
          });
      } else {
        axios
          .post('/api/v1/vendors', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchVendors();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding vendor');
            console.error('Error adding vendor:', error);
          });
      }
    },

    fetchVendors() {
      axios
        .get('/api/v1/vendors')
        .then((response) => {
          this.vendors = response.data;
        })
        .catch((error) => {
          console.error('Error fetching vendors:', error);
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

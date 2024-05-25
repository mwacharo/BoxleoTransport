<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>Vendors</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="addDeal">
              <v-icon>mdi-plus</v-icon>
            </v-btn>

            <v-btn color="primary" @click="refreshDeals">
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterDeals" dense></v-text-field>
          <v-responsive>
            <v-data-table :headers="headers" :items="vendors">
              <!-- <template v-slot:item.status="{ item }">
                <v-chip :color="getStatusColor(item.status)" @click="openStatusModal(item)">
                  {{ item.status }}
                </v-chip>
              </template>

              <template v-slot:item.priority="{ item }">
                <v-chip :color="getPriorityColor(item.priority)">
                  {{ item.priority }}
                </v-chip>
              </template> -->

              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editDeal(item)">mdi-pencil</v-icon>
                  <!-- <v-icon class="mx-1" color="green" @click="editDeal(item)">mdi-history</v-icon> -->
                  <v-icon class="mx-1" color="red" @click="deleteDeal(item)">mdi-delete</v-icon>
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
                <v-btn color="blue darken-1" text @click.prevent="saveDeal">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>

              <v-card-text>This will permanently delete the file. Continue?</v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>

                <v-btn color="primary" text @click="deleteDealConfirm">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="statusModal" max-width="400">
            <v-card>
              <v-card-title>Select Status</v-card-title>
              <v-card-text>
                <v-combobox
                  v-model="selectedStatus"
                  :items="statusOptions"
                  outlined
                  :value="selectedDeal.status"
                ></v-combobox>
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn color="red" text @click="statusModal = false">Close</v-btn>
                <v-btn color="blue darken-1" text @click="submitStatus">Submit</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-main>
      </v-app>
    </v-layout>
  </v-card>
</template>

<script>
export default {
  components: {},

  props: {
    user_id: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      search: '',
      vendors:[],
      // contains industry ,service ,users and branch  values
      filter: [],
      headers: [
        { title: '#', value: 'index' },
        { title: 'Name', value: 'name' },
        { title: 'Adress', value: 'address' },
        { title: 'Email', value: 'email' },
        { title: 'Phone', value: 'phone' },
        { title: 'Actions', value: 'actions', sortable: false }
      ],

      editedIndex: -1,
      editedItem: {
        name: '',
        email: '',
        user_id: this.user_id,
        address: '',
        phone: ''
      },
      // nav

      vendors: [],

      defaultItem: {
        name: '',
        email: '',
        address: '',

        user_id: this.user_id,
        phone: ''
      },

      dialog: false,
      dialogDelete: false
    };
  },
  computed: {
    SearchDeals() {
      if (!this.search) return this.deals;
      return this.deals.filter(
        deal =>
          deal.name.toLowerCase().includes(this.search.toLowerCase()) ||
          deal.email.toLowerCase().includes(this.search.toLowerCase()) ||
          deal.phone.toLowerCase().includes(this.search.toLowerCase()) ||
          deal.user.name.toLowerCase().includes(this.search.toLowerCase())
      );
    },

    formTitle() {
      return this.editedIndex === -1 ? 'New Vendor' : 'Edit ';
    }
  },
  created() {
    this.fetchVendors();
  },
  methods: {
    addDeal() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },

    editDeal(deal) {
      this.editedIndex = this.deals.indexOf(deal);
      this.editedItem = { ...deal };
      this.prepareEditedItem(deal);
      this.dialog = true;
    },
    deleteDeal(deal) {
      this.editedIndex = this.deals.indexOf(deal);
      this.editedItem = { ...deal };
      this.dialogDelete = true;
    },

    deleteDealConfirm() {
      axios
        .delete(`/api/v1/vendors/${this.editedItem.id}`)
        .then(response => {
          this.$toastr.success(response.data.message);
          this.fetchVendors();
          this.closeDelete();
        })
        .catch(error => {
          console.error('Error deleting deal:', error);
          this.$toastr.error('Failed to delete the deal!');
        });
    },
    saveDeal() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        const url = `/api/vendors/${this.editedItem.id}`;
        axios
          .put(url, {
            ...this.editedItem,
            service_ids: this.editedItem.service_ids,
            branch_ids: this.editedItem.branch_ids,
            industries_ids: this.editedItem.industry_ids
          }) // <-- Added closing parenthesis here
          .then(response => {
            this.fetchVendors();
            this.$toastr.success('vendor updated successfully');
            this.closeDialog();
            status;
          })
          .catch(error => {
            this.$toastr.error('Error updating vendor');
            console.error('Error updating vendor:', error);
          });
      } else {
        axios
          .post('/api/v1/vendors', {
            name: this.editedItem.name,
            email: this.editedItem.email,
            phone: this.editedItem.phone,
            address: this.editedItem.address,
            user_id: this.editedItem.user_id
          })
          .then(response => {
            this.$toastr.success(response.data.message);
            this.fetchVendors();
            this.closeDialog();
          })
          .catch(error => {
            this.$toastr.error('Error adding vendor');
            console.error('Error adding vendor:', error);
          });
      }
    },

    fetchVendors() {
      const url = `/api/v1/vendors`;
      axios
        .get(url, {})
        .then(response => {
          this.vendors = response.data;
        })
        .catch(error => {
          console.error('Error fetching vendor:', error);
        });
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
};
</script>

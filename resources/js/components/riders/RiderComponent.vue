<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <v-toolbar flat>
            <v-toolbar-title>Riders</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="addRider">
              <v-icon>mdi-plus</v-icon>
            </v-btn>

            <!-- <v-btn color="primary" @click="refreshRiders">
              <v-icon>mdi-refresh</v-icon>
            </v-btn> -->
          </v-toolbar>

          <v-text-field v-model="search" label="Search" clearable @input="filterRiders" dense></v-text-field>


          <v-responsive>
          <div v-if="selectedItems.length > 0" class="x-actions">
            <v-icon class="mx-1" color="primary" @click="show" title="Print">mdi-map-marker</v-icon>
          </div>

            <v-data-table :headers="headers" :items="searchRiders"
            show-select
              v-model="selectedItems"
            >
              <template v-slot:item.actions="{ item }">
                <div class="d-flex align-center">
                  <v-icon class="mx-1" color="blue" @click="editRider(item)">mdi-pencil</v-icon>
                  <v-icon class="mx-1" color="red" @click="deleteRider(item)">mdi-delete</v-icon>
                </div>
              </template>
            </v-data-table>
          </v-responsive>



          <v-dialog v-model="geofenceDialog" max-width="500">
            <v-card>
              <v-card-title class="headline">Assign Zone</v-card-title>
              <v-card-text>
                <v-select
                  :items="geofences"
                  label="Select Zone"
                  clearable
                  v-model="selectedZone"
                  item-title="name"
                  item-value="id"
                ></v-select>

              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="close">Cancel</v-btn>
                <v-btn color="primary" text @click="assignZone">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

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
                <v-btn color="blue darken-1" text @click.prevent="saveRider">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="290">
            <v-card>
              <v-card-title class="headline">Warning</v-card-title>
              <v-card-text>This will permanently delete the rider. Continue?</v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="primary" text @click="deleteRiderConfirm">OK</v-btn>
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
  props: {
    user_id: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      search: '',
      geofenceDialog: false,
      riders: [],
      geofences:[],
      selectedItems:[],
      selectedZone:'',
      currentRiderId: null,
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
    searchRiders() {
      if (!this.search) return this.riders;
      return this.riders.filter(
        (rider) =>
          rider.name.toLowerCase().includes(this.search.toLowerCase()) ||
          rider.email.toLowerCase().includes(this.search.toLowerCase()) ||
          rider.phone.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    formTitle() {
      return this.editedIndex === -1 ? 'New Rider' : 'Edit Rider';
    },
  },

  created() {
    this.fetchRiders();
      this.fetchZones();
  },

  methods: {
    addRider() {
      this.editedItem = { ...this.defaultItem };
      this.dialog = true;
    },

    editRider(rider) {
      this.editedIndex = this.riders.indexOf(rider);
      this.editedItem = { ...rider };
      this.dialog = true;
    },

    deleteRider(rider) {
      this.editedIndex = this.riders.indexOf(rider);
      this.editedItem = { ...rider };
      this.dialogDelete = true;
    },

    deleteRiderConfirm() {
      axios
        .delete(`/api/v1/riders/${this.editedItem.id}`)
        .then((response) => {
          this.$toastr.success(response.data.message);
          this.fetchRiders();
          this.closeDelete();
        })
        .catch((error) => {
          console.error('Error deleting rider:', error);
          this.$toastr.error('Failed to delete the rider!');
        });
    },

    saveRider() {
      this.editedItem.user_id = this.user_id;

      if (this.editedIndex > -1) {
        axios
          .put(`/api/v1/riders/${this.editedItem.id}`, this.editedItem)
          .then((response) => {
            this.$toastr.success('Rider updated successfully');
            this.fetchRiders();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error updating rider');
            console.error('Error updating rider:', error);
          });
      } else {
        axios
          .post('/api/v1/riders', this.editedItem)
          .then((response) => {
            this.$toastr.success(response.data.message);
            this.fetchRiders();
            this.closeDialog();
          })
          .catch((error) => {
            this.$toastr.error('Error adding rider');
            console.error('Error adding rider:', error);
          });
      }
    },

    fetchRiders() {
      axios
        .get('/api/v1/riders')
        .then((response) => {
          this.riders = response.data;
        })
        .catch((error) => {
          console.error('Error fetching riders:', error);
        });
    },
    fetchZones() {
      axios
        .get('/api/v1/geofences')
        .then((response) => {
          this.geofences = response.data;
        })
        .catch((error) => {
          console.error('Error fetching riders:', error);
        });
    },
    show(selectedItems) {
    if (this.selectedItems.length === 1) {
     this.currentRiderId = this.selectedItems[0];
     this.geofenceDialog = true;
   } else {
     this.$toastr.error('Please select one rider to assign a zone.');
   }
    },
    close() {
      this.geofenceDialog = false;
    },
    assignZone(){
      axios.put(`/api/riders/${this.currentRiderId}/geofence`, { geofence_id: this.selectedZone })
      .then((response) => {
        this.$toastr.success(response.data.message);
        this.fetchRiders();
        this.closeDialog();
      })
      .catch((error) => {
        this.$toastr.error('Error adding rider');
        console.error('Error adding rider:', error);
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




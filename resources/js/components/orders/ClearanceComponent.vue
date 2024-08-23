<template>
  <v-card>
    <v-layout>
      <v-app>
        <v-main>
          <div>
            <v-data-table
              :headers="headers"
              :items="riders"
              class="elevation-1"
              :search="search"
            >
              <template v-slot:top>
                <v-toolbar flat>
                  <v-toolbar-title>Rider Clearance</v-toolbar-title>
                  <v-divider class="mx-4" inset vertical></v-divider>
                  <v-spacer></v-spacer>
                  <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                  ></v-text-field>
                  <v-btn color="primary" @click="toggleFilterDrawer">
              <v-icon>mdi-filter</v-icon>
                  </v-btn>
                </v-toolbar>
              </template>
              <template v-slot:item.status="{ item }">
                <v-chip :color="item.status === 'Cleared' ? 'success' : 'warning'" dark   @click="openClearance(item)"> 
                  {{ item.status }}
                </v-chip>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn icon @click="checkAndClearRider(item)">
                  <v-icon color="primary">mdi-check-circle</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </div>
        </v-main>
      </v-app>
    </v-layout>
  <ClearanceStatus ref="ClearanceStatusComponent"/>
  </v-card>
</template>

<script>
import ClearanceStatus from './ClearanceStatus.vue';

export default {
  components: {
    ClearanceStatus,
  },
  data() {
    return {
      search: '',
   
      headers: [
        { title: 'Rider Name', value: 'name' },
        { title: 'Assigned Orders', value: 'orders_count' },
        { title: 'POD Submitted', value: 'pod_count' },
        { title: 'POD not Submitted ', value: 'no_pod_count' },

        // { title: 'POD Status', value: 'pod_status' },

        { title: 'Status', value: 'status' },
        // { title: 'Actions', value: 'actions', sortable: false },
      ],
      riders:[],
    
    };
  },
  created() {
    this.fetchRiders();
  

  },
  methods: {
    openClearance(){

this.$refs.ClearanceStatusComponent.show()
},

fetchRiders() {
      const url = `/api/v1/riders`;
      axios
        .get(url, {})
        .then(response => {
          this.riders = response.data;
        })
        .catch(error => {
          console.error('Error fetching riders:', error);
        });
    },
    checkAndClearRider(rider) {
      // Simulating an API call
      setTimeout(() => {
        const index = this.riders.findIndex(r => r.id === rider.id);
        if (index !== -1) {
          this.riders[index].status = 'Cleared';
          this.$emit('notify', `Rider ${rider.name} has been cleared`);
        }
      }, 500);
    },

  
  },
};
</script>
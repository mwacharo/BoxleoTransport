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
                <v-chip :color="item.status === 'Cleared' ? 'success' : 'warning'" dark>
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
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      headers: [
        { title: 'Rider Name', value: 'name' },
        { title: 'Assigned Orders', value: 'orders_count' },
        { title: 'POD Status', value: 'pod_status' },
        { title: 'Status', value: 'status' },
        // { title: 'Actions', value: 'actions', sortable: false },
      ],
      // riders:[],
      riders: [
        { id: 1, name: 'John Doe', orders_count: 5, pod_status: 5, status: 'Pending' },
        { id: 2, name: 'Jane Smith', orders_count: 3, pod_status: 4, status: 'Pending' },
        { id: 3, name: 'Bob Johnson', orders_count: 7, pod_status: 7, status: 'Cleared' },
        { id: 4, name: 'Alice Brown', orders_count: 2, pod_status: 2, status: 'Pending' },
        { id: 5, name: 'Charlie Davis', orders_count: 4, pod_status: 2, status: 'Pending' },
      ],
    };
  },
  methods: {
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
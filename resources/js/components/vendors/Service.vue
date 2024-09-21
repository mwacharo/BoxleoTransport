<template>
  <v-dialog v-model="dialog" max-width="800px">
    <v-card>
      <v-card-title>
        <h3>Available Services</h3>
      </v-card-title>
      <v-card-text>
        <v-row v-if="loading">
          <v-col cols="12" class="text-center">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
          </v-col>
        </v-row>
        <v-row v-else>
          <v-col cols="12" sm="6" v-for="service in services" :key="service.id">
            <v-checkbox v-model="service.selected" :label="service.service_name" />

            <v-card v-if="service.selected" outlined class="mt-2">


              <v-card-text>
                <!-- Loop through filtered conditions only -->
                <div v-for="condition in filteredConditions(service)" :key="condition.id">
                  <v-text-field v-if="condition.condition_amount !== null" v-model="condition.condition_amount"
                    :label="`Amount for ${service.service_name}`" type="number" />
                  <v-text-field v-if="condition.condition_percentage !== null" v-model="condition.condition_percentage"
                    :label="`Percentage for ${service.service_name}`" type="number" />
                </div>

                <v-btn @click="addCondition(service)" color="primary" text>
                  Add Condition
                </v-btn>

                <div v-for="(newCondition, index) in service.newConditions" :key="'new-' + index">
                  <v-select v-model="newCondition.type" :items="[
                    { title: 'Amount', value: 'amount' },
                    { title: 'Percentage', value: 'percentage' },
                    { title: '3 Tonne', value: 'rate_3t' },
                    { title: '5 Tonne', value: 'rate_5t' },
                    { title: '7 Tonne', value: 'rate_7t' },
                    { title: '10 Tonne', value: 'rate_10t' },
                  ]" label="Condition Type" dense></v-select>
                  
                  <!-- Dynamically display new condition inputs based on selection -->
                  <v-text-field v-if="newCondition.type === 'amount'" v-model="newCondition.condition_amount"
                    :label="`New Amount for ${service.service_name}`" type="number" />
                  <v-text-field v-if="newCondition.type === 'percentage'" v-model="newCondition.condition_percentage"
                    :label="`New Percentage for ${service.service_name}`" type="number" />

                  <v-text-field v-if="newCondition.type === 'rate_3t'" v-model="newCondition.rate_3t"
                    label="Rate for 3 Tonne" type="number" />
                  <v-text-field v-if="newCondition.type === 'rate_5t'" v-model="newCondition.rate_5t"
                    label="Rate for 5 Tonne" type="number" />
                  <v-text-field v-if="newCondition.type === 'rate_7t'" v-model="newCondition.rate_7t"
                    label="Rate for 7 Tonne" type="number" />
                  <v-text-field v-if="newCondition.type === 'rate_10t'" v-model="newCondition.rate_10t"
                    label="Rate for 10 Tonne" type="number" />
                </div>
              </v-card-text>
              <!-- <v-card-text>
                <div v-for="condition in service.conditions" :key="condition.id">
                  <v-text-field v-if="condition.condition_amount !== null" v-model="condition.condition_amount"
                    :label="`Amount for ${service.service_name}`" type="number" />
                  <v-text-field v-if="condition.condition_percentage !== null" v-model="condition.condition_percentage"
                    :label="`Percentage for ${service.service_name}`" type="number" />
                </div>

                <v-btn @click="addCondition(service)" color="primary" text>
                  Add Condition
                </v-btn>

                <div v-for="(newCondition, index) in service.newConditions" :key="'new-' + index">
                  <v-select v-model="newCondition.type" :items="[
                    { title: 'Amount', value: 'amount' },
                    { title: 'Percentage', value: 'percentage' },
                    { title: '3 Tonne', value: 'rate_3t' },
                    { title: '5 Tonne', value: 'rate_5t' },
                    { title: '7 Tonne', value: 'rate_7t' },
                    { title: '10 Tonne', value: 'rate_10t' },
                  ]" label="Condition Type" dense></v-select>
                  <v-text-field v-if="newCondition.type === 'amount'" v-model="newCondition.condition_amount"
                    :label="`New Amount for ${service.service_name}`" type="number" />
                  <v-text-field v-if="newCondition.type === 'percentage'" v-model="newCondition.condition_percentage"
                    :label="`New Percentage for ${service.service_name}`" type="number" />


                  <v-text-field v-if="newCondition.type === 'rate_3t'" v-model="newCondition.rate_3t"
                    label="Rate for 3 Tonne" type="number" />

                  <v-text-field v-if="newCondition.type === 'rate_5t'" v-model="newCondition.rate_5t"
                    label="Rate for 5 Tonne" type="number" />

                  <v-text-field v-if="newCondition.type === 'rate_7t'" v-model="newCondition.rate_7t"
                    label="Rate for 7 Tonne" type="number" />

                  <v-text-field v-if="newCondition.type === 'rate_10t'" v-model="newCondition.rate_10t"
                    label="Rate for 10 Tonne" type="number" />
                </div>
              </v-card-text> -->
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click="closeDialog" text>Cancel</v-btn>
        <v-btn @click="saveServices" color="primary" :loading="saving">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {

  computed: {
  filteredConditions() {
    return (item) => {
      // Filter the conditions where item.vendor_id matches condition.vendor_id
      return item.conditions.filter(condition => condition.vendor_id === item.vendor_id);
    };
  }
},
  data() {
    return {
      dialog: false,
      services: [],
      loading: false,
      saving: false,
      error: null,
      vendor_id:null,
    };
  },

  created() {
    this.fetchServices();
  },


  methods: {
    async fetchServices() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/v1/services-with-conditions');
        this.services = response.data.map(service => ({
          ...service,
          selected: false,
          newConditions: []
        }));
      } catch (error) {
        console.error('Error fetching services:', error);
        this.error = 'Failed to load services. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    show(item) {
      this.dialog = true;
      this.vendor_id= item.vendor_id;
    },

    closeDialog() {
      this.dialog = false;
    },

    addCondition(service) {
      service.newConditions.push({
        type: null,
        condition_amount: null,
        condition_percentage: null
      });
    },

    async saveServices() {
      this.saving = true;
      const selectedServices = this.services.filter(service => service.selected);

      selectedServices.forEach(service => {
        service.conditions = [
          ...service.conditions,
          ...service.newConditions.map(newCondition => ({
            service_id: service.id,
            condition_amount: newCondition.type === 'amount' ? newCondition.condition_amount : null,
            condition_percentage: newCondition.type === 'percentage' ? newCondition.condition_percentage : null
          }))
        ];
        delete service.newConditions;
      });

      try {
        await axios.post('/api/v1/save-merchant-services', { services: selectedServices });
        this.closeDialog();
        this.$emit('services-updated');
      } catch (error) {
        console.error('Error saving services:', error);
        this.error = 'Failed to save services. Please try again.';
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>
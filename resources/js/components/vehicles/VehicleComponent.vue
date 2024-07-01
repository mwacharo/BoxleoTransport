<template>
  <ReusableCrudComponent
    entity-name="Vehicle"
    api-endpoint="/api/v1/vehicles"
    :entity-fields="entityFields"
  />
</template>

<script>
import ReusableCrudComponent from '@/components/common/ReusableCrudComponent.vue';
import axios from 'axios';

export default {
  components: {
    ReusableCrudComponent
  },
  data() {
    return {
      drivers: [],
    };
  },
  computed: {
    entityFields() {
      return [
        { name: 'license_plate', label: 'License Plate', icon: 'mdi-license' },
        { name: 'capacity', label: 'Capacity', icon: 'mdi-cube' },
        { name: 'status', label: 'Status', icon: 'mdi-checkbox-marked-circle' },
        {
          name: 'name',
          label: 'Driver',
          icon: 'mdi-account',
          type: 'select',
          options: this.drivers,
          itemText: 'name',
          itemValue: 'id'
        }
      ];
    }
  },
  created() {
    this.fetchDrivers();
  },
  methods: {
    async fetchDrivers() {
      try {
        const response = await axios.get('/api/v1/drivers');
        this.drivers = response.data;
      } catch (error) {
        console.error('Error fetching drivers:', error);
      }
    }
  }
};
</script>

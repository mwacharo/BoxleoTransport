<template>
    <ReusableCrudComponent
      entity-name="Zone"
      api-endpoint="/api/v1/geofences"
      :entity-fields="[
        { name: 'name', label: 'Name', icon: 'mdi-account' },
      
      ]"
    />
  </template>

  <script>
  import ReusableCrudComponent from '@/components/common/ReusableCrudComponent.vue';

  export default {
    components: {
      ReusableCrudComponent
    }
  };
  </script>

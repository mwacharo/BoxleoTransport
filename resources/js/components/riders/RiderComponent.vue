<template>
    <ReusableCrudComponent
      entity-name="Rider"
      api-endpoint="/api/v1/riders"
      :entity-fields="[
        { name: 'name', label: 'Name', icon: 'mdi-account' },
        { name: 'email', label: 'Email', icon: 'mdi-email' },
        { name: 'address', label: 'Address', icon: 'mdi-web' },
        { name: 'phone', label: 'Phone', icon: 'mdi-phone' }
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
  
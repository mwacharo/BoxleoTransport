<template>
    <ReusableCrudComponent
      entity-name="Driver"
      api-endpoint="/api/v1/drivers"
      :entity-fields="[
        { name: 'name', label: 'Name', icon: 'mdi-account' },
        { name: 'email', label: 'Email', icon: 'mdi-email' },
        { name: 'address', label: 'Address', icon: 'mdi-web' },
        { name: 'phone', label: 'Phone', icon: 'mdi-account' },
        { name: 'status', label: 'status', icon: 'mdi-phone' },
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
  
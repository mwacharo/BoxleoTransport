<template>
  <ReusableCrudComponent
    entity-name="Types of Orders"
    api-endpoint="/api/v1/ordercategories"
    :entity-fields="[
      { name: 'name', label: 'Name', icon: 'mdi-account' },
      { name: 'description', label: 'Description', icon: 'mdi-email' },
      { name: 'created_at', label: 'Created on', icon: 'mdi-web' },
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

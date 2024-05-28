<template>
  <ReusableCrudComponent
    entity-name="Orders"
    api-endpoint="/api/v1/orders"
    :entity-fields="[
      { name: 'created_at', label: 'Created', icon: 'mdi-calendar' },
      { name: 'order_no', label: 'Order', icon: 'mdi-file-document' },
      { name: 'client_name', label: 'Client', icon: 'mdi-account' },
      { name: 'address', label: 'Address', icon: 'mdi-home' },
      { name: 'city', label: 'City', icon: 'mdi-city' },
      { name: 'phone', label: 'contact', icon: 'mdi-phone' },
      { name: 'alt_phone', label: 'Other', icon: 'mdi-phone-in-talk' },
      { name: 'product_name', label: 'Product', icon: 'mdi-package-variant' },
      { name: 'quantity', label: 'Quantity', icon: 'mdi-format-list-numbered' },
      { name: 'status', label: 'Status', icon: 'mdi-information' },


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

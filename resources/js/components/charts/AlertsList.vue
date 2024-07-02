<template>
  <ul class="alerts-list">
    <li v-for="alert in alerts" :key="alert.id" :class="alert.type">
      <span class="alert-icon">{{ getAlertIcon(alert.type) }}</span>
      <div class="alert-content">
        <h4>{{ alert.title }}</h4>
        <p>{{ alert.message }}</p>
      </div>
    </li>
  </ul>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  alerts: {
    type: Array,
    required: true
  }
});

const getAlertIcon = (type) => {
  switch (type) {
    case 'error': return '⚠️';
    case 'warning': return '⚠️';
    case 'info': return 'ℹ️';
    default: return '📌';
  }
};
</script>

<style scoped>
.alerts-list {
  list-style-type: none;
  padding: 0;
}

.alerts-list li {
  display: flex;
  align-items: start;
  margin-bottom: 10px;
  padding: 10px;
  border-radius: 4px;
}

.alert-icon {
  font-size: 1.5em;
  margin-right: 10px;
}

.alert-content h4 {
  margin: 0 0 5px 0;
}

.alert-content p {
  margin: 0;
}

.error {
  background-color: #ffebee;
}

.warning {
  background-color: #fff8e1;
}

.info {
  background-color: #e3f2fd;
}
</style>

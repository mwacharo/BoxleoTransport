// resources/js/mixins/fetchDataMixin.js
import { fetchData } from '@/utils/api';

export const fetchDataMixin = {
  methods: {
    async fetchDataFromApi(endpoint) {
      try {
        return await fetchData(endpoint);
      } catch (error) {
        console.error(`Error fetching data from ${endpoint}:`, error);
        return [];
      }
    },
  }
};

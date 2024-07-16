<template>
  <div ref="line"></div>
</template>

<script>
import ApexCharts from 'apexcharts';
import axios from 'axios';

export default {
  data() {
    return {
      chartOptions: {
        chart: {
          type: 'line',
          height: 350,
        },
        stroke: {
          curve: 'smooth',
        },
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: [],
          title: {
            text: 'Delivery Time (in minutes)',
          },
        },
        yaxis: {
          title: {
            text: 'Number of Orders',
          },
        },
        title: {
          // text: 'Delivery Time Distribution',
          // align: 'center',
        },
      },
      series: [],
    };
  },
  mounted() {
    // Fetch the data from your API or pass it directly
    this.fetchDeliveryTimeDistribution();
  },
  methods: {
    fetchDeliveryTimeDistribution() {
      // Use axios to fetch data from the Laravel backend
      axios.get('/api/v1/fetchDashboardData')
        .then(response => {
          const deliveryTimeDistribution = response.data.delivery_time_distribution;

          const categories = Object.keys(deliveryTimeDistribution);
          const data = Object.values(deliveryTimeDistribution);

          this.chartOptions.xaxis.categories = categories;
          this.series = [
            {
              name: 'Orders',
              data: data,
            },
          ];

          // Initialize the chart
          const chart = new ApexCharts(this.$refs.line, {
            ...this.chartOptions,
            series: this.series,
          });

          // Render the chart
          chart.render();
        })
        .catch(error => {
          console.error('Error fetching delivery time distribution:', error);
        });
    },
  },
};
</script>

<style>
/* Add any necessary styling here */
</style>

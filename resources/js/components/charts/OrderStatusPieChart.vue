<template>
  <div>
    <div ref="doughnutChart"></div>
  </div>
</template>

<script>
import ApexCharts from 'apexcharts';
import axios from 'axios';

export default {
  data() {
    return {
      orderStatus: [],
      chart: null
    };
  },
  created() {
    this.fetchOrderStatusData();
  },
  methods: {
    fetchOrderStatusData() {
      axios.get('/api/v1/orderStatusCounts')
        .then(response => {
          this.orderStatus = response.data;
          console.log("Order Status Data: ", this.orderStatus); // Debugging line
          this.initDoughnutChart();
        })
        .catch(error => {
          console.error('Error fetching order status data:', error);
        });
    },
    initDoughnutChart() {
      if (!this.orderStatus.length) return;

      const chartOptions = {
        chart: {
          type: 'donut'
        },
        series: this.orderStatus.map(status => status.value),
        labels: this.orderStatus.map(status => status.status),
        dataLabels: {
          enabled: false
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      };

      this.chart = new ApexCharts(this.$refs.doughnutChart, chartOptions);
      this.chart.render();
    }
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.destroy();
    }
  }
};
</script>

<style scoped>
/* Add any required styles here */
</style>

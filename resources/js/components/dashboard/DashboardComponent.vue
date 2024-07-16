<template>
  <div class="row">
    <!-- Greeting and Quick Overview -->
    <div class="col-lg-8 mb-4 order-0">
      <div class="card">
        <div class="d-flex align-items-end row">


          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Hello, {{ user.firstName }}!</h5>
              <p class="mb-4">You have <span class="fw-medium">{{ pending_deliveries_count }}</span> In Transit today.
                Check them now.</p>
              <router-link to="/deliveries?status=pending" class="btn btn-sm btn-outline-primary">View
                Deliveries</router-link>
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src='/icons/truck.png' height="140" alt="Delivery Truck" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPIs and Statistics -->
    <div class="col-lg-4 col-md-4 order-1">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Total Orders Today</span>
              <h3 class="card-title mb-2">{{ totalOrdersToday }}</h3>
            </div>
          </div>
        </div>

        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <span>Completed Deliveries</span>
              <h3 class="card-title text-nowrap mb-1">{{ completed }}</h3>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <span class="d-block mb-1">Pending Deliveries</span>
              <h3 class="card-title text-nowrap mb-2">{{ pendingDeliveries }}</h3>
            </div>
          </div>

        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">Revenue Today</span>
              <h3 class="card-title mb-2">${{ revenueToday }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Central Section: Total Deliveries and Live Map -->
    <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
      <div class="card">
        <div class="row row-bordered g-0">
          <div class="col-md-8">
            <h5 class="card-header m-0 me-2 pb-3">Total Orders Monthly (Current Year)</h5>
            <div class="px-2">
              <DeliveriesMonthlyBarGraph />
            </div>
          </div>

          <div class="col-md-4">
            <div class="card-body">
              <div class="text-center">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="growthReportId"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    2024
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                    <a class="dropdown-item" href="javascript:void(0);">2023</a>
                    <a class="dropdown-item" href="javascript:void(0);">2022</a>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="text-center fw-medium pt-3 mb-2"> Delivery Performance
            </div>
            <div class="card-body">
              <span class="fw-semibold d-block mb-1">On Time Delivery Rate</span>
              <h3 class="card-title mb-2">{{ onTimeDeliveryRate }}%</h3>
              <span class="fw-semibold d-block mb-1">Average Delivery Time</span>
              <h3 class="card-title mb-2">{{ averageDeliveryTime }} mins</h3>
              <span class="fw-semibold d-block mb-1">Late Deliveries</span>
              <h3 class="card-title mb-2">{{ lateDeliveries }}</h3>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Live Map Integration -->
    <div class="col-12 col-lg-4 order-3 order-md-2">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Live Delivery Map</h5>
        </div>
        <div class="card-body">
          <LiveDeliveryMap />
        </div>
      </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="col-md-6 col-lg-4 order-1 mb-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Order Status Distribution</h5>
        </div>
        <div class="card-body">
          <OrderStatusPieChart />

        </div>
      </div>
    </div>

    <!-- Delivery Trends -->
    <div class="col-md-6 col-lg-4 order-2 mb-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title"></h5>
        </div>
        <div class="card-body">
          <DeliveryTrendsLineChart />
        </div>
      </div>
    </div>

    <!-- Notifications and Alerts -->
    <div class="col-md-6 col-lg-4 order-3 mb-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title">Alerts & Notifications</h5>
        </div>
        <div class="card-body">
          <AlertsList :alerts="alerts" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DeliveriesMonthlyBarGraph from '@/components/charts/DeliveriesMonthlyBarGraph.vue';
import LiveDeliveryMap from '@/components/charts/LiveDeliveryMap.vue';
import OrderStatusPieChart from '@/components/charts/OrderStatusPieChart.vue';
import DeliveryTrendsLineChart from '@/components/charts/DeliveryTrendsLineChart.vue';
import AlertsList from '@/components/charts/AlertsList.vue';

export default {
  name: 'Dashboard',
  components: {
    DeliveriesMonthlyBarGraph,
    LiveDeliveryMap,
    OrderStatusPieChart,
    DeliveryTrendsLineChart,
    AlertsList
  },
  data() {
    return {
      user: {
        firstName: 'John',
        lastName: 'Doe'
      },
      pending_deliveries_count: 0,
      totalOrdersToday: 0,
      completedDeliveries: 0,
      pendingDeliveries: 0,
      revenueToday: 0,
      companyGrowth: 0,
      alerts: []
    }
  },
  created() {
    this.fetchDashboardData();
  },
  methods: {
    fetchDashboardData() {
      // Replace with actual API call
      axios.get('/api/v1/fetchDashboardData')
        .then(response => {
          const data = response.data;
          this.pending_deliveries_count = data.pending_deliveries_count;
          this.totalOrdersToday = data.total_orders_today;
          this.completed = data.completed;
          this.pendingDeliveries = data.pending_deliveries;
          this.revenueToday = data.revenue_today;
          this.companyGrowth = data.company_growth;
          this.alerts = data.alerts;
          this.onTimeDeliveryRate = data.on_time_delivery_rate;
          this.averageDeliveryTime = data.average_delivery_time;
          this.lateDeliveries = data.late_deliveries;
          this.deliveryTimeDistribution = data.delivery_time_distribution;



        })
        .catch(error => {
          console.error('Error fetching dashboard data:', error);
        });
    }
  }
}
</script>

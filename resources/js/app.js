import './bootstrap'
import { createApp } from 'vue'
import ToastrPlugin from './toastr-plugin'
import VCalendar from 'v-calendar';
import 'v-calendar/style.css';

// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
})

const app = createApp({})

app.use(VCalendar, {})
app.use(vuetify)
app.use(ToastrPlugin)

import DashboardComponent from './components/dashboard/DashboardComponent.vue';
import BranchComponent from './components/branches/BranchComponent.vue';
import CalendarComponent from './components/calendars/CalendarComponent.vue';
import DealComponent from './components/deals/DealComponent.vue';
import UserComponent from './components/users/UserComponent.vue';
import TaskComponent from './components/tasks/TaskComponent.vue';
import IndustryComponent from './components/industries/IndustryComponent.vue';
import ServiceComponent from './components/services/ServiceComponent.vue';
import ReportComponent from './components/reports/ReportComponent.vue';
import MarketComponent from './components/market/MarketComponent.vue';
import VendorComponent from './components/vendors/VendorComponent.vue';
import OrderComponent from './components/orders/OrderComponent.vue';
import DriverComponent from './components/drivers/DriverComponent.vue';
import RiderComponent from './components/riders/RiderComponent.vue';
import ClientComponent from './components/clients/ClientComponent.vue';
import CategoryComponent from './components/orders/CategoryComponent.vue';
import StatusComponent from './components/orders/StatusComponent.vue';
import ProductComponent from './components/products/ProductComponent.vue';
import WarehouseComponent from './components/warehouse/WarehouseComponent.vue';
import VehicleComponent from './components/vehicles/VehicleComponent.vue';
import ZoneComponent from './components/zone/ZoneComponent.vue';
import FleetComponent from './components/fleet/FleetComponent.vue';
import DispatchComponent from './components/orders/DispatchComponent.vue';
import PickingComponent from './components/orders/PickingComponent.vue';
import ClearanceComponent from './components/orders/ClearanceComponent.vue';
import ReturnComponent from './components/orders/ReturnComponent.vue';
import RoleComponent from './components/users/RoleComponent.vue';
import PermissionComponent from './components/users/PermissionComponent.vue';




// app
app.component('dashboard-component', DashboardComponent);
app.component('branch-component', BranchComponent);
app.component('calendar-component', CalendarComponent);
app.component('deal-component', DealComponent);
app.component('user-component', UserComponent);
app.component('task-component', TaskComponent);
app.component('industry-component', IndustryComponent);
app.component('service-component', ServiceComponent);
app.component('report-component', ReportComponent);
app.component('market-component', MarketComponent);
app.component('vendor-component', VendorComponent);
app.component('order-component', OrderComponent);
app.component('driver-component', DriverComponent);
app.component('rider-component', RiderComponent);
app.component('client-component', ClientComponent);
app.component('category-component', CategoryComponent);
app.component('status-component', StatusComponent);
app.component('product-component',ProductComponent);
app.component('warehouse-component',WarehouseComponent);
app.component('vehicle-component',VehicleComponent);
app.component('zone-component',ZoneComponent);
app.component('fleet-component',FleetComponent);
app.component('dispatch-component',DispatchComponent);
app.component('picking-component',PickingComponent);
app.component('clearance-component', ClearanceComponent);
app.component('return-component', ReturnComponent);
app.component('role-component', RoleComponent);
app.component('permission-component', PermissionComponent);














app.mount('#app');

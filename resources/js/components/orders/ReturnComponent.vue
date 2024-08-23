<template>

    <v-card>
        <v-card-title class="text-h5">Return</v-card-title>
        <v-card-subtitle>Input your return details</v-card-subtitle>


        <!-- update status -->
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>
                    <span class="headline">Update Status</span>
                </v-card-title>

                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-select v-model="selectedStatus" :items="statusOptions" label="Status" required></v-select>

                    </v-form>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="closeDialog">Close</v-btn>
                    <v-btn color="primary" text @click="updateOrder">Update</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- end of update status -->

        <v-card-text>
    
            <v-textarea v-if="waybillType === 'input'" v-model="waybills"
                label="Input waybills separated by commas or a new line"></v-textarea>

            <!-- Search Button -->
            <v-btn color="primary" @click="onSearch">Search</v-btn>


            <div v-if="selectedItems.length > 0" class="x-actions">
                <v-icon class="mx-1" color="primary" @click="bulkUpdateStatus" title="Update Status">mdi-update</v-icon>
            </div>


            <!-- Dispatch Table -->
            <v-data-table :headers="headers" :items="dispatchData" class="mt-4" item-value="id" :items-per-page="10"
                v-model="selectedItems" show-select></v-data-table>
        </v-card-text>

        <!-- Actions -->
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" text @click="dialog = false">Close</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {


    data() {
        return {
            dialog: false,
            waybillType: 'input',
            waybills: '',
            selectedItems: [],
            orders: [],
            selectedStatus: null,
            statusOptions: ['Returned', 'Awaiting Return'],
          
            headers: [
                { title: 'Created On', value: 'created_on' },
                { title: 'Order No', value: 'order_no' },
                { title: 'Client Name', value: 'client_name' },
                { title: 'Client Address', value: 'address' },
                { title: 'Client Phone', value: 'phone' },
                { title: 'Delivery Status', value: 'status' },
                { title: 'Total', value: 'cod_amount' },
                { title: 'Actions', value: 'actions' },
            ],
            dispatchData: [], // Populate this array with your data
        };
    },
    created() {
      this.fetchOrders();
    },
    methods: {
        updateOrder() {
            if (
                !this.selectedItems.length ||
                !this.selectedStatus
            ) {
                this.$toastr.error('select orders.');
                return;
            }
            const payload = {
                order_ids: this.selectedItems,
                status: this.selectedStatus,
            };

            axios.post('/api/v1/returnOrders', payload)
                .then(response => {
                    this.$toastr.success('Orders returned successfully!');
                    this.dialog = false;
                    this.fetchOrders(); // Refresh the orders after update
                })
                .catch(error => {
                    this.$toastr.error('Error updating orders.');
                    console.error('Error updating orders:', error);
                });
        },
        closeDialog() {
            this.dialog = false;
        },
        bulkUpdateStatus() {

            this.dialog = true;
        },
        onSearch() {
            // Split the waybills input into an array of individual waybills, removing extra spaces
            const waybillArray = this.waybills.split(/[\s,]+/).map(waybill => waybill.trim());

            // Filter orders based on matching waybills
            this.dispatchData = this.orders.filter(order => waybillArray.includes(order.order_no));

            // Log the filtered dispatch data
            console.log('Filtered Orders:', this.dispatchData);

            // Handle cases where no orders are found
            if (this.dispatchData.length === 0) {
                this.$toastr.error('No orders found for the given waybills.');
            } else {
                this.$toastr.success('Orders found!');
            }
        },

        fetchOrders() {
            const url = `/api/v1/orders`;
            axios
                .get(url, {})
                .then(response => {
                    this.orders = response.data;
                })
                .catch(error => {
                    console.error('Error fetching zones:', error);
                });
        },
      
    },
};
</script>
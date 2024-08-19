<template>
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Clear Rider</span>
        </v-card-title>
  
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-select
              v-model="status"
              :items="statusOptions"
              label="Status"
              required
            ></v-select>
  
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              :nudge-right="40"
              :return-value.sync="recallDate"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
             
            </v-menu>
  
            <v-textarea
              v-model="comments"
              label="Comments"
              auto-grow
              counter="500"
            ></v-textarea>
          </v-form>
        </v-card-text>
  
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="closeDialog">Close</v-btn>
          <v-btn color="primary" text @click="updateOrder">Update</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
  export default {
    // props: {
    //   order: Object,
    //   dialog: Boolean,
    // },
    data() {
      return {
        valid: false,
        // status: this.order.status,
        // recallDate: this.order.recallDate,
        // comments: this.order.comments,
        statusOptions: [ "Pending", "Cleared"],
        menu: false,
        dialog: false,
      };
    },
    methods: {
      show() {
      this.dialog = true;
    },

    closeDialog() {
      this.dialog = false;
    },

      updateOrder() {
        if (this.$refs.form.validate()) {
          // Emit the updated order details
          this.$emit("update-order", {
            // ...this.order,
            status: this.status,
            recallDate: this.recallDate,
            comments: this.comments,
          });
          this.closeDialog();
        }
      },
    },
  };
  </script>
  
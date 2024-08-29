<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card>
      <v-card-title class="headline">Google Sheets</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-select
          :rules="vendorRules" required
            v-model="vendor"
            :items="vendors"
            item-title="name"
            item-value="id"
            label="Vendor"
            
          ></v-select>

          <v-select
          :rules="branchRules" required
            v-model="selectedbranch"
            :items="branches"
            item-title="name"
            item-value="id"
            label="branch"
            
          ></v-select>

          <v-text-field v-model="sheetName" label="Sheet name" :rules="sheetNameRules" required></v-text-field>
          <v-text-field
            v-model="spreadsheetId"
            label="Spreadsheet id"
            :rules="spreadsheetIdRules"
            required
          ></v-text-field>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close">Close</v-btn>
        <v-btn color="blue darken-1" text @click="save">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {

  props: {
    user_id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      valid: false,
      vendor: '',
      branch:'',
      selectedbranch:'',
      branches:'',
      vendors: [],
      sheetName: '',
      spreadsheetId: '',
      vendorRules: [
        v => !!v || 'Vendor is required',
      ],
      sheetNameRules: [v => !!v || 'Sheet name is required'],
      spreadsheetIdRules: [v => !!v || 'Spreadsheet ID is required']
    };
  },
  created() {
    this.fetchVendors();
    this.fetchBranches();
  },
  methods: {

    // show() {
    //   this.dialog = true;
    // },

    show(item = null) {
    if (item) {
      this.vendor = item.vendor_id; // Adjust this to match your item's structure
      this.branch = item.branch_id; // Adjust this to match your item's structure
      this.sheetName = item.sheet_name;
      this.spreadsheetId = item.post_spreadsheet_id;
    } else {
      this.vendor = '';
      this.branch = '';
      this.sheetName = '';
      this.spreadsheetId = '';
    }
    this.dialog = true;
  },
    close() {
      this.dialog = false;
    },
    
    fetchVendors() {
      const url = `/api/v1/vendors`;
      axios
        .get(url, {})
        .then(response => {
          this.vendors = response.data;
        })
        .catch(error => {
          console.error('Error fetching vendor:', error);
        });
    },

    fetchBranches() {
      const url = `/api/v1/branches`;
      axios
        .get(url, {})
        .then(response => {
          this.branches = response.data;
        })
        .catch(error => {
          console.error('Error fetching vendor:', error);
        });
    },
    async save() {
    if (this.$refs.form && this.$refs.form.validate()) {
      try {
        const response = await axios.post('/api/v1/sheets', {
          vendor_id: this.vendor,
          sheet_name: this.sheetName,
          post_spreadsheet_id: this.spreadsheetId,
          branch_id:this.selectedbranch
        });
        console.log(response.data);
        this.$toastr.success(response.data.message);

        // fetchSheets();
        this.close();
      } catch (error) {
        console.error('There was an error!', error);
      }
    } else {
      console.error('Form reference or validation is not available.');
    }
  }
  },

};
</script>



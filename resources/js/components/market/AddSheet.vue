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
  data() {
    return {
      dialog: false,
      valid: false,
      vendor: '',
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
  },
  methods: {
    
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
    async save() {
    if (this.$refs.form && this.$refs.form.validate()) {
      try {
        const response = await axios.post('/api/v1/sheets', {
          vendor_id: this.vendor,
          sheet_name: this.sheetName,
          post_spreadsheet_id: this.spreadsheetId
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



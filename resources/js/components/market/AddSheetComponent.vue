<template>
    <v-dialog v-model="dialog" max-width="500px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn color="primary" dark v-bind="attrs" v-on="on">Open Dialog</v-btn>
      </template>
      <v-card>
        <v-card-title class="headline">Google Sheets</v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-text-field
              v-model="vendor"
              label="Vendor"
              :rules="vendorRules"
              required
            ></v-text-field>
            <v-text-field
              v-model="sheetName"
              label="Sheet name"
              :rules="sheetNameRules"
              required
            ></v-text-field>
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
          <v-btn color="blue darken-1" text @click="save" :disabled="!valid">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
  export default {
    data() {
      return {
        dialog: false,
        valid: false,
        vendor: '',
        sheetName: 'Sheet1',
        spreadsheetId: '',
        vendorRules: [
          v => !!v || 'Vendor is required',
          v => (v && v.length >= 3) || 'Vendor must be at least 3 characters'
        ],
        sheetNameRules: [
          v => !!v || 'Sheet name is required',
        ],
        spreadsheetIdRules: [
          v => !!v || 'Spreadsheet ID is required',
        ],
      };
    },
    methods: {
      close() {
        this.dialog = false;
      },
      save() {
        if (this.$refs.form.validate()) {
          // Save logic here
          console.log({
            vendor: this.vendor,
            sheetName: this.sheetName,
            spreadsheetId: this.spreadsheetId
          });
          this.close();
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .v-card {
    max-width: 500px;
    margin: auto;
  }
  </style>
  
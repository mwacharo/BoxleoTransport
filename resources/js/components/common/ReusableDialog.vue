<template>
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ title }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form">
            <v-select
              v-if="showStatus"
              v-model="formData.status"
              :items="statusOptions"
              label="Status"
              clearable
            ></v-select>
            <v-textarea
              v-if="showComments"
              v-model="formData.comments"
              label="Comments"
              rows="4"
              clearable
            ></v-textarea>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="red darken-1" text @click="closeDialog">Close</v-btn>
          <v-btn color="blue darken-1" text @click="submitForm">Update</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
  export default {
    props: {
      title: {
        type: String,
        default: 'Dialog'
      },
      showStatus: {
        type: Boolean,
        default: false
      },
      showComments: {
        type: Boolean,
        default: false
      },
      statusOptions: {
        type: Array,
        default: () => []
      }
    },
    data() {
      return {
        dialog: false,
        formData: {
          status: '',
          comments: ''
        }
      };
    },
    methods: {
      openDialog() {
        this.dialog = true;
      },
      closeDialog() {
        this.dialog = false;
      },
      submitForm() {
        this.$emit('submit', this.formData);
        this.closeDialog();
      }
    }
  };
  </script>
  
<template>
    <v-dialog v-model="dialog" max-width="800px" >
      <v-card>
        <v-card-title>
          <span class="text-h5">Upload POD</span>
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
  
        <v-card-text>
          <v-file-input
            v-model="file"
            label="Select Document"
            prepend-icon="mdi-paperclip"
            accept=".pdf,.doc,.docx,.jpg,.png"
            outlined
          ></v-file-input>
        </v-card-text>
  
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" text @click="dialog = false">Close</v-btn>
          <v-btn color="blue darken-1" @click="submitDocument">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
  export default {

    props: {
    orderNo: {
      type: String,
      required: true,
    },
  },
  
    data() {
      return {
        dialog: false,
        file: null,

        podDetails: {
        pod_path: ''
      }
      };
    },
    methods: {

      show() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
    
    },

      async submitDocument() {
    if (this.file) {
      const formData = new FormData();
      formData.append('pod', this.file);
      
      try {
        const response = await axios.post(`/api/v1/order-pod/${this.orderNo}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        console.log('File submitted:', response.data);
        this.dialog = false;
        alert('POD uploaded successfully');
      } catch (error) {
        console.error('Error uploading file:', error.response.data);
        alert('Failed to upload POD. Please try again.');
      }
    } else {
      alert('Please select a document to upload.');
    }
  },

    },

 
  };
  </script>
  
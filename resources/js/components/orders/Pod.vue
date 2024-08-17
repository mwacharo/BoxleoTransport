<template>
  <v-dialog v-model="dialog" max-width="800px">
    <v-card>
      <v-card-title>
        <span class="text-h5">Upload POD</span>
        <v-spacer></v-spacer>
        <v-btn icon @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <!-- If POD exists, display the link to view it -->
        <div v-if="podDetails.pod_path">
          <v-btn  @click="viewPod(podDetails.pod_path)" color="info">
            <v-icon>mdi-file-document"></v-icon>View
          </v-btn>
          

          <v-btn  @click="deletePod" color="warning">
            <v-icon>mdi-delete"></v-icon>Delete
          </v-btn>
     
        </div>

        <!-- upload new file (png, jpeg, pdf) -->
        <v-file-input
          v-model="file"
          label="Upload POD"
          prepend-icon="mdi-file-upload"
          accept=".png, .jpg, .jpeg, .pdf"
          :rules="[v => !!v || 'File is required']"
          show-size
          truncate-length="15"
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
        pod_path: '', // Stores the POD file path if it exists
      },
    };
  },

  methods: {
    show(item) {
      this.podDetails.pod_path = item.pods && item.pods.length ? item.pods[0].pod_path : ''; 
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


// async viewPod(orderNo) {    // https://hrm.boxleocourier.com/storage/leave/documents/1723699083.pdf
//   // http://127.0.0.1:8000/storage/pods/baAto3lwSE0ti8Xao3OStm8oZrUwJGV15s0YuYx3.png

//   try {
//     const response = await axios.get(`/api/v1/order-pod/${this.orderNo}`);
//     const podPath = response.data.pod_path;
//     const fullUrl = `/storage/${podPath}`;
//     console.log('File URL:', fullUrl); // Log the full URL
//     window.open(fullUrl, '_blank');
//   } catch (error) {
//     console.error('Error fetching POD:', error.response.data);
//     alert(`Failed to fetch POD for order ${orderNo}. Please try again.`);
//   }
// }
// ,


async viewPod(orderNo) {
  try {
    const response = await axios.get(`/api/v1/order-pod/${this.orderNo}`, {
      responseType: 'blob', // Ensure the response is handled as a binary blob
    });

    // Create a URL for the file
    const url = window.URL.createObjectURL(new Blob([response.data]));
    
    // Open the file in a new tab
    window.open(url, '_blank');
  } catch (error) {
    // Log the full error for debugging
    console.error('Error fetching POD:', error.response ? error.response.data : error.message);
    alert(`Failed to fetch POD for order ${orderNo}. Please try again.`);
  }
}
,


async deletePod(orderNo) {
  try {
    await axios.delete(`/api/v1/order-pod/${this.orderNo}`);
  alert('POD deleted successfully');
  } catch (error) {
    console.error('Error deleting POD:', error.response.data);
    alert('Failed to delete POD. Please try again.');
  }
}
,

    // async deletePod() {
    //   if (confirm('Are you sure you want to delete this POD?')) {
    //     try {
    //       await axios.delete(`/api/v1/order-pod/${this.orderNo}`);

    //       // Clear the POD path from the UI after deletion
    //       this.podDetails.pod_path = '';
    //       alert('POD deleted successfully');
    //     } catch (error) {
    //       console.error('Error deleting POD:', error.response.data);
    //       alert('Failed to delete POD. Please try again.');
    //     }
    //   }
    // },
  },
};
</script>

<template>
    <div class="text-center pa-4">
        <v-dialog v-model="dialog" transition="dialog-bottom-transition" width="800px">
            <v-card>
                <v-toolbar color="info">
                    <v-btn icon="mdi-close" @click="dialog = false"></v-btn>

                    <v-toolbar-title>Upload</v-toolbar-title>

                    <v-spacer></v-spacer>
                </v-toolbar>

                <v-card class="my-card">
                    <v-list lines="two" subheader>
                        <v-list-subheader color="blue">Import Orders</v-list-subheader>

                        <v-divider></v-divider>

                        <div>
                            <v-select :rules="vendorRules" required v-model="vendor" :items="vendors" item-title="name"
                                item-value="id" label="Vendor"></v-select>


                            <v-select :rules="branchRules" required v-model="selectedbranch" :items="branches"
                                item-title="name" item-value="id" label="Branch"></v-select>

                            <v-list-subheader>Upload from Excel file</v-list-subheader>
                            <!-- select excel file type ... -->

                            <v-file-input label="Select Excel file" accept=".xlsx, .xls"
                                @change="selectFile"></v-file-input>
                        </div>
                    </v-list>
                    <v-toolbar>
                        <v-spacer></v-spacer>


                        <v-btn text @click="uploadFile" :disabled="!selectedFile">
                            Upload
                        </v-btn>
                    </v-toolbar>
                </v-card>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            dialog: false,
            notifications: false,
            sound: true,
            widgets: false,

            selectedFile: null,
            selectedbranch: null,
            vendor: null,
        };
    },


    created() {
        this.fetchVendors();
        this.fetchBranches();
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
        show() {
            console.log();
            this.dialog = true;
            // this.id = item.id;
        },

        selectFile(event) {
            this.selectedFile = event.target.files[0];
        },
        uploadFile() {
            const formData = new FormData();
            
            formData.append("file", this.selectedFile);
            formData.append("vendor_id", this.vendor); // Add vendor ID
            formData.append("branch_id", this.selectedbranch); // Add branch ID

            axios
                .post("/api/v1/orderImport", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    console.log(response.data);
                    this.dialog = false;
                    // You can add additional handling after successful upload
                })
                .catch((error) => {
                    console.error(error);
                    // Handle errors here
                });
        },
    },
};
</script>
<style scoped>
.my-card {
    margin: 100px;
    /* Adjust the margin as needed */
}
</style>

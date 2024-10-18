<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Upload File and Update Database with Master Number</h2>

    <!-- Master Number Input -->
    <!-- <div class="form-group">
      <label for="masterNumber">Master Number:</label>
      <input type="text" v-model="masterNumber" id="masterNumber" class="form-control" placeholder="Enter Master Number">
    </div> -->

    <!-- File Upload Section -->
    <div class="form-group mt-3">
      <label for="fileUpload">Select CSV File:</label>
      <input type="file" @change="onFileChange" id="fileUpload" accept=".csv, .txt" class="form-control">
    </div>

    <!-- Button to Upload the File -->
    <button class="btn btn-primary mt-3" @click="uploadFile" :disabled="!uploadedFile">Upload File</button>

    <!-- Loading Spinner -->
    <div v-if="loading" class="text-center">Uploading...</div>

    <!-- Error Message -->
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Show Results Box for Updated Records -->
    <div v-if="updatedRecords.length > 0" class="results-box mt-5">
      <h4>Updated Records</h4>
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Original col1</th>
            <th>Updated col1</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(record, index) in updatedRecords" :key="index">
            <td>{{ record.MSISDN }}</td>
            <td>{{ record.DNCR_FLAG }}</td>
            <!-- <td>{{ record.master_number }}</td> -->
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      uploadedFile: null,    // The uploaded file
    //   masterNumber: '',      // Master number input
      updatedRecords: [],    // Store updated records from the server
      loading: false,        // Loading state
      error: null            // Error state
    };
  },
  methods: {
    // Handle file selection
    onFileChange(e) {
      this.uploadedFile = e.target.files[0];
    },

    // Upload the file, master number, and customerId to the backend
    uploadFile() {
      if (!this.uploadedFile) {
        return;
      }

      this.loading = true;
      const formData = new FormData();
      formData.append('file', this.uploadedFile);
    //   formData.append('master_number', this.masterNumber); // Append master number to the form data
      formData.append('customerId', this.$route.params.customerId); // Append customerId from route

      const token = localStorage.getItem('auth_token'); // Assuming you are storing the token here

      axios.post('http://localhost:8000/api/upload-dncr', formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(response => {
          this.loading = false;
          this.updatedRecords = response.data.updatedRecords; // Set the updated records from the response
          alert('File uploaded and database updated!');
        })
        .catch(error => {
          this.error = error.response?.data?.error || 'An error occurred during the file upload.';
          this.loading = false;
        });
    }
  }
};
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: auto;
}

.results-box {
  margin-top: 20px;
}

.table {
  margin-top: 10px;
}
</style>

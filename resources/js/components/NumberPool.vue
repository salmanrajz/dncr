<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Number Uploader -
        {{this.$route.params.customerId}}

    </h2>

    <!-- Error and Loading Messages -->
    <div v-if="loading" class="text-center">Loading...</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Textarea for Uploading Numbers -->
    <div v-if="!loading && !error" class="mb-4">
      <textarea v-model="numberInput" class="form-control" rows="8" placeholder="Enter numbers separated by commas or new lines"></textarea>
      <small class="text-muted">Please enter numbers, separated by commas or new lines.</small>
    </div>

    <!-- Show Counts -->
    <div v-if="totalCount > 0" class="alert alert-info">
      <p>Total numbers: {{ totalCount }}</p>
      <p>Unique numbers: {{ uniqueCount }}</p>
      <p>Duplicate numbers: {{ duplicateCount }}</p>
    </div>

    <!-- Update/Upload Button -->
    <div class="text-center ">
      <button v-if="!uploaded" @click="processNumbers" class="btn btn-primary">Update</button>
      <button v-if="uploaded" @click="uploadNumbers" class="btn btn-success">Upload</button>
    </div>
    <div class="text-center mt-2">
      <button v-if="uploaded" @click="refreshPage" class="btn btn-danger">LoadPage Again</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      numberInput: '', // Input field for numbers
      uniqueNumbers: [], // Store unique numbers
      totalCount: 0, // Total count of numbers entered
      uniqueCount: 0, // Count of unique numbers
      duplicateCount: 0, // Count of duplicate numbers
      loading: false, // Loading state
      error: null, // Error state
      uploaded: false, // Flag for whether numbers are ready to upload
    };
  },
  methods: {
    refreshPage() {
      window.location.reload(); // Refreshes the current page
    },
    // Process the numbers entered by the user
    processNumbers() {
      // Split the input by commas, newlines, or spaces
      const numbers = this.numberInput.split(/[\s,]+/);

      // Remove empty values
      const filteredNumbers = numbers.filter(num => num.trim() !== '');

      // Total count of numbers
      this.totalCount = filteredNumbers.length;

      // Remove duplicates and get unique numbers
      this.uniqueNumbers = [...new Set(filteredNumbers)];

      // Count of unique numbers
      this.uniqueCount = this.uniqueNumbers.length;

      // Calculate the duplicate count
      this.duplicateCount = this.totalCount - this.uniqueCount;

      // If unique numbers found, change button to 'Upload'
      if (this.uniqueNumbers.length > 0) {
        this.uploaded = true;
      } else {
        alert('No valid numbers found.');
      }
    },

    // Upload the unique numbers to the API
    uploadNumbers() {
      const token = localStorage.getItem('auth_token');
      this.loading = true;

      axios.post('http://localhost:8000/api/upload-numbers',
        {
            numbers: this.uniqueNumbers,
            MasterNumber: this.$route.params.customerId
        },
        {
          headers: {
            Authorization: `Bearer ${token}` // Send user token for authentication
          }
        })
        .then(response => {
          this.loading = false;
          alert('Numbers uploaded successfully!');
          this.numberInput = ''; // Clear the input field
          this.uniqueNumbers = []; // Reset the unique numbers
          this.uploaded = false; // Reset the upload state
          this.totalCount = 0; // Reset the counts
          this.uniqueCount = 0;
          this.duplicateCount = 0;
                this.$router.push({ name: 'MainDashboard' }); // Use the route name of your dashboard


        })
        .catch(error => {
            console.log(error.response.data)
          this.error = error.response.data.error;
          this.loading = false;
        });
    }
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: auto;
}

textarea {
  margin-bottom: 20px;
}
</style>

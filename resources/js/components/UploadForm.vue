<template>
  <div class="container mt-5">
    <h3 class="text-center">Upload Numbers (CSV/Excel)</h3>

    <form @submit.prevent="uploadFile">
      <div class="mb-3">
        <label for="file" class="form-label">Upload Excel/CSV File</label>
        <input type="file" @change="handleFileUpload" class="form-control" id="file" accept=".csv, .xlsx" required />
      </div>
      <button type="submit" class="btn btn-primary">Upload File</button>
    </form>

    <!-- Success Message -->
    <div v-if="message" class="alert alert-success mt-3">{{ message }}</div>

    <!-- Error Message -->
    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      selectedFile: null,
      message: '',
      error: ''
    };
  },
  methods: {
    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    },
    async uploadFile() {
      if (!this.selectedFile) {
        this.error = 'Please select a file to upload.';
        return;
      }

      const formData = new FormData();
      formData.append('file', this.selectedFile);

      try {
        const response = await axios.post('/api/upload-numbers', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        this.message = response.data.success;
        this.error = '';
        this.selectedFile = null;
      } catch (error) {
        this.message = '';
        this.error = error.response?.data?.error || 'An error occurred during file upload. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
}
</style>

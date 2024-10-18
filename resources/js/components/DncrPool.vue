<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Number Pool</h2>

    <div v-if="loading" class="text-center">Loading...</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-if="!loading && !error" class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Number</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(customer, index) in customers.data" :key="index">
            <td>{{ customer.number  }}</td>
            <input type="hidden"  v-model="customer.id">
            <td>
              <button @click="checkCustomerData(customer)" class="btn btn-primary">
                Choose
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRouter } from 'vue-router'; // To handle page navigation

export default {
  data() {
    return {
      numbers: [], // Store the number pool data from API
      loading: true, // Loading state
      error: null // Error state
    };
  },
  mounted() {
    this.getNumberDetails(); // Fetch the number details on component mount
  },
  methods: {
    // Fetch numbers from the API
    getNumberDetails() {
      const token = localStorage.getItem('auth_token');
      axios.get('http://localhost:8000/api/dncr_number', {
        headers: {
          Authorization: `Bearer ${token}` // Send user token for authentication
        }
      })
        .then(response => {
          this.customers = response.data;
          this.loading = false;
          console.log(this.customers)
        })
        .catch(error => {
          this.error = 'Error fetching number details. Please try again.';
          this.loading = false;
        });
    },
    // Check customer data when number is clicked
    checkCustomerData(number) {
        const token = localStorage.getItem('auth_token');
        axios.post(`http://localhost:8000/api/UploadMasterDataDNCR`,
            {
            number: number.number, // Send the number in the POST body
            number_id: number.id // Send the number in the POST body
            },
            {
            headers: {
                Authorization: `Bearer ${token}` // Send user token for authentication
            }
            })
            .then(response => {
            if (response.data.exists) {
                console.log(response.data.customerId);
                // If number exists in the master data, navigate to the customer data page
                this.$router.push({ name: 'UploadDncr', params: { customerId: response.data.customerId } });
            } else {
                console.log(response.data.Error);
                alert(response.data.Error);
                // alert('No customer data found for this number.');
            }
            })
            .catch(error => {
            console.error('Error checking customer data:', error);
                alert('Please Clear Previous Pool.');
            });
        }

  }
};
</script>

<style scoped>
.table {
  margin-top: 20px;
}
</style>

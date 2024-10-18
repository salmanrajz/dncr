<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4">
        {{this.$route.params.customerId}}
    </h1>
    <h2 class="text-center mb-4">Customer Feedback Dashboard </h2>

    <div v-if="loading" class="text-center">Loading...</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-if="!loading && customerRecords.length > 0" class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Customer Number</th>
            <th>Is WhatsApp</th>
            <th>IsDncr</th>
            <th>Status</th>
            <th>Submit Feedback</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through customer records and display each record in a row -->
          <tr v-for="(record, index) in customerRecords" :key="index">
            <td>{{ record.number }}</td>
            <td>
              <i v-if="record.isWhatsApp == 1" class="fab fa-whatsapp text-success"></i>
            </td>
            <td>{{ record.IsDncr == 1 ? 'Y' : 'N' }}</td>
            <td>
              <select v-model="record.Status" class="form-select">
                <option>--Select Number--</option>
                <option value="Interested">Interested</option>
                <option value="Follow Up">Follow Up</option>
                <option value="No Answer">No Answer</option>
                <option value="Not Interested">Not Interested</option>
                <option value="Invalid">Invalid</option>
                <option value="DNC">DNC</option>
                <!-- NO ANSWER
NOT INTERESTED
INVALID
NORMAL FOLLOW UP
DNC -->
              </select>
            </td>
            <td>
              <button
                @click="submitFeedback(record)"
                class="btn btn-primary"
                :disabled="record.submitted">
                {{ record.submitted ? 'Submitted' : 'Submit' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>

     <div class="row">
         <div class="col-md-6">
        <div class="alert alert-success text-center" >
            Lead
        </div>
      </div>
      <div class="col-md-6">
        <div class="alert alert-success text-center" >
            Follow Up
        </div>
      </div>
     </div>
    </div>

    <div v-else-if="!loading && customerRecords.length === 0" class="text-center">
      <p>No customer records found.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      customerRecords: [], // Store customer records
      loading: true,  // Loading state
      error: null     // Error state
    };
  },
  mounted() {
    // Fetch customer data based on the customerId from route params
    const customerId = this.$route.params.customerId;
    this.getCustomerDataById(customerId);
  },
  methods: {
    // Fetch customer data by Customer ID and loop through records
    getCustomerDataById(customerId) {
      const token = localStorage.getItem('auth_token'); // Ensure token is stored

      // Fetch customer data from API based on the customerId
      axios.get(`http://localhost:8000/api/get-customers/${customerId}`, {
        headers: {
          Authorization: `Bearer ${token}` // Include the user token in the headers
        }
      })
      .then(response => {
        // console.log(response.data.data[0].number)
        if (response.data.total > 0) {

          this.customerRecords = response.data.data; // Store customer records in the component
        //   console.log("Salman=> "+ this.customerRecords.length)
        } else {
          this.error = "No customer records found.";
        }
        this.loading = false;
      })
      .catch(error => {
        console.error('Error fetching customer data:', error);
        this.error = 'Error fetching customer data. Please try again.';
        this.loading = false;
      });
    },

    // Submit feedback for each customer record
    submitFeedback(record) {
      const feedbackData = {
        id: record.id,
        CustomerNumber: record.number,
        IsWhatsApp: record.IsWhatsApp,
        IsDncr: record.IsDncr,
        Status: record.Status
      };

      const token = localStorage.getItem('auth_token');

      // Send the feedback to the backend API
      axios.post('http://localhost:8000/api/submit-feedback', feedbackData, {
        headers: {
          Authorization: `Bearer ${token}` // Include the user token in the headers
        }
      })
      .then(response => {
        console.log('Feedback submitted successfully:', response.data);
        record.submitted = true; // Mark feedback as submitted
        alert(`Feedback for ${record.CustomerNumber} has been submitted successfully!`);
      })
      .catch(error => {
        console.error('Error submitting feedback:', error);
        alert('Error submitting feedback. Please try again.');
      });
    }
  }
};
</script>

<style scoped>
.table {
  margin-top: 20px;
}

/* Center align for WhatsApp icon */
.fa-whatsapp {
  font-size: 1.5rem;
  display: block;
  text-align: center;
}
</style>

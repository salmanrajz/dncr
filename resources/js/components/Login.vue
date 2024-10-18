<template>
  <div class="container-fluid vh-100 d-flex justify-content-center align-items-center login-page">
    <div class="row w-100 justify-content-center">
      <div class="col-md-6 col-lg-4 col-sm-10 col-12">
        <div class="card shadow-lg border-0 rounded-3">
          <div class="card-body p-4">
            <h3 class="text-center mb-4 text-primary">ExpressDial Manager Login</h3>
            <form @submit.prevent="loginUser">
              <!-- Email Field -->
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  placeholder="Enter your email"
                  v-model="email"
                  required
                />
              </div>

              <!-- Password Field -->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  placeholder="Enter your password"
                  v-model="password"
                  required
                />
              </div>

              <!-- Error Message -->
              <p v-if="errorMessage" class="text-danger">{{ errorMessage }}</p>

              <!-- Submit Button -->
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
              </div>

              <!-- Forgot Password Link -->
              <div class="text-center mt-3">
                <a href="#" class="text-muted">Forgot password?</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async loginUser() {
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          email: this.email,
          password: this.password
        }, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });

        if (response.data.success) {
          localStorage.setItem('auth_token', response.data.token);
            localStorage.setItem('user_role', response.data.user.role); // Or 'user'
                window.dispatchEvent(new Event('userRoleUpdated'));
            // this.$store.dispatch('setUserRole', response.data.user.role); // Update role in Vuex

        //   this.$router.push({ name: 'dashboard' });
            const role = localStorage.getItem('user_role');
            if (role === 'Admin') {
            this.$router.push({ name: 'MainDashboard' });
            } else {
            this.$router.push({ name: 'UserDashboard' });
            }

        } else {
          this.errorMessage = response.data.message || 'Login failed. Please try again.';
        }
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
/* Background for the login page */
.login-page {
  background: linear-gradient(135deg, #eceff1, #90caf9);
}

/* Card Shadow for better presentation */
.card {
  background-color: white;
  border-radius: 15px;
  box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
}

h3 {
  font-weight: bold;
}

/* Customize button */
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

/* Adjust input fields */
.form-control {
  height: 45px;
  padding: 10px;
}

/* Responsive layout adjustments */
@media (max-width: 768px) {
  .card-body {
    padding: 1.5rem;
  }
}
</style>

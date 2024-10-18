<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">App</a>

      <ul class="navbar-nav ml-auto">
        <!-- Common links for all users -->
        <li class="nav-item">
          <router-link to="/MainDashboard" class="nav-link">Home</router-link>
        </li>

        <!-- Conditional links based on role -->
        <li class="nav-item" v-if="userRole === 'admin'">
          <router-link to="/admin" class="nav-link">Admin Dashboard</router-link>
        </li>
        <li class="nav-item" v-if="userRole === 'user'">
          <router-link to="/user" class="nav-link">User Dashboard</router-link>
        </li>

        <!-- Authentication links -->
        <li class="nav-item" v-if="!userRole">
          <router-link to="/login" class="nav-link">Login</router-link>
        </li>
        <li class="nav-item" v-if="userRole">
          <button @click="logout" class="btn btn-danger">Logout</button>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      userRole: null // Role will be updated in real-time
    };
  },
  mounted() {
    this.userRole = localStorage.getItem('user_role'); // Fetch the role when the component is mounted
    window.addEventListener('userRoleUpdated', this.updateUserRole); // Listen for custom event
  },
  methods: {
    updateUserRole() {
      this.userRole = localStorage.getItem('user_role'); // Update role in real-time when event is triggered
    },
    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_role');
      this.$router.push({ name: 'login' });
      this.userRole = null; // Reset the role
      window.dispatchEvent(new Event('userRoleUpdated')); // Trigger the event to update other components
    }
  },
  beforeDestroy() {
    window.removeEventListener('userRoleUpdated', this.updateUserRole); // Clean up the event listener when component is destroyed
  }
};
</script>

<style scoped>
/* Custom styles for the navbar */
</style>

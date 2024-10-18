<template>
  <div id="app">
    <!-- <navbar-view></navbar-view> -->
        <Navbar />

    <router-view></router-view> <!-- Vue Router will handle displaying components here -->
  </div>
</template>

<script>
// Import the Navbar component
import Navbar from './components/Navbar.vue';

export default {
  components: {
    Navbar // Register the Navbar component globally in App.vue
  }
}
</script>


<style>
/* Global styles */
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>


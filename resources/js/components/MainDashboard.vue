<template>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Main Dashboard</h2>

        <!-- Loading and Error Messages -->
        <div v-if="loading" class="text-center">Loading...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <!-- Role-Based Dashboard Rendering -->
        <div v-if="!loading && !error">
            <!-- Admin Dashboard -->

            <div v-if="userRole === 'Admin'">
                <div class="col-md-4">
                    <div class="alert alert-success text-center" @click="goToDncrPool">
                        <h4>Number Pool</h4>

                    </div>
                </div>

            </div>
            <div v-if="userRole === 'DNCRManager'">
                <div class="col-md-4">
                    <div class="alert alert-success text-center" @click="goToNumberPool">
                        <h4>Number Pool</h4>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-primary text-center">
                            <h4>Total Work Comp Num</h4>
                            <p>{{ totalWorkNumber }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-primary text-center">
                            <h4>Total Remaining</h4>
                            <p>{{ TotalRemaining }}</p>
                        </div>
                    </div>

                </div>

            </div>
            <div v-if="userRole === 'Sale'">
                <div v-if="!loading" class="row">
                    <!-- Total Assigned Data -->
                    <div class="col-md-6">
                        <div class="alert alert-primary text-center">
                            <h4>Total Assigned Data</h4>
                            <p>{{ totalAssignedData }}</p>
                        </div>
                    </div>

                    <!-- Total Remaining Data -->
                    <div class="col-md-6">
                        <div class="alert alert-secondary text-center">
                            <h4>Total Remaining Data</h4>
                            <p>{{ remainingData }}</p>
                        </div>
                    </div>

                    <!-- Interested -->
                    <div class="col-md-4">
                        <div class="alert alert-info text-center">
                            <h4>Interested</h4>
                            <p>{{ interestedCount }}</p>
                        </div>
                    </div>

                    <!-- Follow Up -->
                    <div class="col-md-4">
                        <div class="alert alert-warning text-center">
                            <h4>Follow Up</h4>
                            <p>{{ followUpCount }}</p>
                        </div>
                    </div>

                    <!-- No Answer -->
                    <div class="col-md-4">
                        <div class="alert alert-danger text-center">
                            <h4>No Answer</h4>
                            <p>{{ noAnswerCount }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success text-center" @click="goToNumberPoolAgent">
                            <h4>Number Pool</h4>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Number Uploader Dashboard -->
            <div v-else-if="userRole === 'number_uploader'">
                <h3>Number Uploader Dashboard</h3>
                <p>Welcome, Number Uploader! Here are your uploaded numbers.</p>
                <!-- Number Uploader-specific content -->
                <div v-if="uploadedNumbers.length">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Number</th>
                                <th>Uploaded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(number, index) in uploadedNumbers" :key="index">
                                <td>{{ number.number }}</td>
                                <td>{{ new Date(number.created_at).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- General User Dashboard -->

        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                userName: '', // Store the user's name
                userRole: '', // Store the user's role (admin, number_uploader, etc.)
                uploadedNumbers: [], // Store numbers for number uploader role
                loading: true, // Loading state
                error: null, // Error state
                totalAssignedData: 0, // Total assigned data count
                remainingData: 0, // Total remaining data count
                interestedCount: 0, // Interested count
                followUpCount: 0, // Follow up count
                noAnswerCount: 0, // No answer count
                loading: true, // Loading state
                error: null, // Error state
                totalWorkNumber: 0,
                TotalRemaining: 0,
            };
        },
        mounted() {
            this.fetchUserData(); // Fetch user data when the component is mounted
            this.getDashboardStats();

        },
        methods: {
            goToNumberPool() {
                this.$router.push({
                    name: 'NumberList'
                }); // Name of the route you want to go to
            },
            goToNumberPoolAgent() {
                this.$router.push({
                    name: 'NumberPoolForAgent'
                }); // Name of the route you want to go to
            },
            goToDncrPool() {
                this.$router.push({
                    name: 'DncrPool'
                }); // Name of the route you want to go to
            },
            // Fetch user data including role
            fetchUserData() {
                const token = localStorage.getItem('auth_token'); // Get the auth token

                axios.get('http://localhost:8000/api/get-user-data', {
                        headers: {
                            Authorization: `Bearer ${token}` // Include the user's token in the request
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        this.userName = data.name;
                        this.userRole = data.role; // Set user role (admin, number uploader, etc.)

                        // If the user is a number uploader, fetch their uploaded numbers
                        if (this.userRole === 'number_uploader') {
                            this.fetchUploadedNumbers();
                        } else {
                            this.loading = false;
                        }
                    })
                    .catch(error => {
                        this.error = 'Error fetching user data. Please try again.';
                        this.loading = false;
                    });
            },
            getDashboardStats() {
                const userId = this.$route.params.userId; // Assume we are passing userId via route params
                const token = localStorage.getItem('auth_token'); // Ensure token is stored

                // Fetch dashboard stats from API
                axios.get(`http://localhost:8000/api/dashboard-stats/${userId}`, {
                        headers: {
                            Authorization: `Bearer ${token}` // Include the user token in the headers
                        }
                    })
                    .then(response => {
                        const data = response.data;

                        this.totalAssignedData = data.totalAssignedData;
                        this.remainingData = data.remainingData;
                        this.interestedCount = data.interestedCount;
                        this.followUpCount = data.followUpCount;
                        this.noAnswerCount = data.noAnswerCount;
                        this.loading = false; // Stop loading
                    })
                    .catch(error => {
                        console.error('Error fetching dashboard stats:', error);
                        this.error = 'Error fetching dashboard stats. Please try again.';
                        this.loading = false;
                    });
            },


            // Fetch uploaded numbers for number uploader
            fetchUploadedNumbers() {
                const token = localStorage.getItem('auth_token');

                axios.get('http://localhost:8000/api/user-dashboard', {
                        headers: {
                            Authorization: `Bearer ${token}` // Include the user's token in the request
                        }
                    })
                    .then(response => {
                        this.uploadedNumbers = response.data; // Store the user's uploaded numbers
                        this.loading = false;
                    })
                    .catch(error => {
                        this.error = 'Error fetching numbers. Please try again.';
                        this.loading = false;
                    });
            },

            // Example admin task method
            adminTask() {
                alert('Performing admin-specific task...');
            }
        }
    };

</script>

<style scoped>
    .container {
        max-width: 800px;
        margin: auto;
    }

    .table {
        margin-top: 20px;
    }

</style>

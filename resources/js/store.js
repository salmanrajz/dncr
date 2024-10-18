import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        userRole: localStorage.getItem('user_role') || null // Get the user role from localStorage initially
    },
    mutations: {
        SET_USER_ROLE(state, role) {
            state.userRole = role;
            localStorage.setItem('user_role', role); // Store role in localStorage
        },
        LOGOUT(state) {
            state.userRole = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
        }
    },
    actions: {
        setUserRole({
            commit
        }, role) {
            commit('SET_USER_ROLE', role);
        },
        logout({
            commit
        }) {
            commit('LOGOUT');
        }
    },
    getters: {
        userRole: state => state.userRole
    }
});

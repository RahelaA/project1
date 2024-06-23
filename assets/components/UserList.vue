<template>
    <div>
      <h1>User List</h1>
      <ul>
        <li v-for="user in users" :key="user.id">
          {{ user.firstName }} {{ user.lastName }} ({{ user.userEmail }})
          <button @click="editUser(user)">Edit</button>
          <button @click="deleteUser(user)">Delete</button>
        </li>
      </ul>
  
      <button @click="showAddModal = true">Add User</button>
  
      <!-- Modal for Add or Edit User -->
      <div v-if="showAddModal">
        <h2>{{ editMode ? 'Edit User' : 'Add User' }}</h2>
        <form @submit.prevent="editMode ? updateUser() : addUser()">
          <input v-model="formData.firstName" placeholder="First Name" required>
          <input v-model="formData.lastName" placeholder="Last Name" required>
          <input v-model="formData.userEmail" placeholder="Email" required>
          <button type="submit">{{ editMode ? 'Update' : 'Add' }}</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        users: [],
        showAddModal: false,
        editMode: false,
        formData: {
          firstName: '',
          lastName: '',
          userEmail: ''
        },
        selectedUserId: null
      };
    },
    created() {
      this.fetchUsers();
    },
    methods: {
      async fetchUsers() {
        try {
          const response = await axios.get('/api/users');
          this.users = response.data['hydra:member'];
        } catch (error) {
          console.error('Error fetching users:', error);
        }
      },
      async addUser() {
        let axiosConfig = {
        headers: {
            'Content-Type':'application/ld+json;charset=UTF-8',
            "Access-Control-Allow-Origin": "*"
        }};
        try {
          const response = await axios.post(
            'http://127.0.0.1:8001/api/users', 
            this.formData,
            axiosConfig
          );
          this.closeModal();
          this.fetchUsers();
        } catch (error) {
          console.error('Error adding user:', error);
        }
      },
      async editUser(user) {
        this.editMode = true;
        this.selectedUserId = user.id;
        this.formData = { ...user };
        this.showAddModal = true;
      },
      async updateUser() {
        let axiosConfig = {
        headers: {
            'Content-Type':'application/ld+json;charset=UTF-8',
            "Access-Control-Allow-Origin": "*"
        }};
        try {
          await axios.put(`http://127.0.0.1:8001/api/users/${this.selectedUserId}`, this.formData, axiosConfig);
          this.closeModal();
          this.fetchUsers();
        } catch (error) {
          console.error('Error updating user:', error);
        }
      },
      async deleteUser(user) {
        let axiosConfig = {
        headers: {
            'Content-Type':'application/ld+json;charset=UTF-8',
            "Access-Control-Allow-Origin": "*"
        }};
        try {
          await axios.delete(`http://127.0.0.1:8001/api/users/${user.id}`, axiosConfig);
          this.fetchUsers();
        } catch (error) {
          console.error('Error deleting user:', error);
        }
      },
      closeModal() {
        this.showAddModal = false;
        this.editMode = false;
        this.formData = {
          firstName: '',
          lastName: '',
          userEmail: ''
        };
        this.selectedUserId = null;
      }
    }
  };
  </script>
  
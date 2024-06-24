<template>
    <div>
      <h1>User List</h1>
      <div>
        <input v-model="searchTerm" placeholder="Search users..." @input="updateSearch">
      </div>
      <div>
        <label for="sortColumn">Sort by:</label>
        <select id="sortColumn" v-model="sortColumn" @change="updateSorting">
          <option value="firstName">First Name</option>
          <option value="lastName">Last Name</option>
          <option value="userEmail">Email</option>
          <option value="id">ID</option>
          <option value="createdAt">Created At</option>
        </select>
        <select v-model="sortOrder" @change="updateSorting">
          <option value="asc">Ascending</option>
          <option value="desc">Descending</option>
        </select>
        <button @click="fetchUsers">Sort</button>
      </div>
      <ul>
        <li v-for="user in users" :key="user.id">
          {{ user.firstName }} {{ user.lastName }} ({{ user.userEmail }})
          <button @click="editUser(user)">Edit</button>
          <button @click="deleteUser(user)">Delete</button>
        </li>
      </ul>

      <div>
        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
      </div>

  
      <button @click="showAddModal = true">Add User</button>
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
        selectedUserId: null,
        sortColumn: 'firstName',
        sortOrder: 'asc',
        searchTerm: '',
        currentPage: 1,
        itemsPerPage: 5,
        totalItems: 0,
        totalPages: 0,
        axiosConfig : {
            headers: {
            'Content-Type': 'application/ld+json;charset=UTF-8',
            'Access-Control-Allow-Origin': '*'
            }
        }
      };
    },
    created() {
      this.sortColumn = localStorage.getItem('sortColumn') || 'firstName';
      this.sortOrder = localStorage.getItem('sortOrder') || 'asc';
      this.searchTerm = localStorage.getItem('searchTerm') || '';
      this.currentPage = parseInt(localStorage.getItem('currentPage')) || 1;
  
      this.fetchUsers();
    },
    methods: {
      async fetchUsers() {
        try {

          const response = await axios.get('http://127.0.0.1:8000/api/users', {
            params: {
              page: this.currentPage, 
              order: {
                [this.sortColumn]: this.sortOrder
              },
              userEmail: this.searchTerm,
              firstName: this.searchTerm,
              lastName: this.searchTerm,
              page: this.currentPage,
              itemsPerPage: this.itemsPerPage
            }
          });
          this.users = response.data['hydra:member'];
          this.currentPage = response.data['hydra:view']['@id'].split('page=')[1];
          this.totalItems = response.data['hydra:totalItems'];
          this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        } catch (error) {
          console.error('Error fetching users:', error);
        }
      },
      async addUser() {
        try {
          await axios.post('http://127.0.0.1:8000/api/users', this.formData, this.axiosConfig);
          this.closeModal();
          this.fetchUsers();
        } catch (error) {
          console.error('Error adding user:', error);
        }
      },
      async editUser(user) {
        this.editMode = true;
        this.formData = { ...user };
        this.showAddModal = true;
      },
      async updateUser() {
        try {
          await axios.put(`http://127.0.0.1:8000/api/users/${this.formData.id}`, this.formData, this.axiosConfig);
          this.closeModal();
          this.fetchUsers();
        } catch (error) {
          console.error('Error updating user:', error);
        }
      },
      async deleteUser(user) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/users/${user.id}`, this.axiosConfig);
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
      },
      prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.fetchUsers();
        }
      },
        nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.fetchUsers();
        }
        },
      updateSorting() {
        localStorage.setItem('sortColumn', this.sortColumn);
        localStorage.setItem('sortOrder', this.sortOrder);
        this.fetchUsers();
      },
      updateSearch() {
        localStorage.setItem('searchTerm', this.searchTerm);
        this.fetchUsers();
      }
    }
  };
  </script>
  
  <style scoped lang="scss">
  /* Add some basic styles */
  h1 {
    font-size: 2em;
    margin-bottom: 0.5em;
  }
  
  div {
    margin-bottom: 1em;
  }
  
  input {
    padding: 0.5em;
    margin-right: 1em;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  select {
    padding: 0.5em;
    margin-right: 1em;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  button {
    padding: 0.5em 1em;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    margin-bottom: 0.5em;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  li button {
    margin-left: 0.5em;
  }
  
  form {
    display: flex;
    flex-direction: column;
  }
  
  form input {
    margin-bottom: 0.5em;
  }
  
  form button {
    align-self: flex-start;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
  }
  
  .pagination button {
    margin: 0 0.25em;
  }
  </style>
  
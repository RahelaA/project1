<template>
    <b-modal v-model="showModal" title="Edit User">
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="editName">Name</label>
          <input id="editName" v-model="editedUser.name" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="editEmail">Email</label>
          <input id="editEmail" v-model="editedUser.email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </b-modal>
  </template>
  
  <script>
  export default {
    props: ['user'],
    data() {
      return {
        showModal: false,
        editedUser: {
          id: null,
          name: '',
          email: ''
        }
      };
    },
    watch: {
      user: {
        handler(newVal) {
          this.editedUser.id = newVal.id;
          this.editedUser.name = newVal.name;
          this.editedUser.email = newVal.email;
        },
        immediate: true
      }
    },
    methods: {
      submitForm() {
        this.$emit('update-user', this.editedUser);
        this.showModal = false;
      }
    }
  };
  </script>
  
  <style scoped>
  /* Stilizare specifică componentului modal */
  </style>
  
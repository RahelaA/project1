<template>
    <div>
        <h2>Add User</h2>
        <form @submit.prevent="submitForm">
            <input v-model="user.name" placeholder="Name" required>
            <input v-model="user.email" placeholder="Email" required>
            <button type="submit">Submit</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            user: {
                name: '',
                email: ''
            }
        };
    },
    methods: {
        async submitForm() {
            try {
                await axios.post('/api/users', this.user);
                // Handle success (e.g., redirect or show a success message)
            } catch (error) {
                console.error('Error submitting form:', error);
            }
        }
    }
};
</script>

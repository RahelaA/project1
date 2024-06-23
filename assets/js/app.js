import { createApp } from 'vue';
import UserList from '../components/UserList.vue';
import UserForm from '../components/UserForm.vue';

const app = createApp({});

app.component('user-list', UserList);
app.component('user-form', UserForm);

app.mount('#app');

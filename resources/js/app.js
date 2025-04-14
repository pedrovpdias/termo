import './bootstrap';
import { createApp } from 'vue';

import App from './components/App.vue';

const app = createApp({});

app.component('app', App);
app.mount('#app'); // Monte a aplicação no elemento com id "app"

import './bootstrap';
import { createApp } from 'vue';

import App from './components/App.vue';
import Row from './components/Row.vue';
import Letter from './components/Letter.vue';

const app = createApp({});

app.component('app', App);
app.component('row', Row);
app.component('letter', Letter);
app.mount('#app'); // Monte a aplicação no elemento com id "app"

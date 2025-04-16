import './bootstrap';
import { createApp } from 'vue';

import App from './components/App.vue';
import Row from './components/Row.vue';
import Letter from './components/Letter.vue';
import Keyboard from './components/Keyboard.vue';
import GameModal from './components/GameModal.vue';

import 'bootstrap-icons/font/bootstrap-icons.css';

const app = createApp({});

app.component('app', App);
app.component('row', Row);
app.component('letter', Letter);
app.component('keyboard', Keyboard);
app.component('game-modal', GameModal);
app.mount('#app'); // Monte a aplicação no elemento com id "app"

import './bootstrap';

import {createApp} from 'vue'
import ExampleCounter from "./components/ExampleCounter.vue";

const app = createApp({
    components: {
        ExampleCounter,
    }
});

app.mount('#app');


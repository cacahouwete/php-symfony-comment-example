import { createApp } from 'vue';

import store from './../store';
import vuetify from '../plugins/vuetify';
import Comment from "./Comment.vue";

export default createApp(Comment).use(vuetify).use(store).mount('#comment')

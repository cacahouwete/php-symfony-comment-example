import {createStore} from 'vuex';
import comment from './modules/comment';

const debug = 'production' !== process.env.NODE_ENV;

export default createStore({
  modules: {
    comment,
  },
  strict: debug,
});

/*
-----------------------------------------------------------------------
GLOBAL VUEX STORE
-----------------------------------------------------------------------
*/
import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersist from 'vuex-persist'

// Init VuexPersist to automatically save the data store on localstorage
const vuexPersist = new VuexPersist({
  key: 'trkplt.com',
  storage: window.localStorage
})

Vue.use(Vuex)

export const store = new Vuex.Store({
  state: {
    tests: []
  },
  mutations: {
    addNewTest(state, test) {
      state.tests.unshift(test);
    },
    updateTests(state, tests) {
      state.tests = tests;
    },
    resetTests(state) {
      state.tests = [];
    },
  },
  getters: {
    tests: state => state.tests
  },
  plugins: [vuexPersist.plugin]
})

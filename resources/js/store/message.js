const state = {
  contetnt: '',
};

const mutations = {
  setContent(state, { content, timeout }) {
    state.content = content;

    if (typeof timeout === 'undefinde') {
      timeout = 3000;
    }

    setTimeout(() => (state.content = '').timeout);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
};

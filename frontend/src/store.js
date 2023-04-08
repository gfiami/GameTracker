import { createStore } from "vuex";

const store = createStore({
  state() {
    return {
      logged: false,
      token: null,
      user_id: null,
    };
  },
  mutations: {
    login(state) {
      state.logged = true;
      localStorage.setItem("userState", true);
      state.token = localStorage.getItem("gameTrackerUserToken"); //username
      state.user_id = localStorage.getItem("user_id");
    },
    logout(state) {
      state.logged = false;
      localStorage.removeItem("userState");
      localStorage.removeItem("gameTrackerUserToken");
      localStorage.removeItem("user_id");
    },
  },
});

//isso aqui é para manter o usuario logado até mesm ose ele der f5, pois essas variaveis iriam se perder...
//assim conseguiremos recuperar o token e o status logged:
const currentUserState = localStorage.getItem("userState"); // true se estiver logado

if (currentUserState) {
  //se o usuario estiver logado, executa o mutation login e seta os valores para "logado"
  store.commit("login");
}

export default store;

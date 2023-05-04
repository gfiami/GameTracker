import { createStore } from "vuex";
import axios from "axios";

const store = createStore({
  state() {
    return {
      logged: false,
      token: null,
      user_id: null,
      personal_token: null,
    };
  },
  mutations: {
    login(state) {
      state.logged = true;
      localStorage.setItem("userState", true);
      state.token = localStorage.getItem("gameTrackerUserToken"); //username
      state.user_id = localStorage.getItem("user_id");
      state.personal_token = localStorage.getItem("personal_token");
    },
    logout(state) {
      localStorage.removeItem("userState");
      localStorage.removeItem("gameTrackerUserToken");
      localStorage.removeItem("user_id");
      localStorage.removeItem("personal_token");
      state.logged = false;
      state.token = null;
      state.user_id = null;
      state.personal_token = null;
    },
  },
});

//isso aqui é para manter o usuario logado até mesm ose ele der f5, pois essas variaveis iriam se perder...
//assim conseguiremos recuperar o token e o status logged:
const currentUserState = localStorage.getItem("user_id"); // true se estiver logado

if (currentUserState) {
  //se o usuario estiver logado, executa o mutation login e seta os valores para "logado"
  store.commit("login");
  //const validToken = checkToken();
  checkToken();
}
async function checkToken() {
  const id = store.state.user_id;
  const personal_token = store.state.personal_token;

  try {
    const response = await axios.get(
      `${process.env.VUE_APP_APIURL}check-token`,
      {
        params: {
          user_id: id,
        },
        headers: {
          Authorization: `Bearer ${personal_token}`,
        },
      }
    );
    const authorized = response.data.message;
    if (authorized == "Not Authorized") {
      store.commit("logout");
    }
  } catch (error) {
    console.log(error.data);
  }
}
export default store;

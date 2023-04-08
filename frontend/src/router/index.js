import { createRouter, createWebHistory } from "vue-router";
import store from "../store";

const routes = [
  {
    path: "/",
    name: "home",
    component: () =>
      import(/* webpackChunkName: "HomeView" */ "../views/HomeView.vue"),
  },
  {
    path: "/test-api",
    name: "testApi",
    component: () =>
      import(/* webpackChunkName: "TestApiView" */ "../views/TestApiView.vue"),
  },
  {
    path: "/testGame/:id",
    name: "specificGame",
    component: () =>
      import(
        /* webpackChunkName: "TestGameView" */ "../views/TestGameView.vue"
      ),
  },
  {
    path: "/login",
    name: "login",
    beforeEnter: (to, from, next) => {
      const loggedIn = store.state.logged;
      if (loggedIn) {
        next("/"); // aqui evitamos que usuario logado entre novametne no login
      } else {
        next(); // segue pro login se n tá logad
      }
    },
    component: () =>
      import(/* webpackChunkName: "LoginView" */ "../views/LoginView.vue"),
  },
  {
    path: "/register",
    name: "register",
    beforeEnter: (to, from, next) => {
      const loggedIn = store.state.logged;
      if (loggedIn) {
        next("/"); // aqui evitamos que usuario logado entre novametne no login
      } else {
        next(); // segue pro login se n tá logad
      }
    },
    component: () =>
      import(
        /* webpackChunkName: "RegisterView" */ "../views/RegisterView.vue"
      ),
  },
  {
    path: "/games",
    name: "games",
    component: () =>
      import(/* webpackChunkName: "GameView" */ "../views/GameView.vue"),
  },
  {
    path: "/profile/:id",
    name: "profile",
    component: () =>
      import(/* webpackChunkName: "ProfileView" */ "../views/ProfileView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

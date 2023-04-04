import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";

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
    component: () =>
      import(/* webpackChunkName: "LoginView" */ "../views/LoginView.vue"),
  },
  {
    path: "/register",
    name: "register",
    component: () =>
      import(
        /* webpackChunkName: "RegisterView" */ "../views/RegisterView.vue"
      ),
  },
  {
    //${this.$route.params.id}
    path: "/games",
    name: "games",
    component: () =>
      import(/* webpackChunkName: "GameView" */ "../views/GameView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

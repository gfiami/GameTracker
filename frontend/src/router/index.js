import { createRouter, createWebHistory } from "vue-router";
import store from "../store";

const routes = [
  {
    path: "/welcome",
    name: "welcome",
    beforeEnter: (to, from, next) => {
      const loggedIn = store.state.logged;
      if (loggedIn) {
        next();
      } else {
        next("/login");
      }
    },
    component: () =>
      import(
        /* webpackChunkName: "WelcomeView" */ "../views/Home/WelcomeView.vue"
      ),
  },
  {
    path: "/",
    name: "home",
    component: () =>
      import(/* webpackChunkName: "HomeView" */ "../views/Home/HomeView.vue"),
  },
  {
    path: "/games/game/:slug",
    name: "specificgame",
    component: () =>
      import(
        /* webpackChunkName: "SpecificGameView" */ "../views/SpecificGameView.vue"
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
    path: "/community",
    name: "community",
    component: () =>
      import(
        /* webpackChunkName: "CommunityView" */ "../views/CommunityView.vue"
      ),
  },
  {
    path: "/profile/:id",
    name: "profile",
    component: () =>
      import(/* webpackChunkName: "ProfileView" */ "../views/ProfileView.vue"),
  },
  {
    path: "/profile/edit",
    name: "editprofile",
    beforeEnter: (to, from, next) => {
      const loggedIn = store.state.logged;
      if (loggedIn) {
        next(); // aqui evitamos que usuario deslogado entre na pagina de edição
      } else {
        next("/login"); // segue pro login se n tá logad
      }
    },
    component: () =>
      import(
        /* webpackChunkName: "EditProfileView" */ "../views/EditProfileView.vue"
      ),
  },
  {
    path: "/profile/reviews/:id",
    name: "reviews",
    component: () =>
      import(/* webpackChunkName: "ReviewView" */ "../views/ReviewsView.vue"),
  },
  {
    path: "/profile/owned-games/:id",
    name: "owned",
    component: () =>
      import(
        /* webpackChunkName: "OwnedView" */ "../views/TrackedGames/OwnedView.vue"
      ),
  },
  {
    path: "/profile/favorite-games/:id",
    name: "favorite",
    component: () =>
      import(
        /* webpackChunkName: "FavoriteView" */ "../views/TrackedGames/FavoriteView.vue"
      ),
  },
  {
    path: "/profile/wishlist-games/:id",
    name: "wishlist",
    component: () =>
      import(
        /* webpackChunkName: "WishlistView" */ "../views/TrackedGames/WishlistView.vue"
      ),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

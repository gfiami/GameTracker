<template>
  <div id="nav">
    <router-link id="website-name" to="/">GameTracker</router-link>
    <div id="secondary-nav">
      <router-link
        v-if="logged"
        :to="{ name: 'profile', params: { id: $store.state.user_id } }"
        :key="$route.fullPath"
        >Profile</router-link
      >
      <router-link to="/games" :key="$route.fullPath">Games</router-link>
      <!--:key="$route.fullPath" FORÇA A rota a renderizar de novo-->
      <router-link v-if="!logged" to="/login">Login</router-link>
      <router-link v-if="!logged" to="/register">Register</router-link>
      <a @click="logout" v-if="logged">Logout</a>
    </div>
    <div id="nav-burger" v-if="!showMenu" @click="toggleMenu">
      <i class="fas fa-bars"></i>
    </div>
    <div id="display-burger" v-if="showMenu">
      <div id="nav-close" v-if="showMenu" @click="toggleMenu">
        <i class="fas fa-times"></i>
      </div>

      <div class="links-container">
        <router-link
          @click="toggleMenu"
          v-if="logged"
          :to="{ name: 'profile', params: { id: $store.state.user_id } }"
          :key="$route.fullPath"
          >Profile</router-link
        >
        <router-link @click="toggleMenu" to="/games" :key="$route.fullPath"
          >Games</router-link
        >
        <!--:key="$route.fullPath" FORÇA A rota a renderizar de novo-->
        <router-link @click="toggleMenu" v-if="!logged" to="/login"
          >Login</router-link
        >
        <router-link @click="toggleMenu" v-if="!logged" to="/register"
          >Register</router-link
        >
        <a @click="logout" v-if="logged">Logout</a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapMutations } from "vuex";

export default {
  name: "Navbar",

  data() {
    return {
      showMenu: false,
    };
  },
  computed: {
    //esse logged é chamado semrpe pq temos um v-if no router link que "checa" o status dele, se mudar vai alterar
    logged() {
      return this.$store.state.logged;
    },
  },
  methods: {
    ...mapMutations(["logout"]),
    toggleMenu() {
      this.showMenu = !this.showMenu;
    },
    logout() {
      this.showMenu = false;
      this.deleteTokenDatabase();
      this.$store.commit("logout", false);
      localStorage.removeItem("gameTrackerUserToken");
      localStorage.removeItem("user_id");
      this.$router.push({
        path: "/",
        query: {
          messageLogout:
            "Your logout was successful. Come back soon to track and review your games on GameTracker!",
        },
      });
    },
    username(name) {
      this.userName = name;
    },
    async deleteTokenDatabase() {
      //removes token from database
      const user_id = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}logout`,
          {
            params: {
              user_id: user_id,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        console.log(response.data);
      } catch (error) {
        console.log(error.response.data.error);
        console.log("Entrou no erro do logou");
      }
    },
  },
};
</script>

<style scoped>
#nav {
  width: 100%;
  background-color: #6842ff;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 15px 50px;
}
#nav-burger {
  display: none;
  font-size: 4vh;
}
#nav #website-name {
  font-size: 32px;
  font-weight: 800;
  margin: auto;
  margin-left: 0;
  color: #fff;
}
#nav a {
  color: #fff;
  text-decoration: none;
  margin: 10px;
  transition: 0.4s;
  cursor: pointer;
}
#nav a:hover {
  color: #161b3a;
}
#nav a.router-link-exact-active {
  color: #161b3a;
}
@media screen and (max-width: 768px) {
  #nav-close {
    position: fixed;
    top: 2.7vh;
    right: 13vw;
    z-index: 999;
    font-size: 4vh;
  }
  #secondary-nav {
    display: none;
  }
  #nav-burger {
    display: block;
  }
  #display-burger {
    position: absolute;
    background-color: #161b3a;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;
    opacity: 95%;
  }
  #display-burger a.router-link-exact-active {
    color: white;
  }
  .links-container {
    position: fixed;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    gap: 10vh;
    font-size: 3vh;
  }
}
</style>

<template>
  <div id="nav">
    <router-link id="website-name" to="/">GameTracker</router-link>
    <router-link v-if="logged" to="/profile">{{ username }}</router-link>
    <router-link to="/games">Games</router-link>
    <router-link v-if="!logged" to="/login">Login</router-link>
    <router-link v-if="!logged" to="/register">Register</router-link>
    <a @click="logout" v-if="logged">Logout</a>
  </div>
</template>

<script>
import { mapMutations } from "vuex";

export default {
  name: "Navbar",
  data() {
    return {
      username: "Profile",
    };
  },
  computed: {
    //esse logged é chamado semrpe pq temos um v-if no router link que "checa" o status dele, se mudar vai alterar
    logged() {
      this.username = this.getUserName(this.$store.state.logged);
      return this.$store.state.logged;
    },
  },
  methods: {
    ...mapMutations(["logout"]),

    logout() {
      this.$store.commit("logout", false);
      localStorage.removeItem("gameTrackerUserToken");
      localStorage.removeItem("user_id");

      this.$router.push("/");
    },
    getUserName(logged) {
      if (logged) {
        return this.$store.state.token;
      }
    },
  },
};
</script>

<style scoped>
#nav {
  background-color: #6842ff;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 15px 50px;
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
</style>

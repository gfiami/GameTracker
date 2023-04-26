<template>
  <div class="main-wrapper">
    <SearchGeneric :searchPlaceholder="placeholder" />
    <div class="users-container" v-if="users">Usuários carregados</div>
    <Loading v-if="loadingUsers" />
    <div class="error" v-if="!users && !loadingUsers">
      <div class="user-doesnt-exist">
        <h1>404</h1>
        <p>No users found</p>
        <i class="fas fa-users-slash"></i>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import SearchGeneric from "../components/SearchGeneric.vue";
import Loading from "../components/Loading.vue";

export default {
  name: "CommunityView",
  components: {
    SearchGeneric,
    Loading,
  },
  data() {
    return {
      users: null,
      loadingUsers: true,
      placeholder: "Search users",
    };
  },
  methods: {
    async getUsers() {
      this.loadingUsers = false;
    },
  },
  mounted() {
    this.getUsers();
  },
};
</script>

<style scoped>
/* user 404 */
.user-doesnt-exist {
  text-align: center;
  position: absolute;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-shadow: 2px 2px #000;
  padding: 2px;
}
.user-doesnt-exist h1 {
  font-weight: bolder;
  font-size: 44px;
  padding: 2px;
}
.user-doesnt-exist p {
  font-weight: 400;
  font-size: 22px;
  padding: 4px;
}
.user-doesnt-exist i {
  font-size: 5vh;
  padding: 2px;
}
</style>

<template>
  <div class="main-wrapper">
    <h1 class="title">Explore community</h1>
    <SearchGeneric :searchPlaceholder="placeholder" />
    <div class="users-container" v-if="users">
      <div v-for="user in users" :key="user.id" class="user-container">
        <router-link
          :to="{ name: 'profile', params: { id: user.id } }"
          :key="$route.fullPath"
        >
          <img
            class="user-image"
            :src="
              user.image
                ? `${imgUrl}${user.image}`
                : require('@/assets/def-avatar-profile.jpg')
            "
            alt="user.name"
          />
        </router-link>
        <router-link
          :to="{ name: 'profile', params: { id: user.id } }"
          :key="$route.fullPath"
        >
          <div class="username">{{ user.name }}</div>
        </router-link>
      </div>
    </div>
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
      imgUrl: `${process.env.VUE_APP_IMAGE_URL}`,
    };
  },
  methods: {
    async getUsers() {
      this.loadingUsers = true;
      const response = await axios.get(`${process.env.VUE_APP_APIURL}users`);
      if (response.data.length == 0) {
        this.loadingUsers == false;
        this.users = null;
        return false;
      }
      this.users = response.data;
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
/* end users 404 */
.title {
  font-size: 5vh;
  font-weight: 800;
  text-align: center;
  width: 100%;
  margin-top: 2.1vh;
}
.users-container {
  width: 90vw;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  margin-top: 4vh;
}
.user-container {
  border-radius: 12px;
  box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.5), 0 0 40px 10px rgba(0, 0, 0, 0.2);
  background-color: #330066;
  padding: 1vh 0vh;
  padding-left: 3vw;
  width: 100%;
  height: 10vh;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 2.3vh;
}
.user-container a {
  text-decoration: none;
  cursor: pointer;
  color: white;
  align-self: flex-start;
}
.user-image {
  height: 8vh;
  width: 8vh;
  border-radius: 50%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
}
.username {
  font-size: 2.3vh;
  margin-left: 2vw;
  align-self: flex-start;
}
@media screen and (min-width: 768px) {
  .users-container {
    width: 40vw;
  }
}
</style>

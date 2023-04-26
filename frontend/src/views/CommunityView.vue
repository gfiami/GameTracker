<template>
  <div class="main-wrapper">
    <h1 class="title">Explore community</h1>
    <SearchGeneric
      :searchPlaceholder="placeholder"
      @searching="searching"
      @changeOrder="changeOrder"
    />
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
    <PaginationReview
      v-if="users"
      :totalPages="totalPages"
      :currentPage="currentPage"
      @goToPage="goToPage"
    />
  </div>
</template>

<script>
import axios from "axios";
import SearchGeneric from "../components/SearchGeneric.vue";
import Loading from "../components/Loading.vue";
import PaginationReview from "../components/PaginationReview.vue";

export default {
  name: "CommunityView",
  components: {
    SearchGeneric,
    Loading,
    PaginationReview,
  },
  data() {
    return {
      users: null,
      totalPages: null,
      loadingUsers: true,
      placeholder: "Search users by name",
      imgUrl: `${process.env.VUE_APP_IMAGE_URL}`,
      currentPage: 1,
      search: "",
      order: "asc",
    };
  },

  methods: {
    async getUsers(search) {
      console.log(search);
      this.loadingUsers = true;
      this.users = null;
      const response = await axios.get(`${process.env.VUE_APP_APIURL}users`, {
        params: {
          search: search,
          page: this.currentPage,
          order: this.order,
        },
      });
      console.log(response.data.data.length);
      if (response.data.data.length == 0) {
        this.loadingUsers = false;
        this.users = null;
        return false;
      }
      this.totalPages = response.data.last_page;
      this.users = response.data.data;
      this.loadingUsers = false;
    },
    goToPage(page) {
      window.scrollTo(0, 0);
      this.currentPage = page;
      this.getUsers(this.search);
    },
    searching(search) {
      console.log(search);
      this.search = search;
      this.getUsers(search);
    },
    changeOrder(order) {
      this.order = order;
      this.getUsers(this.search);
    },
  },
  mounted() {
    this.getUsers(this.search);
  },
};
</script>

<style scoped>
/* user 404 */

.user-doesnt-exist {
  margin-top: 4vh;

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

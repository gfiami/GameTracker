<template>
  <div class="main-wrapper">
    <div class="explore" v-if="!showFriendList">
      <h1 class="title">Explore community</h1>
      <div class="button-container">
        <button class="fc-button" @click="invertShowingFriends" v-if="logged">
          <i class="fas fa-address-book"></i>Friends List
        </button>
      </div>

      <SearchBar
        :key="resetSearch"
        :searchPlaceholder="placeholder"
        @searching="searching"
        @changeOrder="changeOrder"
      />
      <div class="users-container" v-if="users">
        <div
          v-for="user in users"
          :key="user.id"
          class="user-container"
          v-show="!($store.state.user_id == user.id)"
        >
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
          <div class="friend-interaction">
            <button>Add As Friend</button>
          </div>
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
      <Pagination
        v-if="users"
        :totalPages="totalPages"
        :currentPage="currentPage"
        @goToPage="goToPage"
      />
    </div>
    <!-- friend list -->
    <div class="friends" v-if="showFriendList">
      <h1 class="title">Your Friends</h1>
      <div class="button-container">
        <button class="fc-button" @click="invertShowingFriends">
          <i class="fas fa-user-plus"></i>Add a Friend
        </button>
      </div>
      <SearchBar
        :key="resetSearch"
        :searchPlaceholder="placeholderFriends"
        @searching="searching"
        @changeOrder="changeOrder"
      />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import SearchBar from "../components/Tools/SearchBar.vue";
import Loading from "../components/Tools/Loading.vue";
import Pagination from "../components/Tools/Pagination.vue";

export default {
  name: "CommunityView",
  components: {
    SearchBar,
    Loading,
    Pagination,
  },
  data() {
    return {
      users: null,
      totalPages: null,
      loadingUsers: true,
      placeholder: "Search users by name",
      placeholderFriends: "Search friends by name",
      imgUrl: `${process.env.VUE_APP_IMAGE_URL}`,
      currentPage: 1,
      search: "",
      order: "asc",
      showFriends: true,
    };
  },
  watch: {
    "$route.fullPath"(newValue) {
      if (newValue == "/community") {
        this.currentPage = 1;
        this.order = "asc";
        this.search = "";
        this.getUsers(this.search);
      }
    },
  },
  computed: {
    resetSearch() {
      if (this.$route.fullPath == "/community") {
        return this.$route.fullPath;
      }
    },
    logged() {
      return this.$store.state.logged;
    },
    showFriendList() {
      if (!this.logged) {
        this.showFriends = false;
        return false;
      }
      if (this.logged && this.showFriends) {
        return true;
      } else {
        return false;
      }
    },
  },
  methods: {
    async getUsers(search) {
      this.loadingUsers = true;
      this.users = null;
      const response = await axios.get(`${process.env.VUE_APP_APIURL}users`, {
        params: {
          search: search,
          page: this.currentPage,
          order: this.order,
        },
      });
      if (response.data.data.length == 0) {
        this.loadingUsers = false;
        this.users = null;
        return false;
      }
      this.totalPages = response.data.last_page;
      this.users = response.data.data;
      this.loadingUsers = false;
    },
    invertShowingFriends() {
      this.showFriends = !this.showFriends;
    },
    goToPage(page) {
      window.scroll(0, 0);
      this.currentPage = page;
      this.getUsers(this.search);

      const order = this.order;
      const search = this.search;
      this.$router.push({
        path: "/community",
        query: { page, search, order },
      });
    },
    searching(search) {
      this.search = search;
      this.currentPage = 1;
      this.getUsers(search);

      const order = this.order;
      const page = this.currentPage;
      this.$router.push({
        path: "/community",
        query: { page, search, order },
      });
    },
    changeOrder(order) {
      this.order = order;
      this.getUsers(this.search);
      const search = this.search;
      const page = this.currentPage;
      this.$router.push({
        path: "/community",
        query: { page, search, order },
      });
    },
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    this.currentPage = parseInt(urlParams.get("page")) || 1;
    this.search = urlParams.get("search") || "";
    this.order = urlParams.get("order") || "asc";
    this.getUsers(this.search);
  },
};
</script>

<style scoped>
/* user 404 */
.button-container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.fc-button {
  background: #1abc9c;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}
.fc-button:hover {
  color: #4c1bbc;
}
.friend-interaction {
  align-self: flex-end;
  margin-left: auto;
  padding-right: 2vw;
}
.friend-interaction button {
  background: #1abc9c;
  padding: 1.5vh 1.5vw;
  border-radius: 5px;
  width: 25vw;
  cursor: pointer;
  font-size: 1.3vh;
  font-weight: 700;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}

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
  .fc-button {
    width: 15vw;
  }
  .friend-interaction button {
    width: 10vw;
  }
}
</style>

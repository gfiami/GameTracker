<template>
  <div class="main-wrapper">
    <!-- explorar / adicionar amigos -->
    <div class="explore" v-if="showCommunity">
      <h1 class="title">Explore community</h1>
      <div class="button-container" v-if="logged">
        <button class="fc-button" @click="invertShowing('list')">
          <i class="fas fa-address-book"></i>Friends List
        </button>
        <button class="fc-button" @click="invertShowing('request')">
          <i class="fas fa-hourglass"></i>Friend Requests
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
          v-show="
            !($store.state.user_id == user.id) &&
            !this.friends.includes(user.id)
          "
        >
          <router-link
            :to="{
              name: 'profile',
              params: { id: user.id },
              query: { redirect: $route.fullPath },
            }"
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
          <div class="secondary-container-profile">
            <router-link
              :to="{
                name: 'profile',
                params: { id: user.id },
                query: { redirect: $route.fullPath },
              }"
              :key="$route.fullPath"
            >
              <div class="username">{{ user.name }}</div>
            </router-link>
            <div class="friend-interaction" v-if="logged">
              <button
                class="cancel-friend"
                @click="removeFriend(user.id)"
                v-if="this.friends.includes(user.id)"
              >
                <i class="fas fa-user-minus"></i> Remove Friend
              </button>
              <button
                v-if="Object.values(this.requestReceived).includes(user.id)"
                class="pending"
                @click="acceptFriend(user.id)"
              >
                <i class="fas fa-user-check"></i> Accept
              </button>
              <button
                v-if="Object.values(this.requestReceived).includes(user.id)"
                class="cancel-request pending"
                @click="declineFriend(user.id)"
              >
                <i class="fas fa-user-times"></i> Decline
              </button>
              <button
                @click="addFriend(user.id)"
                v-else-if="
                  !this.requestSend.includes(user.id) &&
                  !this.friends.includes(user.id)
                "
              >
                <i class="fas fa-user-plus"></i> Add Friend
              </button>
              <button
                class="cancel-request"
                @click="cancelRequest(user.id)"
                v-else-if="
                  this.requestSend.includes(user.id) &&
                  !this.friends.includes(user.id)
                "
              >
                <i class="fas fa-user-clock"></i> Cancel Request
              </button>
            </div>
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
    <!-- fim de explorar / adicionar amigos -->

    <!-- friend list -->
    <div class="friends" v-if="logged && showFriends">
      <h1 class="title">Your Friends</h1>
      <div class="button-container">
        <button class="fc-button" @click="invertShowing('add')">
          <i class="fas fa-user-plus"></i>Add Friends
        </button>
        <button class="fc-button" @click="invertShowing('request')">
          <i class="fas fa-hourglass"></i>Friend Requests
        </button>
      </div>
      <div class="users-container friend-list" v-if="allUsers">
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container"
          v-show="this.friends.includes(user.id)"
        >
          <router-link
            :to="{
              name: 'profile',
              params: { id: user.id },
              query: { redirect: $route.fullPath },
            }"
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
          <div class="secondary-container-profile">
            <router-link
              :to="{
                name: 'profile',
                params: { id: user.id },
                query: { redirect: $route.fullPath },
              }"
              :key="$route.fullPath"
            >
              <div class="username">{{ user.name }}</div>
            </router-link>
            <div class="friend-interaction" v-if="logged">
              <button
                class="cancel-friend"
                @click="removeFriend(user.id)"
                v-if="this.friends.includes(user.id)"
              >
                <i class="fas fa-user-minus"></i> Remove Friend
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- fim da friend list -->

    <!-- pending lists -->
    <div class="requests" v-if="logged && showPending">
      <h1 class="title">Friend Requests</h1>
      <div class="button-container">
        <button class="fc-button" @click="invertShowing('add')">
          <i class="fas fa-user-plus"></i>Add Friends
        </button>
        <button class="fc-button" @click="invertShowing('list')">
          <i class="fas fa-address-book"></i>Friends List
        </button>
      </div>
      <div class="button-container">
        <button
          class="request-button"
          @click="invertRequestDisplay"
          v-if="!showingSent"
        >
          Show sent requests
        </button>
        <button
          class="request-button"
          @click="invertRequestDisplay"
          v-if="showingSent"
        >
          Show received requests
        </button>
      </div>
      <!-- requests recebidos -->
      <h3 class="request-title" v-if="allUsers && !showingSent">
        All received requests
      </h3>
      <div class="users-container request-list" v-if="allUsers && !showingSent">
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container"
          v-show="Object.values(this.requestReceived).includes(user.id)"
        >
          <router-link
            :to="{
              name: 'profile',
              params: { id: user.id },
              query: { redirect: $route.fullPath },
            }"
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
          <div class="secondary-container-profile">
            <router-link
              :to="{
                name: 'profile',
                params: { id: user.id },
                query: { redirect: $route.fullPath },
              }"
              :key="$route.fullPath"
            >
              <div class="username">{{ user.name }}</div>
            </router-link>
            <div class="friend-interaction">
              <button class="pending" @click="acceptFriend(user.id)">
                <i class="fas fa-user-check"></i> Accept
              </button>
              <button
                class="cancel-request pending"
                @click="declineFriend(user.id)"
              >
                <i class="fas fa-user-times"></i> Decline
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- fim dos recebidos -->
      <!-- inicio dos enviados -->
      <h3 class="request-title" v-if="allUsers && showingSent">
        All sent requests
      </h3>
      <div class="users-container request-list" v-if="allUsers && showingSent">
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container"
          v-show="Object.values(this.requestSend).includes(user.id)"
        >
          <router-link
            :to="{
              name: 'profile',
              params: { id: user.id },
              query: { redirect: $route.fullPath },
            }"
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
          <div class="secondary-container-profile">
            <router-link
              :to="{
                name: 'profile',
                params: { id: user.id },
                query: { redirect: $route.fullPath },
              }"
              :key="$route.fullPath"
            >
              <div class="username">{{ user.name }}</div>
            </router-link>
            <div class="friend-interaction">
              <button
                class="cancel-request pending"
                @click="cancelRequest(user.id)"
              >
                <i class="fas fa-user-clock"></i> Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- fim dos enviados-->
    </div>
    <!-- fim da pending lists -->
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
      allUsers: null,
      requestSend: [],
      requestReceived: [],
      friends: [],
      totalPages: null,
      loadingUsers: true,
      placeholder: "Search users by name",
      placeholderFriends: "Search friends by name",
      imgUrl: `${process.env.VUE_APP_IMAGE_URL}`,
      currentPage: 1,
      search: "",
      order: "asc",
      showCommunity: true,
      showFriends: false,
      showPending: false,
      showingSent: false,
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
  },
  methods: {
    async getUsers(search) {
      this.loadingUsers = true;
      this.users = null;
      const userId = this.$store.state.user_id || 0;
      const response = await axios.get(`${process.env.VUE_APP_APIURL}users`, {
        params: {
          search: search,
          page: this.currentPage,
          order: this.order,
          user_id: userId,
        },
      });
      if (response.data.requestsSend !== undefined) {
        this.requestSend = response.data.requestsSend;
      }
      if (response.data.requestsReceived !== undefined) {
        this.requestReceived = response.data.requestsReceived;
      }
      if (response.data.allUsers == undefined) {
        this.loadingUsers = false;
        this.allUsers = null;
      } else {
        this.allUsers = response.data.allUsers;
      }
      if (response.data.users.data.length == 0) {
        this.loadingUsers = false;
        this.users = null;
        return false;
      }
      if (this.logged) {
        this.friends = response.data.friends;
      }
      this.totalPages = response.data.users.last_page;
      this.users = response.data.users.data;
      this.loadingUsers = false;
    },
    invertShowing(string) {
      switch (string) {
        case "list":
          this.showFriends = true;
          this.showCommunity = false;
          this.showPending = false;
          break;
        case "request":
          this.showFriends = false;
          this.showCommunity = false;

          this.showPending = true;
          break;
        case "add":
          this.showCommunity = true;
          this.showFriends = false;
          this.showPending = false;
          break;
      }
    },

    invertRequestDisplay() {
      this.showingSent = !this.showingSent;
    },
    async addFriend(receiver) {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}add-friend`,
          {
            user_id: userId,
            request_to: receiver,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.requestSend = response.data;
      } catch (error) {
        console.log(error);
      }
    },
    async cancelRequest(receiver) {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;

      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}cancel-friend-request`,
          {
            params: {
              user_id: userId,
              request_to: receiver,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        console.log(response.data);
        this.requestSend = response.data;
      } catch (error) {
        console.log(error);
      }
    },
    async declineFriend(sender) {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}decline-friend`,
          {
            params: {
              user_id: userId,
              sender: sender,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        console.log(response.data);
        this.requestReceived = response.data;
      } catch (error) {
        console.log(error);
      }
    },
    async acceptFriend(sender) {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      try {
        const response = await axios.put(
          `${process.env.VUE_APP_APIURL}accept-friend`,
          {
            user_id: userId,
            sender: sender,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        console.log(response.data);
        this.friends = response.data.friends;
        this.requestReceived = response.data.requestsReceived;
      } catch (error) {
        console.log(error);
      }
    },
    async removeFriend(friend) {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-friend`,
          {
            params: {
              user_id: userId,
              friend: friend,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.friends = response.data.friends;
      } catch (error) {
        console.log(error);
      }
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
.friends,
.requests {
  margin-bottom: 5vh;
}

.request-title {
  width: 90vh;
  margin: 0 auto;
  padding: 0 2vh;
}
.button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2vw;
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
  color: rgba(54, 30, 148, 0.9);
}
.request-button {
  margin-top: 1vh;
  background: #bb8ece;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.2vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}
.request-button:hover {
  color: rgba(54, 30, 148, 0.9);
}
.friend-interaction {
  text-align: center;
  align-self: flex-end;
  margin-left: auto;
  margin-right: 1.5vw;
}
.friend-interaction button {
  text-align: center;
  background: #1abc9c;
  padding: 1vh 1.3vh;
  border-radius: 5px;
  width: 25vw;
  cursor: pointer;
  font-size: 1.3vh;
  font-weight: 700;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
  height: 100%;
}
.pending {
  margin-right: 0.2vw;
}
.secondary-container-profile {
  width: 100%;
  display: flex;
  flex-direction: column;
}
.friend-interaction .cancel-friend {
  padding: 0.5vh 0.5vh;
}
.friend-interaction .cancel-friend {
  background: #bc1a3a;
}
.friend-interaction .cancel-request {
  background: #bc4b1a;
}
.friend-interaction .pending {
  width: 18vw;
}
.friend-interaction button:hover {
  color: rgba(54, 30, 148, 0.9);
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
  justify-content: space-between;
  margin-top: 4vh;
}
div .friend-list,
div .request-list {
  height: 70vh;
  padding: 3vh 3vh;
  overflow-y: scroll;
  justify-content: flex-start;
  display: block;
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
  .users-container,
  .request-title {
    width: 40vw;
  }
  .fc-button {
    width: 15vw;
  }
  .friend-interaction button {
    width: 10vw;
  }
  .friend-interaction .pending {
    width: 8vw;
  }
  .request-button {
    width: 14vw;
  }
}
</style>

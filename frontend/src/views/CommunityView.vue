<template>
  <div class="main-wrapper">
    <!-- explorar / adicionar amigos -->
    <div class="tracker offline" v-if="!logged">
      <p class="offline-text">
        <router-link
          :to="{ path: '/login', query: { redirect: $route.fullPath } }"
          >Login</router-link
        >
        or
        <router-link
          :to="{ path: '/register', query: { redirect: $route.fullPath } }"
          >Register</router-link
        >
        to interact with our community!
      </p>
    </div>
    <div class="explore" v-if="showCommunity">
      <h1 class="title">Explore community</h1>
      <div class="button-container" v-if="logged">
        <button class="fc-button" @click="invertShowing('list')">
          <i class="fas fa-address-book"></i>Friends List
        </button>
        <button
          class="fc-button received-container"
          @click="invertShowing('request')"
        >
          <i class="fas fa-hourglass"></i>Friend Requests
          <span v-if="!receivedEmpty && !loadingUsers" class="notification"
            ><i class="fas fa-exclamation"></i
          ></span>
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
    <!--confirmation remove friend -->
    <div class="confirmations" v-if="showRemoveConfirm">
      <div class="confirm-option">
        <h3 class="confirm-title">Remove Friend</h3>
        <p class="confirm-text">
          Remove {{ removeFriendName }} from your friends?
        </p>
        <div class="button-container">
          <button
            class="confirm-button danger"
            @click="removeFriend(removeFriendId)"
          >
            Remove
          </button>
          <button class="cancel-button" @click="cancelConfirm">Cancel</button>
        </div>
      </div>
    </div>
    <!-- end confirmation remove friend -->
    <!-- friend list -->
    <div class="friends" v-if="logged && showFriends">
      <h1 class="title">
        Your Friends
        <span v-if="!friendsEmpty && !loadingUsers"
          >({{ friendsCounter }})</span
        >
      </h1>
      <div class="button-container">
        <button class="fc-button" @click="invertShowing('add')">
          <i class="fas fa-user-plus"></i>Add Friends
        </button>
        <button
          class="fc-button received-container"
          @click="invertShowing('request')"
        >
          <i class="fas fa-hourglass"></i>Friend Requests
          <span v-if="!receivedEmpty && !loadingUsers" class="notification"
            ><i class="fas fa-exclamation"></i
          ></span>
        </button>
      </div>
      <div class="empty-list" v-if="friendsEmpty">
        <div class="empty-item">
          Your friendlist is empty <i class="fas fa-heart-broken"></i>
        </div>
      </div>
      <div class="users-container friend-list" v-if="allUsers && !friendsEmpty">
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container-primary"
        >
          <div class="user-container" v-if="this.friends.includes(user.id)">
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
                  @click="showConfirm(user.name, user.id)"
                  v-if="this.friends.includes(user.id)"
                >
                  <i class="fas fa-user-minus"></i> Remove Friend
                </button>
              </div>
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
          class="request-button received-container"
          @click="invertRequestDisplay"
          v-if="showingSent"
        >
          Show received requests
          <span v-if="!receivedEmpty && !loadingUsers" class="notification"
            ><i class="fas fa-exclamation"></i
          ></span>
        </button>
      </div>
      <!-- requests recebidos -->
      <h3 class="request-title" v-if="!showingSent">
        All received requests
        <span v-if="!receivedEmpty && !loadingUsers"
          >({{ receivedCounter }})</span
        >
      </h3>
      <div
        class="empty-list"
        v-if="(receivedEmpty && !showingSent) || (!allUsers && !showingSent)"
      >
        <div class="empty-item">No requests found.</div>
      </div>

      <div
        class="users-container request-list"
        v-if="allUsers && !showingSent && !receivedEmpty && !receivedEmpty"
      >
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container-primary"
        >
          <div
            class="user-container"
            v-if="Object.values(this.requestReceived).includes(user.id)"
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
      </div>
      <!-- fim dos recebidos -->
      <!-- inicio dos enviados -->
      <h3 class="request-title" v-if="showingSent">All sent requests</h3>
      <div
        class="empty-list"
        v-if="(sentEmpty && showingSent) || (!allUsers && showingSent)"
      >
        <div class="empty-item">No requests found.</div>
      </div>
      <div
        class="users-container request-list"
        v-if="allUsers && showingSent && !sentEmpty"
      >
        <div
          v-for="user in allUsers"
          :key="user.id"
          class="user-container-primary"
        >
          <div
            class="user-container"
            v-if="Object.values(this.requestSend).includes(user.id)"
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
      friendsEmpty: null,
      receivedEmpty: null,
      sentEmpty: null,
      receivedCounter: 0,
      friendsCounter: 0,
      showRemoveConfirm: false,
      removeFriendName: "",
      removeFriendId: null,
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
      console.log(userId);
      console.log(this.$store.state.personal_token);

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
        response.data.requestsSend.length == 0
          ? (this.sentEmpty = true)
          : (this.sentEmpty = false);
      }
      console.log(this.sentEmpty);
      if (response.data.requestsReceived !== undefined) {
        this.requestReceived = response.data.requestsReceived;
        this.receivedCounter = response.data.requestsReceived.length;

        this.receivedCounter == 0
          ? (this.receivedEmpty = true)
          : (this.receivedEmpty = false);
      }
      console.log(this.receivedEmpty);
      if (response.data.allUsers == undefined) {
        this.loadingUsers = false;
        this.allUsers = null;
      } else {
        this.allUsers = response.data.allUsers;
      }
      if (this.logged) {
        this.friends = response.data.friends;
        this.friendsCounter = response.data.friends.length;
        this.friendsCounter == 0
          ? (this.friendsEmpty = true)
          : (this.friendsEmpty = false);
      }
      if (response.data.users.data.length == 0) {
        this.loadingUsers = false;
        this.users = null;
        return false;
      }

      this.totalPages = response.data.users.last_page;
      this.users = response.data.users.data;
      console.log(this.users);

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
        response.data.length == 0
          ? (this.sentEmpty = true)
          : (this.sentEmpty = false);
      } catch (error) {
        this.getUsers(this.search);
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
        response.data.length == 0
          ? (this.sentEmpty = true)
          : (this.sentEmpty = false);
      } catch (error) {
        this.getUsers(this.search);
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
        this.receivedCounter = response.data.requestsReceived.length;

        this.receivedCounter == 0
          ? (this.receivedEmpty = true)
          : (this.receivedEmpty = false);
      } catch (error) {
        this.getUsers(this.search);

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
        this.receivedCounter = response.data.requestsReceived.length;
        this.friendsCounter = response.data.friends.length;

        this.receivedCounter == 0
          ? (this.receivedEmpty = true)
          : (this.receivedEmpty = false);
        this.friendsCounter == 0
          ? (this.friendsEmpty = true)
          : (this.friendsEmpty = false);
      } catch (error) {
        this.getUsers(this.search);

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
        this.friendsCounter = response.data.friends.length;
        this.friendsCounter == 0
          ? (this.friendsEmpty = true)
          : (this.friendsEmpty = false);
      } catch (error) {
        console.log(error);
      }
      this.getUsers(this.search);

      this.cancelConfirm();
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
    cancelConfirm() {
      this.removeFriendName = "";
      this.removeFriendId = null;
      this.showRemoveConfirm = false;
    },
    showConfirm(name, id) {
      this.removeFriendName = name;
      this.removeFriendId = id;
      this.showRemoveConfirm = true;
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
.empty-item {
  color: #ffffffa4;
  font-weight: 300;
  border-radius: 6px;
  font-size: 12px;
  background-color: rgba(48, 25, 189, 0.62);
  width: 40%;
  padding: 10px;
  margin-bottom: 0;
  text-align: center;
}
/*botão de confirmar deleção de amigo */
.confirmations {
  position: absolute;
  background-color: #161b3a;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  z-index: 998;
  opacity: 95%;
}
.confirm-option {
  margin: 0 auto;
  position: fixed;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 50%;
}
.confirm-text {
  margin: 2vh;
}
.confirm-button {
  background: #1abc9c;
  color: #23272a;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
}

.cancel-button {
  background: rba(255, 255, 255, 0.596);
  color: #23272a;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
}
.cancel-button:hover {
  background-color: white;
}
.danger {
  background: #bc1a3a;
  color: white;
}
.danger:hover {
  background: #f3224b;
}
/*fim deleção*/
.received-container {
  position: relative;
  display: inline-block;
}
.notification {
  position: absolute;
  top: -10px;
  right: -10px;
  padding: 5px 10px;
  border-radius: 50%;
  background: red;
  color: white;
  font-size: 1.5vh;
}
.notification i {
  font-weight: 600;
}
/* not logged message tracker */
.tracker {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  justify-content: center;
  gap: 30px;
}
div .offline {
  width: 100%;
}
.offline-text {
  text-align: center;
}

.offline {
  background-color: rgba(0, 0, 0, 0.315);
  padding: 20px;
  font-size: 2.2vh;
  margin: 2vh 0;
}

.offline a {
  color: #6842ff;
  font-weight: 500;
  text-decoration: none;
  transition: 0.4s;
}
.offline a:hover {
  color: #d9ff42;
}
.tracker a {
  font-size: 2.2vh;
}
.friends,
.requests {
  margin-bottom: 5vh;
}

.request-title,
.empty-list {
  width: 90%;
  margin: 0 auto;
  margin-top: 2vh;
  padding: 0 2vh;
}
.empty-list {
  display: flex;
  justify-content: center;
  align-items: center;
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
  border-radius: 12px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}
.fc-button:hover {
  background: #22ebc2;
}
.request-button {
  margin-top: 1vh;
  background: #bb8ece;
  padding: 2vh 2vw;
  border-radius: 12px;
  width: 35vw;
  cursor: pointer;
  font-size: 1.2vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
}
.request-button:hover {
  background: #dea9f5;
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
.friend-interaction button:hover {
  background: #22ebc2;
}
.friend-interaction .cancel-friend {
  background: #bc1a3a;
}
.friend-interaction .cancel-friend:hover {
  background: #f3224b;
}
.friend-interaction .cancel-request {
  background: #bc4b1a;
}
.friend-interaction .cancel-request:hover {
  background: #f05f21;
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
  background: #bc1a3a;
}
.friend-interaction .cancel-request {
  background: #bc4b1a;
}
.friend-interaction .pending {
  width: 18vw;
}

.user-doesnt-exist {
  margin: 0 auto;
  margin-top: 6vh;
  margin-bottom: 3vh;
  text-align: center;

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
  margin-top: 2vh;
}
div .friend-list,
div .request-list {
  height: 70vh;
  padding: 2.5vh 2.5vh;
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
  margin-left: 0.8vw;
  align-self: flex-start;
}
@media screen and (min-width: 768px) {
  .confirm-button,
  .cancel-button {
    width: 15vw;
  }
  .users-container,
  .request-title,
  .empty-list {
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
  div .friend-list,
  div .request-list {
    padding: 3.5vh 3.5vh;
  }
}
</style>

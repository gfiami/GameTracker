<template>
  <div class="main-wrapper">
    <div class="back-route" v-if="redirect">
      <router-link :to="redirect">
        <i class="fas fa-caret-left"></i> Back</router-link
      >
    </div>
    <div v-if="user">
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
          to add new friends!
        </p>
      </div>
      <!-- checar se o user existe -->
      <div class="personnal-info">
        <img class="profile-image" :src="userImage" alt="" />
        <div class="user-edit-container">
          <h1 class="username">{{ user.name }}</h1>
          <div class="check-friend" v-if="!checkOwnProfile && logged">
            <div class="friends" v-if="friends">
              <i class="fas fa-gamepad"></i> You and <i>{{ user.name }}</i> are
              friends!
            </div>
            <div class="sent" v-if="sent">
              <i class="fas fa-user-clock"></i> Friend request sent
            </div>
            <div class="received" v-if="received">
              <i class="fas fa-user-clock"></i> Friend request received
            </div>

            <select
              name=""
              v-model="profileInteraction"
              @change="confirmInteraction"
              class="fa"
            >
              <option selected disabled value="..." class="fa">
                <span class="fa">&#xf4fe;</span>
                &#x200B;
                <span id="reset-font">Interact</span>
              </option>
              <option v-if="friends" value="remove">Unfriend</option>
              <option v-if="!friends && !received && !sent" value="add">
                Add as Friend
              </option>
              <option v-if="sent" value="cancel">Cancel Friend Request</option>
              <option v-if="received" value="decline">
                Decline Friend Request
              </option>
              <option v-if="received" value="accept">
                Accept Friend Request
              </option>
            </select>
            <div class="confirmations" v-if="showConfirm">
              <div class="confirm-option" v-if="showAddConfirm">
                <h3 class="confirm-title">Add Friend</h3>
                <p class="confirm-text">
                  Send friend request to {{ user.name }}?
                </p>
                <div class="button-container">
                  <button
                    class="confirm-button"
                    @click="profileSelectInteraction"
                  >
                    Send
                  </button>
                  <button class="cancel-button" @click="cancelConfirm">
                    Cancel
                  </button>
                </div>
              </div>
              <div class="confirm-option" v-if="showRemoveConfirm">
                <h3 class="confirm-title">Remove Friend</h3>
                <p class="confirm-text">
                  Remove {{ user.name }} from your friends?
                </p>
                <div class="button-container">
                  <button
                    class="confirm-button danger"
                    @click="profileSelectInteraction"
                  >
                    Remove
                  </button>
                  <button class="cancel-button" @click="cancelConfirm">
                    Cancel
                  </button>
                </div>
              </div>
              <div class="confirm-option" v-if="showCancelConfirm">
                <h3 class="confirm-title">Cancel Friend Request</h3>
                <p class="confirm-text">
                  Cancel friend request to {{ user.name }}?
                </p>
                <div class="button-container">
                  <button
                    class="confirm-button danger"
                    @click="profileSelectInteraction"
                  >
                    Cancel Friend Request
                  </button>
                  <button class="cancel-button" @click="cancelConfirm">
                    Don't Cancel Request
                  </button>
                </div>
              </div>
              <div class="confirm-option" v-if="showDeclineConfirm">
                <h3 class="confirm-title">Decline Friend Request</h3>
                <p class="confirm-text">
                  Decline friend request from {{ user.name }}?
                </p>
                <div class="button-container">
                  <button
                    class="confirm-button danger"
                    @click="profileSelectInteraction"
                  >
                    Decline
                  </button>
                  <button class="cancel-button" @click="cancelConfirm">
                    Cancel
                  </button>
                </div>
              </div>
              <div class="confirm-option" v-if="showAcceptConfirm">
                <h3 class="confirm-title">Accept Friend Request</h3>
                <p class="confirm-text">
                  Accept friend request from {{ user.name }}?
                </p>
                <div class="button-container">
                  <button
                    class="confirm-button"
                    @click="profileSelectInteraction"
                  >
                    Accept
                  </button>
                  <button class="cancel-button" @click="cancelConfirm">
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
          <router-link
            v-if="checkOwnProfile"
            class="edit-profile"
            to="/profile/edit"
            ><i class="fas fa-user-edit"></i> Edit Profile</router-link
          >
        </div>
      </div>
      <div class="nav-profile">
        <div class="user-reviews">
          <router-link
            :to="{
              name: 'reviews',
              params: { id: user.id },
              query: { redirect: redirect },
            }"
            :key="$route.fullPath"
            ><i class="fas fa-comments"></i> Reviews</router-link
          >
        </div>
        <div class="user-owned-games">
          <router-link
            :to="{
              name: 'owned',
              params: { id: $route.params.id },
              query: { redirect: redirect },
            }"
            :key="$route.fullPath"
            ><i class="fas fa-gamepad"></i> Owned</router-link
          >
        </div>
        <div class="user-favorite-games">
          <router-link
            :to="{
              name: 'favorite',
              params: { id: $route.params.id },
              query: { redirect: redirect },
            }"
            :key="$route.fullPath"
            ><i class="fas fa-star"></i> Favorite</router-link
          >
        </div>
        <div class="user-wihlist-games">
          <router-link
            :to="{
              name: 'wishlist',
              params: { id: $route.params.id },
              query: { redirect: redirect },
            }"
            :key="$route.fullPath"
            ><i class="fas fa-heart"></i> Wishlist</router-link
          >
        </div>
      </div>
      <Profile :user="user.id" />
    </div>
    <div class="loading-user" v-if="loadingUser">
      <div class="lds-facebook">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div v-else-if="!user" class="user-doesnt-exist">
      <h1>404</h1>
      <p>Profile not found</p>
      <i class="fas fa-user-slash"></i>
    </div>
  </div>
</template>

<script>
import Profile from "../../components/Profile/Profile.vue";
import axios from "axios";
export default {
  name: "ProfileView",
  components: {
    Profile,
  },
  data() {
    return {
      ownProfile: null,
      anotherProfile: null,
      notLogged: null,
      ownedGames: [],
      favoriteGames: [],
      wishListedGames: [],
      user: null,
      loadingUser: true,
      redirect: this.$route.query.redirect,
      friends: false,
      received: false,
      sent: false,
      profileInteraction: "...",
      showConfirm: false,
      showAddConfirm: false,
      showRemoveConfirm: false,
      showCancelConfirm: false,
      showDeclineConfirm: false,
      showAcceptConfirm: false,
    };
  },
  computed: {
    userImage() {
      if (this.user) {
        if (this.user.image) {
          return `${process.env.VUE_APP_IMAGE_URL}${this.user.image}`;
        } else {
          return require("@/assets/def-avatar-profile.jpg");
        }
      }
    },
    logged() {
      return this.$store.state.logged;
    },
    checkOwnProfile() {
      //checa se tá logado
      if (this.$store.state.logged) {
        return this.$route.params.id == this.$store.state.user_id;
      }
      return false;
    },
    checkAnotherProfile() {
      if (this.$store.state.logged) {
        return this.$route.params.id !== this.$store.state.user_id;
      }
      return false;
    },
  },
  watch: {
    "$route.params.id": {
      handler: "getUserInfo",
    },
  },
  methods: {
    async getUserInfo() {
      this.loadingUser = true;
      this.user = null;
      const id = this.$route.params.id;
      try {
        const response = await axios
          .get(`${process.env.VUE_APP_APIURL}userinfo/${id}`)
          .then((response) => {
            this.user = response.data.user;
          });

        console.log(this.user);
      } catch (error) {
        console.log(error.response.data.error);
      }
      this.loadingUser = false;
    },
    async checkFriendship() {
      try {
        const userId = this.$store.state.user_id;
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-friends`,
          {
            params: {
              user_id: userId,
              profile_id: this.$route.params.id,
            },
          }
        );
        this.friends = response.data.friends;
        this.received = response.data.received;
        this.sent = response.data.sent;
        console.log(this.sent);
        //checar se recebeu amizade para o "accept or deny";
      } catch (error) {
        console.log(error);
      }
    },
    cancelConfirm() {
      this.showConfirm = false;
      this.showAddConfirm = false;
      this.showRemoveConfirm = false;
      this.showCancelConfirm = false;
      this.showDeclineConfirm = false;
      this.showAcceptConfirm = false;
      this.profileInteraction = "...";
    },
    confirmInteraction() {
      switch (this.profileInteraction) {
        case "...":
          return false;
        case "add":
          this.showConfirm = true;
          this.showAddConfirm = true;
          break;
        case "remove":
          this.showConfirm = true;
          this.showRemoveConfirm = true;
          break;
        case "cancel":
          this.showConfirm = true;

          this.showCancelConfirm = true;
          break;
        case "decline":
          this.showConfirm = true;
          this.showDeclineConfirm = true;
          break;
        case "accept":
          this.showConfirm = true;
          this.showAcceptConfirm = true;
          break;
        default:
          this.profileInteraction = "...";
          return false;
      }
    },
    profileSelectInteraction() {
      switch (this.profileInteraction) {
        case "...":
          return false;
        case "add":
          this.addProfileFriend();
          break;
        case "remove":
          this.removeProfileFriend();
          break;
        case "cancel":
          this.cancelProfileFriend();
          break;
        case "decline":
          this.declineProfileFriend();
          break;
        case "accept":
          this.acceptProfileFriend();
          break;
        default:
          this.profileInteraction = "...";
          return false;
      }
      this.profileInteraction = "...";
      this.cancelConfirm();
    },
    async addProfileFriend() {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      const receiver = this.$route.params.id;
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
        this.sent = true;
      } catch (error) {
        console.log(error);
      }
    },
    async removeProfileFriend() {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      const friend = this.$route.params.id;
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
        this.friends = false;
      } catch (error) {
        console.log(error);
      }
    },
    async cancelProfileFriend() {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      const receiver = this.$route.params.id;

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
        this.sent = false;
      } catch (error) {
        console.log(error);
      }
    },
    async declineProfileFriend() {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      const sender = this.$route.params.id;

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
        this.received = false;
      } catch (error) {
        console.log(error);
      }
    },
    async acceptProfileFriend() {
      const userId = this.$store.state.user_id;
      const personal_token = this.$store.state.personal_token;
      const sender = this.$route.params.id;

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
        this.friends = true;
        this.received = false;
      } catch (error) {
        console.log(error);
      }
    },
  },
  async created() {
    await this.getUserInfo();
    if (this.logged) {
      await this.checkFriendship();
    }
  },

  mounted() {
    window.scrollTo(0, 0);
  },
};
</script>

<style scoped>
/* font awesome inside select tag */

.fa {
  font-family: "Font Awesome 5 Free", Open Sans;
}

#reset-font {
  font-family: "Nunito", sans-serif;
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
.confirm-button:hover {
  background: #22ebc2;
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
select {
  width: 30vw;
  height: 4vh;
  text-align: center;
  background-color: #23272a;
  color: rgba(255, 255, 255, 0.596);
  font-weight: 1.3vh;
  border-radius: 10px;
  transition: 0.4s;
}

select:hover {
  color: #fff;
}
.back-route {
  margin-left: 2vw;
  margin-top: 1vh;
}
.back-route a {
  color: white;
}
.edit-profile {
  text-decoration: none;
  width: 10vw;
  text-align: center;
  background-color: #23272a;
  color: rgba(255, 255, 255, 0.596);
  font-size: 1.7vh;
  font-weight: 300;
  padding: 0.8vh 0.8vh;
  border-radius: 10px;
  transition: 0.4s;
}
.edit-profile:hover {
  color: #fff;
}
.user-edit-container {
  margin-bottom: 1vh;
}
.loading-user {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
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
  font-size: 22px;
  padding: 2px;
}

.lds-facebook {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80px;
  height: 80px;
}
.personnal-info {
  display: flex;
  flex-direction: column;
  width: 90%;
  margin: 0 auto;
  margin-top: 30px;
}
.nav-profile {
  margin: 0 auto;
  margin-top: 1vh;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
  display: flex;
  width: 90%;
  padding: 1vh 2vw;
  height: 10%;
  font-size: 1.7vh;
  text-shadow: 2px 2px #000;
  border-radius: 6px;
  justify-content: space-around;
  align-items: center;
  background-color: #330066;
}
.nav-profile a {
  text-decoration: none;
  transition: 0.4s;

  color: rgba(255, 255, 255, 0.596);
}
.nav-profile a:hover {
  color: white;
}
@media screen and (min-width: 768px) {
  .confirm-button,
  .cancel-button {
    width: 15vw;
  }
  select {
    width: 15vw;
  }
  .personnal-info {
    display: flex;
    flex-direction: row;
    gap: 20px;
    width: 90%;
    margin: 0 auto;
    margin-top: 30px;
  }
  .nav-profile {
    display: flex;
    width: 50%;
    padding: 1vh 2vw;
    height: 10%;
    border-radius: 6px;
    gap: 2vw;
    align-items: center;
  }
}

.username {
  font-size: 4vh;
  font-weight: 700;
  margin-bottom: 1.5vh;
}
.profile-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}

.lds-facebook div {
  display: inline-block;
  position: absolute;
  left: 8px;
  width: 16px;
  background: #fff;
  animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
}
.lds-facebook div:nth-child(1) {
  left: 8px;
  animation-delay: -0.24s;
}
.lds-facebook div:nth-child(2) {
  left: 32px;
  animation-delay: -0.12s;
}
.lds-facebook div:nth-child(3) {
  left: 56px;
  animation-delay: 0;
}
@keyframes lds-facebook {
  0% {
    top: 8px;
    height: 64px;
  }
  50%,
  100% {
    top: 24px;
    height: 32px;
  }
}

@keyframes lds-dual-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>

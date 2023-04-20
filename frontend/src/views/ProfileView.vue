<template>
  <div class="main-wrapper">
    <div class="initial checker" v-if="user">
      <!-- checar se o user existe -->
      <div class="personnal-info">
        <img
          class="profile-image"
          src="../assets/def-avatar-profile.jpg"
          alt=""
        />
        <div class="user-edit-container">
          <h1 class="username">{{ user.name }}</h1>
          <a v-if="checkOwnProfile" class="edit-profile" href=""
            >Edit profile</a
          >
        </div>
        <div class="nav-profile">
          <div class="user-reviews">
            <router-link
              :to="{ name: 'reviews', params: { id: user.id } }"
              :key="$route.fullPath"
              >Reviews</router-link
            >
          </div>
          <div class="user-owned-games">
            <router-link
              :to="{ name: 'owned', params: { id: $route.params.id } }"
              :key="$route.fullPath"
              >Own</router-link
            >
          </div>
          <div class="user-owned-games">
            <router-link
              :to="{ name: 'favorite', params: { id: $route.params.id } }"
              :key="$route.fullPath"
              >Favorite</router-link
            >
          </div>
          <div class="user-owned-games">
            <router-link
              :to="{ name: 'wishlist', params: { id: $route.params.id } }"
              :key="$route.fullPath"
              >Wishlist</router-link
            >
          </div>
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
import Profile from "../components/Profile.vue";
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
    };
  },
  computed: {
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
  },
  async created() {
    await this.getUserInfo();
  },
};
</script>

<style scoped>
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
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);

  margin-top: 1vh;
  display: flex;
  align-self: center;
  width: 100%;
  gap: 5vw;
  padding: 1vh 2vw;
  height: 10%;
  font-size: 2vh;
  text-shadow: 2px 2px #000;
  border-radius: 6px;
  justify-content: space-between;
  gap: 2vw;
  align-items: center;
  background-color: #330066;
}
.nav-profile a {
  text-decoration: none;
  color: white;
}
@media screen and (min-width: 768px) {
  .personnal-info {
    display: flex;
    flex-direction: row;
    gap: 20px;
    width: 90%;
    margin: 0 auto;
    margin-top: 30px;
  }
  .nav-profile {
    margin-left: 10vw;
    display: flex;
    align-self: flex-end;
    width: fit-content;
    padding: 1vh 2vw;
    height: 10%;
    font-size: 2vh;
    border-radius: 6px;
    justify-content: space-between;
    gap: 2vw;
    align-items: center;
  }
}

.username {
  font-size: 36px;
  font-weight: 700;
}
.profile-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}
.edit-profile {
  text-decoration: none;
  color: #fff;
  font-weight: 400;
  font-size: 12px;
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

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
      </div>
      <Profile :user="user.id" />
    </div>
    <div class="loading-user" v-if="loadingUser">Carregando...</div>
    <div v-else-if="!user" class="user-doesnt-exist">Usuario não existe</div>
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
  methods: {
    async getOwnedGames() {
      const user_id = this.$route.params.id;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-owned`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        //pega os jogos "owned" e bota no array
        this.ownedGames = response.data;
        //console.log("owned: " + this.ownedGames);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async getFavoriteGames() {
      const user_id = this.$route.params.id;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-favorite`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        //pega os jogos "favorited" e bota no array
        this.favoriteGames = response.data;
        // console.log("favorite: " + this.favoriteGames);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async getWishedGames() {
      const user_id = this.$route.params.id;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-wished`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        //pega os jogos "wishlisted" e bota no array
        this.wishListedGames = response.data;
        // console.log("wishlisted: " + this.wishListedGames);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async getUserInfo(id) {
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
  mounted() {
    this.getUserInfo(this.$route.params.id);
  },
};
</script>

<style></style>

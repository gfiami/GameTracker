<template>
  <div class="main-wrapper">
    <div class="loading-user" v-if="loadingUser">
      <Loading />
    </div>
    <div v-else-if="user">
      <div class="back-profile">
        <router-link
          :to="{ name: 'profile', params: { id: user.id } }"
          :key="$route.fullPath"
        >
          <i class="fas fa-caret-left"></i> Back to Profile
        </router-link>
      </div>
      <div class="title" v-if="checkOwnProfile">Your Owned Games</div>
      <div class="title" v-else>
        <i>{{ user.name }}'s </i>&nbsp; Owned Games
      </div>
      <div class="owned-profile-container">
        <TrackedGames
          @ownedGamesUpdate="updateOwned"
          :gameIds="ownedIds"
          :owned="ownedIds"
          :favorite="favoriteIds"
          :wished="[]"
          @trackerClicked="onButtonClicked"
        />
      </div>
    </div>
    <div v-else-if="!user">
      <Profile404 />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import TrackedGames from "../../components/TrackedGames.vue";
import Loading from "../../components/Loading.vue";
import Profile404 from "../../components/Profile404.vue";

export default {
  name: "OwnedView",
  components: {
    TrackedGames,
    Loading,
    Profile404,
  },
  watch: {
    "$route.params.id": {
      handler: "getUserInfo",
    },
  },
  data() {
    return {
      user: null,
      ownedIds: "",
      favoriteIds: "",
      loadingUser: true,
    };
  },
  computed: {
    checkOwnProfile() {
      //checa se tá logado
      if (this.$store.state.logged) {
        return this.$route.params.id == this.$store.state.user_id;
      }
      return false;
    },
  },
  methods: {
    updateOwned(newValue) {
      this.ownedIds = newValue;
    },
    async onButtonClicked(buttonType) {
      //removeOwned | addOwned | removeWishlist | addWishList | removeFavorite | addFavorite
      switch (buttonType) {
        case "removeOwned":
          await this.getOwnedGames();
          await this.getFavoriteGames();
          break;

        case "removeFavorite":
          await this.getFavoriteGames();
          await this.getOwnedGames();
          break;

        case "addFavorite":
          await this.getOwnedGames();
          await this.getFavoriteGames();
          break;

        default:
          console.log("erro reconhecendo button type");
          break;
      }
    },
    async getUserInfo() {
      const id = this.$route.params.id;
      try {
        const response = await axios
          .get(`${process.env.VUE_APP_APIURL}userinfo/${id}`)
          .then((response) => {
            this.user = response.data.user;
            return this.user;
          });
      } catch (error) {
        console.log(error.response.data.error);
        this.loadingUser = false;

        return false;
      }
      this.loadingUser = false;
    },
    async getOwnedGames() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-owned`,
          {
            params: {
              user_id: this.$route.params.id,
            },
          }
        );
        //pega os jogos "owned" e bota no array
        this.ownedIds = response.data;
        if (this.ownedIds.length == 0) {
          this.emptyOwned = true;
        } else {
          this.emptyOwned = false;
        }
        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async getFavoriteGames() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-favorite`,
          {
            params: {
              user_id: this.$route.params.id,
            },
          }
        );

        //pega os jogos "favorite" e bota no array
        this.favoriteIds = response.data;
        if (this.favoriteIds.length == 0) {
          this.emptyFavorite = true;
        } else {
          this.emptyFavorite = false;
        }

        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
  },
  mounted() {
    Promise.all([this.getUserInfo()]).then((values) => {
      if (values[0] !== false) {
        this.getOwnedGames();
        this.getFavoriteGames();
      }
    });
  },
};
</script>

<style scoped>
.title {
  display: flex;
  width: 90%;
  margin: 0 auto;
  margin-top: 30px;
  font-size: 3vh;
}
.back-profile {
  margin-left: 2vw;
  margin-top: 1vh;
}
.back-profile a {
  color: white;
}
</style>
<style>
.owned-profile-container #own-icon {
  display: none;
}
</style>

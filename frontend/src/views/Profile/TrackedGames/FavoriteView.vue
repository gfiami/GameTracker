<template>
  <div class="main-wrapper">
    <div class="loading-user" v-if="loadingUser">
      <Loading />
    </div>
    <div v-else-if="user">
      <div class="back-profile">
        <router-link
          :to="{
            name: 'profile',
            params: { id: user.id },
            query: { redirect: redirect },
          }"
          :key="$route.fullPath"
        >
          <i class="fas fa-caret-left"></i> Back to Profile
        </router-link>
      </div>
      <div class="title" v-if="checkOwnProfile">Your Favorite Games</div>
      <div class="title" v-else>
        <i>{{ user.name }}'s </i>&nbsp; Favorite Games
      </div>

      <div class="favorite-profile-container">
        <TrackedGames
          @favoriteGamesUpdate="updateFavorite"
          :gameIds="favoriteIds"
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
import TrackedGames from "../../../components/TrackedGames.vue";
import Loading from "../../../components/Tools/Loading.vue";
import Profile404 from "../../../components/Profile/Profile404.vue";

export default {
  name: "FavoriteView",
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
      redirect: this.$route.query.redirect,
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
    updateFavorite(newValue) {
      this.favoriteIds = newValue;
    },
    async onButtonClicked(buttonType) {
      await this.getIdsGamesTracked();
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
    async getIdsGamesTracked() {
      const user_id = this.user.id;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}game-ids-user-tracked`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        this.ownedIds = response.data.owned;
        this.favoriteIds = response.data.favorite;
        if (this.ownedIds.length == 0) {
          this.emptyOwned = true;
        } else {
          this.emptyOwned = false;
        }
        if (this.favoriteIds.length == 0) {
          this.emptyFavorite = true;
        } else {
          this.emptyFavorite = false;
        }
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
  },
  mounted() {
    Promise.all([this.getUserInfo()]).then((values) => {
      if (values[0] !== false) {
        this.getIdsGamesTracked();
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
.favorite-profile-container #indicators {
  display: none;
}
</style>

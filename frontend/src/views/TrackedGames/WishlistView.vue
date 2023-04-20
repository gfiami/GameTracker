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
      <div class="title" v-if="checkOwnProfile">Your Wishlisted Games</div>
      <div class="title" v-else>
        <i>{{ user.name }}'s </i>&nbsp; Wishlisted Games
      </div>
      <TrackedGames
        @wishListedGamesUpdate="updateWishlist"
        :gameIds="wishedIds"
        :owned="ownedIds"
        :favorite="[]"
        :wished="wishedIds"
        @trackerClicked="onButtonClicked"
      />
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
      wishedIds: "",
      ownedIds: "",
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
    updateWishlist(newValue) {
      this.wishedIds = newValue;
    },
    async onButtonClicked(buttonType) {
      //removeOwned | addOwned | removeWishlist | addWishList | removeFavorite | addFavorite
      switch (buttonType) {
        case "addOwned":
          await this.getWishedGames();
          await this.getOwnedGames();

          break;

        case "removeWishlist":
          await this.getWishedGames();
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
    async getWishedGames() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-wished`,
          {
            params: {
              user_id: this.$route.params.id,
            },
          }
        );
        this.wishedIds = response.data;
        console.log(this.wishedIds);
        if (this.wishedIds.length == 0) {
          this.emptyWished = true;
        } else {
          this.emptyWished = false;
        }
        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
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
  },
  mounted() {
    Promise.all([this.getUserInfo()]).then((values) => {
      if (values[0] !== false) {
        this.getWishedGames();
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

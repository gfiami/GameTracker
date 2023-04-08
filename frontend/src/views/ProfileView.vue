<template>
  <div class="main-wrapper">
    <div class="routerChecker">
      <div v-if="checkOwnProfile">
        <div class="wait-data" v-if="ownedGames.length">
          <OwnProfile
            :ownedGames="ownedGames"
            :favoriteGames="favoriteGames"
            :wishListedGames="wishListedGames"
          />
        </div>
      </div>
      <div v-else-if="checkAnotherProfile">Another</div>
      <div v-else>Not logged</div>
    </div>
  </div>
</template>

<script>
import OwnProfile from "../components/OwnProfile.vue";
import axios from "axios";
export default {
  name: "ProfileView",
  components: {
    OwnProfile,
  },
  data() {
    return {
      ownProfile: null,
      anotherProfile: null,
      notLogged: null,
      ownedGames: [],
      favoriteGames: [],
      wishListedGames: [],
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
  },
  mounted() {
    this.getOwnedGames();
    this.getFavoriteGames();
    this.getWishedGames();
  },
};
</script>

<style></style>

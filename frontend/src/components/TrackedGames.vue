<template>
  <div class="tracked-games">
    <div v-if="loadingGames"><Loading /></div>
    <div
      class="owned-container"
      v-else-if="fetched && gameIds && owned && favorite && wished"
    >
      <GameLayout
        :user="$route.params.id"
        :games="fetched"
        :checkIds="gameIds"
        :checkOwned="owned"
        :checkFavorite="favorite"
        :checkWished="wished"
        @button-clicked="clickedButton"
        @ownedGamesUpdate="updateOwned"
        @favoriteGamesUpdate="updateFavorite"
        @wishListedGamesUpdate="updateWished"
      />
      <PaginationReview
        :totalPages="totalPages"
        :currentPage="currentPage"
        @goToPage="goToPage"
      />
    </div>
    <div class="error" v-else-if="!fetched">No games found</div>
  </div>
</template>

<script>
import GameLayout from "./GameLayout.vue";
import PaginationReview from "./PaginationReview.vue";
import Loading from "./Loading.vue";

import axios from "axios";

export default {
  name: "TrackedGames",
  components: {
    GameLayout,
    PaginationReview,
    Loading,
  },
  data() {
    return {
      fetched: null,
      loadingGames: true,
      currentPage: 1,
    };
  },
  watch: {
    gameIds: {
      handler(newGames, oldGames) {
        if (newGames !== null) {
          this.fetchTrackedCategory();
        }
      },
    },
  },
  props: {
    gameIds: null,
    owned: null,
    wished: null,
    favorite: null,
  },
  computed: {
    totalPages() {
      if (this.gameIds !== null && this.gameIds !== undefined)
        return Math.ceil(this.gameIds.length / 18);
    },
  },
  methods: {
    goToPage(page) {
      this.currentPage = page;
      console.log(page);
      this.fetchTrackedCategory();
      if (this.gameIds !== null && this.gameIds !== undefined) {
        window.scrollTo(0, 0);
      }
    },
    updateOwned(newValue) {
      this.$emit("ownedGamesUpdate", newValue);
    },
    updateFavorite(newValue) {
      this.$emit("favoriteGamesUpdate", newValue);
    },
    updateWished(newValue) {
      this.$emit("wishListedGamesUpdate", newValue);
    },
    clickedButton(buttonType) {
      this.$emit("trackerClicked", buttonType);
    },
    async fetchTrackedCategory() {
      if (this.gameIds.length == 0) {
        this.fetched = null;
        this.loadingGames = false;
        return false;
      }
      console.log(this.currentPage);
      if (this.gameIds == null) {
        console.log("ue");
        this.loadingGames = false;
        return false;
      }
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}category-tracked-games`,
        {
          params: {
            game_ids: this.gameIds,
            page: this.currentPage,
          },
        }
      );
      this.fetched = response.data.games.results;
      console.log(response.data.games.results);
      this.loadingGames = false;
    },
  },
};
</script>

<style scoped>
.error {
  width: 70%;
  margin: 0 auto;
  margin-top: 3vh;
}
</style>

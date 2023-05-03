<template>
  <div class="tracked-games">
    <div v-if="loadingGames"><Loading /></div>
    <div class="owned-container" v-else-if="fetched && gameIds">
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
      <Pagination
        :totalPages="totalPages"
        :currentPage="currentPage"
        @goToPage="goToPage"
      />
    </div>
    <p class="error" v-else-if="currentPageWrong">
      No games found for page {{ currentPage }}.
      <button class="page-button" @click="goToPage(totalPages)">
        Go to Last Page
      </button>
    </p>
    <div class="error" v-else-if="!fetched">No games found</div>
  </div>
</template>

<script>
import GameLayout from "./GameLayout.vue";
import Pagination from "./Tools/Pagination.vue";
import Loading from "./Tools/Loading.vue";

import axios from "axios";

export default {
  name: "TrackedGames",
  components: {
    GameLayout,
    Pagination,
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
    currentPageWrong() {
      return this.currentPage > this.totalPages && this.totalPages > 0;
    },
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
      const path = this.$route.path;
      this.$router.push({
        path: path,
        query: { page },
      });
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
      console.log("pegano jogo na categoria");
      console.log(this.fetched);
      console.log(response.data.games.results);
      this.loadingGames = false;
    },
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    this.currentPage = parseInt(urlParams.get("page")) || 1;
  },
};
</script>

<style scoped>
.page-button {
  text-align: center;
  font-weight: 800;
  background-color: #23272a;
  color: #fff;
  font-size: 1.3vh;
  margin: 0 0.6vw;
  border: 1px solid #fff;
  border-radius: 10px;
  cursor: pointer;
  padding: 2vh;
}
.error {
  width: 70%;
  margin: 0 auto;
  margin-top: 3vh;
}
</style>

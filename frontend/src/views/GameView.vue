<template>
  <div class="main-wrapper">
    <!-- @está pegando lá de dentro, : está mandando -->
    <SearchBar :key="routeReset" @search="search" :counter="gameinfo.count" />
    <GameLayout
      :key="routeReset"
      :user="userId"
      class="game-layout-gameview"
      :games="gameinfo.results"
    />
    <Paginations
      :key="routeReset"
      @gamedata="gamedata"
      :searchText="searchQuery"
    />
  </div>
</template>

<script>
import Paginations from "../components/Paginations.vue";
import SearchBar from "../components/SearchBar.vue";
import GameLayout from "../components/GameLayout.vue";
export default {
  name: "GameView",
  components: {
    Paginations,
    SearchBar,
    GameLayout,
  },
  data() {
    return {
      searchQuery: "", //isso pegamos via emit lá do searchbar
      gameinfo: "", //isso pegamos via emit lá do paginations
    };
  },
  computed: {
    userId() {
      return this.$store.state.user_id;
    },
    routeReset() {
      return this.$route.fullPath;
    },
  },
  methods: {
    search(query) {
      this.searchQuery = query;
    },
    gamedata(response) {
      this.gameinfo = response;
    },
  },
};
</script>

<style></style>

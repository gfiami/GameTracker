<template>
  <div class="main-wrapper">
    <!-- @está pegando lá de dentro, : está mandando -->
    <SearchBar @search="search" :counter="gameinfo.count" />
    <GameLayout :games="gameinfo.results" />
    <Paginations @gamedata="gamedata" :searchText="searchQuery" />
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

<style scoped></style>

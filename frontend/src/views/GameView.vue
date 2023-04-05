<template>
  <div class="main-wrapper">
    <SearchBar @search="search" :counter="gameinfo.count" />
    <GameLayout :games="gameinfo.results" />
    <Paginations @gamedata="gamedata" />
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

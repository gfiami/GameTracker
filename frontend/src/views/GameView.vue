<template>
  <div class="main-wrapper">
    <!-- @está pegando lá de dentro, : está mandando -->
    <SearchBar @search="search" :counter="gameinfo.count" />
    <GameLayout class="game-layout-gameview" :games="gameinfo.results" />
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

<style>
.game-layout-gameview .indicators {
  display: flex;
  align-items: center;
  gap: 2px;
  justify-content: center;
  position: absolute;
  top: 0;
  left: 0;
  padding: 2px;
  background-color: rgba(0, 0, 0, 0.6);
  border-top-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.game-layout-gameview .game:hover .indicators {
  display: none;
}
</style>

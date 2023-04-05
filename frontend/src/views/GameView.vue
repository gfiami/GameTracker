<template>
  <div class="main-wrapper">
    <FetchGames />
    <SearchBar @search="search" :counter="gameinfo.count" />
    <!-- 
        Isso ainda tem que ser passado da paginations pra cá
        <ul>
          <li v-for="game in games" :key="game.id">{{ game.name }}</li>
        </ul> -->
    <Paginations @gamedata="gamedata" />
  </div>
</template>

<script>
import Paginations from "../components/Paginations.vue";
import SearchBar from "../components/SearchBar.vue";
import FetchGames from "../components/FetchGames.vue";
export default {
  name: "GameView",
  components: {
    Paginations,
    SearchBar,
    FetchGames,
  },
  data() {
    return {
      searchQuery: "", //isso pegamos via emit lá do searchbar
      gameinfo: "", //isso pegamos via emit lá do paginations
      fetchGames: "", //via emit vindo da fetch games
    };
  },
  methods: {
    search(query) {
      this.searchQuery = query;
    },
    gamedata(response) {
      this.gameinfo = response;
      //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
    },
  },
};
</script>

<style scoped></style>

<template>
  <div class="main-wrapper">
    <!-- @está pegando lá de dentro, : está mandando -->
    <SearchBar
      :key="resetSearch"
      @search="search"
      @order="order"
      :counter="gameinfo.count"
      :updateSearch="updateSearch"
      :updateOrder="updateOrder"
    />
    <div v-if="loadingGames">
      <Loading />
    </div>
    <div v-else-if="gameinfo.count !== 0">
      <GameLayout
        :key="routeReset"
        :user="userId"
        class="game-layout-gameview"
        :games="gameinfo.results"
      />
    </div>
    <div v-else>
      <div class="game-doesnt-exist" v-if="game404">
        <h1>404</h1>
        <p>No games found</p>
        <i class="fas fa-book-dead"></i>
      </div>
    </div>

    <Paginations
      :key="routeReset"
      @gamedata="gamedata"
      @setOrder="setOrder"
      @setSearch="setSearch"
      :searchText="searchQuery"
      :orderSet="orderSymbol"
      @updateLoading="updateLoading"
      @noGameFound="noGameFound"
    />
  </div>
</template>

<script>
import Paginations from "../components/Paginations.vue";
import SearchBar from "../components/SearchBar.vue";
import GameLayout from "../components/GameLayout.vue";
import Loading from "../components/Loading.vue";
export default {
  name: "GameView",
  components: {
    Paginations,
    SearchBar,
    GameLayout,
    Loading,
  },
  data() {
    return {
      searchQuery: "", //isso pegamos via emit lá do searchbar
      orderSymbol: "-added", //isso pegamos via emit lá do search bar (padrão é -added(popularidade))
      gameinfo: "", //isso pegamos via emit lá do paginations
      updateOrder: "",
      updateSearch: "",
      loadingGames: "",
      game404: false,
    };
  },
  computed: {
    userId() {
      return this.$store.state.user_id;
    },
    routeReset() {
      this.searchQuery = "";
      this.orderSymbol = "-added";
      return this.$route.fullPath;
    },
    resetSearch() {
      if (this.$route.fullPath == "/games") {
        return this.$route.fullPath;
      }
    },
  },
  methods: {
    search(query) {
      this.searchQuery = query;
    },
    order(query) {
      this.orderSymbol = query;
    },
    gamedata(response) {
      this.gameinfo = response;
    },
    setOrder(order) {
      this.updateOrder = order;
    },
    setSearch(search) {
      this.updateSearch = search;
    },
    updateLoading(status) {
      this.loadingGames = status;
    },
    noGameFound(update) {
      this.game404 = update;
    },
  },
};
</script>

<style scoped>
/* game 404 */
.game-doesnt-exist {
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-shadow: 2px 2px #000;
  padding: 2px;
}
.game-doesnt-exist h1 {
  font-weight: bolder;
  font-size: 44px;
  padding: 2px;
}
.game-doesnt-exist p {
  font-weight: 400;
  font-size: 22px;
  padding: 4px;
}
.game-doesnt-exist i {
  font-size: 30px;
  padding: 2px;
}
</style>

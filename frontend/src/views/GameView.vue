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
    <div v-else>
      <GameLayout
        :key="routeReset"
        :user="userId"
        class="game-layout-gameview"
        :games="gameinfo.results"
      />
    </div>

    <Paginations
      :key="routeReset"
      @gamedata="gamedata"
      @setOrder="setOrder"
      @setSearch="setSearch"
      :searchText="searchQuery"
      :orderSet="orderSymbol"
      @updateLoading="updateLoading"
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
  },
};
</script>

<style></style>

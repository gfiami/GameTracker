<template>
  <div class="main-wrapper">
    <div class="title">Never lose track of your favorite games again</div>
    <!-- @está pegando lá de dentro, : está mandando -->
    <ApiSearch
      v-if="!loadingGames"
      @search="search"
      @order="order"
      :counter="gameinfo.count"
    />
    <div class="tracker offline" v-if="!logged && !loadingGames">
      <p class="offline-text">
        <router-link
          :to="{ path: '/login', query: { redirect: $route.fullPath } }"
          >Login</router-link
        >
        or
        <router-link
          :to="{ path: '/register', query: { redirect: $route.fullPath } }"
          >Register</router-link
        >
        to track your games!
      </p>
    </div>
    <div v-if="loadingGames" class="loading">
      <Loading />
    </div>
    <div v-else-if="gameinfo.count !== 0 && !game404">
      <GameLayout
        :key="routeReset"
        :user="userId"
        class="game-layout-gameview"
        :games="gameinfo.results"
      />
    </div>
    <div class="game-doesnt-exist" v-else-if="game404">
      <h1>404</h1>
      <p>No games found</p>
      <i class="fas fa-book-dead"></i>
    </div>

    <ApiPagination
      :key="routeReset"
      @gamedata="gamedata"
      @updateLoading="updateLoading"
      @noGameFound="noGameFound"
    />
  </div>
</template>

<script>
import ApiPagination from "../components/ApiGames/ApiPagination.vue";
import ApiSearch from "../components/ApiGames/ApiSearch.vue";
import GameLayout from "../components/GameLayout.vue";
import Loading from "../components/Tools/Loading.vue";
export default {
  name: "GameView",
  components: {
    ApiPagination,
    ApiSearch,
    GameLayout,
    Loading,
  },
  data() {
    return {
      searchQuery: "", //isso pegamos via emit lá do searchbar
      orderSymbol: "-added", //isso pegamos via emit lá do search bar (padrão é -added(popularidade))
      gameinfo: "", //isso pegamos via emit lá do paginations
      loadingGames: "",
      game404: false,
    };
  },
  computed: {
    userId() {
      return this.$store.state.user_id;
    },
    logged() {
      return this.$store.state.logged;
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
.title {
  margin: 0 auto;
  margin-top: 2.5vh;
  text-align: center;
  font-size: 4vh;
  font-weight: 900;
}
/* not logged message tracker */
.tracker {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  justify-content: center;
  gap: 30px;
}
div .offline {
  width: 100%;
}
.offline-text {
  text-align: center;
}

.offline {
  background-color: rgba(0, 0, 0, 0.315);
  padding: 20px;
  font-size: 2.2vh;
  margin: 2vh 0;
}

.offline a {
  color: #6842ff;
  font-weight: 500;
  text-decoration: none;
  transition: 0.4s;
}
.offline a:hover {
  color: #d9ff42;
}
.tracker a {
  font-size: 2.2vh;
}
/* game 404 */
.game-doesnt-exist {
  text-align: center;
  margin: 0 auto;
  margin-top: 2vh;
  margin-bottom: 2vh;
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
@media screen and (max-width: 768px) {
  .tracker .offline {
    display: inline;
    margin: 0 auto;
  }
}
</style>

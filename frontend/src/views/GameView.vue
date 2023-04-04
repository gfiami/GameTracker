<template>
  <div class="main-wrapper">
    <SearchBar @search="search" />
    <!-- 
        Isso ainda tem que ser passado da paginations pra cá
        <ul>
          <li v-for="game in games" :key="game.id">{{ game.name }}</li>
        </ul> -->
    <Paginations />
  </div>
</template>

<script>
import Paginations from "../components/Paginations.vue";
import SearchBar from "../components/SearchBar.vue";

export default {
  name: "GameView",
  components: {
    Paginations,
    SearchBar,
  },
  data() {
    return {
      searchQuery: "", //isso pegamos via emit lá do searchbar
    };
  },
  methods: {
    search(query) {
      this.searchQuery = query;
      console.log(this.searchQuery);
    },
  },
};
</script>

<style scoped></style>

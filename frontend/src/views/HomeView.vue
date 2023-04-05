<template>
  <div class="main-wrapper">
    <SearchBar />
  </div>
</template>

<script>
import SearchBar from "../components/SearchBar.vue";
import GameLayout from "../components/GameLayout.vue";
export default {
  name: "HomeView",
  components: { SearchBar, GameLayout },
};
</script>

<style scoped></style>

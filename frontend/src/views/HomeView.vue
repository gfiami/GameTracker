<template>
  <div class="main-wrapper">
    <h1>Home</h1>
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
export default {
  name: "HomeView",
  components: {
    Paginations,
  },
};
</script>

<style scoped></style>

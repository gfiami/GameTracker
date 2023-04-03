<template>
  <div>
    test api
    <div v-for="game in games" :key="game.id">
      <h3>{{ game.name }}</h3>
      <router-link
        :to="{
          name: 'specificGame',
          params: { id: game.id },
        }"
        >Ver detalhes</router-link
      >
      <img :src="game.background_image" alt="Capa do jogo" />
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "TestApiView",
  data() {
    return {
      games: [],
    };
  },
  async mounted() {
    await this.gameRequest();
  },
  methods: {
    async gameRequest() {
      const response = await axios.get(`${process.env.VUE_APP_APIURL}gameinfo`);
      this.games = response.data.games.results;
    },
  },
};
</script>

<style scoped></style>

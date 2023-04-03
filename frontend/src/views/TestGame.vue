<template>
  <div>
    <div v-if="game">
      <div>{{ game.name }}</div>
      <p>{{ game.description.replace(/<\/?p>/g, "") }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TestGame",
  data() {
    return {
      game: null,
    };
  },
  props: ["id"],
  async mounted() {
    await this.gameRequest();
  },
  methods: {
    async gameRequest() {
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}game/${this.$route.params.id}`
      );
      this.game = response.data.game;
      console.log(this.game);
    },
  },
};
</script>

<style></style>

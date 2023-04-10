<template>
  <div>
    <div
      v-if="game && backgroundColorGame"
      class="testando-fundo"
      :style="{ backgroundColor: backgroundColorGame }"
    >
      <div>{{ game.name }}</div>
      <p>{{ game.description.replace(/<\/?p>/g, "") }}</p>
      <img :src="game.background_image" alt="" />
      <img :src="game.background_image_additional" alt="" />
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TestGameView",
  data() {
    return {
      game: null,
      backgroundColorGame: null,
    };
  },
  props: ["id"],
  async mounted() {
    await this.gameRequest();
    this.backgroundColorGame = "#" + this.game.dominant_color;
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

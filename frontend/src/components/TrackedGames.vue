<template>
  <div>
    <GameLayout />
  </div>
</template>

<script>
import GameLayout from "./GameLayout.vue";
import axios from "axios";

export default {
  name: "TrackedGames",
  components: {
    GameLayout,
  },
  data() {
    return {
      fetched: "",
      loadingGames: true,
      currentPage: 1,
    };
  },
  watch: {
    gameIds: {
      handler(newGames, oldGames) {
        if (newGames !== null) {
          this.fetchTrackedCategory();
        }
      },
    },
  },
  props: {
    gameIds: null,
  },
  methods: {
    async fetchTrackedCategory() {
      if (this.gameIds == null) {
        console.log("ue");
        this.loadingGames = false;
        return false;
      }
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}category-tracked-games`,
        {
          params: {
            game_ids: this.gameIds,
            page: this.currentPage,
          },
        }
      );

      this.fetched = response.data;
      console.log(this.fetched);
      this.loadingGames = false;
    },
  },
};
</script>

<style></style>

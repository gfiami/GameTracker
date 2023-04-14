<template>
  <div id="reviewArticles">
    <h1>review articles</h1>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ReviewsArticles",
  props: { game: "", userId: "", logged: Boolean },
  data() {
    return {
      userHasReview: false,
    };
  },
  methods: {
    async fetchReviews(game) {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-game-reviews`,
          {
            params: {
              game_api_id: game,
            },
          }
        );
        console.log("pegando reviews");
        console.log(response.data);

        if (this.logged) {
          console.log("online, checando se tem review...");
          response.data.forEach((review) => {
            if (this.userId == review.user_id) {
              this.userHasReview = true;
              console.log("tem review");
            }
          });
        }
        if (!this.userHasReview) {
          console.log("user não tem review");
        }
      } catch (error) {
        console.log(error);
      }
    },
  },
  mounted() {
    this.fetchReviews(this.game.id);
  },
};
</script>

<style></style>

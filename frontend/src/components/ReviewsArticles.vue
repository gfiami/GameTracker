<template>
  <div id="reviewArticles">
    <h1>review articles</h1>
    <h2>User review: {{ changeUserReview }}</h2>
    <h2>Todas reviews: {{ changeAllReviews }}</h2>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ReviewsArticles",
  props: {
    game: "",
    userId: "",
    logged: Boolean,
    fetchNewDataUser: null,
    fetchNewDataAll: null,
  },
  data() {
    return {
      reviews: null,
      userReview: null,
    };
  },
  computed: {
    changeUserReview() {
      console.log("user reviews: ");
      console.log(this.fetchNewDataUser);

      if (this.fetchNewDataUser !== null) {
        return (this.userReview = this.fetchNewDataUser);
      } else {
        return this.userReview;
      }
    },
    changeAllReviews() {
      console.log("all reviews: ");
      console.log(this.fetchNewDataAll);

      if (this.fetchNewDataAll !== null) {
        return (this.reviews = this.fetchNewDataAll);
      } else {
        return this.reviews;
      }
    },
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
        console.log(response.data);
        this.reviews = response.data;

        //Se o usuario estiver logado, irá pegar a review dele.
        if (this.logged) {
          for (const review of response.data) {
            if (this.userId == review.user_id) {
              this.$emit("userReview", review);
              this.userReview = review;
              this.$emit("reviewChecker", true);
              break;
            }
          }
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

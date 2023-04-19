<template>
  <div class="main-wrapper">
    <div v-if="checkOwnProfile">Own profile</div>
    <div v-else>Not own profile</div>
    <div class="review-container">
      <h3 class="review-title" v-if="userReviews">
        <i>{{ userName }}'s</i> Reviews
      </h3>
      <h3 class="review-title" v-else>No reviews found</h3>
      <hr />
      <ReviewsArticles
        :profileReviews="userReviews"
        :logged="checkOwnProfile"
        @deleteClicked="deleteProfileReview"
      />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ReviewsForm from "../components/ReviewsForm.vue";
import ReviewsArticles from "../components/ReviewsArticles.vue";

export default {
  name: "ReviewsView",
  components: {
    ReviewsForm,
    ReviewsArticles,
  },
  data() {
    return {
      userId: this.$route.params.id,
      userReviews: null,
    };
  },
  computed: {
    checkOwnProfile() {
      //checa se tá logado
      if (this.$store.state.logged) {
        return this.$route.params.id == this.$store.state.user_id;
      }
      return false;
    },
    userName() {
      if (this.userReviews) {
        return this.userReviews[0].username;
      }
    },
  },
  methods: {
    async fetchUserReviews() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-user-reviews`,
          {
            params: {
              user_id: this.userId,
            },
          }
        );
        if (response.data.length == 0) {
          return false;
        }
        this.userReviews = response.data;
        console.log(this.userReviews);
      } catch (error) {
        console.log(error);
      }
    },
    async deleteProfileReview(userId, gameId) {
      try {
        const personal_token = this.$store.state.personal_token;
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}delete-review`,
          {
            params: {
              user_id: userId,
              game_api_id: gameId,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log("no delete");
        console.log(response.data.profileReviews);
        if (response.data.profileReviews.length == 0) {
          this.userReviews = null;
          return false;
        }
        this.userReviews = response.data.profileReviews;
      } catch (error) {
        console.log(error.response.data.message);
        console.log(error.response.data.validation);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
      }
    },
  },
  mounted() {
    this.fetchUserReviews();
  },
};
</script>

<style scoped>
.review-container {
  margin: 0 auto;
  padding: 2vh;
  width: 70%;
  max-width: 70%;
}
.review-title {
  text-shadow: 1px 1px #000;
  font-size: 4vh;
}

hr {
  margin-bottom: 2vh;
  font-weight: 100;
  border: 1px solid rgba(54, 30, 148, 0.9);
}
@media screen and (max-width: 768px) {
  .review-container {
    width: 90%;
    max-width: 90%;
    padding: 0px;
  }
  .review-title {
    text-shadow: 1px 1px #000;
    font-size: 3vh;
  }
}
</style>

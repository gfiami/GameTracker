<template>
  <div id="review">
    <!-- edit review -->
    <div v-if="reviewChecker" class="form-container">
      <form class="review-form" action="" method="post" @submit.prevent>
        <div class="form-data-container">
          <div class="simple-container">
            <label for="review" class="review"
              >Edit your review for {{ game.name }}</label
            >
            <button
              type="button"
              class="closeReview"
              @click="$emit('hideReviewForm')"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
          <p class="review-tips">
            Write what you like or dislike about the game. Be polite and follow
            the <u class="link">Review Rules</u>.
          </p>
          <textarea
            :placeholder="loggedUserReview.review"
            cols="30"
            rows="10"
            maxlength="1000"
            v-model="review"
            id="editReview"
          ></textarea>
          <div class="thumbs-container">
            <i class="thumbs fas fa-thumbs-up" ref="thumbsUp" @click="like"></i>
            <i
              class="thumbs fas fa-thumbs-down"
              ref="thumbsDown"
              @click="dislike"
            ></i>
          </div>

          <button class="review-button" @click="editReview" type="button">
            Edit Review
          </button>
        </div>
      </form>
    </div>

    <!-- create review -->
    <div class="form-container" v-if="logged">
      <form class="review-form" action="" method="post" @submit.prevent>
        <div class="form-data-container">
          <div class="simple-container">
            <label for="review" class="review"
              >Write a review for {{ game.name }}</label
            >
            <button
              type="button"
              class="closeReview"
              @click="$emit('hideReviewForm')"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
          <p class="review-tips">
            Write what you like or dislike about the game. Be polite and follow
            the <u class="link">Review Rules</u>.
          </p>
          <textarea
            placeholder="Maximum 1000 characters"
            cols="30"
            rows="10"
            maxlength="1000"
            v-model="review"
            id="review"
          ></textarea>
          <div class="thumbs-container">
            <i class="thumbs fas fa-thumbs-up" ref="thumbsUp" @click="like"></i>
            <i
              class="thumbs fas fa-thumbs-down"
              ref="thumbsDown"
              @click="dislike"
            ></i>
          </div>

          <button class="review-button" @click="submitReview" type="button">
            Post Review
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ReviewsForm",
  props: {
    game: "", //id do jogo
    logged: Boolean,
    userId: "",
    loggedUserReview: null,
    reviewChecker: null,
  },
  data() {
    return {
      review: null,
      rating: null,
    };
  },
  methods: {
    like(event) {
      const dislike = this.$refs.thumbsDown;
      dislike.style.color = "";
      event.target.style.color = "rgba(45, 250, 45, 0.849)";
      this.rating = "positive";
    },
    dislike(event) {
      const like = this.$refs.thumbsUp;
      like.style.color = "";
      event.target.style.color = "rgba(250, 45, 45, 0.849)";
      this.rating = "negative";
    },
    async editReview() {
      try {
        const personal_token = this.$store.state.personal_token;

        const response = await axios.put(
          `${process.env.VUE_APP_APIURL}edit-review`,
          {
            user_id: this.userId,
            game_api_id: this.game.id,
            review: this.review,
            rating: this.rating,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log("no edit");
        console.log(response.data.reviews);
        this.$emit("fetchNewData_All", response.data.reviews);

        for (const review of response.data.reviews) {
          if (this.userId == review.user_id) {
            this.$emit("userReview", review);
            break;
          }
        }

        this.$emit("hideReviewForm");
        this.review = null;
        this.rating = null;
      } catch (error) {
        //caso haja erro
        this.$emit("hideReviewForm");
        console.log(error.response.data.message);
        console.log(error.response.data.validation);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
      }
    },

    async submitReview() {
      try {
        const personal_token = this.$store.state.personal_token;
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}add-review`,

          {
            user_id: this.userId,
            game_api_id: this.game.id,
            review: this.review,
            rating: this.rating,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log(response.data);
        this.$emit("fetchNewData_All", response.data.reviews);
        for (const review of response.data.reviews) {
          if (this.userId == review.user_id) {
            this.$emit("userReview", review);
            this.$emit("reviewChecker", true);
            break;
          }
        }

        this.$emit("hideReviewForm");
        this.review = null;
        this.rating = null;
      } catch (error) {
        //caso haja erro
        this.$emit("hideReviewForm");
        console.log(error.response.data.message);
        console.log(error.response.data.validation);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
      }
    },
  },
};
</script>

<style scoped>
#review,
#editReview {
  margin: 2.5vh;
}
.review-tips {
  font-size: 2vh;
  color: #ffffffa4;
  font-weight: 300;
  text-shadow: 1px 1px #000;
  padding: 0.5vh;
}
.link {
  cursor: pointer;
}
.form-container {
  padding: 2vh 4vh;
  width: 60vw;
  max-width: 60%;
  height: auto;
  background: rgba(48, 25, 189, 0.62);
  border-radius: 15px;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-content: center;
}
.form-data-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-content: center;
}
.review {
  font-size: 3vh;
  font-weight: 400;
  text-shadow: 1px 1px #000;
  align-self: flex-start;
}
.review-button {
  background: #1abc9c;
  border-radius: 6px;
  height: 7vh;
  width: 11vw;
  padding: 1vh;
  align-self: flex-end;
  cursor: pointer;
  font-size: 1.8vh;
}

.form-data-container textarea {
  width: 100%;
  padding: 0.5vh;
  border: 1px solid #d9ff42;
  font-size: 2vh;
  outline: none;
  resize: none;
  align-self: center;
}
.simple-container {
  display: flex;
  justify-content: space-between;
}
.form-data-container .closeReview {
  border: none;
  box-shadow: none;
  outline: none;
  background: none;
  align-self: flex-end;
  color: #fff;
  padding: 1.5vh;
  font-size: 2vh;
  cursor: pointer;
}
.thumbs-container {
  display: flex;
  width: 30%;
  max-width: 80%;
  cursor: pointer;
  margin: 0 auto;
  justify-content: space-around;
}
.thumbs {
  padding: 1.5vh;
  font-size: 2.5vh;
}

/* TELAS MENORES */
@media screen and (max-width: 768px) {
  .form-container {
    width: 90vw;
    max-width: 90%;
    padding: 1vh 2vh;
  }
  .review-button {
    height: 7vh;
    width: 25vw;
    padding: 1vh;
  }
}
</style>

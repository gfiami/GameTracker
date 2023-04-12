<template>
  <div id="review">
    <!-- <p>Teste: {{ game }}</p>-->
    <div class="form-container">
      <form
        class="review-form"
        action=""
        method="post"
        v-if="logged"
        @submit.prevent
      >
        <div class="form-data-container">
          <label for="review" class="review"
            >Write a review for {{ game.name }}</label
          >
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
          <button class="review-button" @click="submitReview" type="button">
            Post Review
          </button>
        </div>
      </form>
    </div>
    <div class="reviews-container"></div>
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
  },
  data() {
    return {
      review: null,
    };
  },
  methods: {
    async submitReview() {
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}add-review`,
          {
            user_id: this.userId,
            game_api_id: this.game.id,
            review: this.review,
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log(response);
      } catch (error) {
        //caso haja erro
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
#review {
  margin: 10px;
}
.review-tips {
  font-size: 12px;
  color: #ffffffa4;
  font-weight: 300;
  text-shadow: 1px 1px #000;
  padding: 2px;
}
.link {
  cursor: pointer;
}
.form-container {
  padding: 20px;
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
  font-size: 14px;
  font-weight: 400;
  text-shadow: 1px 1px #000;
  align-self: flex-start;
}
.review-button {
  background: #1abc9c;
  border-radius: 6px;
  height: 50px;
  width: 125px;
  align-self: flex-end;
  cursor: pointer;
}

.form-data-container textarea {
  width: 100%;
  padding: 5px;
  border: 1px solid #d9ff42;
  outline: none;
  resize: none;
  align-self: center;
}
</style>

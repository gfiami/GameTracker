<template>
  <div class="main-wrapper">
    <div v class="loading" v-if="loading">
      <div class="lds-facebook">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div class="main-container" v-else-if="game">
      <div class="title-container">
        <h1 class="title">{{ game.name }}</h1>
      </div>

      <div class="top-container">
        <div class="secondary-container">
          <img
            class="secondary-image"
            :src="game.background_image_additional"
            :alt="game.name"
          />
        </div>
        <div class="right-panel-container">
          <div class="main-image-container">
            <img
              class="main-image"
              :src="game.background_image"
              :alt="game.name"
            />
          </div>
          <div class="info-container">
            <p class="released" v-if="releaseDate">
              RELEASE DATE:
              <span class="released-legend">{{ releaseDate }}</span>
            </p>
            <p class="developer">
              DEVELOPER:
              <span class="developer-legend">{{
                game.developers[0].name
              }}</span>
            </p>
            <p class="rating">
              ESRB:
              <span class="rating-legend">{{ game.esrb_rating.name }}</span>
            </p>
            <p class="genres">
              GENRES:
              <span class="genres-legend"
                ><span v-for="(genre, index) in game.genres" :key="genre.id"
                  >{{ genre.name
                  }}<span v-if="index !== game.genres.length - 1">, </span>
                </span></span
              >
            </p>
            <p class="platforms">
              PLATFORMS:
              <span class="platforms-legend"
                ><span
                  v-for="(platform, index) in game.platforms"
                  :key="platform.id"
                  >{{ platform.platform.name
                  }}<span v-if="index !== game.platforms.length - 1">, </span>
                </span></span
              >
            </p>
          </div>
        </div>
      </div>
      <div class="description-container">
        <h3 @click="changeAbout()" class="description-title hideShowAbout">
          About this game
          <span><i :class="hideShowClass"></i></span>
        </h3>
        <hr />
        <p v-show="about == 'show'" class="description">
          {{ game.description_raw }}
        </p>
        <hr v-show="about == 'show'" />
      </div>
      <!-- logged details -->
      <div class="tracker online" v-if="logged">
        <button v-if="!emptyOwned" @click="removeOwned(game.id)" type="button">
          <i class="fas fa-gamepad removeOwned"></i>
          <p class="button-legend">Remove from owned</p>
        </button>
        <button v-if="emptyOwned" @click="addOwned(game.id)" type="button">
          <i class="fas fa-gamepad"></i>
          <p class="button-legend">Add as owned</p>
        </button>

        <button
          v-if="!emptyWished"
          @click="removeWishList(game.id)"
          type="button"
        >
          <i class="fas fa-heart removeWishlist"></i>
          <p class="button-legend">Remove from Wishlist</p>
        </button>
        <button
          v-if="emptyWished && emptyOwned"
          @click="addWishList(game.id)"
          type="button"
        >
          <i class="far fa-heart"></i>
          <p class="button-legend">Add to Wishlist</p>
        </button>

        <button
          v-if="!emptyFavorite"
          @click="removeFavorite(game.id)"
          type="button"
        >
          <i class="fas fa-star removeFavorite"></i>
          <p class="button-legend">Remove favorite</p>
        </button>

        <button
          v-if="emptyFavorite && !emptyOwned"
          @click="addFavorite(game.id)"
          type="button"
        >
          <i class="far fa-star addFavorite"></i>
          <p class="button-legend">Add favorite</p>
        </button>
        <button v-if="!showForm" @click="showAddReview()" type="button">
          <i class="fas fa-comments showReview"></i>
          <p class="button-legend">Write a review</p>
        </button>
        <button v-if="showForm" @click="showEditReview()" type="button">
          <i class="fas fa-comments editReview"></i>
          <p class="button-legend">Edit your review</p>
        </button>
      </div>

      <ReviewsForm
        @fetchNewData_All="fetchNewData_All"
        @userReview="userReview"
        @hideReviewForm="hideReviewForm"
        @reviewChecker="reviewChecker"
        v-if="showHideAdd"
        :game="game"
        :logged="logged"
        :userId="userId"
      />

      <ReviewsForm
        @fetchNewData_All="fetchNewData_All"
        @userReview="userReview"
        @hideReviewForm="hideReviewForm"
        v-if="showHideEdit"
        :loggedUserReview="editReview"
        :game="game"
        :userId="userId"
        :reviewChecker="userHasReview"
      />

      <div class="tracker offline" v-if="!logged">
        <p class="offline-text">
          <router-link
            :to="{ path: '/login', query: { redirect: $route.path } }"
            >Login</router-link
          >
          or
          <router-link
            :to="{ path: '/register', query: { redirect: $route.path } }"
            >Register</router-link
          >
          to track your games and write reviews.
        </p>
      </div>
      <div class="review-container">
        <h3 class="review-title">Reviews</h3>
        <hr />
        <ReviewsArticles
          @reviewChecker="reviewChecker"
          @userReview="userReview"
          :game="game"
          :userId="userId"
          :logged="logged"
          :fetchNewDataUser="fetchNewDataUser"
          :fetchNewDataAll="fetchNewDataAll"
        />
        <hr v-if="fetchNewDataAll" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ReviewsForm from "../components/ReviewsForm.vue";
import ReviewsArticles from "../components/ReviewsArticles.vue";

export default {
  name: "SpecificGameView",
  components: {
    ReviewsForm,
    ReviewsArticles,
  },
  data() {
    return {
      game: null,
      releaseDate: null,
      loading: true,
      ownedGame: [],
      favoriteGame: [],
      wishListedGame: [],
      emptyOwned: false,
      emptyFavorite: false,
      emptyWished: false,
      loadingTracker: true,
      showHideAdd: null,
      showHideEdit: null,
      about: "hide",
      userHasReview: false,
      loggedUserReview: "",
      fetchNewDataUser: null,
      fetchNewDataAll: null,
    };
  },
  computed: {
    logged() {
      return this.$store.state.logged;
    },
    userId() {
      return this.$store.state.user_id;
    },
    hideShowClass() {
      return "fas fa-angle" + (this.about === "hide" ? "-right" : "-down");
    },
    showForm() {
      return this.userHasReview;
    },
    editReview() {
      return this.loggedUserReview;
    },
  },

  watch: {
    game(newVal, oldVal) {
      if (this.logged) {
        this.fetchTrackedSpecific(newVal.id, this.userId);
      } else {
      }
    },
  },
  async mounted() {
    await this.gameRequest();
  },
  methods: {
    fetchNewData_All(reviews) {
      this.fetchNewDataAll = reviews;
    },
    reviewChecker(checker) {
      this.userHasReview = checker;
    },
    changeAbout() {
      this.about = this.about === "hide" ? "show" : "hide";
    },

    showAddReview() {
      this.showHideAdd = true;
    },
    showEditReview() {
      this.showHideEdit = true;
    },
    userReview(review) {
      this.loggedUserReview = review;
      this.fetchNewDataUser = review;
    },
    //usado no botão de x do formulário
    hideReviewForm() {
      this.showHideAdd = false;
      this.showHideEdit = false;
    },
    async gameRequest() {
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}game/${this.$route.params.slug}`
      );
      this.game = response.data.game;
      this.releaseDate = this.formatReleaseDate();
      this.loading = false;
      console.log(this.game);
    },
    formatReleaseDate() {
      const months = [
        "Jan.",
        "Feb.",
        "Mar.",
        "Apr.",
        "May.",
        "Jun.",
        "Jul.",
        "Aug.",
        "Sep.",
        "Oct.",
        "Nov.",
        "Dec.",
      ];
      if (!this.game.released) {
        return "";
      }
      const [year, month, day] = this.game.released.split("-");
      const formattedMonth = months[parseInt(month) - 1];
      return `${year}/${formattedMonth}/${day}`;
    },
    async fetchTrackedSpecific(game, user_id) {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}specific-tracked-game`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        //pega os jogos e bota no array
        this.ownedGame = response.data.owned_game;
        this.favoriteGame = response.data.favorited_game;
        this.wishListedGame = response.data.wishlisted_game;
        if (this.ownedGame == null) {
          this.emptyOwned = true;
        } else {
          this.emptyOwned = false;
        }
        if (this.favoriteGame == null) {
          this.emptyFavorite = true;
        } else {
          this.emptyFavorite = false;
        }
        if (this.wishListedGame == null) {
          this.emptyWished = true;
        } else {
          this.emptyWished = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async addOwned(game) {
      const user_id = this.userId;
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}specific-owned`,
          {
            user_id: user_id,
            game_api_id: game,
          }
        );
        this.ownedGame = response.data.owned_game;
        this.wishListedGame = response.data.wishlisted_game;
        this.emptyOwned = false;
        this.emptyWished = true;
        //this.$emit("button-clicked", "addOwned");
      } catch (error) {
        console.log(error);
      }
    },
    async removeOwned(game) {
      const user_id = this.userId;
      try {
        console.log("Entrou no try do remove Owned");
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-specific-owned`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        this.ownedGame = response.data.owned_game;
        this.favoriteGame = response.data.favorite_game;
        this.emptyOwned = true;
        this.emptyFavorite = true;
        //        this.$emit("button-clicked", "removeOwned");
      } catch (error) {
        console.log(error);
        console.log("Entrou no erro do Remove owned");
      }
    },
    async addFavorite(game) {
      const user_id = this.userId;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}specific-favorite`,
          {
            user_id: user_id,
            game_api_id: game,
          }
        );
        this.favoriteGame = response.data;
        this.emptyFavorite = false;
        //this.$emit("button-clicked", "addFavorite");
      } catch (error) {
        console.log(error);
      }
    },

    async removeFavorite(game) {
      const user_id = this.userId;

      try {
        console.log("entrou no try do remove favorite");
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-specific-favorite`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        this.favoriteGame = response.data;
        this.emptyFavorite = true;
        //this.$emit("button-clicked", "removeFavorite");
      } catch (error) {
        console.log(error);

        console.log("entrou no error do remove favorite");
      }
    },
    async addWishList(game) {
      const user_id = this.userId;
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}specific-wishlist`,
          {
            user_id: user_id,
            game_api_id: game,
          }
        );
        this.wishListedGame = response.data;
        this.emptyWished = false;
        //this.$emit("button-clicked", "addWishList");
      } catch (error) {
        console.log(error);

        console.log(error.response.data.error);
      }
    },

    async removeWishList(game) {
      const user_id = this.userId;

      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-specific-wishlist`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );

        this.wishListedGame = response.data;
        this.emptyWished = true;
        //  this.$emit("button-clicked", "removeWishlist");
      } catch (error) {
        //console.log(error.response.data.error);
        console.log("jogo nao estava na wishlist");
      }
    },
  },
};
</script>

<style scoped>
.tracker {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  justify-content: center;
  gap: 30px;
}
div .offline {
  width: 100%;
  display: inline;
}
.offline-text {
  text-align: center;
}

.tracker button {
  background: none;
  color: inherit;
  border: none;
  padding: 0;
  font: inherit;
  cursor: pointer;
  outline: inherit;
  font-size: 4vh;
  margin: 0.5vh;
  text-shadow: 2px 2px 4px #000000;
}

.tracker a {
  font: inherit;
  font-size: 2.2vh;
}
.button-legend {
  text-align: center;
  font-size: 3vh;
  font-weight: 700;
  margin: 0 auto;
  padding: 0;
  text-align: center;
  text-shadow: 2px 2px 4px #000000;
}
.addFavorite {
  background: transparent;
}
.removeFavorite {
  color: rgba(250, 250, 45, 0.849);
}
.removeOwned {
  color: rgba(45, 250, 45, 0.849);
}
.removeWishlist {
  color: rgba(250, 45, 45, 0.849);
}
.offline {
  background-color: rgba(0, 0, 0, 0.315);
  padding: 20px;
  font-size: 2.2vh;
  margin: 10px;
}

.offline a {
  color: #6842ff;
  font-weight: 500;
  text-decoration: none;
  transition: 0.4s;
}
.offline a:hover {
  color: #d9ff42;
}
.main-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.title-container {
  width: 70%;
  margin: 0 auto;
}
.hideShowAbout {
  cursor: pointer;
}
.title {
  padding: 4vh;
  text-shadow: 2px 2px #000;
}
.description-container,
.review-container {
  margin: 0 auto;
  padding: 2vh;
  width: 70%;
  max-width: 70%;
}
.description-title,
.review-title {
  text-shadow: 1px 1px #000;
  font-size: 4vh;
}
.description {
  font-size: 2.2vh;
  text-align: justify;
  text-shadow: 2px 2px #000;
  margin-bottom: 2vh;
}
hr {
  margin-bottom: 2vh;
  font-weight: 100;
  border: 1px solid rgba(54, 30, 148, 0.9);
}

.released,
.developer,
.rating,
.genres,
.platforms {
  padding-top: 0.5vh;
  font-size: 2vh;
  font-weight: 300;
  color: #999;
  font-style: italic;
  text-shadow: 1px 1px #000;
}
.released-legend,
.developer-legend,
.rating-legend,
.genres-legend,
.platforms-legend {
  font-size: 2vh;
  font-weight: 500;
  color: #fff;
  font-style: normal;
}
.top-container {
  gap: 1vh;
  width: 70%;
  display: flex;
  justify-content: center;
}
.info-container {
  width: 100%;
  text-shadow: 1px 1px #000;
  padding-left: 10px;
}

.secondary-container {
  width: 100%;
}
.right-panel-container {
  width: 50%;
  display: flex;
  flex-direction: column;
}
img {
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  border: 5px solid rgba(54, 30, 148, 0.9);
  border-radius: 6px;
}
.main-image {
  max-width: 100%;
  height: auto;
}
.secondary-image {
  width: 100%;
  height: auto;
}

/* loading */
.loading {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.lds-facebook {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80px;
  height: 80px;
}
.lds-facebook div {
  display: inline-block;
  position: absolute;
  left: 8px;
  width: 16px;
  background: #fff;
  animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
}
.lds-facebook div:nth-child(1) {
  left: 8px;
  animation-delay: -0.24s;
}
.lds-facebook div:nth-child(2) {
  left: 32px;
  animation-delay: -0.12s;
}
.lds-facebook div:nth-child(3) {
  left: 56px;
  animation-delay: 0;
}
@keyframes lds-facebook {
  0% {
    top: 8px;
    height: 64px;
  }
  50%,
  100% {
    top: 24px;
    height: 32px;
  }
}

@keyframes lds-dual-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* TELAS MENORES */
@media screen and (max-width: 768px) {
  .tracker .offline {
    display: inline;
    margin: 0 auto;
  }
  .tracker button {
    width: 100%;
  }
  .description-container,
  .review-container {
    width: 90%;
    max-width: 90%;
    padding: 0px;
  }
  .description-title,
  .review-title {
    text-shadow: 1px 1px #000;
    font-size: 3vh;
  }
  .description {
    width: 100%;
    font-size: 2.2vh;
    text-align: justify;
    text-shadow: 2px 2px #000;
    margin-bottom: 2vh;
  }
  .title-container {
    width: 90%;
    margin: 0 auto;
  }
  .title {
    padding: 0;
    text-align: center;
    font-size: 5vh;
    text-shadow: 2px 2px #000;
  }
  .top-container {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .right-panel-container {
    display: flex;
    width: 90vw;
    flex-direction: column;
    align-items: center;
  }
  .secondary-container {
    width: 90vw;
  }
  .main-image {
    max-width: 90vw;
    height: auto;
  }
  .secondary-image {
    width: 90vw;
    height: auto;
  }

  .info-container {
    width: 80vw;
  }
  .info-container {
    padding: 1vh;
  }
  .right-panel-container {
    width: 50%;
    display: flex;
    flex-direction: column;
  }
}
</style>

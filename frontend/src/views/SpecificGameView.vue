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
        <h3 class="description-title">About this game</h3>
        <hr />
        <p class="description">{{ game.description_raw }}</p>
        <hr />
      </div>
      <!-- logged details -->
      <div class="tracker online" v-if="logged">
        Adicionar área e Botões para Owned | Favorite | Wishlist
      </div>
      <div class="tracker offline" v-else>
        <router-link to="/login">Login</router-link> or
        <router-link to="/register">Register</router-link> to track your games
        and write reviews.
      </div>
      <!-- logged details -->
      <div class="review-container">
        <h3 class="review-title">Reviews</h3>
        <hr />
        <ReviewsForm :game="game" :logged="logged" :userId="userId" />
        <hr />
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ReviewsForm from "../components/ReviewsForm.vue";

export default {
  name: "SpecificGameView",
  components: {
    ReviewsForm,
  },
  data() {
    return {
      game: null,
      releaseDate: null,
      loading: true,
    };
  },
  computed: {
    logged() {
      return this.$store.state.logged;
    },
    userId() {
      return this.$store.state.user_id;
    },
  },
  async mounted() {
    await this.gameRequest();
  },
  methods: {
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
  },
};
</script>

<style scoped>
.offline {
  background-color: rgba(0, 0, 0, 0.315);
  padding: 20px;
  font-size: 16px;
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
.title {
  padding: 15px;
  text-shadow: 2px 2px #000;
}
.description-container,
.review-container {
  margin: 0 auto;
  padding: 10px;
  width: 70%;
}
.description-title,
.review-title {
  text-shadow: 1px 1px #000;
  font-size: 18px;
}
.description {
  text-align: justify;
  text-shadow: 2px 2px #000;
  margin-bottom: 10px;
}
hr {
  margin-bottom: 10px;
  font-weight: 100;
  border: 1px solid rgba(54, 30, 148, 0.9);
}

.released,
.developer,
.rating,
.genres,
.platforms {
  padding-top: 2px;
  font-size: 12px;
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
  font-size: 14px;
  font-weight: 500;
  color: #fff;
  font-style: normal;
}
.top-container {
  gap: 10px;
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
</style>

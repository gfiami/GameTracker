<template>
  <div class="main-wrapper">
    <div class="main-container" v-if="game">
      <div class="title-container">
        <h1 class="title">{{ game.name }}</h1>
      </div>

      <div class="top-container">
        <div class="image-container">
          <div class="main-image-container">
            <img
              class="main-image"
              :src="game.background_image"
              alt="{{ game.name }}"
            />
          </div>
          <div class="secondary-container">
            <img
              class="secondary-image"
              :src="game.background_image_additional"
              alt="{{ game.name }}"
            />
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
      </div>

      <div class="description-container">
        <h3 class="description-title">About this game</h3>
        <hr />
        <p class="description">{{ game.description_raw }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SpecificGameView",
  data() {
    return {
      game: null,
      releaseDate: null,
    };
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
.description-container {
  margin: 0 auto;
  padding: 10px;
  width: 70%;
}
.description-title {
  text-shadow: 1px 1px #000;
}
.description {
  text-align: justify;
  text-shadow: 2px 2px #000;
}
hr {
  margin-bottom: 10px;
  font-weight: 100;
  border: 1px solid rgba(54, 30, 148, 0.9);
}
.description-title {
  font-size: 16px;
}
.top-container {
  width: 70%;
  display: flex;
  justify-content: center;
  flex-direction: column;
}
.info-container {
  width: 100%;
  text-shadow: 1px 1px #000;
  padding-left: 10px;
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
.image-container {
  width: 100%;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 15px;
}
.secondary-container {
  display: flex;
  gap: 10px;
  flex-direction: column;
  align-items: flex-end;
}
.image-container img {
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  border: 5px solid rgba(54, 30, 148, 0.9);
  border-radius: 6px;
}
.main-image {
  max-width: 100%;
  height: auto;
}
.secondary-image {
  max-width: 100%;
  height: auto;
}
</style>

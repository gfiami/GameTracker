<template>
  <div id="reviewArticles">
    <!-- profile review -->
    <ReviewsForm
      :editProfileReview="profileEditReview"
      @hideProfileEdit="hideProfileEdit"
      @updateProfileReviews="updateProfileReviews"
    />
    <div
      v-if="profileReviewsChecker && profileReviews && !editingReview"
      id="profileReviews"
    >
      <div v-for="review in paginatedProfile" :key="review.id">
        <div class="review-container">
          <div class="container-left">
            <h3 class="game-review-title">
              <router-link
                :to="{
                  name: 'specificgame',
                  params: { slug: review.game_api_id },
                }"
                >{{ review.game_name }}
              </router-link>
            </h3>
            <div class="rating">
              <p v-if="review.rating == 'positive'" class="positive">
                <i class="thumbs fas fa-thumbs-up" ref="thumbsUp"></i>
                Recommended
              </p>
              <p v-else class="negative">
                <i class="thumbs fas fa-thumbs-down" ref="thumbsDown"></i> Not
                recommended
              </p>
            </div>
          </div>

          <div class="container-right">
            <div class="info">
              <p class="date">
                Published: {{ formattedDate(review.created_at) }}
              </p>
              <p class="review-text">{{ review.review }}</p>
            </div>
          </div>
          <div class="button-container" v-if="logged">
            <!-- logged aqui é = perfil do usuario e nao logged exatamente -->
            <button class="edit-button" type="button" @click="showEdit(review)">
              <i class="fas fa-comments editReview"></i>
              <p class="button-legend">Edit this review</p>
            </button>
            <button
              class="delete-button"
              type="button"
              @click="
                $emit('deleteClicked', review.user_id, review.game_api_id)
              "
            >
              <i class="fas fa-comments deleteReview"></i>
              <p class="button-legend">Delete this review</p>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- user review -->
    <div v-if="changeUserReview">
      <h3 class="review-title">Your review</h3>
      <div class="review-container">
        <div class="container-left">
          <div class="personnal-info">
            <router-link
              :to="{ name: 'profile', params: { id: userReview.user_id } }"
              :key="$route.fullPath"
            >
              <img
                class="profile-image"
                :src="
                  userReview.image
                    ? `${reviewImage}${userReview.image}`
                    : require('@/assets/def-avatar-profile.jpg')
                "
                alt=""
              />
              <span class="username">{{ userReview.username }}</span>
            </router-link>
          </div>

          <div class="rating">
            <p v-if="userReview.rating == 'positive'" class="positive">
              <i class="thumbs fas fa-thumbs-up"></i> Recommended
            </p>
            <p v-else class="negative">
              <i class="thumbs fas fa-thumbs-down"></i> Not recommended
            </p>
          </div>
        </div>
        <div class="container-right">
          <div class="info">
            <p class="date">
              Published: {{ formattedDate(userReview.created_at) }}
            </p>
            <p class="review-text">{{ userReview.review }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- all reviews, except user if exists -->
    <div v-if="changeAllReviews">
      <hr v-if="changeUserReview" />

      <!-- SEARCH BY RATING -->
      <div class="search-container" v-if="changeAllReviews">
        <label>
          <input type="radio" name="options" v-model="checkedFilter" value="" />
          <span class="radio-button">All</span>
        </label>
        <label>
          <input
            type="radio"
            name="options"
            v-model="checkedFilter"
            value="positive"
          />
          <span class="radio-button">Recommended</span>
        </label>
        <label>
          <input
            type="radio"
            name="options"
            v-model="checkedFilter"
            value="negative"
          />
          <span class="radio-button">Not Recommended</span>
        </label>
      </div>
      <!--END OF SEARCH BY RATING -->

      <p
        class="review-title"
        v-if="
          changeAllReviews.length <= 5 &&
          changeUserReview &&
          changeAllReviews.length - 1 !== 0
        "
      >
        Showing 1 to {{ changeAllReviews.length - 1 }} of
        {{ changeAllReviews.length - 1 }}
      </p>
      <p
        class="review-title"
        v-else-if="
          changeAllReviews.length <= 5 &&
          changeUserReview == null &&
          changeAllReviews.length !== 0
        "
      >
        Showing 1 to {{ changeAllReviews.length }} of
        {{ changeAllReviews.length }}
      </p>

      <p
        class="review-title"
        v-else-if="changeUserReview && changeAllReviews.length - 1 != 0"
      >
        Showing {{ (currentPage - 1) * 5 + 1 }} to {{ currentPage * 5 }} of
        {{ changeAllReviews.length - 1 }}
      </p>
      <p
        class="review-title"
        v-else-if="
          changeUserReview == null && !changeAllReviews.length - 1 != 0
        "
      >
        Showing {{ (currentPage - 1) * 5 + 1 }} to {{ currentPage * 5 }} of
        {{ changeAllReviews.length }}
      </p>
      <div v-for="review in paginatedReviews" :key="review.id">
        <div class="review-container">
          <div class="container-left">
            <div class="personnal-info">
              <router-link
                :to="{ name: 'profile', params: { id: review.user_id } }"
                :key="$route.fullPath"
              >
                <img
                  class="profile-image"
                  :src="
                    review.image
                      ? `${reviewImage}${review.image}`
                      : require('@/assets/def-avatar-profile.jpg')
                  "
                  alt=""
                />
                <span class="username">{{ review.username }}</span>
              </router-link>
            </div>
            <div class="rating">
              <p v-if="review.rating == 'positive'" class="positive">
                <i class="thumbs fas fa-thumbs-up" ref="thumbsUp"></i>
                Recommended
              </p>
              <p v-else class="negative">
                <i class="thumbs fas fa-thumbs-down" ref="thumbsDown"></i> Not
                recommended
              </p>
            </div>
          </div>
          <div class="container-right">
            <div class="info">
              <p class="date">
                Published: {{ formattedDate(review.created_at) }}
              </p>
              <p class="review-text">{{ review.review }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="getUrl == 'games'">
      <h3 id="otherUsers" class="review-title">No reviews found.</h3>
    </div>

    <div class="pages" v-if="!editingReview">
      <PaginationReview
        :totalPages="totalPages"
        :currentPage="currentPage"
        @goToPage="goToPage"
      />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import PaginationReview from "./PaginationReview.vue";
import ReviewsForm from "./ReviewsForm.vue";

export default {
  name: "ReviewsArticles",
  components: {
    PaginationReview,
    ReviewsForm,
  },
  props: {
    game: null,
    userId: "",
    logged: Boolean,
    fetchNewDataUser: null,
    fetchNewDataAll: null,
    profileReviews: null,
  },
  data() {
    return {
      reviews: null,
      filteredReviews: [],
      profileFilteredReviews: [],
      userReview: null,
      currentPage: 1,
      reviewsPerPage: 5,
      profileEditReview: null,
      editingReview: false,
      reviewImage: `${process.env.VUE_APP_IMAGE_URL}`,
      checkedFilter: "",
    };
  },
  watch: {
    checkedFilter: {
      handler(newValue) {
        this.fetchReviews(this.game.id);
      },
    },
    userReview: {
      handler(newValue, oldValue) {
        if (oldValue !== null && oldValue.updated_at !== newValue.updated_at) {
          //resetando filtro para "all"
          this.checkedFilter = "";
        }
      },
    },
  },
  computed: {
    profileReviewsChecker() {
      if (this.profileReviews !== null && this.profileReviews !== undefined) {
        return this.profileReviews;
      }
    },
    paginatedProfile() {
      const startIndex = (this.currentPage - 1) * this.reviewsPerPage;
      let filteredReviews = this.profileReviews;

      const endIndex = startIndex + this.reviewsPerPage;
      this.profileFilteredReviews = filteredReviews.slice(startIndex, endIndex);
      if (this.profileFilteredReviews.length == 0) {
        return (this.profileFilteredReviews = null);
      }
      return this.profileFilteredReviews;
    },
    paginatedReviews() {
      const startIndex = (this.currentPage - 1) * this.reviewsPerPage;
      let filteredReviews = this.reviews;
      if (this.changeUserReview !== null) {
        filteredReviews = this.reviews.filter(
          (review) => review.user_id !== this.userReview.user_id
        );
      }
      const endIndex = startIndex + this.reviewsPerPage;
      this.filteredReviews = filteredReviews.slice(startIndex, endIndex);
      if (this.filteredReviews.length == 0) {
        return (this.filteredReviews = null);
      }
      return this.filteredReviews;
    },
    totalPages() {
      if (
        this.changeAllReviews !== null &&
        this.changeAllReviews !== undefined
      ) {
        if (
          this.changeUserReview !== null &&
          this.changeUserReview !== undefined
        ) {
          return (
            Math.ceil(this.changeAllReviews.length / this.reviewsPerPage) - 1
          );
        }
        return Math.ceil(this.changeAllReviews.length / this.reviewsPerPage);
      }
      if (this.profileReviews !== null && this.profileReviews !== undefined) {
        return Math.ceil(this.profileReviews.length / this.reviewsPerPage);
      }
    },
    changeUserReview() {
      if (this.fetchNewDataUser !== null) {
        this.userReview = this.fetchNewDataUser;
        return this.userReview;
      } else {
        this.userReview = null;
        return null;
      }
    },
    changeAllReviews() {
      if (this.fetchNewDataAll !== null) {
        this.reviews = this.fetchNewDataAll;
        return this.reviews;
      } else {
        return this.reviews;
      }
    },
    formattedDates() {
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
      return (created_at) => {
        if (!created_at) {
          return "";
        }
        const datePart = created_at.split("T")[0];

        const [year, month, day] = datePart.split("-");
        const formattedMonth = months[parseInt(month) - 1];
        return `${year}/${formattedMonth}/${day}`;
      };
    },
    getUrl() {
      const firstPath = this.$route.path.split("/");
      return firstPath[1];
    },
  },
  methods: {
    showEdit(review) {
      this.profileEditReview = review;
      this.editingReview = true;
    },
    hideProfileEdit() {
      this.editingReview = false;
      this.profileEditReview = null;
    },
    updateProfileReviews(reviews) {
      this.$emit("updatingReview", reviews);
    },
    goToPage(page) {
      this.currentPage = page;
      let section;
      if (
        this.changeAllReviews !== null &&
        this.changeAllReviews !== undefined
      ) {
        section = document.querySelector("#otherUsers");
        section.scrollIntoView();
      } else {
        window.scrollTo(0, 0);
      }
    },
    async fetchReviews(game) {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-game-reviews`,
          {
            params: {
              game_api_id: game,
              filter_reviews: this.checkedFilter,
            },
          }
        );
        if (response.data.length == 0) {
          return false;
        }
        this.reviews = response.data;
        this.$emit("fetchNewData_All", response.data);

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
    formattedDate(created_at) {
      return this.formattedDates(created_at);
    },
  },
  mounted() {
    if (this.game !== null && this.game !== undefined) {
      this.fetchReviews(this.game.id);
    }
  },
};
</script>

<style scoped>
.button-container {
  display: flex;
  justify-content: space-around;
}
#otherUsers {
  margin-bottom: 2vh;
}
.delete-button,
.edit-button {
  background: none;
  color: inherit;
  border: none;
  padding: 0;
  font: inherit;
  cursor: pointer;
  outline: inherit;
  font-size: 2.9vh;
  margin: 0.5vh;
  text-shadow: 2px 2px 4px #000000;
}
.delete-button {
  color: rgba(250, 45, 45, 0.849);
}
.edit-button {
  color: rgba(250, 250, 45, 0.849);
}
.button-legend {
  color: white;
  text-align: center;
  font-size: 1.6vh;
  font-weight: 700;
  margin: 0 auto;
  padding: 0;
  text-align: center;
  text-shadow: 2px 2px 4px #000000;
}
hr {
  width: 95%;
  margin: 0 auto;
  margin-bottom: 2vh;
  font-weight: 50;
  border: 1px solid rgba(54, 30, 148, 0.644);
}
.profile-image {
  height: 10vh;
  width: 10vh;
  border-radius: 50%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
}
.review-title {
  font-weight: 300;
  text-shadow: 1px 1px #000;

  width: 95%;
  margin: 0 auto;
}
.review-container {
  border-radius: 12px;
  margin: 0 auto;
  box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.5), 0 0 40px 10px rgba(0, 0, 0, 0.2);

  background-color: #330066;
  padding: 2vh 5vw;
  width: 95%;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  gap: 4vw;
  margin-bottom: 2vh;
}
.container-left {
  display: flex;
  gap: 2vh;
  flex-direction: column;
}
.container-right {
  display: flex;
  width: 100%;
  flex-direction: column;
  gap: 1vh;
}
.rating {
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
  width: 60%;
  align-self: center;
  padding: 1.2vh 1.4vw;
  border-radius: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #161b3a;
  max-width: 100%;
  max-height: 10vh;
  font-size: 2.2vh;
  text-shadow: 1px 1px #000;
}

.fa-thumbs-up {
  color: rgba(45, 250, 45, 0.849);
}
.fa-thumbs-down {
  color: rgba(250, 45, 45, 0.849);
}
.username {
  display: flex;
  align-items: flex-start;
  font-size: 2.3vh;
}
.personnal-info a {
  display: flex;
  margin: 0 auto;
  justify-content: flex-start;
  gap: 0.6vw;
  font-size: 2.6vh;
  text-decoration: none;
  transition: 0.4s;
  cursor: pointer;
  font-weight: 500;
  text-shadow: 1px 1px #000;
  color: #cccccc;
}

.date {
  font-size: 2.1vh;
  font-weight: 300;
  color: #999;
  font-style: italic;
  text-shadow: 1px 1px #000;
}
.info {
  word-wrap: break-word;
  max-width: 100%;
}
.review-text {
  max-width: 100%;
  font-size: 2.5vh;
  text-shadow: 1px 1px #000;
}
.game-review-title a {
  text-decoration: none;
  font-size: 2.7vh;
  text-align: center;
  color: white;
}
/* search container */
.search-container {
  margin: 0 auto;
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
}
.search-container label {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 33%;
  cursor: pointer;
  background-color: #6b5b95;
  border: none;
  border-radius: 5px;
  user-select: none;
  font-size: 1.7vh;
  text-align: center;
}

.search-container label:hover {
  background-color: #9b8ece;
}
.search-container .radio-button {
  width: 100%;
  height: 100%;
  padding: 1.3vh 1.3vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.search-container label input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  border-radius: 5px;
}
.search-container label input:checked + .radio-button,
.search-container label input:focus + .radio-button {
  background-color: #bb8ece;
  border-radius: 5px;
}
@media screen and (min-width: 768px) {
  .container-left {
    flex-direction: row;
    gap: 2vw;
    justify-content: space-between;
    align-items: center;
    margin: 0;
  }
  .game-title {
    font-size: 2.4vh;
    text-align: left;
  }
  .review-container {
    padding: 2vh 2vw;
    width: 70%;
  }
  .review-title {
    width: 70%;
  }
  .profile-image {
    width: 6vw;
    height: 6vw;
  }
  .review-text,
  .rating {
    font-size: 2.2vh;
  }
  .date {
    font-size: 1.8vh;
  }
  .personnal-info a {
    font-size: 2.5vh;
  }
  .review-container {
    gap: 2vw;
  }
  .rating {
    width: 50%;
  }
}
</style>

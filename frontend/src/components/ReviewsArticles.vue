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
      <div class="search-container" v-if="changeAllReviews && showHideButtons">
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
      <p class="review-title" id="otherUsers">
        {{ showingCounter }}
      </p>
      <p class="review-title" id="otherUsers" v-if="currentPageWrong">
        No reviews found for page {{ currentPage }}.
        <button class="page-button" @click="goToPage(totalPages)">
          Go to Last Page
        </button>
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
      <h3 id="otherUsers" class="review-title">{{ updateErrorMessage }}</h3>
    </div>
    <div v-else-if="getUrl == 'games'">
      <h3 id="otherUsers" class="review-title">{{ updateErrorMessage }}</h3>
    </div>

    <div class="pages" v-if="!editingReview">
      <PaginationReview
        v-if="!currentPageWrong"
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
    showHideButtons: Boolean,
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
      checkedFilter: null,
      showingCounter: "",
      pageStart: true,
    };
  },
  watch: {
    checkedFilter: {
      handler(newValue, oldValue) {
        if (oldValue !== null) {
          this.fetchReviews(this.game.id);
          this.currentPage = 1;
          const page = this.currentPage;
          const path = this.$route.path;
          const order = newValue;
          console.log("valor antigo");
          console.log(oldValue);
          this.$router.push({
            path: path,
            query: { page, order },
          });
        }
      },
    },
    changeAllReviews: {
      handler(newValue) {
        this.showingCounter = this.updateShowingCounter();
      },
    },
    userReview: {
      handler(newValue, oldValue) {
        if (this.pageStart) {
          this.pageStart = false;
        } else {
          //user editou review
          if (oldValue !== null && newValue !== null) {
            if (oldValue.updated_at !== newValue.updated_at) {
              console.log("editou review");
              this.checkedFilter = "";
            }
            //deletou review
          } else if (newValue == null) {
            this.checkedFilter = "";
            //criou review
          } else if (newValue !== null && oldValue == null) {
            console.log("criou review");
            this.checkedFilter = "";
          }
        }
      },
    },
  },
  computed: {
    currentPageWrong() {
      return this.currentPage > this.totalPages && this.totalPages > 0;
    },
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
      const userReview = this.changeUserReview;
      const allReviews = this.changeAllReviews;
      const filter = this.checkedFilter;
      const reviewsPerPage = this.reviewsPerPage;
      let allCounter;
      let countLessUser;
      if (allReviews) {
        allCounter = allReviews.length;
      }
      if (userReview) {
        countLessUser = allReviews.length - 1;
      }
      if (allReviews) {
        if (userReview) {
          if (userReview.rating == filter || filter == "") {
            return Math.ceil(countLessUser / reviewsPerPage);
          } else {
            return Math.ceil(allCounter / reviewsPerPage);
          }
        } else {
          return Math.ceil(allCounter / reviewsPerPage);
        }
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
      console.log("iniciando mudança no changeAll");

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
    updateErrorMessage() {
      const userReview = this.changeUserReview;
      const allReviews = this.changeAllReviews;
      const filter = this.checkedFilter;
      let allCounter;
      let countLessUser;
      if (allReviews) {
        allCounter = allReviews.length;
      }
      if (userReview) {
        countLessUser = allReviews.length - 1;
      }
      if (allReviews) {
        if (userReview) {
          if (userReview.rating == filter || filter == "") {
            if (countLessUser > 0) {
              return "";
            } else {
              return "No reviews found from other users.";
            }
          } else {
            if (allCounter == 0) {
              return "No reviews found from other users.";
            } else {
              return "";
            }
          }
        } else {
          if (allCounter == 0) {
            return "No reviews found";
          }
        }
      }
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
      const path = this.$route.path;
      const order = this.checkedFilter;
      this.$router.push({
        path: path,
        query: { page, order },
      });
    },
    async fetchReviews(game) {
      try {
        if (this.logged) {
          const response = await axios.get(
            `${process.env.VUE_APP_APIURL}fetch-game-reviews`,
            {
              params: {
                game_api_id: game,
                filter_reviews: this.checkedFilter,
                user_id: this.userId,
              },
            }
          );
          console.log(response.data.reviewsData);
          this.reviews = response.data.reviewsData;
          this.$emit("fetchNewData_All", response.data.reviewsData);
          if (response.data.userReviewData) {
            const review = response.data.userReviewData[0];
            this.$emit("userReview", review);
            this.userReview = review;
            this.$emit("reviewChecker", true);
          }
        } else {
          const response = await axios.get(
            `${process.env.VUE_APP_APIURL}fetch-game-reviews`,
            {
              params: {
                game_api_id: game,
                filter_reviews: this.checkedFilter,
              },
            }
          );
          this.reviews = response.data.reviewsData;
          this.$emit("fetchNewData_All", response.data.reviewsData);
        }
      } catch (error) {
        console.log(error);
      }
    },
    formattedDate(created_at) {
      return this.formattedDates(created_at);
    },
    updateShowingCounter() {
      console.log("iniciando mudanças no showingCounter");
      const userReview = this.changeUserReview;
      const allReviews = this.changeAllReviews;
      const filter = this.checkedFilter;
      const page = this.currentPage;
      const totalPages = this.totalPages;
      const reviewsPerPage = this.reviewsPerPage;
      let allCounter;
      let countLessUser;
      if (allReviews) {
        allCounter = allReviews.length;
      }
      if (userReview) {
        countLessUser = allReviews.length - 1;
      }
      //caso usuario tenha reviews
      if (allReviews) {
        if (!userReview) {
          //não está na ultima página
          if (page < totalPages) {
            return (
              "Showing " +
              (page * reviewsPerPage - (reviewsPerPage - 1)) +
              " to " +
              reviewsPerPage * page +
              " of " +
              allCounter
            );
          }
          //está na ultima página
          if (page == totalPages) {
            return (
              "Showing " +
              (page * reviewsPerPage - (reviewsPerPage - 1)) +
              " to " +
              allCounter +
              " of " +
              allCounter
            );
          }
          //caso user tenha review
        } else {
          if (userReview.rating == filter || filter == "") {
            //nao está na ultima pagina
            if (page < totalPages) {
              return (
                "Showing " +
                (page * reviewsPerPage - (reviewsPerPage - 1)) +
                " to " +
                reviewsPerPage * page +
                " of " +
                countLessUser
              );
            }
            //esta na ultima página
            if (page == totalPages) {
              return (
                "Showing " +
                (page * reviewsPerPage - (reviewsPerPage - 1)) +
                " to " +
                countLessUser +
                " of " +
                countLessUser
              );
            }
          } else {
            if (page < totalPages) {
              return (
                "Showing " +
                (page * reviewsPerPage - (reviewsPerPage - 1)) +
                " to " +
                reviewsPerPage * page +
                " of " +
                allCounter
              );
            }
            if (page == totalPages) {
              return (
                "Showing " +
                (page * reviewsPerPage - (reviewsPerPage - 1)) +
                " to " +
                allCounter +
                " of " +
                allCounter
              );
            }
          }
        }
      }
    },
  },
  mounted() {
    if (this.game !== null && this.game !== undefined) {
      const urlParams = new URLSearchParams(window.location.search);
      this.currentPage = parseInt(urlParams.get("page")) || 1;
      this.checkedFilter = urlParams.get("order") || "";
      this.fetchReviews(this.game.id);
      console.log(this.showHideButtons);
    }
  },
};
</script>

<style scoped>
.page-button {
  text-align: center;
  font-weight: 800;
  background-color: #23272a;
  color: #fff;
  font-size: 1.3vh;
  margin: 0 0.6vw;
  border: 1px solid #fff;
  border-radius: 10px;
  cursor: pointer;
  padding: 2vh;
}
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
  .page-button {
    padding: 2vh 1.6vw;
  }
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

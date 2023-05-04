<template>
  <div class="main-wrapper">
    <Loading v-if="loadingUser || loadingReviews" />
    <div
      v-else-if="!user && !loadingUser && !loadingReviews"
      class="user-doesnt-exist"
    >
      <h1>404</h1>
      <p>Profile not found</p>
      <i class="fas fa-user-slash"></i>
    </div>
    <div v-if="user">
      <div class="back-profile">
        <router-link
          :to="{
            name: 'profile',
            params: { id: userId },
            query: { redirect: redirect },
          }"
          :key="$route.fullPath"
        >
          <i class="fas fa-caret-left"></i> Back to Profile
        </router-link>
      </div>
    </div>
    <div
      class="review-container"
      v-if="user && !loadingUser && !loadingReviews"
    >
      <h3 class="review-title" v-if="userReviews && checkOwnProfile">
        Your reviews
      </h3>
      <h3 class="review-title" v-else-if="userReviews">
        <i>{{ userName }}'s</i> reviews
      </h3>

      <h3 class="review-title" v-else-if="checkOwnProfile">
        You haven't done any reviews
      </h3>
      <h3 class="review-title" v-else>No reviews found</h3>

      <ReviewsArticles
        @updatingReview="updateUserReviews"
        :profileReviews="userReviews"
        :logged="checkOwnProfile"
        @deleteClicked="setVariables"
      />
      <!-- delete review confirmation -->
      <div class="confirmations" v-if="deleteConfirm">
        <div class="confirm-option">
          <h3 class="confirm-title">Delete Review</h3>
          <p class="confirm-text">
            Are you sure you want to delete your review?
          </p>
          <div class="button-container">
            <button
              class="confirm-button"
              @click="deleteProfileReview(removeId, gameId)"
            >
              Delete Review
            </button>
            <button class="cancel-button" @click="showConfirmDelete">
              Cancel
            </button>
          </div>
        </div>
      </div>
      <!-- end delete review confirmation -->
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ReviewsForm from "../../components/Review/ReviewsForm.vue";
import ReviewsArticles from "../../components/Review/ReviewsArticles.vue";
import Loading from "../../components/Tools/Loading.vue";

export default {
  name: "ReviewsView",
  components: {
    ReviewsForm,
    ReviewsArticles,
    Loading,
  },
  data() {
    return {
      userId: this.$route.params.id,
      userReviews: null,
      loadingUser: true,
      loadingReviews: true,
      user: null,
      redirect: this.$route.query.redirect,
      deleteConfirm: false,
      removeId: null,
      gameId: null,
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
    setVariables(userId, gameId) {
      this.removeId = null;
      this.gameId = null;
      this.removeId = userId;
      this.gameId = gameId;
      this.showConfirmDelete();
    },
    showConfirmDelete() {
      this.deleteConfirm = !this.deleteConfirm;
    },
    updateUserReviews(reviews) {
      this.userReviews = reviews;
    },
    async fetchUserReviews() {
      this.loadingReviews = true;
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
          this.loadingReviews = false;

          return false;
        }
        this.loadingReviews = false;

        this.userReviews = response.data;
      } catch (error) {
        this.loadingReviews = false;

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
        this.showConfirmDelete();
        console.log("depois: " + this.deleteConfirm);
        //aqui recebo o que o laravel me retornou
        if (response.data.profileReviews.length == 0) {
          this.userReviews = null;
          return false;
        }
        console.log(this.deleteConfirm);
        console.log(this.deleteConfirm);
        this.userReviews = response.data.profileReviews;
      } catch (error) {
        this.showConfirmDelete();

        console.log(error.response.data.message);
        console.log(error.response.data.validation);
        //aqui vai mostrar os erros pra cada uma das validações!
        console.log(error.response.data.errors);
      }
    },
    async getUserInfo() {
      this.loadingUser = true;
      this.user = null;
      const id = this.$route.params.id;
      try {
        const response = await axios
          .get(`${process.env.VUE_APP_APIURL}userinfo/${id}`)
          .then((response) => {
            this.user = response.data.user;
          });
      } catch (error) {
        this.loadingReviews = false;

        console.log(error.response.data.error);
      }
      this.loadingUser = false;
    },
  },
  mounted() {
    this.getUserInfo();
    this.fetchUserReviews();
  },
};
</script>

<style scoped>
/*confirmations */
.confirmations {
  position: absolute;
  background-color: #161b3a;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  z-index: 998;
  opacity: 95%;
}
.confirm-option {
  margin: 0 auto;
  position: fixed;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 50%;
}
.confirm-text {
  margin: 2vh;
}
.confirm-button {
  background: #bc1a3a;
  color: white;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 15vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
}
.confirm-button:hover {
  background: #f3224b;
}

.cancel-button {
  background: rba(255, 255, 255, 0.596);
  color: #23272a;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 15vw;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
}
.cancel-button:hover {
  background-color: white;
}
/*end confirm buttons */
.back-profile {
  margin-left: 2vw;
  margin-top: 1vh;
}
.back-profile a {
  color: white;
}
.loading-user {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
.user-doesnt-exist {
  text-align: center;
  position: absolute;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-shadow: 2px 2px #000;
  padding: 2px;
}
.user-doesnt-exist h1 {
  font-weight: bolder;
  font-size: 44px;
  padding: 2px;
}
.user-doesnt-exist p {
  font-weight: 400;
  font-size: 22px;
  padding: 4px;
}
.user-doesnt-exist i {
  font-size: 22px;
  padding: 2px;
}

.review-container {
  margin: 0 auto;
  padding: 2vh;
  width: 70%;
  max-width: 70%;
  margin-top: 1vh;
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
  .confirm-button,
  .cancel-button {
    width: 35vw;
  }
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

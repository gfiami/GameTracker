<template>
  <div class="main-wrapper">
    <Message :messageLogout="messageLogout" />
    <div class="user-welcome" v-if="logged">
      Welcome back,

      <router-link
        :to="{ name: 'profile', params: { id: $store.state.user_id } }"
        :key="$route.fullPath"
        ><span class="username"
          ><u>{{ userName }} </u></span
        ></router-link
      >
    </div>
    <div class="title">
      All Your <span class="games-text">Games</span> In One Place
    </div>
    <div class="register" v-if="!logged">
      <router-link to="/register">Join now and start exploring!</router-link>
    </div>
    <div class="main-container">
      <div class="image-section-container">
        <div class="image-container">
          <img src="@/assets/gamer-room.jpg" alt="" />
        </div>
      </div>
      <div class="info-container" v-if="!logged">
        <div class="info-title">With GameTracker you can:</div>
        <div class="info">
          <i class="fas fa-gamepad"></i>
          <p class="info-description">
            Get all the details you need about your favorite games, including
            release dates, ratings, reviews, and more.
          </p>
        </div>
        <div class="info">
          <i class="fas fa-comments"></i>

          <p class="info-description">
            Share your thoughts and opinions on the games you play by leaving
            detailed reviews and ratings for others to see.
          </p>
        </div>
        <div class="info">
          <i class="fas fa-users"></i>
          <p class="info-description">
            Connect with fellow gamers and check out their profiles to see what
            games they're playing, what they've reviewed, and what they're
            excited about.
          </p>
        </div>
        <div class="info">
          <i class="fas fa-play-circle"></i>
          <p class="info-description">
            Keep track of your gaming library by adding games to your collection
            in different ways.
          </p>
        </div>
      </div>
      <!-- usuario logado -->
      <div class="info-container" v-else>
        <div class="info">
          <i class="fas fa-gamepad"></i>
          <p class="info-description">
            If you want to find information about games and update your
            collection, you should check the
            <router-link to="/games" :key="$route.fullPath"
              ><u>Games</u></router-link
            >
            page.
          </p>
        </div>
        <div class="info">
          <i class="fas fa-comments"></i>
          <p class="info-description">
            Share your opinions on the games you've played by rating them and
            leaving detailed reviews. Access reviews from other users to help
            inform your own gaming decisions.
          </p>
        </div>
        <div class="info">
          <i class="fas fa-users"></i>
          <p class="info-description">
            Connect with other by browsing profiles, where you can check each
            user reviews and collection!
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Message from "../../components/Message.vue";
export default {
  name: "HomeView",
  data() {
    return {
      messageLogout: null || this.$route.query.messageLogout,
    };
  },
  watch: {
    "$route.query": {
      handler: "setMessage",
    },
  },
  computed: {
    logged() {
      return this.$store.state.logged;
    },
    userName() {
      return this.$store.state.token;
    },
  },
  components: {
    Message,
  },
  mounted() {
    if (this.messageLogout) {
      setTimeout(() => {
        this.messageLogout = null;
      }, 10000);
    }
  },
  methods: {
    setMessage() {
      this.messageLogout = this.$route.query.messageLogout;
      setTimeout(() => {
        this.messageLogout = null;
      }, 10000);
    },
  },
};
</script>

<style scoped>
.user-welcome {
  text-align: center;
  margin: 0 auto;
  margin-top: 2.5vh;
  font-size: 2.7vh;
}
.user-welcome a {
  text-decoration: none;
}
.main-container {
  width: 90vw;
  margin: 0 auto;
  margin-bottom: 4vh;
  align-items: flex-start;
}
.title {
  margin: 0 auto;
  margin-top: 2.5vh;
  text-align: center;
  font-size: 4vh;
  font-weight: 900;
}
.register {
  text-align: center;
  margin: 0 auto;
  margin-top: 2vh;
  font-size: 1.9vh;
  font-weight: 700;
}
.register a {
  background-color: #1abc9c;
  padding: 1.5vh 1.5vh;
  width: 65vw;
  border-radius: 12px;

  text-decoration: none;
  color: black;
}
.games-text {
  color: #1abc9c;
}
.username {
  color: #1abc9c;
}
.image-section-container {
  margin: 0 auto;
  margin-top: 2vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.image-container {
  display: flex;
  width: 90vw;
  border-radius: 22px;
  align-content: center;
  justify-content: center;
  gap: 3vh;
}
.image-container img {
  width: 100%;
  border-radius: 20px;
}
.info-container {
  display: flex;
  flex-direction: column;
  width: 90vw;
  margin: 0 auto;
  margin-top: 4vh;
  gap: 1vh;
}
.info-title {
  margin-bottom: 1vh;
}
.info {
  padding: 1.2vh 1.2vh;
  border-radius: 2px;
  display: flex;
  gap: 3vw;
  background: #1abc9c;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
  color: black;
  justify-content: center;
  align-items: center;
}
.info i {
  font-size: 4vh;
  padding: 0 1vw;
}
.info a {
  text-decoration: none;
  color: black;
}
.info p {
  font-size: 2vh;
  font-weight: 700;
}
@media screen and (min-width: 768px) {
  .register {
    width: 25vw;
  }
  .image-section-container {
    margin-left: auto;
    width: 35%;
    margin: 0;
    margin-top: 2vh;
    justify-content: flex-start;
  }
  .image-container {
    justify-items: flex-start;
  }
  .info-container {
    margin: 0;
    margin-top: 2vh;
    width: 45%;
    justify-content: center;
  }

  .main-container {
    margin-top: 4vh;
    display: flex;
    justify-content: space-around;
  }
}
</style>

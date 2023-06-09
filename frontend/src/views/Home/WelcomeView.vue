<template>
  <div class="main-wrapper">
    <div class="back-route" v-if="redirect">
      <router-link :to="redirect">
        <i class="fas fa-caret-left"></i> Go back to where you were</router-link
      >
    </div>
    <div id="welcome">
      <div class="title-container">
        <h1 class="title">Thank you for joining GameTracker!</h1>
      </div>
      <div class="welcome-message-container">
        <p class="welcome-message">
          Keep track of your favorite games from a variety of platforms.
        </p>
        <p class="welcome-message">
          Our website has over 800,000 games to track, with information and
          reviews. You can also check out other players' profiles and see how
          they're doing!
        </p>
        <p class="welcome-message">
          Feel free to contact our support team with any questions or feedback.
        </p>
      </div>
      <p class="welcome-end">Happy gaming!</p>

      <div class="tutorial-container">
        <div class="tutorial-title">
          <h2 class="subtitle">GameTracker tutorial</h2>
          <h3 class="info-category">Tracking games</h3>
          <div class="tutorial-content">
            <div class="tutorial-image-container">
              <img class="tutorial-image" src="@/assets/tutorial.png" alt="" />
            </div>
            <div class="tutorial-message-container">
              <p class="tutorial-message">
                In the <i>Games</i> page you can search all games you want to
                track!
              </p>
              <p class="tutorial-message">
                On mobile devices, tap the icons from each game to track it,
                mark it as owned, add it to your wishlist, or favorite it.
              </p>
              <p class="tutorial-message">
                On desktop devices, hover over the game with your cursor to show
                the options.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="tutorial-container">
        <div class="tutorial-title">
          <h2 class="subtitle">GameTracker tutorial</h2>

          <h3 class="info-category">Game Info and Reviews</h3>
          <div class="tutorial-content">
            <div class="tutorial-message-container">
              <p class="tutorial-message">
                Each game has its own page with lots of information and reviews!
              </p>
              <p class="tutorial-message">
                On mobile devices, tap the game title to go to the game page. On
                desktop devices, click the info icon that appears on hover to go
                to the game page.
              </p>
              <p class="tutorial-message">
                This page allows you to track games and write/read reviews.
              </p>
            </div>
            <div class="tutorial-image-container">
              <img
                class="tutorial-image"
                src="@/assets/tutorial-info.png"
                alt=""
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "WelcomeView",
  data() {
    return {
      redirect: this.$route.query.redirect,
    };
  },
  mounted() {
    window.scrollTo(0, 0);
  },
};
</script>

<style scoped>
* {
  text-shadow: 1px 1px 2px #6842ff;
}
#welcome {
  margin-bottom: 8vh;
}
.back-route {
  margin-left: 2vw;
  margin-top: 1vh;
}
.back-route a {
  color: white;
}
.welcome-end {
  width: 100%;
  text-align: center;
  font-size: 2.3vh;
  font-weight: 400;
  margin-bottom: 2vh;
}
.title-container {
  margin-top: 1vh;
}
.info-category {
  margin-left: 5vw;
  margin-top: 2vh;
}
.title {
  text-align: center;
  font-size: 4vh;
  font-weight: 700;
}
.subtitle {
  background-color: #280051;
  width: 100%;
  margin: 0 auto;
  padding: 2vh;
  border-radius: 12px;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.tutorial-container {
  padding-bottom: 4vh;
  margin: 0 auto;
  background-color: #330066;
  width: 90%;
  border-radius: 12px;
  margin-bottom: 4vh;
  box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.5), 0 0 40px 10px rgba(0, 0, 0, 0.2);
}

.subtitle {
  text-align: center;
  font-size: 3vh;
  font-weight: 600;
}
.welcome-message-container {
  width: 100%;
  margin: 0 auto;
  padding: 2vh;
}
.welcome-message,
.tutorial-message {
  text-align: center;
  font-size: 2vh;
  margin-bottom: 1.3vh;
}

.tutorial-image-container {
  display: flex;
  align-items: center;
  justify-content: center;
}
.tutorial-image {
  width: 100%;
  border-radius: 12px;
  box-shadow: 1px;
  margin-bottom: 1vh;
}
@media screen and (min-width: 768px) {
  .title {
    font-size: 5vh;
  }
  .welcome-message {
    font-size: 2.1vh;
  }

  .tutorial-image {
    width: 100%;
  }
  .tutorial-container {
    width: 70%;
  }
  .tutorial-content {
    width: 80%;
    margin: 0 auto;
    margin-top: 2vh;
    display: flex;
    justify-content: center;
    gap: 1.5vw;
  }
  .welcome-end {
    font-size: 2.4vh;
  }
  .welcome-message-container,
  .tutorial-message-container {
    width: 50vw;
    display: flex;
    gap: 1.3vw;
    align-items: center;
  }
  .welcome-message-container {
    width: 70vw;
  }
  .welcome-message,
  .tutorial-message {
    text-align: left;
    flex-basis: 0;

    flex-grow: 1;
  }
}
</style>

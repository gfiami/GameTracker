<template>
  <div id="footer">
    <div class="copyright">
      <p>GameTracker &copy; {{ new Date().getFullYear() }}</p>
    </div>
    <div class="links">
      <div class="regular-link">
        <router-link to="/contact" :key="$route.fullPath"
          ><u>Contact</u></router-link
        >
      </div>
      <div class="regular-link" v-if="logged">
        <router-link to="/welcome" :key="$route.fullPath"
          ><u>Help</u></router-link
        >
      </div>
      <div class="social-link"><i class="fab fa-twitter-square"></i></div>
      <div class="social-link"><i class="fab fa-reddit-square"></i></div>
      <div class="social-link"><i class="fab fa-instagram-square"></i></div>
    </div>
    <div class="api-credits">
      <a href="https://rawg.io/" target="_blank"
        >Powered by <span class="rawg">RAWG</span></a
      >
    </div>
  </div>
</template>

<script>
export default {
  name: "Footer",
  computed: {
    logged() {
      return this.$store.state.logged;
    },
  },
};
</script>

<style scoped>
#footer {
  height: 40vh;
  background: rgba(54, 30, 148, 0.9);
  color: #fff;
  display: flex;
  align-items: center;
  flex-direction: column;
  width: 100%;
  position: absolute;
  bottom: 0;
  left: 0;
  gap: 4vh;
}
#footer i {
  color: rgba(255, 255, 255, 0.596);
  transition: 0.4s;
}
#footer i:hover {
  color: #fff;
}
.copyright {
  display: flex;
  justify-content: center;
  margin-top: 9vh;
}
.copyright p {
  font-weight: 400;
  font-size: 3vh;
}

.links {
  display: flex;
  gap: 2vw;
}
.regular-link {
  display: flex;
  justify-content: center;
  align-items: center;
}
.regular-link a {
  text-decoration: none;
  color: rgba(255, 255, 255, 0.596);
  transition: 0.4s;
}
.regular-link a:hover {
  color: white;
}
.social-link {
  font-size: 3.5vh;
  cursor: pointer;
}
.api-credits a {
  text-decoration: none;
  color: rgba(255, 255, 255, 0.596);
  cursor: pointer;
  font-size: 1.7vh;
  transition: 0.4s;
}
.api-credits a:hover {
  color: white;
}
.rawg {
  color: white;
  background-color: black;
  border-radius: 6px;
  padding: 0.5vh 0.5vh;
}
@media screen and (min-width: 768px) {
}
</style>

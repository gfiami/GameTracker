<template>
  <div id="footer">
    <div class="copyright">
      <p>GameTracker &copy; {{ new Date().getFullYear() }}</p>
    </div>
    <div class="footer-secondary">
      <div class="api-credits">
        <a href="https://rawg.io/" target="_blank"
          >Powered by <span class="rawg">RAWG</span></a
        >
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
  height: 7vh;
  background: rgba(54, 30, 148, 0.9);
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  width: 100%;
  position: absolute;
  bottom: 0;
  left: 0;
  padding: 7vh 2vh;
}
.copyright {
  display: flex;
  justify-content: center;
}
.copyright p {
  font-weight: 400;
  font-size: 2vh;
}
.footer-secondary {
  display: flex;
  width: 80%;
  align-items: center;
  justify-content: center;
}
.api-credits a {
  text-decoration: none;
  color: white;
  cursor: pointer;
  font-size: 1.7vh;
}
.rawg {
  background-color: black;
  border-radius: 6px;
  padding: 0.5vh 0.5vh;
}
.api-credits,
.links {
  width: 50%;
}
.links {
  display: flex;
  justify-content: flex-end;
  gap: 1vw;
}
.regular-link {
  display: flex;
  justify-content: center;
  align-items: center;
}
.regular-link a {
  text-decoration: none;
  color: white;
}
.social-link {
  font-size: 3.5vh;
  cursor: pointer;
}
@media screen and (min-width: 768px) {
  #footer {
    flex-direction: row;
    justify-content: center;
  }

  .api-credits {
    display: flex;
    justify-content: flex-end;
  }
}
</style>

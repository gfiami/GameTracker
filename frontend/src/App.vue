<template>
  <div class="app">
    <Navbar />
    <div class="main-wrapper">
      <router-view />
    </div>
    <Footer />
  </div>
</template>

<script>
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";
export default {
  components: {
    Navbar,
    Footer,
  },
};
</script>
<style>
@import url("https://fonts.googleapis.com/css2?family=Nunito&display=swap");
* {
  font-family: "Nunito", sans-serif;
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}
html,
body {
  height: 100%;
}
body {
  background-color: #161b3a;
}
.app {
  height: 100%;
  position: relative;
}
.main-wrapper {
  min-height: calc(100vh - 65px);
  position: relative;
  padding-bottom: 65px;
}
</style>

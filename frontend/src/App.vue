<template>
  <div class="app">
    <Navbar />
    <router-view />
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
  color: #fff;
}
body {
  background-color: #161b3a;
}
.app {
  height: 100%;
  position: relative;
}
.main-wrapper {
  min-height: calc(100vh - 6vh);
  position: relative;
  padding-bottom: 40vh;
}
::-webkit-scrollbar {
  height: 2px;
  width: 7px;
  background: gray;
}
::-webkit-scrollbar-thumb:vertical {
  background: rgba(54, 30, 148, 0.9);
  border-radius: 2px;
}
::-webkit-scrollbar-thumb:horizontal {
  background: rgba(54, 30, 148, 0.9);
  border-radius: 2px;
}
</style>

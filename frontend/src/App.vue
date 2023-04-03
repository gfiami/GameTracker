<template>
  <div>
    <Navbar />
  </div>
</template>

<script>
import Navbar from "./components/Navbar.vue";
export default {
  components: {
    Navbar,
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

/*'
nav {
  padding: 30px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
} */
</style>

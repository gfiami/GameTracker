<template>
  <div id="nav">
    <router-link id="website-name" to="/">GameTracker</router-link>
    <router-link class="nav-link" to="/games">Games</router-link>
    <router-link class="nav-link" to="/login">Login</router-link>
    <router-link class="nav-link" to="/register">Register</router-link>
  </div>
</template>

<script>
export default {
  name: "Navbar",
};
</script>

<style scoped>
#nav {
  background-color: #6842ff;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 15px 50px;
}
#nav #website-name {
  font-size: 32px;
  font-weight: 800;
  margin: auto;
  margin-left: 0;
  color: #fff;
}
#nav a {
  color: #fff;
  text-decoration: none;
  margin: 10px;
  transition: 0.4s;
}
#nav a:hover {
  color: #161b3a;
}
#nav a.router-link-exact-active {
  color: #161b3a;
}
</style>

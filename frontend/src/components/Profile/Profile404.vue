<template>
  <div class="user-doesnt-exist">
    <h1>404</h1>
    <p>Profile not found</p>
    <i class="fas fa-user-slash"></i>
  </div>
</template>

<script>
export default {
  name: "Profile404",
};
</script>

<style scoped>
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
</style>

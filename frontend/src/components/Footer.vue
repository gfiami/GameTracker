<template>
  <div id="footer">
    <p>GameTracker &copy; {{ new Date().getFullYear() }}</p>
  </div>
</template>

<script>
export default {
  name: "Footer",
};
</script>

<style scoped>
#footer {
  height: 65px;
  background: rgba(54, 30, 148, 0.9);
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  position: absolute;
  bottom: 0;
  left: 0;
  font-weight: 400;
  font-size: 20px;
}
</style>

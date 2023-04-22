<template>
  <div class="main-wrapper">
    <h1>HOME</h1>
    <Message :messageLogout="messageLogout" />
  </div>
</template>

<script>
import Message from "../../components/Message.vue";
export default {
  name: "HomeView",
  data() {
    return {
      messageLogout: this.$route.query.messageLogout || null,
    };
  },

  components: {
    Message,
  },
  mounted() {
    if (this.messageLogin || this.messageLogout) {
      setTimeout(() => {
        this.messageLogin = null;
        this.messageLogout = null;
        this.loginSuccess = null;
      }, 4000);
    }
  },
};
</script>

<style scoped></style>

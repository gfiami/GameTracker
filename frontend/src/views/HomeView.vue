<template>
  <div class="main-wrapper">
    <h1>HOME</h1>
    <Message
      :messageLogin="messageLogin"
      :loginSuccess="loginSuccess"
      :messageLogout="messageLogout"
    />
  </div>
</template>

<script>
import Message from "../components/Message.vue";

export default {
  name: "HomeView",
  data() {
    return {
      messageLogin: this.$route.query.messageLogin || null,
      loginSuccess: this.$route.query.loginSuccess || null,
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

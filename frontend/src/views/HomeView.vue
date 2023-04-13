<template>
  <div class="main-wrapper">
    <h1>HOME</h1>
    <Message :messageLogin="messageLogin" :loginSuccess="loginSuccess" />
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
    };
  },
  components: {
    Message,
  },
};
</script>

<style scoped></style>

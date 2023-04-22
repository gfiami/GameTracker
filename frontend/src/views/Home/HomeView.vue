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
      messageLogout: null || this.$route.query.messageLogout,
    };
  },
  watch: {
    "$route.query": {
      handler: "setMessage",
    },
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
  methods: {
    setMessage() {
      this.messageLogout = this.$route.query.messageLogout;
      console.log(this.$route.query.messageLogout);
    },
  },
};
</script>

<style scoped></style>

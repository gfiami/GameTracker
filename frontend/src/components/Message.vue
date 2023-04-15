<template>
  <div>
    <p v-if="loginSuccess && showMessage && logged">
      Login successeful! <i class="fas fa-check-circle"></i>
      <br />
      Welcome <b>{{ messageLogin }} </b>
    </p>
    <p v-if="message && showMessage">{{ message }}</p>
    <p v-if="messageLogout && showMessage">{{ messageLogout }}</p>
  </div>
</template>

<script>
export default {
  name: "Message",
  props: {
    message: "",
    messageLogin: "",
    messageLogout: "",
    loginSuccess: "",
  },
  data() {
    return {
      showMessage: true,
    };
  },
  mounted() {
    /*if (this.message || this.messageLogin || this.messageLogout) {
      setTimeout(() => {
        this.showMessage = false;
      }, 4000);
    }*/
  },
  computed: {
    logged() {
      return this.$store.state.logged;
    },
  },
};
</script>

<style scoped>
p {
  width: 90%;
  font-size: 2vh;
  font-weight: 600;
  margin: 0 auto;
  border-radius: 4px;
  padding-top: 20px;
  padding-bottom: 20px;
  background-color: #1abc9c;
  color: black;
  text-align: center;
  margin-top: 20px;
}
@media screen and (min-width: 768px) {
  p {
    width: 45%;
  }
}
</style>

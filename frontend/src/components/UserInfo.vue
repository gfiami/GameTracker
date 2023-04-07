<template>
  <div></div>
</template>

<script>
import { mapMutations } from "vuex";
import axios from "axios";

export default {
  name: "UserInfo",
  methods: {
    logged() {
      if (this.$store.state.logged) {
        console.log("chamou");
        this.getUserName();
      }
    },
    async getUserName() {
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}userinfo`,
        {
          token: this.$store.state.token,
        }
      );
      console.log("teste antes do emit");
      console.log(response.data.user);
      this.$emit("userName", response.data.user.name);
    },
    mounted() {
      this.logged();
    },
  },
};
</script>

<style></style>

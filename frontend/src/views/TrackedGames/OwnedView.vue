<template>
  <div class="main-wrapper">
    <h1>Owned Games</h1>
    <TrackedGames :gameIds="ownedIds" />
  </div>
</template>

<script>
import axios from "axios";
import TrackedGames from "../../components/TrackedGames.vue";
export default {
  name: "OwnedView",
  components: {
    TrackedGames,
  },
  watch: {},
  data() {
    return {
      ownedIds: "",
    };
  },
  methods: {
    async getOwnedGames() {
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-owned`,
          {
            params: {
              user_id: this.$route.params.id,
            },
          }
        );
        //pega os jogos "owned" e bota no array
        this.ownedIds = response.data;
        if (this.ownedIds.length == 0) {
          this.emptyOwned = true;
        } else {
          this.emptyOwned = false;
        }
        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
  },
  mounted() {
    this.getOwnedGames();
  },
};
</script>

<style scoped></style>

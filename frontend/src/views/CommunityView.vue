<template>
  <div class="main-wrapper">
    <SearchGeneric />
  </div>
</template>

<script>
import axios from "axios";
import SearchGeneric from "../components/SearchGeneric.vue";
export default {
  name: "CommunityView",
  components: {
    SearchGeneric,
  },
};
</script>

<style scoped></style>

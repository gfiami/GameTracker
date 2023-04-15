<template>
  <div>
    total: {{ totalPages }} atual: {{ currentPage }}
    <div class="pagination" v-if="totalPages">
      <button class="page-button" @click="changePage(2)">Previous</button>
      <button class="page-button" @click="changePage(2)"></button>
      <button class="page-button" @click="changePage(2)"></button>
      <button class="page-button" @click="changePage(2)"></button>
      <button class="page-button" @click="changePage()"></button>
      <button></button>
    </div>
  </div>
</template>

<script>
export default {
  name: "PaginationsReview",
  props: {
    totalPages: Number,
    currentPage: Number,
  },
  data() {
    return {
      nextButton: "",
      previousButton: "",
      firstButton: true,
      lastButton: true,
    };
  },
  methods: {
    changePage(page) {
      this.$emit("goToPage", page);
    },
  },
};
</script>

<style scoped>
.pagination {
  bottom: 0;
  display: flex;
  justify-content: center;
  margin-top: 40px;
  margin-bottom: 20px;
}
.pagination {
  width: 100%;
  height: 90px;
  position: absolute;
  bottom: 0;
}

.page-button {
  font-weight: 700;
  background-color: #23272a;
  color: #fff;
  font-size: 16px;
  margin: 0 5px;
  padding: 5px 10px;
  border: 1px solid #fff;
  border-radius: 10px;
  cursor: pointer;
  height: 35px;
}
.dots {
  color: #fff;
  padding-top: 10px;
}
#currentPage {
  background-color: #546e7a;
}
</style>

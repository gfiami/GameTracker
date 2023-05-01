<template>
  <div>
    <div class="pagination" id="desktop" v-if="totalPages">
      <!-- pagina anterior -->
      <button
        v-if="previous"
        class="page-button"
        @click="changePage(currentPage - 1)"
      >
        Previous
      </button>

      <!-- pagina inicial se a página atual nao for 1 -->
      <button
        v-if="currentPage !== 1"
        class="page-button"
        @click="changePage(1)"
      >
        1
      </button>
      <span v-if="currentPage - 3 > 1">...</span>
      <!-- pagina atual -2 -->
      <button
        v-if="currentPage - 2 > 1"
        class="page-button"
        @click="changePage(currentPage - 2)"
      >
        {{ currentPage - 2 }}
      </button>
      <!-- pagina atual - 1 -->
      <button
        v-if="currentPage - 1 > 1"
        class="page-button"
        @click="changePage(currentPage - 1)"
      >
        {{ currentPage - 1 }}
      </button>
      <!-- pagina atual -->
      <button id="currentPage" class="page-button">{{ currentPage }}</button>

      <!-- pagina atual mais um -->
      <button
        v-if="currentPage + 1 < totalPages"
        class="page-button"
        @click="changePage(currentPage + 1)"
      >
        {{ currentPage + 1 }}
      </button>
      <!-- pagina atual mais 2 -->
      <button
        v-if="currentPage + 2 < totalPages"
        class="page-button"
        @click="changePage(currentPage + 2)"
      >
        {{ currentPage + 2 }}
      </button>

      <!-- se a pagina atual mais 2 for menor que a ultima página n mostra reticiencias -->
      <span v-if="currentPage + 3 < totalPages">...</span>
      <button
        v-if="currentPage !== totalPages"
        class="page-button"
        @click="changePage(totalPages)"
      >
        {{ totalPages }}
      </button>
      <button
        v-if="next"
        class="page-button"
        @click="changePage(currentPage + 1)"
      >
        Next
      </button>
    </div>

    <!-- mobile -->
    <div class="pagination" id="mobile" v-if="totalPages">
      <!-- pagina anterior -->
      <button
        v-if="previous"
        class="page-button"
        @click="changePage(currentPage - 1)"
      >
        Previous
      </button>

      <!-- pagina atual -->
      <select
        name=""
        id=""
        v-model="selectedPage"
        @change="changePage(selectedPage)"
      >
        <option value="1" v-if="currentPage !== 1">1</option>
        <option disabled class="dots" v-if="currentPage > 4">...</option>
        <option :value="currentPage - 2" v-if="currentPage - 2 > 1">
          {{ currentPage - 2 }}
        </option>
        <option :value="currentPage - 1" v-if="currentPage - 1 > 1">
          {{ currentPage - 1 }}
        </option>
        <option :value="currentPage">
          {{ currentPage }}
        </option>
        <option :value="currentPage + 1" v-if="currentPage + 1 < totalPages">
          {{ currentPage + 1 }}
        </option>
        <option :value="currentPage + 2" v-if="currentPage + 2 < totalPages">
          {{ currentPage + 2 }}
        </option>
        <option disabled class="dots" v-if="currentPage > 4">...</option>
        <option :value="totalPages" v-if="currentPage !== totalPages">
          {{ totalPages }}
        </option>
      </select>
      <button
        v-if="next"
        class="page-button"
        @click="changePage(currentPage + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "Pagination",
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
      buttonsOnSide: 2,
      selectedPage: 1,
    };
  },
  computed: {
    previous() {
      if (this.totalPages !== null) {
        if (this.currentPage > 1) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
    next() {
      if (this.totalPages !== null) {
        if (this.currentPage < this.totalPages) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
  },
  methods: {
    changePage(page) {
      this.selectedPage = page;
      this.$emit("goToPage", page);
    },
  },
};
</script>

<style scoped>
#desktop {
  display: none;
}
select {
  width: 10vw;
  text-align: center;
  background-color: #23272a;
  color: #fff;
  font-weight: 1.3vh;
  border-radius: 10px;
}
.pagination {
  bottom: 0;
  display: flex;
  justify-content: center;
  margin-top: 5vh;
  margin-bottom: 5vh;
}
span {
  display: flex;
  align-items: flex-end;
}
.pagination button {
  display: flex;
  align-items: center;
  padding: 1vh 2vw;
  margin: 0 1vw;
}
.page-button {
  text-align: center;
  font-weight: 800;
  background-color: #23272a;
  color: #fff;
  font-size: 1.3vh;
  margin: 0 0.6vw;
  border: 1px solid #fff;
  border-radius: 10px;
  cursor: pointer;
  height: 4vh;
}
.dots {
  color: #fff;
  padding-top: 10px;
}
#currentPage {
  background-color: #546e7a;
}
@media screen and (min-width: 768px) {
  .page-button {
    padding: 2vh 1.6vw;
  }
  #desktop {
    display: flex;
  }
  #mobile {
    display: none;
  }
}
</style>

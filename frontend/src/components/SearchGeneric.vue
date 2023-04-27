<template>
  <div class="search-container">
    <form class="search-features" @submit.prevent="">
      <div class="search-box">
        <input
          type="text"
          id="searchBar"
          :placeholder="searchPlaceholder"
          v-model="searchQuery"
        />
        <button @click="showSearch" id="searchButton" type="button">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <div id="sort">
        <span
          ><i
            :class="sortIconClass"
            @click="changeSortIcon()"
            id="sortButton"
          ></i
        ></span>
      </div>
    </form>
    <div class="search-info">
      <div v-if="showSearchResults" id="results">
        <span class="result-for">Results for &nbsp;</span>
        <span class="search-bar-input"> {{ showSearchResults }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SearchGeneric",
  props: {
    searchPlaceholder: String,
  },
  data() {
    return {
      sortOrder: "",
      searchQuery: "",
      showSearchResults: "",
      resetSearch: "",
    };
  },
  computed: {
    sortIconClass() {
      return "fas fa-sort" + (this.sortOrder === "" ? "-up" : "-down");
    },
  },
  methods: {
    showSearch() {
      //passar search query para a view, pelo search
      this.showSearchResults = this.searchQuery;
      this.$emit("searching", this.searchQuery);
    },
    changeSortIcon() {
      this.sortOrder = this.sortOrder === "" ? "-" : "";
      if (this.sortOrder == "") {
        this.$emit("changeOrder", "asc");
      } else {
        this.$emit("changeOrder", "desc");
      }
    },
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderChecker = urlParams.get("order") || "asc";
    if (orderChecker == "asc") {
      this.sortOrder = "";
    } else {
      this.sortOrder = "-";
    }
    const searchChecker = urlParams.get("search") || "";
    this.showSearchResults = searchChecker;
    this.searchQuery = searchChecker;
  },
};
</script>

<style scoped>
.search-container {
  display: flex;
  flex-direction: column;
  width: 80vw;
  align-items: center;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  margin-top: 2vh;
}

.search-info {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1vw;
}

#results {
  width: 100%;
  display: flex;
  justify-content: center;
  font-size: 2vh;
  font-weight: 500;
}
#sort {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}
#results .search-bar-input {
  font-size: 2vh;
  font-weight: 900;
  display: inline-block;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
  max-width: 70%;
}
.search-features {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

#sort,
#searchBar {
  opacity: 0.75;
}
#sortButton {
  cursor: pointer;
  padding: 1.3vh 2.3vh;
}

#sort {
  background-color: #23272a;
  border-radius: 10px;
}

/* botão bonitim com icone de pesquisa */
.search-box {
  position: relative;
  display: inline-block;
  margin: 10px;
}

#searchBar {
  width: 100%;
  border-radius: 10px;
  color: #fff;
  background-color: #23272a;
  border: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  font-size: 2.3vh;
  padding: 1vh 0;
  padding-left: 2vw;
  padding-right: 11vw;
}
#searchButton {
  color: #fff;
  position: absolute;
  top: 0;
  right: 0;
  width: 50px;
  height: 100%;
  border: none;
  background-color: transparent;
  cursor: pointer;
}

#searchButton:hover {
  color: #6842ff;
}

i.fas.fa-search {
  font-size: 18px;
}
@media screen and (min-width: 768px) {
  #results {
    width: 45%;
  }
  #searchBar {
    padding-right: 5vw;
  }
}
</style>

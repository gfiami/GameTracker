<template>
  <div class="search-container">
    <div class="search-info">
      <div id="counter">299,999 items</div>
      <div id="results">
        Results for
        <span class="search-bar-input">search bar input</span>
      </div>
    </div>

    <form class="search-features" @submit.prevent="">
      <div class="search-box">
        <input
          type="text"
          id="searchBar"
          placeholder="Search game"
          v-model="searchQuery"
        />
        <button @click="searchGames" id="searchButton" type="button">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <div id="sort">
        <span><i :class="sortIconClass" @click="changeSortIcon()"></i></span>
      </div>
      <select id="category">
        <option value="">Popular</option>
        <option value="">Title</option>
        <option value="">Release Date</option>
      </select>
    </form>
  </div>
</template>
<script>
export default {
  name: "SearchBar",
  data() {
    return {
      sortOrder: "asc",
      searchQuery: "",
    };
  },
  computed: {
    sortIconClass() {
      return "fas fa-sort" + (this.sortOrder === "asc" ? "-up" : "-down");
    },
  },
  methods: {
    changeSortIcon() {
      this.sortOrder = this.sortOrder === "asc" ? "desc" : "asc";
    },
    searchGames() {
      this.$emit("search", this.searchQuery);
    },
  },
};
</script>

<style scoped>
.search-container {
  display: flex;
  padding: 15px 50px;
  align-items: center;
  justify-content: center;
}
.search-features {
  display: flex;
  align-items: center;
  margin: 10px;
}
.search-info {
  display: flex;
  align-items: center;
  margin: auto;
  margin-left: 0;
}
.search-info #counter,
.search-info #results,
.search-features div {
  margin: 10px;
}
.search-info #results {
  margin-left: 150px;
}
#counter {
  font-size: 16px;
  font-weight: 400;
}
#results {
  font-size: 24px;
  font-weight: 500;
}
#results .search-bar-input {
  font-size: 24px;
  font-weight: 900;
}
#searchBar {
  height: 30px;
  width: 200px;
}
#counter,
#sort,
#searchBar,
#category {
  opacity: 0.75;
}
#searchBar,
#category {
  border: none;
  border-radius: 10px;
  color: #fff;
  background-color: #23272a;
}

#category {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);

  padding: 10px;
  width: 150px;
}

/* botão bonitim com icone de pesquisa */
.search-box {
  position: relative;
  display: inline-block;
  margin: 10px;
}

#searchBar {
  padding: 15px;
  border: none;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  font-size: 16px;
  width: 250px;
  height: 35px;
  padding-right: 45px;
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
</style>

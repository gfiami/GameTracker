<template>
  <div class="search-container">
    <div class="search-info">
      <div v-if="counter" id="counter">{{ counter }} games</div>
      <div v-else id="counter">0 games</div>
      <div v-if="showSearchResults" id="results">
        Results for
        <span class="search-bar-input">{{ showSearchResults }}</span>
      </div>
    </div>

    <form class="search-features" @submit.prevent="">
      <div class="search-box">
        <input
          type="text"
          id="searchBar"
          placeholder="Search games"
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
      <select id="category" v-model="filter" @change="changeFilter">
        <option value="added">Popularity</option>
        <option value="name">Title</option>
        <option value="released">Release Date</option>
        <option value="rating">Rating</option>
      </select>
    </form>
  </div>
</template>
<script>
export default {
  name: "ApiSearch",
  props: {
    counter: Number,
    resetSearch: "",
    //updateSearch: "",
    //updateOrder: "",
  },
  /*watch: {
    "$route.fullPath"(newVal) {
      if (newVal === "/games") {
        console.log("games");
        this.sortOrder = "-";
        this.searchQuery = "";
        this.orderAndFilter = null;
        this.showSearchResults = "";
        this.filter = "added";
      }
    },
    updateSearch: {
      immediate: true,
      /*handler(newValue, oldValue) {
        if (newValue !== "" && newValue !== "+") {
          this.searchQuery = newValue;
          this.showSearchResults = newValue;
          console.log("handler updateSearch");
          console.log(newValue);
          console.log(oldValue);
          //this.$emit("search", this.searchQuery);

          //console.log("após emit search");
          this.orderAndFilter = this.sortOrder + this.filter;
          //this.$emit("order", this.orderAndFilter);
          const page = 1;
          const search = newValue; //? newValue : null;
          const ordering = this.orderAndFilter;

          this.$router.push({
            path: "/games",
            query: { page, search, ordering },
          });
        }
      },
    },
    updateOrder: {
      immediate: true,
      /*handler(newValue, oldValue) {
        if (newValue !== "") {
          if (newValue[0] !== "-") {
            this.sortOrder = "";
            this.filter = newValue;
          } else {
            this.sortOrder = "-";
            this.filter = newValue.slice(1);
          }
          this.orderAndFilter = this.sortOrder + this.filter;
          this.$emit("order", this.orderAndFilter);
          this.$emit("search", this.searchQuery);
        }
      },
    },
  },*/
  data() {
    return {
      sortOrder: "-",
      searchQuery: "",
      orderAndFilter: null,
      showSearchResults: "",
      filter: "added",
    };
  },
  computed: {
    sortIconClass() {
      return "fas fa-sort" + (this.sortOrder === "" ? "-up" : "-down");
    },
  },
  methods: {
    changeFilter() {
      this.orderAndFilter = this.sortOrder + this.filter;
      this.$emit("order", this.orderAndFilter);
      //this.sortOrder = this.sortOrder === "" ? "-" : "";
      //this.orderAndFilter = this.sortOrder + this.filter;
      //this.showSearchResults = this.searchQuery;

      const page = 1;
      const search = this.searchQuery;
      const ordering = this.orderAndFilter;
      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },

    changeSortIcon() {
      this.sortOrder = this.sortOrder === "" ? "-" : "";
      this.orderAndFilter = this.sortOrder + this.filter;
      //this.showSearchResults = this.searchQuery;

      this.$emit("order", this.orderAndFilter);
      const page = 1;
      const search = this.searchQuery;
      const ordering = this.orderAndFilter;
      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },
    showSearch() {
      //passar search query para a view, pelo search
      this.showSearchResults = this.searchQuery;
      this.$emit("search", this.searchQuery);

      const page = 1;
      const search = this.searchQuery; //? newValue : null;
      const ordering = this.orderAndFilter;

      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },
    setNewOrder(newOrder) {
      if (newOrder[0] !== "-") {
        this.sortOrder = "";
        this.filter = newOrder;
      } else {
        this.sortOrder = "-";
        this.filter = newOrder.slice(1);
      }
      this.orderAndFilter = this.sortOrder + this.filter;
      this.$emit("order", this.orderAndFilter);
      this.$emit("search", this.searchQuery);
    },
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    const ordering = urlParams.get("ordering") || "-added";
    this.setNewOrder(ordering);
    this.searchQuery = urlParams.get("search") || "";
    this.searchQuery == "+"
      ? (this.searchQuery = "")
      : (this.searchQuery = this.searchQuery);

    this.showSearchResults = this.searchQuery;
  },
};
</script>

<style scoped>
.search-container {
  display: flex;
  padding: 4vh 10vh;
  align-items: center;
  justify-content: center;
  width: 70vw;
  margin: 0 auto;
}
.search-features {
  display: flex;
  align-items: center;
  margin: 1.5vh;
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
  margin-left: 5vw;
}
#counter {
  font-size: 16px;
  font-weight: 400;
}
#results {
  font-size: 3vh;
  font-weight: 500;
}
#results .search-bar-input {
  font-size: 3vh;
  font-weight: 900;
  display: block;
  width: 30vw;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
#searchBar {
  height: 4vh;
  width: 20vw;
}
#sortButton {
  cursor: pointer;
  padding: 1.3vh 2.3vh;
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
#sort {
  background-color: #23272a;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

#category {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  cursor: pointer;
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
  width: 20vw;
  height: 4vh;
  padding-right: 5.5vw;
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
@media screen and (max-width: 768px) {
  .search-container {
    width: 80vw;
    padding: 2vh 2vh;
    flex-direction: column;
    align-content: center;
  }
  .search-info {
    width: 100%;
    flex-direction: column;

    align-items: center;
    justify-content: center;
    margin: 0 auto;
  }
  .search-info #results {
    text-align: center;
    margin: 0 auto;
  }
  #results .search-bar-input {
    display: block;
    width: 60vw;
    margin: 0 auto;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
  }
  .search-features {
    flex-direction: column;
  }
  #searchBar {
    width: 40vw;
    padding-right: 10vw;
  }
}
</style>

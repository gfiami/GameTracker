<template>
  <div>
    <div id="paginationWrapper" v-if="totalPages">
      <div class="pagination" id="desktop">
        <button
          class="page-button"
          v-if="previousButton"
          @click="goToPage(currentPage - 1)"
        >
          Previous
        </button>
        <button class="page-button" v-if="pages[0] > 1" @click="goToPage(1)">
          1
        </button>

        <!--Mostra reticencias se a primeira página exibida for maior que 4-->
        <span class="dots" v-if="currentPage > 4"> ...</span>

        <button
          :id="currentPage == page ? 'currentPage' : null"
          class="page-button"
          v-for="page in pages"
          :key="page"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>

        <!-- Mostra reticencias se a página a ultima pagina exibida no paginate
            for menor que a penultima página
            exemplo: 100 páginas
            Se estiver na página 96, e a última página mostrada no nosso array for menor que o total(100)-1, ou seja
            menor que 99, mostra reticencias
            no caso de estar na página 96, a página 98 é a última do array, então por ser menor que 99, mostra reticencias

        -->
        <span class="dots" v-if="pages[pages.length - 1] < totalPages - 1"
          >...</span
        >
        <!-- aqui é para mostrar a última página caso a ultima página do array seja menor
        (sempre mostrará pois sempre é menor, exceto quando a current page for ela, ai mostra só a current page)
        -->
        <button
          class="page-button"
          v-if="pages[pages.length - 1] < totalPages"
          @click="goToPage(totalPages)"
        >
          {{ totalPages }}
        </button>
        <button
          class="page-button"
          v-if="nextButton"
          @click="goToPage(currentPage + 1)"
        >
          Next
        </button>
      </div>

      <div class="pagination" id="mobile">
        <button
          class="page-button"
          v-if="previousButton"
          @click="goToPage(currentPage - 1)"
        >
          Previous
        </button>
        <!--
          <option v-for="i in totalPages" :key="i" :value="i">
            {{ i }}
          </option>
          -->

        <select v-model="selectedPage" @change="changePage(selectedPage)">
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
          <option :value="totalPages">
            {{ totalPages }}
          </option>
        </select>

        <button
          class="page-button"
          v-if="nextButton"
          @click="goToPage(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ApiPagination",
  props: {
    routeReset: "",
  },
  data() {
    return {
      currentPage: 1,
      selectedPage: 1,
      totalPages: null,
      nextButton: "",
      previousButton: "",
      firstButton: false,
      lastButton: false,
    };
  },
  //recalcula as páginas sempre que ocorre alguma alteração devido a mudar as informações
  computed: {
    pages() {
      const buttonsOnSide = 2;
      const pageArray = [];
      let showFirst = true;
      let showLast = true;
      for (
        let i = Math.max(1, this.currentPage - buttonsOnSide);
        i <= Math.min(this.totalPages, this.currentPage + buttonsOnSide);
        i++
      ) {
        if (
          i === 1 ||
          i === this.totalPages ||
          (i >= this.currentPage - buttonsOnSide &&
            i <= this.currentPage + buttonsOnSide)
        ) {
          pageArray.push(i);
          showFirst = i > 2;
          showLast = i < this.totalPages - 1;
        } else if (showFirst || showLast) {
          pageArray.push("...");
          showFirst = false;
          showLast = false;
        }
      }
      this.firstButton = showFirst;
      this.lastButton = showLast;
      return pageArray;
    },
  },
  methods: {
    //requisição a api de acordo com número de páginas (lá no controller tá pegando 24 jogos por página)

    async fetchGames(page, search, order) {
      this.$emit("updateLoading", true);
      console.log("Fetching Games");
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}games/${page}/${search}/${order}`
      );
      console.log(response.data.games);

      //nenhum jogo encontrado
      if (response.data.games.count == 0) {
        console.log("No games found");
        this.totalPages = null;
        this.$emit("updateLoading", false);
        console.log(response.data.games);
        this.$emit("gamedata", response.data.games);
        this.$emit("noGameFound", true);
        return false;
      }
      if (response.data.games.detail == "Invalid page.") {
        console.log("Invalid Page");
        this.totalPages = null;
        this.$emit("updateLoading", false);
        this.$emit("gamedata", response.data.games);
        this.$emit("noGameFound", true);
        return false;
      }

      //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
      this.$emit("gamedata", response.data.games);
      //atualiza página atual
      this.currentPage = page;
      this.selectedPage = page;

      //checa total de páginas para criar botões de páginas
      this.totalPages = Math.ceil(response.data.games.count / 18);
      this.nextButton = response.data.games.next;
      this.previousButton = response.data.games.previous;
      this.$emit("updateLoading", false);
    },
    changePage(page) {
      this.goToPage(page);
    },
    goToPage(page) {
      if (page < 1 || page > this.totalPages) {
        return;
      }
      const urlParams = new URLSearchParams(window.location.search);
      const urlSearch = urlParams.get("search");
      const search = urlSearch ? urlSearch : "+";
      const ordering = urlParams.get("ordering") || "-added";

      //ir para topo da página ao carregar novamente
      window.scrollTo(0, 0);

      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },
  },
  mounted() {
    // pega o valor da página da URL
    const urlParams = new URLSearchParams(window.location.search);
    const page = parseInt(urlParams.get("page")) || 1;
    const search = urlParams.get("search") || "+";
    const ordering = urlParams.get("ordering") || "-added";
    this.fetchGames(page, search, ordering);
  },
};
</script>

<style scoped>
#desktop {
  display: none;
}
select {
  width: 15vw;
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

<template>
  <div>
    <div class="loading-games" v-if="loadingGames">
      <div class="lds-facebook">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div id="paginationWrapper">
      <div class="pagination">
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
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Paginations",
  props: {
    searchText: null,
    orderSet: null,
    routeReset: "",
  },
  watch: {
    searchText(newValue, oldValue) {
      const page = 1;
      const search = newValue ? newValue : null;
      const ordering = this.orderset;
      console.log("entrou no search");
      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },
    orderSet(newValue, oldValue) {
      const page = 1;
      const search = this.searchText ? this.searchText : "+";
      const ordering = newValue;
      this.$router.push({
        path: "/games",
        query: { page, search, ordering },
      });
    },
  },
  data() {
    return {
      currentPage: 1,
      totalPages: 1,
      nextButton: "",
      previousButton: "",
      firstButton: false,
      lastButton: false,
      loadingGames: true,
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
      console.log("Fetching Games");
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}games/${page}/${search}/${order}`
      );
      console.log(response.data.games);
      if (response.data.games.count == 0) {
        console.log("No games found");
        this.loadingGames = false;
        this.totalPages = null;
        return false;
      }
      //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
      this.$emit("gamedata", response.data.games);
      //atualiza página atual
      this.currentPage = page;
      //checa total de páginas para criar botões de páginas
      this.totalPages = Math.ceil(response.data.games.count / 24);
      this.nextButton = response.data.games.next;
      this.previousButton = response.data.games.previous;
      this.loadingGames = false;
    },
    goToPage(page) {
      if (page < 1 || page > this.totalPages) {
        return;
      }
      this.loadingGames = true;
      const search = this.searchText ? this.searchText : "+";
      const ordering = this.orderSet;
      console.log("Fetch no page");
      //ir para topo da página ao carregar novamente
      window.scrollTo(0, 0);
      //isso aqui serve para atualizar a url da página e permitir mais interação direto com a url
      //também serve para permitir voltar para a página inicial de games ao clicar na navbar, pois atualize fullpath
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
    console.log("Fetch no mounted");

    this.fetchGames(page, search, ordering);
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
/*loading */
.loading-games {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}

.lds-facebook {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}

.lds-facebook div {
  display: inline-block;
  position: absolute;
  left: 8px;
  width: 16px;
  background: #fff;
  animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
}

.lds-facebook div:nth-child(1) {
  left: 8px;
  animation-delay: -0.24s;
}

.lds-facebook div:nth-child(2) {
  left: 32px;
  animation-delay: -0.12s;
}

.lds-facebook div:nth-child(3) {
  left: 56px;
  animation-delay: 0;
}

@keyframes lds-facebook {
  0% {
    top: 8px;
    height: 64px;
  }
  50%,
  100% {
    top: 24px;
    height: 32px;
  }
}
</style>

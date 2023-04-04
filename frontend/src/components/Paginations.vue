<template>
  <div>
    {{ totalPages }}
    {{ currentPage }}
    <!-- <ul>
      <li v-for="game in games" :key="game.id">{{ game.name }}</li>
    </ul> -->
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
        <!--Mostra reticencias se a primeira página exibida for maior que 2 (ou seja 3 pra frente) -->

        <span class="dots" v-if="currentPage > 3">...</span>

        <button
          :id="currentPage == page ? 'currentPage' : null"
          class="page-button"
          v-for="page in pages"
          :key="page"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>

        <!-- Mostra '...' se a última página exibida for menor que a penúltima página do total de páginas menos 1.
      Com isso, sempre teremos dois botões no máximo depois, reticencias, e última página
-->
        <span class="dots" v-if="pages[pages.length - 1] < totalPages - 1"
          >...</span
        >
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
  data() {
    return {
      games: [],
      currentPage: 1,
      totalPages: 1,
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
    async fetchGames(page) {
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}games/${page}`
      );
      console.log(response);
      //pega os jogos da resposta
      this.games = response.data.games.results;
      //atualiza página atual
      this.currentPage = page;
      //checa total de páginas para criar botões de páginas
      this.totalPages = Math.ceil(response.data.games.count / 24);
      this.nextButton = response.data.games.next;
      this.previousButton = response.data.games.previous;
    },
    goToPage(page) {
      if (page < 1 || page > this.totalPages) {
        return;
      }
      this.fetchGames(page);
    },
  },
  mounted() {
    this.fetchGames(1);
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

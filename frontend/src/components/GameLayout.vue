<template>
  <div class="game-list">
    <div v-for="game in games" :key="game.id" class="game">
      <img :src="game.background_image" :alt="game.name" />
      <div class="game-hover">
        <p v-if="!logged">
          <router-link to="/login">Login</router-link> or
          <router-link to="/register">Register</router-link> to track your games
        </p>
        <p v-if="logged">
          <button
            v-if="ownedGames.includes(game.id)"
            @click="removeOwned(game.id)"
            type="button"
          >
            Remove from Owned
          </button>
          <button v-else @click="addOwned(game.id)" type="button">
            Add as Owned
          </button>

          <button
            v-if="
              wishListedGames.includes(game.id) && !ownedGames.includes(game.id)
            "
            @click="removeWishList(game.id)"
            type="button"
          >
            Remove from Wishlist
          </button>
          <button
            v-if="
              !wishListedGames.includes(game.id) &&
              !ownedGames.includes(game.id)
            "
            @click="addWishList(game.id)"
            type="button"
          >
            Add to Wishlist
          </button>

          <button
            v-if="
              favoriteGames.includes(game.id) && ownedGames.includes(game.id)
            "
            @click="removeFavorite(game.id)"
            type="button"
          >
            Remove from favorites
          </button>

          <button
            v-else-if="ownedGames.includes(game.id)"
            @click="addFavorite(game.id)"
            type="button"
          >
            Add as Favorite
          </button>
        </p>
        <!-- ajustar essa rota, apenas teste no momento -->
        <router-link
          :to="{
            name: 'specificGame',
            params: { id: game.id },
          }"
          ><i class="fa fa-info-circle"></i>
        </router-link>
      </div>
      <div class="game-title">{{ game.name }}</div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "GameLayout",
  props: {
    games: "",
  },
  data() {
    return {
      ownedGames: [],
      favoriteGames: [],
      wishListedGames: [],
    };
  },
  watch: {
    games: {
      immediate: true,
      handler(newGames, oldGames) {
        console.log("Valor atual da prop games:", newGames); //aqui eu posso usar um "LOADING para aguardar o games chegar!"
        if (newGames !== undefined) {
          const gameIds = newGames.map((game) => game.id);
          this.checkIfOwn(gameIds);
          this.checkIfFavorite(gameIds);
          this.checkIfWishlist(gameIds);
        }
      },
    },
  },
  computed: {
    //esse logged é chamado semrpe pq temos um v-if no router link que "checa" o status dele, se mudar vai alterar
    logged() {
      return this.$store.state.logged;
    },
  },
  methods: {
    async addOwned(game) {
      const user_id = localStorage.getItem("user_id");

      const game_api_id = game;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}owned`,
          {
            user_id: user_id,
            game_api_id: game_api_id,
          }
        );
        this.removeWishList(game);

        //aqui recebo o que o laravel me retornou dos itens encontrados no banco de dados iguais
        console.log(response.data);
        //pega os jogos na tela de novo e checa se tão no "owned"
        const gameIds = this.games.map((game) => game.id);
        this.checkIfOwn(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async checkIfOwn(gameIds) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-owned`,
          {
            params: {
              user_id: user_id,
              game_api_ids: gameIds,
            },
          }
        );
        //pega os jogos "owned" e bota no array
        this.ownedGames = response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async removeOwned(game) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-owned`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        this.removeFavorite(game);
        console.log(response.data);
        const gameIds = this.games.map((game) => game.id);
        this.checkIfOwn(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async addFavorite(game) {
      const user_id = localStorage.getItem("user_id");
      const game_api_id = game;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}favorite`,
          {
            user_id: user_id,
            game_api_id: game_api_id,
          }
        );
        //aqui recebo o que o laravel me retornou dos itens encontrados no banco de dados iguais
        console.log(response.data);
        const gameIds = this.games.map((game) => game.id);
        this.checkIfFavorite(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async checkIfFavorite(gameIds) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-favorite`,
          {
            params: {
              user_id: user_id,
              game_api_ids: gameIds,
            },
          }
        );
        this.favoriteGames = response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async removeFavorite(game) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-favorite`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        console.log(response.data);
        const gameIds = this.games.map((game) => game.id);
        this.checkIfFavorite(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async addWishList(game) {
      const user_id = localStorage.getItem("user_id");
      const game_api_id = game;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}wishlist`,
          {
            user_id: user_id,
            game_api_id: game_api_id,
          }
        );
        //aqui recebo o que o laravel me retornou dos itens encontrados no banco de dados iguais
        console.log(response.data);
        const gameIds = this.games.map((game) => game.id);
        this.checkIfWishlist(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async checkIfWishlist(gameIds) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-wishlist`,
          {
            params: {
              user_id: user_id,
              game_api_ids: gameIds,
            },
          }
        );
        this.wishListedGames = response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async removeWishList(game) {
      const user_id = localStorage.getItem("user_id");
      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-wishlist`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
            },
          }
        );
        console.log(response.data);
        const gameIds = this.games.map((game) => game.id);
        this.checkIfWishlist(gameIds);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
  },
};
</script>

<style>
.game-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin: 20px 20px;
}

.game {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 15%;
  margin-bottom: 40px;
  position: relative;
  border-radius: 12px;
}

.game img {
  border-radius: 12px;
  width: 100%;
  height: 200px;
  object-fit: cover;
  margin-bottom: 10px;
}

.game-title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
}

.game:hover .game-hover {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 20px;
}

.game-hover {
  display: none;
  width: 100%;
  height: 200px;
  position: absolute;
  top: 0;
  left: 0;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.5);
  border-radius: 12px;
  color: white;
}
.game-hover p a {
  color: #6842ff;
  text-decoration: none;
}
.game-hover p {
  text-align: center;
  margin: 12px;
  background: rgba(44, 47, 51, 0.75);
  border-radius: 15px;
  padding: 10px;
  border: none;
  border-radius: 5px;
}
.game-hover i {
  font-size: 40px;
}
.game-hover a {
  color: white;
  font-size: 16px;
  font-weight: 500;
}

/* AJUSTES MOBILE (nem sei se tão bom mas no chrome ficou ok*/
@media only screen and (max-width: 768px) {
  .game-list {
    justify-content: center;
  }
  .game {
    width: 40%;
    margin: 20px;
  }
  .game-hover {
    height: 150px;
  }
  .game img {
    height: 150px;
    margin-bottom: 5px;
  }
}
@media only screen and (max-width: 480px) {
  .game-list {
    margin: 10px;
  }
  .game {
    width: 80%;
    margin: 10px;
  }
  .game-hover {
    height: 100px;
  }
  .game img {
    height: 100px;
    margin-bottom: 5px;
  }
}
</style>

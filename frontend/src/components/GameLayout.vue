<template>
  <div class="game-list">
    <div v-for="game in games" :key="game.id" class="game">
      <img :src="game.background_image" :alt="game.name" />
      <div
        v-if="
          ownedGames.includes(game.id) ||
          wishListedGames.includes(game.id) ||
          favoriteGames.includes(game.id)
        "
        class="indicators"
      >
        <span v-if="ownedGames.includes(game.id)" class="fa"
          ><i class="fas fa-gamepad own"></i
        ></span>
        <span v-if="wishListedGames.includes(game.id)" class="fa"
          ><i class="fas fa-heart wish"></i
        ></span>
        <span v-if="favoriteGames.includes(game.id)" class="fa"
          ><i class="fas fa-star fav"></i
        ></span>
      </div>
      <div class="game-hover">
        <p v-if="!logged" class="login-register-offline">
          <router-link to="/login">Login</router-link> or
          <router-link to="/register">Register</router-link> to track your games
        </p>
        <p class="logged-container" v-if="logged">
          <button
            v-if="ownedGames.includes(game.id)"
            @click="removeOwned(game.id)"
            type="button"
          >
            <i class="fas fa-gamepad removeOwned"></i>
            <p class="button-legend">Remove from owned</p>
          </button>
          <button v-else @click="addOwned(game.id)" type="button">
            <i class="fas fa-gamepad"></i>
            <p class="button-legend">Add as owned</p>
          </button>

          <button
            v-if="
              wishListedGames.includes(game.id) && !ownedGames.includes(game.id)
            "
            @click="removeWishList(game.id)"
            type="button"
          >
            <i class="fas fa-heart removeWishlist"></i>
            <p class="button-legend">Remove from Wishlist</p>
          </button>
          <button
            v-if="
              !wishListedGames.includes(game.id) &&
              !ownedGames.includes(game.id)
            "
            @click="addWishList(game.id)"
            type="button"
          >
            <i class="far fa-heart"></i>
            <p class="button-legend">Add to Wishlist</p>
          </button>

          <button
            v-if="
              favoriteGames.includes(game.id) && ownedGames.includes(game.id)
            "
            @click="removeFavorite(game.id)"
            type="button"
          >
            <i class="fas fa-star removeFavorite"></i>
            <p class="button-legend">Remove favorite</p>
          </button>

          <button
            v-else-if="ownedGames.includes(game.id)"
            @click="addFavorite(game.id)"
            type="button"
          >
            <i class="far fa-star addFavorite"></i>
            <p class="button-legend">Add favorite</p>
          </button>
        </p>
        <!-- ajustar essa rota, apenas teste no momento -->
        <router-link
          class="info-link"
          :to="{
            name: 'specificGame',
            params: { id: game.id },
          }"
          ><i class="fa fa-info-circle game-info"></i>
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
          console.log(typeof newGames);
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
  padding-bottom: 0;
  transition: all 0.5s ease-in-out;
}

.game img {
  border-radius: 12px;
  width: 100%;
  height: 250px;
  object-fit: cover;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
}
.indicators {
  display: flex;
  align-items: center;
  gap: 2px;
  justify-content: center;
  position: absolute;
  top: 0;
  left: 0;
  padding: 2px;
  background-color: rgba(0, 0, 0, 0.6);
  border-top-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.fav {
  padding: 2px;
  color: rgba(250, 250, 45, 0.849);
  font-size: 12px;
}
.own {
  padding: 2px;

  color: rgba(45, 250, 45, 0.849);
}
.wish {
  padding: 2px;

  color: rgba(250, 45, 45, 0.849);
}
.game:hover .indicators {
  display: none;
}
.game-title {
  font-size: 16px;
  font-weight: bolder;
  text-align: center;
  position: absolute;
  bottom: 0;
  padding: 15px;
  background-image: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0) 0%,
    rgba(0, 0, 0, 0.5788690476190477) 40%,
    rgba(0, 0, 0, 0.8029586834733894) 100%
  );
  width: 100%;
  text-shadow: 2px 2px 4px #000000;
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.game:hover .game-title {
  background-image: none;
}
/* add hover */
.game:hover .game-hover {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 20px;
}
.game:hover {
  transform: scale(1.15);
  z-index: 100;
}

.game-hover {
  display: none;
  width: 100%;
  height: 250px;
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

/* design dos botões ao estar logado*/
.game-hover .game-info {
  text-shadow: 2px 2px 4px #000000;
  font-size: 30px;
}
.game-hover .info-link {
  padding: 15px;
  margin-bottom: 50px;
}
.game-hover .login-register-offline {
  background-color: rgba(0, 0, 0, 0.8029586834733894);
}
.game-hover .logged-container {
  display: flex;
  gap: 10px;
  align-content: center;
  justify-content: center;
  margin: 0;
  margin-top: 20px;
  padding: 0;
}

.logged-container button {
  background: none;
  color: inherit;
  border: none;
  padding: 0;
  font: inherit;
  cursor: pointer;
  outline: inherit;
  font-size: 10px;
  margin: 2px;
  text-shadow: 2px 2px 4px #000000;
}
.logged-container .button-legend {
  font-size: 12px;
  font-weight: 700;
  margin: 0;
  padding: 0;
  text-align: center;
  text-shadow: 2px 2px 4px #000000;
}

.addFavorite {
  background: transparent;
}
.removeFavorite {
  color: rgba(250, 250, 45, 0.849);
}
.removeOwned {
  color: rgba(45, 250, 45, 0.849);
}
.removeWishlist {
  color: rgba(250, 45, 45, 0.849);
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

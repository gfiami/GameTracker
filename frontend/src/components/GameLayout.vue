<template>
  <div class="game-list">
    <div
      v-for="game in games"
      :key="game.id"
      class="game"
      v-show="
        !checkIds ||
        (checkIds && Object.values(this.checkIds).includes(game.id))
      "
    >
      <img :src="game.background_image" :alt="game.name" />

      <!--\/ pequenos botões no canto superior esquerdo \/-->
      <div class="indicators" v-if="profileRoute">
        <!-- caso esteja no perfil isso faz sumir os indicadorzim -->
      </div>
      <div
        v-else-if="logged && (checkOwned || checkFavorite || checkWished)"
        class="indicators"
      >
        <span v-if="checkOwned.includes(game.id)" class="fa"
          ><i class="fas fa-gamepad own"></i
        ></span>
        <span v-if="checkWished.includes(game.id)" class="fa"
          ><i class="fas fa-heart wish"></i
        ></span>
        <span v-if="checkFavorite.includes(game.id)" class="fa"
          ><i class="fas fa-star fav"></i
        ></span>
      </div>
      <div
        v-else-if="
          logged &&
          (ownedGames.includes(game.id) ||
            wishListedGames.includes(game.id) ||
            favoriteGames.includes(game.id))
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
      <!--/\ pequenos botões no canto superior esquerdo /\

            \/ coisas que aparecem on hover \/ -->
      <div class="game-hover">
        <p v-if="!logged" class="login-register-offline">
          <router-link
            :to="{ path: '/login', query: { redirect: $route.path } }"
            >Login</router-link
          >
          or
          <router-link
            :to="{ path: '/register', query: { redirect: $route.path } }"
            >Register</router-link
          >
          to track
        </p>
        <p
          v-if="logged && !ownProfile && !gameRoute"
          class="login-container-another-profile"
        >
          Go to
          <router-link :to="'/profile/' + loggedId">Profile</router-link> or
          <router-link to="/games">Game Page</router-link> to track
        </p>

        <p
          class="logged-container"
          v-else-if="logged && (checkOwned || checkFavorite || checkWished)"
        >
          <button
            v-if="shouldShowIndicators && checkOwned.includes(game.id)"
            @click="removeOwned(game.id)"
            type="button"
          >
            <i class="fas fa-gamepad removeOwned"></i>
            <p class="button-legend">Remove from owned</p>
          </button>
          <button
            v-if="shouldShowIndicators && !checkOwned.includes(game.id)"
            @click="addOwned(game.id)"
            type="button"
          >
            <i class="fas fa-gamepad"></i>
            <p class="button-legend">Add as owned</p>
          </button>

          <button
            v-if="shouldShowIndicators && checkWished.includes(game.id)"
            @click="removeWishList(game.id)"
            type="button"
          >
            <i class="fas fa-heart removeWishlist"></i>
            <p class="button-legend">Remove from Wishlist</p>
          </button>
          <button
            v-if="
              shouldShowIndicators &&
              !checkWished.includes(game.id) &&
              !checkOwned.includes(game.id)
            "
            @click="addWishList(game.id)"
            type="button"
          >
            <i class="far fa-heart"></i>
            <p class="button-legend">Add to Wishlist</p>
          </button>

          <button
            v-if="
              shouldShowIndicators &&
              checkFavorite.includes(game.id) &&
              !checkWished.includes(game.id) &&
              checkOwned.includes(game.id)
            "
            @click="removeFavorite(game.id)"
            type="button"
          >
            <i class="fas fa-star removeFavorite"></i>
            <p class="button-legend">Remove favorite</p>
          </button>

          <button
            v-if="
              shouldShowIndicators &&
              checkOwned.includes(game.id) &&
              !checkFavorite.includes(game.id) &&
              !checkWished.includes(game.id)
            "
            @click="addFavorite(game.id)"
            type="button"
          >
            <i class="far fa-star addFavorite"></i>
            <p class="button-legend">Add favorite</p>
          </button>
        </p>
        <!-- gameview -->
        <p class="logged-container" v-else-if="logged">
          <button
            v-if="shouldShowIndicators && ownedGames.includes(game.id)"
            @click="removeOwned(game.id)"
            type="button"
          >
            <i class="fas fa-gamepad removeOwned"></i>
            <p class="button-legend">Remove from owned</p>
          </button>
          <button
            v-if="shouldShowIndicators && !ownedGames.includes(game.id)"
            @click="addOwned(game.id)"
            type="button"
          >
            <i class="fas fa-gamepad"></i>
            <p class="button-legend">Add as owned</p>
          </button>

          <button
            v-if="shouldShowIndicators && wishListedGames.includes(game.id)"
            @click="removeWishList(game.id)"
            type="button"
          >
            <i class="fas fa-heart removeWishlist"></i>
            <p class="button-legend">Remove from Wishlist</p>
          </button>
          <button
            v-if="
              shouldShowIndicators &&
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
              shouldShowIndicators &&
              favoriteGames.includes(game.id) &&
              !wishListedGames.includes(game.id) &&
              ownedGames.includes(game.id)
            "
            @click="removeFavorite(game.id)"
            type="button"
          >
            <i class="fas fa-star removeFavorite"></i>
            <p class="button-legend">Remove favorite</p>
          </button>

          <button
            v-if="
              shouldShowIndicators &&
              ownedGames.includes(game.id) &&
              !favoriteGames.includes(game.id) &&
              !wishListedGames.includes(game.id)
            "
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
            name: 'specificgame',
            params: { slug: game.slug },
          }"
          ><i class="fa fa-info-circle game-info"></i>
        </router-link>
      </div>

      <!-- /\ coisas que aparecem on hover -->
      <div class="game-title">{{ game.name }}</div>
      <div class="game-title-mobile">
        <router-link
          class="info-link-mobile"
          :to="{
            name: 'specificgame',
            params: { slug: game.slug },
          }"
          >{{ game.name }}</router-link
        >
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "GameLayout",
  props: {
    games: "",
    checkIds: "",
    checkOwned: null,
    checkFavorite: null,
    checkWished: null,
    user: null,
    routeReset: "",
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
        //aqui eu posso usar um "LOADING para aguardar o games chegar!"
        if (newGames !== undefined) {
          const gameIds = newGames.map((game) => game.id);
          this.checkIfOwn(gameIds);
          this.checkIfFavorite(gameIds);
          this.checkIfWishlist(gameIds);
        }
      },
    },
  },
  //vou usar isso para passar owned/wished/favortes
  computed: {
    //esse logged é chamado semrpe pq temos um v-if no router link que "checa" o status dele, se mudar vai alterar
    logged() {
      return this.$store.state.logged;
    },

    loggedId() {
      return this.$store.state.user_id;
    },
    shouldShowIndicators() {
      const gamesRouteLogged =
        this.$route.name == "games" && this.$store.state.logged;
      const ownProfile =
        this.$route.name == "profile" &&
        this.$route.params.id == this.$store.state.user_id;
      return gamesRouteLogged || ownProfile;
    },
    profileRoute() {
      const profileRoute = this.$route.name == "profile";
      return profileRoute;
    },
    gameRoute() {
      const gamesRoute = this.$route.name == "games";
      return gamesRoute;
    },
    ownProfile() {
      const ownProfile =
        this.$route.name == "profile" &&
        this.$route.params.id == this.$store.state.user_id;
      return ownProfile;
    },
  },
  methods: {
    async addOwned(game) {
      const gameIds = this.games.map((game) => game.id);
      const user_id = this.user;
      const personal_token = this.$store.state.personal_token;
      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}owned`,
          {
            user_id: user_id,
            game_api_id: game,
            game_api_ids: gameIds,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.ownedGames = response.data.owned_games;
        this.wishListedGames = response.data.wishlisted_games;
        this.$emit("favoriteGamesUpdate", this.favoriteGames);
        this.$emit("ownedGamesUpdate", this.ownedGames);

        this.$emit("wishListedGamesUpdate", this.wishListedGames);

        this.$emit("button-clicked", "addOwned");
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async checkIfOwn(gameIds) {
      const user_id = this.user;

      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-owned-starter`,
          {
            params: {
              user_id: user_id,
              game_api_ids: gameIds,
            },
          }
        );
        this.ownedGames = response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async removeOwned(game) {
      const gameIds = this.games.map((game) => game.id);
      const user_id = this.user;
      const personal_token = this.$store.state.personal_token;

      try {
        console.log("Entrou no try do remove Owned");
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-owned`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
              game_api_ids: gameIds,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.ownedGames = response.data.owned_games;
        this.favoriteGames = response.data.favorite_games;
        this.$emit("favoriteGamesUpdate", this.favoriteGames);
        this.$emit("ownedGamesUpdate", this.ownedGames);

        this.$emit("wishListedGamesUpdate", this.wishListedGames);
        this.$emit("button-clicked", "removeOwned");
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async addFavorite(game) {
      const user_id = this.user;
      const gameIds = this.games.map((game) => game.id);
      const personal_token = this.$store.state.personal_token;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}favorite`,
          {
            user_id: user_id,
            game_api_id: game,
            game_api_ids: gameIds,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );

        this.favoriteGames = response.data;
        this.$emit("favoriteGamesUpdate", this.favoriteGames);
        this.$emit("ownedGamesUpdate", this.ownedGames);

        this.$emit("wishListedGamesUpdate", this.wishListedGames);

        this.$emit("button-clicked", "addFavorite");
      } catch (error) {
        console.log(error.response.data.error);
      }
    },

    async checkIfFavorite(gameIds) {
      const user_id = this.user;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-favorite-starter`,
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
      const user_id = this.user;
      const gameIds = this.games.map((game) => game.id);
      const personal_token = this.$store.state.personal_token;

      try {
        console.log("entrou no try do remove favorite");
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-favorite`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
              game_api_ids: gameIds,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.favoriteGames = response.data;
        this.$emit("favoriteGamesUpdate", this.favoriteGames);
        this.$emit("ownedGamesUpdate", this.ownedGames);

        this.$emit("wishListedGamesUpdate", this.wishListedGames);
        this.$emit("button-clicked", "removeFavorite");
      } catch (error) {
        console.log(error);
      }
    },
    async addWishList(game) {
      const user_id = this.user;
      const gameIds = this.games.map((game) => game.id);
      const personal_token = this.$store.state.personal_token;

      try {
        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}wishlist`,
          {
            user_id: user_id,
            game_api_id: game,
            game_api_ids: gameIds,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        this.wishListedGames = response.data;
        this.$emit("wishListedGamesUpdate", this.wishListedGames);
        this.$emit("button-clicked", "addWishList");
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async checkIfWishlist(gameIds) {
      const user_id = this.user;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}check-wishlist-starter`,
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
      const gameIds = this.games.map((game) => game.id);
      const user_id = this.user;
      const personal_token = this.$store.state.personal_token;

      try {
        const response = await axios.delete(
          `${process.env.VUE_APP_APIURL}remove-wishlist`,
          {
            params: {
              user_id: user_id,
              game_api_id: game,
              game_api_ids: gameIds,
            },
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );

        this.wishListedGames = response.data;
        this.$emit("favoriteGamesUpdate", this.favoriteGames);
        this.$emit("ownedGamesUpdate", this.ownedGames);

        this.$emit("wishListedGamesUpdate", this.wishListedGames);
        this.$emit("button-clicked", "removeWishlist");
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
.game-list {
  width: 90%;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 7vh;
}

.game {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 45%;
  height: 35vh;
  margin: 1vh 1vw;
  position: relative;
  border-radius: 12px;
  padding-bottom: 0;
  transition: all 0.5s ease-in-out;
}

.game img {
  border-radius: 12px;
  width: 100%;
  height: 35vh;
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
  font-size: 2vh;
}
.own {
  padding: 2px;
  color: rgba(45, 250, 45, 0.849);
}
.wish {
  padding: 2px;
  color: rgba(250, 45, 45, 0.849);
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

.game-hover {
  display: none; /* revert to none */
  width: 100%;
  height: 35vh;
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
.game-hover .login-register-offline,
.game-hover .login-container-another-profile {
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
.login-container-another-profile,
.login-register-offline {
  white-space: pre-wrap;
}

/* AJUSTES Desktop (nem sei se tão bom mas no chrome ficou ok*/
@media screen and (min-width: 768px) {
  .game {
    width: 15%;
    margin: 1vh 0.3vw;
  }
}
@media (hover: hover) {
  .game-title-mobile {
    display: none;
  }

  .game:hover .game-title,
  .game:active .game-title {
    background-image: none;
  }
  .game:hover .game-hover,
  .game:active .game-hover {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
  }
  .game:hover,
  .game:active {
    transform: scale(1.05);
    z-index: 100;
  }
  .game:hover .indicators,
  .game:active .indicators {
    display: none;
  }
}

@media (hover: none) {
  /* mobile styles */
  .game-hover .login-register-offline,
  .game-hover .login-container-another-profile {
    display: none;
  }

  .game-title {
    display: none;
  }
  .info-link {
    display: none;
  }
  .info-link-mobile {
    text-decoration: none;
    color: white;
    font-size: 16px;
    font-weight: bolder;
    text-align: center;
    position: absolute;
    bottom: 0;
    left: 0;
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
  .game-hover .logged-container {
    padding: 2px;
    border-top-left-radius: 0px;
    border-bottom-left-radius: 12px;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 0px;
    margin-top: 0;
    position: absolute;
    top: 0;
    right: 0;
    width: 25%;
    flex-direction: column;
    background-color: rgba(0, 0, 0, 0.5788690476190477);
  }
  .logged-container button {
    font-size: 5px;
    margin: 2px;
  }
  .logged-container .button-legend {
    font-size: 1vh;
    font-weight: 700;
    text-shadow: none;
  }
  .game-hover i {
    font-size: 2vh;
  }
  .game-hover .info-link {
    background-color: blue;
    position: absolute;
    bottom: 0;
    width: 100%;
  }
  .game .indicators {
    display: none;
  }
  .game .game-hover {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    background: none;
  }
}
</style>

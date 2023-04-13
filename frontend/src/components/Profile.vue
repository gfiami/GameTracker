<template>
  <div>
    <div class="game-info-container">
      <div class="owned-games">
        <div class="category-title"><h3>Owned Games</h3></div>
        <div class="loading-games" v-if="loadingGames">
          <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
        <div class="empty-container" v-if="emptyOwned">
          <div class="empty-games">No owned games</div>
        </div>

        <div v-if="allGames && ownedIds">
          <div class="layout-container">
            <!--
                Com a função getOwned eu pego os jogos do usuario
                Com a fetch, eu uso o obtido na get e pego da api os jogos que eu quero
                Daí, faço passo o prop games com o valor dos jogos obtidos da api, e aí ele monta o layout
                O v-on:ownedGamesUpdate é o emit recebido quando ocorre alguma alteração no "ownedGames"
                  Ele recebe o novo valor dos jogos que o usuario possui, e manda de volta pro GameLayout 
                (vou ter que ficar atento nisso e ficar preparado para que cada gamelayout tenha isso para wish/fav/owned) e usar no checkIf lá
                (porque pode rolar um update em outro gamelayout e se nao tiver emit pra todas as opções vai dar ruim)
                Aí o updateOwnedGames daqui é chamado e cria uma nova requisição de api, jogando um novo :games no layout
                que considerada o novo valor dos ownedGames e atualiza a requisição da api para renderizar de novo a tela só com os ids que o user tem
                Esse "chedkIds" lá no gameLayout é apenas um array com os números dos ids dos jogos salvos
                            Ele será util na renderização, para que tire o jogo da página

                    Isso acontece porque:
                      >Abro o perfil e vejo todos os jogos
                      >Click para remover um jogo dos owned
                      >Ele remove e checa os jogos que tenho, e devolve o novo array dos ids dos jogos
                      >Com os ids, faço requisição na api e pego os jogos
                      >Mando esses valores da requisição pro layout e ele renderiza tudo, e o "checkIds" apenas ajuda a forçar excluir mesmo
                      o button-clicked serve para pegar qual botão (add owned/remove/wishlist etc) foi clicado e interagir com as listas de outros componentes
              -->
            <GameLayout
              :user="user"
              class="game-layout-owned"
              :games="allGames"
              @ownedGamesUpdate="updateOwnedGames"
              :checkIds="ownedIds"
              :checkOwned="ownedIds"
              :checkFavorite="favoriteIds"
              :checkWished="wishedIds"
              @button-clicked="onButtonClicked"
            />
          </div>
        </div>
      </div>
      <div class="favorite-games">
        <div class="category-title"><h3>Favorite Games</h3></div>
        <div class="loading-games" v-if="loadingGames">
          <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
        <div class="empty-container" v-if="emptyFavorite">
          <div class="empty-games">No favorited games</div>
        </div>
        <div v-if="allGames && favoriteIds">
          <div class="layout-container">
            <GameLayout
              :user="user"
              class="game-layout-favorite"
              :games="allGames"
              @favoriteGamesUpdate="updateFavoriteGames"
              :checkIds="favoriteIds"
              :checkOwned="ownedIds"
              :checkFavorite="favoriteIds"
              :checkWished="wishedIds"
              @button-clicked="onButtonClicked"
            />
          </div>
        </div>
      </div>
      <div class="wishlist-games">
        <div class="category-title"><h3>Wishlist Games</h3></div>
        <div class="loading-games" v-if="loadingGames">
          <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
          </div>
        </div>
        <div class="empty-container" v-if="emptyWished">
          <div class="empty-games">No wishlisted games</div>
        </div>

        <div v-if="allGames && wishedIds">
          <div class="layout-container">
            <GameLayout
              :user="user"
              class="game-layout-wished"
              :games="allGames"
              @wishListedGamesUpdate="updateWishedGames"
              :checkIds="wishedIds"
              :checkOwned="ownedIds"
              :checkFavorite="favoriteIds"
              :checkWished="wishedIds"
              @button-clicked="onButtonClicked"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import GameLayout from "./GameLayout.vue";
export default {
  name: "Profile",
  props: {
    user: null,
  },
  watch: {
    user(newVal, oldVal) {
      console.log("novo user: " + newVal);
      Promise.all([
        this.getWishedGames(),
        this.getFavoriteGames(),
        this.getOwnedGames(),
      ]).then((values) => {
        const owned_fetcher = this.ownedIds;
        const wished_fetcher = this.wishedIds;
        const favorte_fetcher = this.favoriteIds;
        const fetcher = [].concat(
          owned_fetcher,
          wished_fetcher,
          favorte_fetcher
        );
        this.allGamesUserTracked(fetcher);
      });
    },
  },

  components: {
    GameLayout,
  },
  data() {
    return {
      ownedIds: "",
      favoriteIds: "",
      wishedIds: "",
      allGames: "",
      loadingGames: true,
      emptyOwned: false,
      emptyFavorite: false,
      emptyWished: false,
    };
  },
  methods: {
    async allGamesUserTracked(ids) {
      if (this.emptyOwned && this.emptyFavorite && this.emptyWished) {
        this.loadingGames = false;

        return false;
      }
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}all-rawg-games/${ids}`
      );
      console.log(response.data.games.results);
      this.allGames = response.data.games.results;
      console.log("teste tamanho: " + this.allGames.length);
      this.loadingGames = false;
    },

    async onButtonClicked(buttonType) {
      //removeOwned | addOwned | removeWishlist | addWishList | removeFavorite | addFavorite
      console.log("Botão clickado: " + buttonType);
      switch (buttonType) {
        case "removeOwned":
          await this.getOwnedGames();
          await this.getFavoriteGames();
          break;
        case "addOwned":
          await this.getWishedGames();
          await this.getOwnedGames();

          break;
        case "removeWishlist":
          await this.getWishedGames();
          break;
        case "addWishList":
          console.log("nem deveria ser possível aqui rsrsrs");
          break;

        case "removeFavorite":
          await this.getFavoriteGames();
          await this.getOwnedGames();

          break;
        case "addFavorite":
          await this.getOwnedGames();
          await this.getFavoriteGames();
          this.allGames = this.allGames;

          break;
        default:
          console.log("erro reconhecendo button type");
          break;
      }
    },
    /* owned functions */
    async getOwnedGames() {
      const user_id = this.user;
      console.log("get owned: " + this.user);
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-owned`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        //pega os jogos "owned" e bota no array
        this.ownedIds = response.data;
        if (this.ownedIds.length == 0) {
          this.emptyOwned = true;
        } else {
          this.emptyOwned = false;
        }
        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    updateOwnedGames(newValue) {
      this.ownedIds = newValue;
    },

    /* favorite functions */
    async getFavoriteGames() {
      const user_id = this.user;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-favorite`,
          {
            params: {
              user_id: user_id,
            },
          }
        );

        //pega os jogos "favorite" e bota no array
        this.favoriteIds = response.data;
        if (this.favoriteIds.length == 0) {
          this.emptyFavorite = true;
        } else {
          this.emptyFavorite = false;
        }

        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    updateFavoriteGames(newValue) {
      this.favoriteIds = newValue;
    },
    /*wished functions */
    async getWishedGames() {
      const user_id = this.user;
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}fetch-wished`,
          {
            params: {
              user_id: user_id,
            },
          }
        );
        //pega os jogos "favorite" e bota no array
        this.wishedIds = response.data;
        if (this.wishedIds.length == 0) {
          this.emptyWished = true;
        } else {
          this.emptyWished = false;
        }
        return response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    updateWishedGames(newValue) {
      this.wishedIds = newValue;
    },
  },
  mounted() {
    Promise.all([
      this.getWishedGames(),
      this.getFavoriteGames(),
      this.getOwnedGames(),
    ]).then((values) => {
      const owned_fetcher = this.ownedIds;
      const wished_fetcher = this.wishedIds;
      const favorte_fetcher = this.favoriteIds;
      const fetcher = [].concat(owned_fetcher, wished_fetcher, favorte_fetcher);
      this.allGamesUserTracked(fetcher);
    });
  },
};
</script>

<style>
/* style nao é scoped para mexermos no layout dosotro */
.owned-games,
.favorite-games,
.wishlist-games {
  margin-bottom: 20px;
}
.personnal-info {
  display: flex;
  gap: 20px;
  width: 90%;
  margin: 0 auto;
  margin-top: 30px;
}
.category-title {
  width: 70%;
  margin: 0 auto;
  margin-bottom: 0;
}
.user-edit-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-around;
}
.username {
  font-size: 36px;
  font-weight: 700;
}
.profile-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}
.edit-profile {
  text-decoration: none;
  color: #fff;
  font-weight: 400;
  font-size: 12px;
}
.layout-container {
  width: 70%;
  margin: 0 auto;
}

.layout-container .game-list {
  display: block;
  overflow-x: scroll;
  overflow-y: hidden;
  white-space: nowrap;
  margin: 0;
}
::-webkit-scrollbar {
  height: 7px;
  width: 0px;
  background: gray;
}
::-webkit-scrollbar-thumb:horizontal {
  background: rgba(54, 30, 148, 0.9);
  border-radius: 2px;
}
.layout-container .game-list .game {
  display: inline-block;
  margin: 10px;
}

.layout-container .game-list .game-title {
  font-size: 10px;
  font-weight: bold;
  padding: 10px;
  word-wrap: break-word;
  white-space: pre-wrap;
  word-break: break-word;
}
.layout-container .game img {
  width: 100%;
  height: 200px;
}
.layout-container .game-hover {
  width: 100%;
  height: 200px;
}
.layout-container .game-hover .login-register-offline,
.layout-container .game-hover .login-container-another-profile {
  white-space: initial;
  font-size: 12px;
}
.layout-container .game-hover .login-register-offline a,
.layout-container .game-hover .login-container-another-profile a {
  white-space: initial;
  font-size: 12px;
}
.layout-container .game:hover {
  transform: scale(1.1);
}
.layout-container .game-hover i {
  font-size: 20px;
}
.layout-container .game-hover {
  gap: 5px;
}
.layout-container .game-hover .logged-container {
  margin-top: 10px;
  flex-direction: column;
  align-content: center;
  justify-content: flex-start;
  margin-top: 10px;
}
.layout-container .logged-container button {
  font-size: 8px;
}
.layout-container .logged-container .button-legend {
  font-size: 10px;
  font-weight: 500;
}
.layout-container .game-hover .info-link {
  padding: 15px;
  margin-bottom: 10px;
}
.empty-container {
  margin: 0 auto;
  width: 60%;
  margin-top: 10px;
  margin-bottom: 10px;
  justify-content: flex-start;
  flex-direction: column;
  gap: 15px;
}
.empty-games {
  color: #ffffffa4;
  font-weight: 300;
  border-radius: 6px;
  font-size: 12px;
  background-color: rgba(48, 25, 189, 0.62);
  width: 40%;
  padding: 10px;
  margin-bottom: 0;
  align-self: flex-start;
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

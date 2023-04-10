<template>
  <div>
    <div class="personnal-info">
      <img
        class="profile-image"
        src="../assets/def-avatar-profile.jpg"
        alt=""
      />
      <div class="user-edit-container">
        <h1 class="username">{{ $store.state.token }}</h1>
        <a class="edit-profile" href="">Edit profile</a>
      </div>
    </div>
    <div class="game-info-container">
      <div class="owned-games">
        <div class="owned-title"><h3>Owned Games</h3></div>
        <div v-if="ownedApi && ownedIds" class="testando-owned">
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
              class="game-layout-owned"
              :games="ownedApi"
              @ownedGamesUpdate="updateOwnedGames"
              :checkIds="ownedIds"
              @button-clicked="onButtonClicked"
            />
          </div>
        </div>
      </div>
      <div class="favorite-games">
        <div class="favorite-title"><h3>Favorite Games</h3></div>
        <div v-if="favoriteApi && favoriteIds">
          <div class="layout-container">
            <GameLayout
              class="game-layout-favorite"
              :games="favoriteApi"
              @favoriteGamesUpdate="updateFavoriteGames"
              :checkIds="favoriteIds"
              @button-clicked="onButtonClicked"
            />
          </div>
        </div>
      </div>
      <div class="wishlist-games">
        <div class="wishlist-title"><h3>Wishlist Games</h3></div>
        <div v-if="wishedApi && wishedIds">
          <div class="layout-container">
            <GameLayout
              class="game-layout-wished"
              :games="wishedApi"
              @wishListedGamesUpdate="updateWishedGames"
              :checkIds="wishedIds"
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
  name: "OwnProfile",
  components: {
    GameLayout,
  },
  data() {
    return {
      ownedGamesArray: [],
      favoriteGamesArray: [],
      wishListedGamesArray: [],
      ownedApi: "",
      ownedIds: "",
      favoriteApi: "",
      favoriteIds: "",
      wishedApi: "",
      wishedIds: "",
      forceOwnedUpdate: 0,
      forceWishedUpdate: 0,
    };
  },
  methods: {
    async onButtonClicked(buttonType) {
      console.log("o botão clickado foi: " + buttonType);
      //removeOwned | addOwned | removeWishlist | addWishList | removeFavorite | addFavorite
      switch (buttonType) {
        case "removeOwned":
          await this.getOwnedGames().then(() => {
            this.fetchOwnedGames(this.ownedGamesArray);
          });
          await this.getFavoriteGames().then(() => {
            this.fetchFavoriteGames(this.favoriteGamesArray);
          });
          break;
        case "addOwned":
          await this.getWishedGames().then(() => {
            this.fetchWishedGames(this.wishListedGamesArray);
          });
          await this.getOwnedGames().then(() => {
            this.fetchOwnedGames(this.ownedGamesArray);
          });

          break;
        case "removeWishlist":
          await this.getWishedGames().then(() => {
            this.fetchWishedGames(this.wishListedGamesArray);
          });
          break;
        case "addWishList":
          console.log("nem deveria ser possível aqui rsrsrs");
          break;

        case "removeFavorite":
          await this.getFavoriteGames().then(() => {
            this.fetchFavoriteGames(this.favoriteGamesArray);
          });
          await this.getOwnedGames().then(() => {
            this.fetchOwnedGames(this.ownedGamesArray);
          });

          break;
        case "addFavorite":
          await this.getFavoriteGames().then(() => {
            this.fetchFavoriteGames(this.favoriteGamesArray);
          });

          break;
        default:
          console.log("erro reconhecendo button type");
          break;
      }
      /*this.getFavoriteGames();
      this.getOwnedGames();
      this.getWishedGames();*/
    },
    /* owned functions */
    async getOwnedGames() {
      const user_id = this.$route.params.id;
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

        this.ownedGamesArray = response.data;
        console.log(
          this.ownedGamesArray + "ARRAY DOS OWNED DENTRO DO GAMEOWNED FUNCTION"
        );
        //console.log("owned: " + this.ownedGames);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async fetchOwnedGames(ownedArray) {
      if (ownedArray.length < 1) {
        //nao tem OwnedGames || evita fetch a toa
        return false;
      }
      console.log("na busca da api:" + ownedArray);
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}game-api-owned/${ownedArray}`
        );
        //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
        this.ownedApi = response.data;
        this.ownedIds = ownedArray;
        console.log("passou no fetchowned");
      } catch (error) {
        console.log(error.response.data.error);
      }
    },

    updateOwnedGames(newValue) {
      this.ownedGamesArray = newValue; //só é usado aqui
      console.log("vendo no update" + this.ownedIds);
      this.ownedIds = newValue;
      //console.log(this.ownedGamesArray.length);
      console.log("tamanho dos owned: " + this.ownedIds.length); //isso é bom usar depois para ver as paradas de qnt por page
    },

    /* favorite functions */
    async getFavoriteGames() {
      const user_id = this.$route.params.id;
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
        this.favoriteGamesArray = response.data;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async fetchFavoriteGames(favoriteArray) {
      if (favoriteArray.length < 1) {
        //nao tem favorite games
        return false;
      }
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}game-api-favorite/${favoriteArray}`
        );
        //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
        this.favoriteApi = response.data;
        this.favoriteIds = favoriteArray;
      } catch (error) {
        console.log(error);
        if (error.response.data.error == undefined) {
          console.log("Não possui favoritos");
        }
      }
    },
    updateFavoriteGames(newValue) {
      this.favoriteGamesArray = newValue; //só é usado aqui
      this.favoriteIds = newValue;
      console.log("tamanho dos favoritos: " + this.favoriteIds.length); //isso é bom usar depois para ver as paradas de qnt por page
    },
    /*wished functions */
    async getWishedGames() {
      const user_id = this.$route.params.id;
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
        this.wishListedGamesArray = response.data;
        console.log("teste wished" + this.wishListedGamesArray);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async fetchWishedGames(wishedArray) {
      if (wishedArray.length < 1) {
        //nao tem wishlisted games
        return false;
      }
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}game-api-wished/${wishedArray}`
        );
        //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
        this.wishedApi = response.data;
        this.wishedIds = wishedArray;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    updateWishedGames(newValue) {
      this.wishListedGamesArray = newValue; //só é usado aqui
      this.wishedIds = newValue;
      console.log("tamanho dos wished: " + this.wishedIds.length); //isso é bom usar depois para ver as paradas de qnt por page
    },
  },
  mounted() {
    this.getWishedGames().then(() => {
      this.fetchWishedGames(this.wishListedGamesArray);
    });
    this.getFavoriteGames().then(() => {
      this.fetchFavoriteGames(this.favoriteGamesArray);
    });
    this.getOwnedGames().then(() => {
      this.fetchOwnedGames(this.ownedGamesArray);
    });
  },
};
</script>

<style>
/* style nao é scoped para mexermos no layout dosotro */
.game-layout-profile .indicators {
  display: none;
}

.personnal-info {
  display: flex;
  gap: 20px;
  width: 90%;
  margin: 0 auto;
  margin-top: 30px;
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
</style>

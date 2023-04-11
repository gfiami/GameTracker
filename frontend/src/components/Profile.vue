<template>
  <div>
    <div class="game-info-container">
      <div class="owned-games">
        <div class="owned-title"><h3>Owned Games</h3></div>
        <div v-if="allGames && ownedIds" class="testando-owned">
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
        <div class="favorite-title"><h3>Favorite Games</h3></div>
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
        <div class="wishlist-title"><h3>Wishlist Games</h3></div>
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
    };
  },
  methods: {
    async allGamesUserTracked(ids) {
      const response = await axios.get(
        `${process.env.VUE_APP_APIURL}all-rawg-games/${ids}`
      );
      console.log(response.data.games.results);
      this.allGames = response.data.games.results;
      console.log("teste tamanho: " + this.allGames.length);
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
        console.log("Atualizando favoritos Ids");

        //pega os jogos "favorite" e bota no array
        this.favoriteIds = response.data;
        console.log("Id dos favoritos: " + this.favoriteIds);

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

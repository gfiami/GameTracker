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
      <div class="game-info-container">
        <div class="owned-games">
          <div class="owned-title">{{ ownedGamesArray }}</div>
          <div v-if="ownedApi && ownedIds" class="testando-owned">
            <div>
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
              -->
              <GameLayout
                class="game-layout-profile"
                :games="ownedApi"
                @ownedGamesUpdate="updateOwnedGames"
                :checkIds="ownedIds"
              />
            </div>
          </div>
          <!-- para cada game layout eu passo o array, mas antes eu tenho que buscar na api pra pegar e mandar -->
        </div>
        <div class="favorite-games">
          <!-- <div class="favorite-title">{{ favoriteGamesArray }}</div> -->
          <!-- <GameLayout /> -->
        </div>
        <div class="wishlist-games">
          <!-- <div class="wishlist-title">{{ wishListedGamesArray }}</div> -->
          <!-- <GameLayout />-->
        </div>
        <div class="teste"></div>
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
      checkOwnedCounter: 0,
    };
  },
  methods: {
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
        this.fetchOwnedGames(this.ownedGamesArray);
        //console.log("owned: " + this.ownedGames);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    async fetchOwnedGames(ownedArray) {
      console.log("na busca da api:" + ownedArray);
      try {
        const response = await axios.get(
          `${process.env.VUE_APP_APIURL}game-api-owned/${ownedArray}`
        );
        //passar variavel gamedata para ser usada(.games .results .count .next . next . previous)
        this.ownedApi = response.data;
        this.ownedIds = ownedArray;
      } catch (error) {
        console.log(error.response.data.error);
      }
    },

    updateOwnedGames(newValue) {
      this.ownedGamesArray = newValue; //só é usado aqui
      this.ownedIds = newValue;
      console.log(this.ownedGamesArray.length);
      console.log(this.ownedIds.length); //isso é bom usar depois para ver as paradas de qnt por page

      /*  this.checkOwnedCounter++;
      console.log(this.checkOwnedCounter);
     if (this.checkOwnedCounter == 2) {
        this.getOwnedGames();
        this.checkOwnedCounter = 0;
      } */
    },
  },
  mounted() {
    this.getOwnedGames();
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

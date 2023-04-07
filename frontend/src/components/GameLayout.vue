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
          <button @click="markOwned(game.id)" type="button">
            Mark as Owned
          </button>
          <button @click="addWishList(game.id)" type="button">
            Add to Wishlist
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
  computed: {
    //esse logged é chamado semrpe pq temos um v-if no router link que "checa" o status dele, se mudar vai alterar
    logged() {
      return this.$store.state.logged;
    },
  },
  mounted() {
    setTimeout(() => {
      console.log(this.games);
    }, 3333);
  },
  methods: {
    async markOwned(game) {
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
        //aqui recebo o que o laravel me retornou
        console.log(response.data.message);
      } catch (error) {
        console.log(error.response.data.error);
      }
    },
    addWishList(game) {
      console.log("wishlist" + game);
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

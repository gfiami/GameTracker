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
          <!-- <div class="owned-title">{{ ownedGamesArray }}</div>-->
        </div>
        <div class="favorite-games">
          <!-- <div class="favorite-title">{{ favoriteGamesArray }}</div> -->
        </div>
        <div class="wishlist-games">
          <!-- <div class="wishlist-title">{{ wishListedGamesArray }}</div> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import GameLayout from "./GameLayout.vue";
export default {
  name: "OwnProfile",
  components: {
    GameLayout,
  },
  props: {
    ownedGames: Array,
    favoriteGames: Array,
    wishListedGames: Array,
  },
  data() {
    return {
      ownedGamesArray: [], //esperar chegar e observar o prop com watch pra nao receber vazio
      favoriteGamesArray: [],
      wishListedGamesArray: [],
    };
  },
  watch: {
    ownedGames: {
      immediate: true, //isso significa que a função é chamada mesmo com ownedGames ainda undefined
      handler(newOwned) {
        //aqui eu posso usar um "LOADING"
        if (newOwned !== undefined) {
          this.ownedGamesArray = newOwned;
        }
      },
    },
    favoriteGames: {
      immediate: true, //isso significa que a função é chamada mesmo com ownedGames ainda undefined
      handler(newFavorite) {
        //aqui eu posso usar um "LOADING"
        if (newFavorite !== undefined) {
          this.favoriteGamesArray = newFavorite;
        }
      },
    },
    wishListedGames: {
      immediate: true, //isso significa que a função é chamada mesmo com ownedGames ainda undefined
      handler(newWish) {
        //aqui eu posso usar um "LOADING"
        if (newWish !== undefined) {
          this.wishListedGamesArray = newWish;
        }
      },
    },
  },
  methods: {},
};
</script>

<style scoped>
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

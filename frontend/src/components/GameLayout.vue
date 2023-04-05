<template>
  <div class="game-list">
    <div v-for="game in games" :key="game.id" class="game">
      <img :src="game.background_image" :alt="game.name" />
      <div class="game-title">{{ game.name }}</div>
      <div class="game-hover">
        <button>Login or Register to track your games</button>
        <!-- ajustar essa rota, apenas teste no momento -->
        <router-link
          :to="{
            name: 'specificGame',
            params: { id: game.id },
          }"
          >Info</router-link
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "GameLayout",
  props: {
    games: "",
  },
  mounted() {
    setTimeout(() => {
      console.log(this.games);
    }, 3333);
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
  width: 100%;
  height: 200px;
  object-fit: cover;
  margin-bottom: 10px;
}

.game-title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 10px;
}

.game:hover .game-hover {
  display: flex;
}

.game-hover {
  display: none;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
}

.game-hover button {
  background-color: #0077ff;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
  margin-right: 10px;
}

.game-hover a {
  font-size: 14px;
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
  .game img {
    height: 100px;
    margin-bottom: 5px;
  }
}
</style>

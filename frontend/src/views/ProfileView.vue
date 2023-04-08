<template>
  <div class="main-wrapper">
    <div class="routerChecker">
      <div v-if="checkOwnProfile">
        <OwnProfile />
      </div>
      <div v-else-if="checkAnotherProfile">Another</div>
      <div v-else>Not logged</div>
    </div>
  </div>
</template>

<script>
import { getCurrentInstance } from "vue";

import OwnProfile from "../components/OwnProfile.vue";
export default {
  name: "ProfileView",
  components: {
    OwnProfile,
  },
  data() {
    return {
      ownProfile: null,
      anotherProfile: null,
      notLogged: null,
    };
  },
  computed: {
    logged() {
      return this.$store.state.logged;
    },
    checkOwnProfile() {
      //checa se tá logado
      if (this.$store.state.logged) {
        return this.$route.params.id == this.$store.state.user_id;
      }
      return false;
    },
    checkAnotherProfile() {
      if (this.$store.state.logged) {
        return this.$route.params.id !== this.$store.state.user_id;
      }
      return false;
    },
  }, //isso aqui vai servir para "forçar" um update da rota caso estejamos em outro perfil de outra pessoa e vamos para o nosso
  mounted() {},
  methods: {
    //nao tenho ideia de como isso funciona, só sei que é para atualziar a rota ao ser clicada nela meio que "já" estando nela
    methodThatForcesUpdate() {
      const instance = getCurrentInstance();
      this.$forceUpdate();
      instance.proxy.forceUpdate();
    },
  },
};
</script>

<style></style>

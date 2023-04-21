<template>
  <div class="main-wrapper">
    <Loading v-if="loadingUser" />
    <div v-else-if="user">
      <div class="pagetitle">
        <p class="title">Edit your profile</p>
      </div>
      <div class="personnal-info">
        <img class="profile-image" :src="userImage" alt="" />
        <div class="user-edit-container">
          <h1 class="username">{{ user.name }}</h1>
        </div>
      </div>
      <div class="form-edit">
        <div class="form-container">
          <form class="form-username" action="" method="post" @submit.prevent>
            <div class="input-group">
              <div class="label-container">
                <label for="username">Change username</label>
                <span v-if="usernameError" class="error"
                  >*{{ usernameError }}</span
                >
              </div>
              <input
                v-model="username"
                type="text"
                id="username"
                placeholder="4-12 characters"
              />
            </div>
            <button @click="editUsername" type="button">Edit username</button>
          </form>
        </div>
        <div class="form-container">
          <form
            class="form-image"
            action=""
            method="post"
            @submit.prevent
            enctype="multipart/form-data"
          >
            <div class="input-group">
              <div class="label-container">
                <label for="image">Change avatar</label>
                <span v-if="imageError" class="error">*{{ imageError }}</span>
              </div>
              <input type="file" v-on:change="checkImage" accept="image/*" />
            </div>
            <button @click="editImage" type="button">Edit avatar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Loading from "../components/Loading.vue";

export default {
  name: "EditProfileView.vue",
  data() {
    return {
      user: null,
      error: null,
      usernameError: null,
      imageError: null,
      loadingUser: true,
      username: null,
      image: null,
    };
  },
  computed: {
    userImage() {
      if (this.user) {
        if (this.user.image) {
          return `${process.env.VUE_APP_IMAGE_URL}${this.user.image}`;
        } else {
          return require("@/assets/def-avatar-profile.jpg");
        }
      }
    },
  },
  components: {
    Loading,
  },
  methods: {
    async getUserInfo() {
      this.loadingUser = true;
      const id = this.$store.state.user_id;
      try {
        const response = await axios
          .get(`${process.env.VUE_APP_APIURL}userinfo/${id}`)
          .then((response) => {
            this.user = response.data.user;
          });
      } catch (error) {
        console.log(error.response.data.error);
      }
      this.loadingUser = false;
    },
    async editUsername() {
      this.loadingUser = true;
      try {
        const userId = this.$store.state.user_id;
        const personal_token = this.$store.state.personal_token;
        const response = await axios.put(
          `${process.env.VUE_APP_APIURL}edit-username`,
          {
            user_id: userId,
            username: this.username,
          },
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
            },
          }
        );
        //aqui recebo o que o laravel me retornou
        console.log(response.data);
        this.user = response.data.user;
        this.username = null;
      } catch (error) {
        console.log(error.response.data);
      }
      this.loadingUser = false;
    },
    async editImage() {
      this.loadingUser = true;
      try {
        const userId = this.$store.state.user_id;
        const personal_token = this.$store.state.personal_token;
        const formData = new FormData();
        formData.append("image", this.image);

        const response = await axios.post(
          `${process.env.VUE_APP_APIURL}edit-image`,
          formData,
          {
            headers: {
              Authorization: `Bearer ${personal_token}`,
              "Content-Type": "multipart/form-data",
            },
            params: {
              user_id: userId,
            },
          }
        );
        this.user = response.data.user;
        //aqui recebo o que o laravel me retornou
        console.log(response.data.user);
      } catch (error) {
        console.log(error.response.data);
      }
      this.loadingUser = false;
    },
    checkImage(event) {
      this.image = event.target.files[0];
    },
  },
  async created() {
    await this.getUserInfo();
  },
};
</script>

<style scoped>
.personnal-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 90%;
  margin: 0 auto;
  margin-top: 30px;
}
.profile-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}
.pagetitle {
  margin: 0 auto;
}
.title {
  text-align: center;
  font-size: 6vh;
  font-weight: 800;
}
.username {
  font-size: 4vh;
  font-weight: 700;
}
.form-edit {
  background: rgba(48, 25, 189, 0.62);
  border-radius: 20px;
  margin: 0 auto;
  padding: 2vh 2vw;
  display: flex;
  flex-direction: column;
  margin-bottom: 10vh;
  width: 85vw;
  height: 50vh;
  justify-content: space-around;
  align-items: center;
  max-height: 100%;
  margin-top: 3vh;
}
.form-edit button {
  background: #1abc9c;
  padding: 2vh 2vw;
  border-radius: 35px;
  width: 25vw;
  height: 80%;
  cursor: pointer;
  font-size: 1.5vh;
  font-weight: bolder;
  border: none;
  box-shadow: 5px 5px 4px rgba(0, 0, 0, 0.3);
  transition: 0.4s;
  margin-bottom: 2.5vh;
}
.form-username,
.form-image {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.input-group {
  display: flex;
  flex-direction: column;
  width: 90%;
  max-width: 100%;
}
.input-group input {
  width: 100%;
  margin-bottom: 1.3vh;
  padding: 2vh 1.2vw;
  font-size: 2vh;
  align-self: center;
}

@media screen and (min-width: 768px) {
  .personnal-info {
    display: flex;
    justify-content: center;
    flex-direction: row;
    gap: 20px;
    width: 90%;
    margin: 0 auto;
    margin-top: 30px;
  }
  .form-edit {
    width: 30vw;
    height: 70vh;
  }
  .form-edit button {
    width: 15vw;
  }
}
</style>

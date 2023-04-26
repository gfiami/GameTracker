<template>
  <div class="main-wrapper">
    <Loading v-if="loadingUser" />
    <div v-else-if="user">
      <div class="back-profile">
        <router-link
          :to="{ name: 'profile', params: { id: user.id } }"
          :key="$route.fullPath"
        >
          <i class="fas fa-caret-left"></i> Back to Profile
        </router-link>
      </div>
      <div class="pagetitle">
        <p class="title">Edit your profile</p>
      </div>
      <div class="personnal-info">
        <img class="profile-image" :src="userImage" alt="" :id="imageChanged" />
        <div class="user-edit-container">
          <h1 class="username" :id="userNameChanged">{{ user.name }}</h1>
        </div>
      </div>
      <div class="form-edit">
        <div class="form-container">
          <form class="form-username" action="" method="post" @submit.prevent>
            <div class="input-group">
              <span v-if="usernameError" class="error"
                >*{{ usernameError }}</span
              >
              <div class="label-container">
                <label for="username" class="username-label"
                  >Change username</label
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
              <span v-if="imageError" class="error">*{{ imageError }}</span>

              <div class="label-container">
                <label for="file-upload" class="custom-upload"
                  >Upload new avatar</label
                >
              </div>
              <input
                type="file"
                id="file-upload"
                v-on:change="checkImage"
                accept="image/*"
              />
            </div>
            <div class="file-name-container">
              <p class="file-name">{{ fileName }}</p>
            </div>
            <button @click="editImage" type="button">Submit avatar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Loading from "../components/Loading.vue";
import { mapMutations } from "vuex";

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
      newImage: false,
      newUserName: false,
      fileName: "No image selected",
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
    imageChanged() {
      return this.newImage ? "focusImage" : "";
    },
    userNameChanged() {
      return this.newUserName ? "focusUser" : "";
    },
  },
  components: {
    Loading,
  },
  methods: {
    ...mapMutations(["login"]),
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
      this.usernameError = null;
      if (this.username == undefined) {
        this.usernameError = "The username field is required.";
        return false;
      }
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
        localStorage.removeItem("gameTrackerUserToken");
        localStorage.setItem("gameTrackerUserToken", response.data.user.name);
        this.$store.commit("login", true);

        this.user = response.data.user;
        this.username = null;
        this.newUserName = true;
        setTimeout(() => {
          this.newUserName = false;
        }, 2000);
      } catch (error) {
        this.username = null;
        console.log(error.response.data.errors.username);
        this.usernameError = error.response.data.errors.username[0];
      }
    },
    async editImage() {
      this.imageError = null;
      if (this.image == undefined) {
        this.imageError = "Upload an image to change your avatar";
        return false;
      }
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
        this.newImage = true;
        setTimeout(() => {
          this.newImage = false;
        }, 2000);

        //aqui recebo o que o laravel me retornou
        console.log(response.data.user);
      } catch (error) {
        if (error.response.data.errors.image == "The image must be an image.") {
          this.imageError = "Your avatar needs to be an image.";
        }
        if (error.response.data.errors.image == "The image failed to upload.") {
          this.imageError = "Please upload an image that is smaller than 2 MB.";
        }

        console.log(error.response.data);
      }
      this.image = null;
      this.fileName = "No image selected";
    },
    checkImage(event) {
      this.imageError = null;
      this.image = event.target.files[0];
      if (this.image !== undefined) {
        this.fileName = this.image.name;
      } else {
        this.fileName = "No image selected";
      }
    },
  },
  async created() {
    await this.getUserInfo();
  },
};
</script>

<style scoped>
#focusImage {
  border-radius: 50%;
  border: 2px solid #1abc9c;
  transform: scale(1.1);
  transition: transform 0.5s ease-in-out;
}
#focusUser {
  transform: scale(1.3);
  color: #1abc9c;
  transition: transform 0.5s ease-in-out;
}
.back-profile {
  margin-left: 2vw;
  margin-top: 1vh;
}
.back-profile a {
  color: white;
}
.error {
  margin: 0 auto;
  text-align: center;
  color: #d9ff42;
  font-size: 1.4vh;
  font-weight: 500;
  text-shadow: 1px 1px 1px #000000;
}
.personnal-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 90%;
  margin: 0 auto;
  margin-top: 2vh;
}
.profile-image {
  transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;

  width: 10vh;
  height: 10vh;
  border-radius: 50%;
}
.pagetitle {
  margin: 0 auto;
}
.title {
  margin-top: 1vh;
  text-align: center;
  font-size: 4vh;
  font-weight: 700;
}
.username {
  transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;

  font-size: 2vh;
  font-weight: 400;
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
.username-label {
  display: inline-block;
}
.custom-upload {
  border-radius: 6px;
  border: 2px solid #161b3a;
  display: inline-block;
  padding: 6px 12px;
  cursor: pointer;
  margin-bottom: 0.5vh;
}
.file-name-container {
  margin-bottom: 1vh;
}
.custom-upload:hover {
  background-color: #161b3a;
  color: white;
}
input[type="file"] {
  display: none;
}
.input-group input {
  width: 100%;
  margin-bottom: 1.3vh;
  padding: 2vh 1.2vw;
  font-size: 2vh;
  align-self: center;
}
.label-container {
  text-align: center;
}
.form-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
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
    height: 60vh;
  }
  .form-edit button {
    width: 15vw;
  }
}
</style>

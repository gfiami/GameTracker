<template>
  <div>
    <div class="personnal-info">
      <img
        class="profile-image"
        src="../assets/def-avatar-profile.jpg"
        alt=""
      />
      <div class="user-edit-container">
        <h1 class="username">Username</h1>
        <a class="edit-profile" href="">Edit profile</a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OwnProfile",
  data() {
    return {
      //username: this.$store.state.token,
    };
  },
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

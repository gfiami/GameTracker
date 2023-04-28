<template>
  <div class="main-wrapper">
    <div class="title-container">
      <div class="title">Contact GameTracker</div>
      <div class="contact-container">
        <p class="contact-message">
          If you have any questions, comments, or feedback, please email us at
          <a href="mailto:contact@gametracker.com"
            ><u>contact@gametracker.com</u></a
          >. We value your input and will strive to respond within 24 hours.
          Thank you for visiting our website!
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Contact",
  mounted() {
    window.scrollTo(0, 0);
  },
};
</script>

<style scoped>
.title-container {
  margin-top: 1vh;
}

.title {
  text-align: center;
  font-size: 4vh;
  font-weight: 700;
}
.contact-container {
  padding: 3vh 3vh;
  display: flex;
  justify-content: center;
  text-align: center;
  margin: 0 auto;
  background-color: #330066;
  width: 90%;
  border-radius: 12px;
  box-shadow: 0 0 20px 5px rgba(0, 0, 0, 0.5), 0 0 40px 10px rgba(0, 0, 0, 0.2);
}
.contact-message {
  font-size: 2vh;
}
.contact-message a {
  text-decoration: none;
  color: white;
}

@media screen and (min-width: 768px) {
  .title {
    font-size: 5vh;
  }

  .contact-container {
    width: 70%;
  }
}
</style>

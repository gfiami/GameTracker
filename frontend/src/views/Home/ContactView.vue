<template>
  <div class="main-wrapper">
    <div class="title-container">
      <div class="title">Contact GameTracker</div>
      <div class="contact-container">
        <p class="contact-message">
          Thanks for visiting GameTracker! We take your feedback seriously and
          believe that it's a crucial part of our ongoing efforts to improve our
          website. So, if you have any questions, comments, or suggestions,
          please don't hesitate to reach out to us at
          <a href="mailto:contact@gametracker.com"
            ><u>contact@gametracker.com</u></a
          >. We promise to respond as quickly as possible, and we appreciate
          your patience in advance.
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
  margin-top: 4vh;
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

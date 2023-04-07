<template>
  <div>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
export default {
  name: "Message",
  props: ["message"],
};
</script>

<style></style>

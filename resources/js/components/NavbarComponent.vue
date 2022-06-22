<template>
  <div>
    <nav class="navbar">
      <router-link to="/edit">mon compte</router-link>
      <router-link to="/projet">projet</router-link>
    </nav>
  </div>
</template>

<script>
export default {};
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-around;
}
</style>
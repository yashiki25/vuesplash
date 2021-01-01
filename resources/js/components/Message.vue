<template>
  <div class="message" v-show="message">
    {{ message }}
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: "Message",
  computed: {
    ...mapState({
      message: state => state.message.content
    })
  }
}
</script>

<style scoped>

</style>

<template>
  <div>
    <header>
      <Navbar />
    </header>
    <main>
      <div class="container">
        <Message />
        <RouterView />
      </div>
    </main>
    <footer>
      <Footer />
    </footer>
  </div>
</template>

<script>
import Message from './components/Message.vue';
import Navbar from "./components/Navbar";
import Footer from "./components/Footer";
import { UNAUTHORIZED, INTERNAL_SERVER_ERROR } from './util'

export default {
name: "App.vue",
  components: {
    Message,
    Navbar,
    Footer
  },
  computed: {
    errorCode () {
      return this.$store.state.error.code
    }
  },
  watch: {
    errorCode: {
      async handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        } else if (val === UNAUTHORIZED) {
          // 未認証の場合、トークンをリフレッシュ
          await axios.get('/api/refresh-token')
          this.$store.commit('auth/setUser', null)
          this.$router.push('/login')
        }
      },
      immediate: true
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>

<style scoped>

</style>

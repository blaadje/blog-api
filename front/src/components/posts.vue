<template>
  <div>
    {{ posts }}
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      posts: []
    }
  },
  created () {
    axios.get('/posts').then((response) => {
      this.posts = response.data
    })
  }
}
</script>
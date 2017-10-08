<template>
  <div class="ui divided items">
    <div class="item" v-for="post,index in posts">
      <div class="content">
        <a class="header">{{ post.name }}</a>
        <div class="meta">
          <span class="cinema">{{ post.description }}</span>
        </div>
        <div class="description">
          <p></p>
        </div>
        <div class="extra">
          <div class="ui label">IMAX</div>
          <div class="ui label"><i class="globe icon"></i> Additional Languages</div>
        </div>
      </div>
    </div>
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
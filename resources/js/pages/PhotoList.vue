<template>
  <div class="photo-list">
    <ul class="tab" style="padding:10px">
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 1 }"
        @click="tab = 1"
      >All Post</li>
      <li
        v-if="isLogin"
        class="tab__item"
        :class="{'tab__item--active': tab === 2 }"
        @click="tab = 2"
      >Likes post</li>
      <li
        v-if="isLogin"
        class="tab__item"
        :class="{'tab__item--active': tab === 3 }"
        @click="tab = 3"
      >Follow users post</li>
    </ul>
    <div v-show="tab === 1" class="grid">
      <Photo
        class="grid__item"
        v-for="photo in photos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />
    </div>
    <div v-show="tab === 2" class="grid">
      <Photo
        class="grid__item"
        v-for="photo in likesPhotos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />

    </div>
    <div v-show="tab === 3" class="grid">
      <Photo
        class="grid__item"
        v-for="photo in followPhotos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />

    </div>
    <Pagination :current-page="currentPage" :last-page="lastPage" />
  </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo.vue'
import Pagination from '../components/Pagination.vue'

export default {
  components: {
    Photo,
    Pagination
  },
  props: {
    page: {
        type: Number,
        required: false,
        default: 1
    }
  },
  data () {
    return {
      tab: 1,
      likesPhotos:[],
      followPhotos:[],
      photos: [],
      currentPage: 0,
      lastPage: 0
    }
  },
  methods: {
    async fetchPhotos () {
      const response = await axios.get(`/api/photos/?page=${this.page}`)

      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.photos = response.data.data
      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page

      this.viewLikesOnly()
      this.viewFollowOnly()
    },
    viewLikesOnly() {
      const onlyLike = []
      for(let photo of this.photos) {
        if(photo.liked_by_user) {
          onlyLike.push(photo)
        }
      }
      this.likesPhotos = onlyLike
    },
    viewFollowOnly() {
      const onlyFollow = []
      for(let photo of this.photos) {
        if(photo.owner.follow_by_user) {
          onlyFollow.push(photo)
        }
      }
      this.followPhotos = onlyFollow
    },
    onLikeClick ({ id, liked }) {
        if (! this.$store.getters['auth/check']) {
            alert('いいね機能を使うにはログインしてください。')
            return false
        }

        if (liked) {
            this.unlike(id)
        } else {
            this.like(id)
        }
    },
    async like (id) {
        const response = await axios.put(`/api/photos/${id}/like`)

        if (response.status !== OK) {
            this.$store.commit('error/setCode', response.status)
            return false
        }

        this.photos = this.photos.map(photo => {
            if (photo.id === response.data.photo_id) {
                photo.likes_count += 1
                photo.liked_by_user = true
            }
            this.viewLikesOnly()
            return photo
        })
    },
    async unlike (id) {
        const response = await axios.delete(`/api/photos/${id}/like`)

        if (response.status !== OK) {
            this.$store.commit('error/setCode', response.status)
            return false
        }

        this.photos = this.photos.map(photo => {
            if (photo.id === response.data.photo_id) {
                photo.likes_count -= 1
                photo.liked_by_user = false
            }
            this.viewLikesOnly()
            return photo
        })
    },
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchPhotos()
      },
      immediate: true
    }
  },
  computed: {
  // ログイン状態をstoreでチェック
    isLogin () {
      return this.$store.getters['auth/check']
    }
  }
}

</script>

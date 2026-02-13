import { createRouter, createWebHistory } from 'vue-router'
import Login from "@/pages/auth/Login.vue";
import Home from "@/pages/home/Home.vue";
import Register from "@/pages/auth/Register.vue";
import ArtistStudio from "@/pages/artist-studio/ArtistStudio.vue";
import { useAuthStore } from '@/stores/auth.js'
import Release from '@/pages/release/Release.vue'
import Playlist from '@/pages/playlist/Playlist.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
      {
          path: '/login',
          name: 'login',
          component: Login,
      },
      {
          path: '/register',
          name: 'register',
          component: Register,
      },
      {
          path: '/',
          name: 'home',
          component: Home,
      },
      {
          path: '/release/:releaseId',
          name: 'release',
          component: Release
      },
      {
          path: '/playlist/:playlistId',
          name: 'playlist',
          component: Playlist
      },
      {
          path: '/artists',
          name: 'artists',
          component: ArtistStudio,
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.role !== '2' || auth.user?.role !== '2') next('/');
              else next();
          }
      }
  ],
})
export default router

import { createRouter, createWebHistory } from 'vue-router'
import Login from "@/pages/auth/Login.vue";
import Home from "@/pages/home/Home.vue";
import Register from "@/pages/auth/Register.vue";
import ArtistStudio from "@/pages/artist-studio/ArtistStudio.vue";
import { useAuthStore } from '@/stores/auth.js'
import Moderation from '@/pages/admin/moderation/Moderation.vue'
import Playlist from '@/pages/playlist/Playlist.vue'
import Admin from '@/pages/admin/Admin.vue'
import Users from "@/pages/admin/users/Users.vue";
import Release from "@/pages/release/Release.vue";
import Playlists from "@/pages/admin/playlists/Playlists.vue";
import ArtistCard from "@/pages/artist-card/ArtistCard.vue";
import Subscription from "@/pages/subscription/Subscription.vue";

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
          path: '/subscription',
          name: 'subscription',
          component: Subscription,
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
          path: '/artist/:artistId',
          name: 'artist',
          component: ArtistCard
      },
      {
          path: '/artists',
          name: 'artists',
          component: ArtistStudio,
          meta: { requiresAuth: true },
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.roles[0]?.slug !== 'premium') next('/');
              else next();
          }
      },
      {
          path: '/admin/overview',
          name: 'admin/overview',
          component: Admin,
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.roles[0]?.slug !== 'admin') next('/');
              else next();
          }
      },
      {
          path: '/admin/users',
          name: 'admin/users',
          component: Users,
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.roles[0]?.slug !== 'admin') next('/');
              else next();
          }
      },
      {
          path: '/admin/moderation',
          name: 'admin/moderation',
          component: Moderation,
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.roles[0]?.slug !== 'admin') next('/');
              else next();
          }
      },
      {
          path: '/admin/playlists',
          name: 'admin/playlists',
          component: Playlists,
          beforeEnter: (to, from, next) => {
              const auth = useAuthStore()

              if (auth.user?.roles[0]?.slug !== 'admin') next('/');
              else next();
          }
      },
  ],
})

export default router

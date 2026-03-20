import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
    optimizeDeps: {
        exclude: ['@babel/types', '@babel/generator', '@babel/parser']
    },
  server: {
    host: true,
    port: 5175
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
        '@babel/types': false,
        '@babel/parser': false,
        '@babel/generator': false
    },
  },
})

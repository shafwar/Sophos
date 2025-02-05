import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [
    // Laravel Vite plugin
    laravel({
      input: [
        'resources/sass/app.scss', // SCSS entry file
        'resources/js/app.jsx',     // JavaScript entry file (your app.js that imports React and other files)
      ],
      refresh: true, // Automatically refresh when files change
    }),
    // React plugin to handle JSX and React components
    react(),
  ],
  // Additional Vite config options can be added here if necessary
});

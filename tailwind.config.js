import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',   // Memastikan file React termasuk
    './resources/**/*.jsx',  // Memastikan file JSX React juga termasuk
    './resources/**/*.vue',  // Memastikan file Vue termasuk
    './resources/**/*.{html,js,jsx,php}',  // Jalur tambahan untuk file HTML, JS, JSX, PHP
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#003B7B',   // Warna biru utama
        'secondary': '#00BFFF', // Warna biru muda
        'accent': '#f4f7fc',    // Warna latar belakang aksen
        'success': '#28a745',   // Warna hijau untuk sukses
        'warning': '#ffc107',   // Warna kuning untuk peringatan
        'danger': '#dc3545',    // Warna merah untuk bahaya
      },
      fontFamily: {
        sans: ['Poppins', ...defaultTheme.fontFamily.sans],  // Font kustom menggunakan Poppins
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: 0, transform: 'translateY(-10px)' },
          '100%': { opacity: 1, transform: 'translateY(0)' }
        }
      },
      animation: {
        fadeIn: 'fadeIn 0.2s ease-out' // Pengaturan animasi fadeIn
      },
      boxShadow: {
        'lg': '0 4px 12px rgba(0, 0, 0, 0.1)', // Bayangan khusus untuk navbar
        'xl': '0 8px 20px rgba(0, 0, 0, 0.08)', // Bayangan khusus untuk card
      }
    },
  },
  plugins: [],
};

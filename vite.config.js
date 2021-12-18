import {defineConfig} from 'vite'
import reactRefresh from '@vitejs/plugin-react-refresh' // Spécifique à react

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [reactRefresh()],
    root: './assets',
    base: '/build/',
    server: {
        hmr: {
            protocol: 'ws'
        }
    },
    build: {
        manifest: true,
        assetsDir: '',
        outDir: '../public/build/',
        rollupOptions: {
            output: {
                manualChunks: undefined // On ne veut pas créer un fichier vendors, car on n'a ici qu'un point d'entré
            },
            input: {
                'app.js': './assets/app.js',
                // 'security/app.js': './assets/app.js',
            }
        }
    }
})
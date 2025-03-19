import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig(({ mode }) => {
    // Load environment variables
    const env = loadEnv(mode, process.cwd(), "");

    return {
        plugins: [
            laravel({
                input: "resources/js/app.js",
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                "@": path.resolve(__dirname, "resources/js"), // Set alias for '@/'
            },
            extensions: [".js", ".json", ".vue"],
        },
        server: {
            host: "0.0.0.0", // Allow access from network
            port: 5173,
            cors: true,
            hmr: {
                host: new URL(env.APP_URL).hostname, // Dynamically use APP_URL from .env
                protocol: "ws",
            },
        },
    };
});
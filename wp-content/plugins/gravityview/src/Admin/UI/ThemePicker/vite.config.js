import { defineConfig } from "vite";
import cssInjectedByJsPlugin from "vite-plugin-css-injected-by-js";
import path from "node:path";
import { fileURLToPath } from "node:url";

// `import.meta.dirname` is only available on Node 20.11+/21.2+; derive the
// directory the portable way so the build runs on the project's Node 20.x.
const dirname = path.dirname(fileURLToPath(import.meta.url));

/**
 * IIFE bundle for the Styles-tab visual pickers. React + ReactDOM are
 * externalized to WordPress's own globals (the `react` / `react-dom` script
 * handles set `window.React` / `window.ReactDOM`), so the widget shares the
 * page's React instance and the bundle stays tiny. The classic JSX transform
 * (esbuild) compiles to `React.createElement`; `React` is auto-injected.
 * CSS is inlined into the JS and injected at runtime, so a single asset ships.
 *
 * Output: assets/admin-theme-picker/theme-picker.js
 */
const WP_EXTERNALS = {
  react: "React",
  "react-dom": "ReactDOM",
  "react-dom/client": "ReactDOM",
};

export default defineConfig({
  plugins: [cssInjectedByJsPlugin()],
  esbuild: {
    jsx: "transform",
    jsxFactory: "React.createElement",
    jsxFragment: "React.Fragment",
    jsxInject: "import React from 'react'",
  },
  build: {
    outDir: path.resolve(dirname, "../../../../assets/admin-theme-picker"),
    emptyOutDir: false,
    sourcemap: false,
    rollupOptions: {
      input: path.resolve(dirname, "src/main.jsx"),
      external: Object.keys(WP_EXTERNALS),
      output: {
        format: "iife",
        entryFileNames: "theme-picker.js",
        assetFileNames: "theme-picker[extname]",
        globals: WP_EXTERNALS,
      },
    },
  },
});

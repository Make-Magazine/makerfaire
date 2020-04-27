import svelte from 'rollup-plugin-svelte-hot';
import resolve from '@rollup/plugin-node-resolve';
import commonjs from '@rollup/plugin-commonjs';
import livereload from 'rollup-plugin-livereload';
import { terser } from 'rollup-plugin-terser';
import postcss from 'rollup-plugin-postcss';
import hmr, { autoCreate } from 'rollup-plugin-hot';
import babel from 'rollup-plugin-babel';
import autoPreprocess from 'svelte-preprocess';

const production = !! process.env.PRODUCTION;
const nollup = !! process.env.NOLLUP;
const watch = !! process.env.ROLLUP_WATCH;
const useLiveReload = !! process.env.LIVERELOAD;
const dev = ! production && ( watch || useLiveReload );
const hot = watch && ! useLiveReload;

export default {
	input: production ? 'src/index-prod.js' : 'src/index-dev.js',
	output: {
		sourcemap: true,
		format: 'iife',
		name: 'AdvancedFilter',
		file: production ? '../advanced-filter.js' : 'public/build/bundle.js',
	},
	plugins: [
		svelte( {
			// enable run-time checks when not in production
			dev: ! production,
			hot: hot && {
				// Set true to enable support for Nollup
				nollup: false,
				// Prevent preserving local component state
				noPreserveState: false,
				// Prevent doing a full reload on next HMR update after fatal error
				noReload: false,
				// Try to recover after runtime errors in component init
				optimistic: false,
			},
			// we'll extract any component CSS out into
			// a separate file — better for performance
			/*			css: css => {
			 css.write( production ? '../../css/advanced-filter.css' : 'public/build/bundle.css' );
			 },*/
			...( production && {
				css: css => {
					css.write( '../../css/advanced-filter.css' );
				},
			} ),
			preprocess: autoPreprocess(),
		} ),
		postcss(),
		resolve( {
			browser: true,
			//dedupe: importee => importee === 'svelte' || importee.startsWith( 'svelte/' ),
		} ),
		commonjs(),

		// In dev mode, call `npm run start:dev` once the bundle has been generated
		dev && ! nollup && serve(),

		// Watch the `public` directory and refresh the browser on changes when not in production
		useLiveReload && livereload( 'public' ),

		// If we're building for production (npm run build instead of npm run dev), minify
		production && terser(),

		// Automatically create missing imported files. This helps keeping
		// the HMR server alive, because Rollup watch tends to crash and
		// hang indefinitely after you've tried to import a missing file.
		hot && autoCreate( {
			include: 'src/**/*',
			// Prevent recreating a file that has just been
			// deleted (Rollup watch will crash when you do that though).
			recreate: true,
		} ),

		production && babel({
			extensions: ['.js', '.mjs', '.html', '.svelte'],
			runtimeHelpers: true,
			exclude: ['node_modules/@babel/**'],
			presets: [
				['@babel/preset-env', {
					targets: '> 0.25%, not dead, IE 11'
				}]
			],
		}),

		!production && hmr( {
			public: 'public',
			inMemory: true,
			// This is needed, otherwise Terser (in npm run build) chokes
			// on import.meta. With this option, the plugin will replace
			// import.meta.hot with module.hot, and will do nothing else.
			compatModuleHot: ! hot,
		} ),
	],
	watch: {
		clearScreen: false,
	},
};

function serve() {
	let started = false;

	return {
		name: 'svelte/template:serve',
		writeBundle() {
			if ( ! started ) {
				started = true;
				const flags = [ 'run', 'start', '--', '--dev' ];
				require( 'child_process' ).spawn( 'npm', flags, {
					stdio: [ 'ignore', 'inherit', 'inherit' ],
					shell: true,
				} );
			}
		},
	};
}

const path = require( 'path' );
const fs = require( 'fs' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const entryPoints = { ...defaultConfig?.entry };
const blocksFolder = path.resolve( process.cwd(), 'blocks' );
const sharedSourcesFolder = path.resolve( process.cwd(), 'shared' );

module.exports = ( async () => {
	const blocks = await fs.promises.readdir( blocksFolder, ( err, folders ) => folders );

	for ( const block of blocks ) {
		// Skip system files and hidden files.
		if ( block.startsWith( '.' ) || ! fs.statSync( path.resolve( blocksFolder, block ) ).isDirectory() ) {
			continue;
		}

		entryPoints[ block ] = path.resolve( process.cwd(), `${ blocksFolder }/${ block }` );
	}

	return {
		...defaultConfig,
		devServer: {
			...defaultConfig.devServer,
			allowedHosts: 'all',
		},
		entry: entryPoints,
		resolve: {
			...defaultConfig.resolve,
			alias: {
				...defaultConfig.resolve.alias,
				'shared': sharedSourcesFolder,
			},
			// Babel injects @babel/runtime helper imports into transpiled
			// sources, including ones living OUTSIDE this directory (the
			// shared page-builder sources one level up). Module resolution
			// starts from each source file's own path, so this module's
			// node_modules must be an explicit resolution root or those
			// helpers only resolve when the plugin root happens to have
			// @babel/runtime hoisted.
			modules: [ path.resolve( __dirname, 'node_modules' ), 'node_modules' ],
		},
	};
} )();

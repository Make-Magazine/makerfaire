var path = require('path');

const paths = {
	root: __dirname,
	src: path.join( __dirname, 'assets/js/src/public' ),
	dist: path.join( __dirname, 'assets/js/dist' )
};

module.exports = {
	entry: paths.src,
	output: {
		path: paths.dist,
		filename: 'bundle.js'
	},
	externals: {
		jquery: 'jQuery',
		L: 'L',
		MapifyTooltip: 'MapifyTooltip'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-env'],
					plugins: ['@babel/plugin-transform-runtime']
				}
			}
		]
	},
	devtool: 'cheap-source-map',
	mode: 'production',
};
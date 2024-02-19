const path = require('path');
const webpack = require('webpack');

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
		loaders: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				query: {
				  presets: ['env']
				}
			}
		]
	},
	devtool: 'cheap-source-map',
	plugins: [
		new webpack.ProvidePlugin({
			Promise: 'bluebird'
		})
	]
};

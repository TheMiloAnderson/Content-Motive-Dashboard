var path = require('path');
var webpack = require('webpack');

module.exports = {
	entry: './webpack-src/dash.js',
	output: {
		path: path.resolve(__dirname, 'web/js/build'),
		filename: 'dashboard.bundle.min.js'
	},
	module: {
		loaders: [{
			test: /\.js$/,
			loader: 'babel-loader',
			query: {
				presets: ['es2015']
			}
		}]
	},
	stats: {
		colors: true
	},
	plugins: [
		new webpack.optimize.UglifyJsPlugin({minimize: true})
	],
	devtool: 'source-map',
	resolve: {
		alias: {
			'node_modules': path.join(__dirname, 'node_modules')
		}
	}
};
var path = require('path');
var webpack = require('webpack');

module.exports = {
	entry: ['./web/js/chart.js','./web/js/table.js','./web/js/dashboard.js'],
	output: {
		path: path.resolve(__dirname, 'web/js/build'),
		filename: 'dashboard.bundle.js'
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
	devtool: 'source-map'
};
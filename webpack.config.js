var path = require('path');
var webpack = require('webpack');

module.exports = {
	entry: {
		dashboard: './webpack-src/dashboard.js',
		dealerForm: './webpack-src/dealerForm.js',
		site: './webpack-src/site.js'
	},
	output: {
		path: path.resolve(__dirname, 'web/js/build'),
		filename: '[name].bundle.min.js'
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
			'node_modules': path.join(__dirname, 'node_modules'),
			'yii2': path.join(__dirname, 'vendor/yiisoft/yii2'),
			'bower': path.join(__dirname, 'vendor/bower'),
		}
	}
};
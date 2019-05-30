/**
 * Stores all path info for application.
 *
 * @type {{app: string, module: Node.js path module, theme: {}}}
 */
const path = {
    module: require('path'),
};

const webpack = require('webpack');

module.exports =
    {
        name: 'product-configurator',
        entry: {
            app: './webroot/js/src/app.js'
        },
        output: {
            filename: '[name].bundle.js',
            path: path.module.resolve(__dirname + '/', './webroot/js/dist')
        },
        performance: {
            hints: false
        },
        plugins: [
            new webpack.ProvidePlugin({
                "$": 'jquery',
                jQuery: 'jquery',
                "window.jQuery": "jquery",
            })
        ],
        mode: 'production'
    };

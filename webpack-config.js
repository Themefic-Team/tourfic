const path = require('path');
const glob = require('glob');
// const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const entryPoints = {};

const appJs = glob.sync('./sass/app/js/*.js');
const adminJs = glob.sync('./sass/admin/js/*.js');

entryPoints['app/js/tourfic-scripts'] = appJs;
entryPoints['app/js/tourfic-scripts.min'] = appJs;
entryPoints['admin/js/tourfic-admin-scripts'] = adminJs;
entryPoints['admin/js/tourfic-admin-scripts.min'] = adminJs;

const config = {
    entry: entryPoints,

    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: '[name].js',
    },
    optimization: {
        minimize: true,
        // minimizer: [new UglifyJsPlugin({
        //     include: /\.min\.js$/
        // })]
    }
}

// Export the config object.
module.exports = config;
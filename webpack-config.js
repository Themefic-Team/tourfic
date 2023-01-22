const path = require('path');
const glob = require('glob');
const entryPoints = {};

const appJs = glob.sync('./sass/app/js/*.js');
const adminJs = glob.sync('./sass/admin/js/*.js');

entryPoints['app/js/tourfic-scripts'] = appJs;
entryPoints['app/js/tourfic-scripts.min'] = appJs;
entryPoints['admin/js/tourfic-admin-scripts'] = adminJs;
entryPoints['admin/js/tourfic-admin-scripts.min'] = adminJs;

//scss entry points
// const appScss = glob.sync('./sass/app/css/tourfic.scss');
// const adminScss = glob.sync('./sass/admin/css/tourfic-admin.scss');
//
// entryPoints['app/css/tourfic-style'] = appScss;
// entryPoints['admin/css/tourfic-admin'] = adminScss;

const config = {
    entry: entryPoints,

    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: '[name].js',
        clean: false
    },

    /*module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    // Creates `style` nodes from JS strings
                    'style-loader',
                    // Translates CSS into CommonJS
                    'css-loader',
                    // Compiles Sass to CSS
                    'sass-loader',
                ],
            },
        ],
    }*/
}

// Export the config object.
module.exports = config;
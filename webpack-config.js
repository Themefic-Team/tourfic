const path = require('path');
const glob = require('glob');
const entryPoints = {};

const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');
const proAppJs = glob.sync('./sass/app/js/pro/*.js');
const proAdminJs = glob.sync('./sass/admin/js/pro/*.js');

//tourfic free
entryPoints['tourfic/assets/app/js/tourfic-scripts'] = freeAppJs;
entryPoints['tourfic/assets/app/js/tourfic-scripts.min'] = freeAppJs;
entryPoints['tourfic/assets/admin/js/tourfic-admin-scripts.min'] = freeAdminJs;
//tourfic pro
entryPoints['tourfic-pro/assets/app/js/tourfic-pro'] = proAppJs;
entryPoints['tourfic-pro/assets/admin/js/tourfic-pro-admin'] = proAdminJs;

//scss entry points
// const appScss = glob.sync('./sass/app/css/tourfic.scss');
// const adminScss = glob.sync('./sass/admin/css/tourfic-admin.scss');
//
// entryPoints['app/css/tourfic-style'] = appScss;
// entryPoints['admin/css/tourfic-admin'] = adminScss;

const config = {
    entry: entryPoints,

    output: {
        path: path.resolve(__dirname, '../'),
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
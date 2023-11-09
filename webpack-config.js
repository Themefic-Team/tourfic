const path = require('path');
const glob = require('glob');
const entryPoints = {};

const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');
const vendorAdminJs = glob.sync('./sass/admin/js/addon/vendor/*.js');
const vendorAppJs = glob.sync('./sass/app/js/addon/vendor/*.js');
const iCalAdminJs = glob.sync('./sass/admin/js/addon/ical/*.js');
const proAppJs = glob.sync('./sass/app/js/pro/*.js');
const proAdminJs = ['./sass/admin/js/pro/locationpicker.jquery.js','./sass/admin/js/pro/locationpicker-custom.js','./sass/admin/js/pro/admin.js'];

//tourfic free
entryPoints['tourfic/assets/app/js/tourfic-scripts'] = freeAppJs;
entryPoints['tourfic/assets/app/js/tourfic-scripts.min'] = freeAppJs;
entryPoints['tourfic/assets/admin/js/tourfic-admin-scripts.min'] = freeAdminJs;
//tourfic pro
entryPoints['tourfic-pro/assets/app/js/tourfic-pro'] = proAppJs;
entryPoints['tourfic-pro/assets/admin/js/tourfic-pro-admin'] = proAdminJs;

//tourfic vendor addon
entryPoints['tourfic-vendor/admin/assets/js/tourfic-vendor-scripts.min'] = vendorAdminJs;
entryPoints['tourfic-vendor/public/assets/js/tourfic-vendor'] = vendorAppJs;

//tourfic ical addon
entryPoints['tourfic-ical/assets/admin/js/tourfic-ical.min'] = iCalAdminJs;

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
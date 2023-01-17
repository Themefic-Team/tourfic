const path = require('path');
const glob = require('glob');
const entryPoints = {};

const appJs = glob.sync('./sass/app/js/*.js');
const adminJs = glob.sync('./sass/admin/js/*.js');

entryPoints['app/js/tourfic-scripts'] = appJs;
entryPoints['app/js/tourfic-scripts.min'] = appJs;
entryPoints['admin/js/tourfic-admin-scripts.min'] = adminJs;

const config = {
    entry: entryPoints,

    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: '[name].js',
        clean: false
    },
}

// Export the config object.
module.exports = config;
const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const glob = require('glob');
const entryPoints = {};

const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');
const proAppJs = glob.sync('./sass/app/js/pro/*.js');
const proAdminJs = ['./sass/admin/js/pro/locationpicker.jquery.js','./sass/admin/js/pro/locationpicker-custom.js','./sass/admin/js/pro/admin.js'];

//tourfic free
entryPoints['tourfic/assets/app/js/tourfic-scripts'] = freeAppJs;
entryPoints['tourfic/assets/app/js/tourfic-scripts.min'] = freeAppJs;
entryPoints['tourfic/assets/admin/js/tourfic-admin-scripts.min'] = freeAdminJs;
//tourfic pro
entryPoints['tourfic-pro/assets/app/js/tourfic-pro'] = proAppJs;
entryPoints['tourfic-pro/assets/admin/js/tourfic-pro-admin'] = proAdminJs;


const js = {
    ...defaultConfig,
    entry: {
        'app/js/tourfic-scripts': glob.sync('./sass/app/js/free/*.js'),
        'app/js/tourfic-scripts.min': glob.sync('./sass/app/js/free/*.js'),
        'admin/js/tourfic-admin-scripts.min': glob.sync('./sass/admin/js/free/*.js'),
    },
    output: {
        path: path.resolve(__dirname, 'assets/'),
        filename: '[name].js',
        clean: false
    }
}

const ReactJs = {
    ...defaultConfig,
    entry: {
        'dashboard': './src/dashboard.js',
    },
    output: {
        path: path.resolve(__dirname, 'build/'),
        filename: '[name].js',
        clean: false
    }
}

const scss = {
    ...defaultConfig,
    entry: {
        'app/css/tourfic-style': './sass/app/css/free/tourfic.scss',
        'admin/css/tourfic-admin': './sass/admin/css/free/tourfic-admin.scss',
    },
    output: {
        path: path.resolve(__dirname, 'assets/'),
        clean: false
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    }
};

module.exports = [js, ReactJs, scss];
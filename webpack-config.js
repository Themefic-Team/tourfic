const path = require('path');

const config = {
    entry: {
        'tourfic-scripts': './sass/app/js/tourfic.js',
        // admin: './sass/admin/js/admin-index.js'
    },

    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: 'app/js/[name].js',
        clean: false
    },

    module: {
        rules: [
            {
                // Look for any .js files.
                test: /\.js$/,
                // Exclude the node_modules folder.
                exclude: /node_modules/,
                // Use babel loader to transpile the JS files.
                loader: 'babel-loader'
            }
        ]
    }
}

// Export the config object.
module.exports = config;
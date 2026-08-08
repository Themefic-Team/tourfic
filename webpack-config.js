const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Entrypoints Object
const entryPoints = {};
const minEntryPoints = {};
const StyleEntryPoints = {};

const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');

//tourfic free
entryPoints['assets/app/js/tourfic-scripts'] = freeAppJs;
minEntryPoints['assets/app/js/tourfic-scripts.min'] = freeAppJs;
entryPoints['assets/admin/js/tourfic-admin-scripts'] = freeAdminJs;
minEntryPoints['assets/admin/js/tourfic-admin-scripts.min'] = freeAdminJs;
entryPoints['assets/admin/js/tourfic-admin-api'] = glob.sync('./sass/admin/js/free/tf-api-doc.js');
minEntryPoints['assets/admin/js/tourfic-admin-api.min'] = glob.sync('./sass/admin/js/free/tf-api-doc.js');

// SASS entry points
const appScss = glob.sync('./sass/app/css/free/tourfic.scss');
const adminScss = glob.sync('./sass/admin/css/free/tourfic-admin.scss');
const CarAppScss = glob.sync('./sass/app/css/free/car/car.scss');
const ApartmentAppScss = glob.sync('./sass/app/css/free/apartment/apartment.scss');
const TourAppScss = glob.sync('./sass/app/css/free/tour/tour.scss');
const HotelAppScss = glob.sync('./sass/app/css/free/hotel/hotel.scss');
const RoomAppScss = glob.sync('./sass/app/css/free/room/room.scss');

StyleEntryPoints['assets/app/css/tourfic-style'] = appScss;
StyleEntryPoints['assets/admin/css/tourfic-admin'] = adminScss;
StyleEntryPoints['assets/admin/css/tourfic-admin-dashboard'] = glob.sync('./sass/admin/css/free/tourfic-dashboard.scss');
StyleEntryPoints['assets/admin/css/tourfic-admin-api'] = glob.sync('./sass/admin/css/free/tourfic-api.scss');
StyleEntryPoints['assets/app/css/tourfic-carrentals'] = CarAppScss;
StyleEntryPoints['assets/app/css/tourfic-apartment'] = ApartmentAppScss;
StyleEntryPoints['assets/app/css/tourfic-tour'] = TourAppScss;
StyleEntryPoints['assets/app/css/tourfic-hotel'] = HotelAppScss;
StyleEntryPoints['assets/app/css/tourfic-room'] = RoomAppScss;


const JSconfig = {
    entry: entryPoints,
    mode: 'development',
    output: {
        path: path.resolve(__dirname),
        filename: '[name].js',
        clean: false
    },
    optimization: {
        minimize: false
    }
}

const JSminConfig = {
    entry: minEntryPoints,
    mode: 'production',
    output: {
        path: path.resolve(__dirname),
        filename: '[name].js',
        clean: false
    },
}

const styleMinConfig = {
    entry: StyleEntryPoints,
    devtool: 'source-map',
    performance: {
        hints: false,
        maxEntrypointSize: 500,
        maxAssetSize: 500
    },

    output: {
        path: path.resolve(__dirname),
        filename: '[name].css.js',
        clean: false
    },

    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    'style-loader',
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            esModule: false,
                        },

                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            sourceMap: true
                        },
                    },           
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            sassOptions: {
                                outputStyle: "compressed",
                              },
                        },
                    },         
                ],
            },
        ],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].min.css',
        })
    ]
}
const StyleConfig = {
    entry: StyleEntryPoints,
    devtool: 'source-map',
    performance: {
        maxEntrypointSize: 500,
        maxAssetSize: 500
    },

    output: {
        path: path.resolve(__dirname),
        filename: '[name].css.js',
        clean: false
    },

    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    'style-loader',
                    {
                        loader: MiniCssExtractPlugin.loader, 
                        options: {
                            esModule: false,
                        },
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            sourceMap: true
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            sassOptions: {
                                outputStyle: "expanded",
                              },
                        },
                    },         
                ],
            },
        ],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].css',
        })
    ]
}

// Export the config object.
module.exports = [JSconfig, JSminConfig, StyleConfig, styleMinConfig];

const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Entrypoints Object
const entryPoints = {};
const minEntryPoints = {};
const StyleEntryPoints = {};

const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');
const vendorAdminJs = glob.sync('./sass/admin/js/addon/vendor/*.js');
const vendorAppJs = glob.sync('./sass/app/js/addon/vendor/*.js');
const tfepAdminJs = glob.sync('./sass/admin/js/addon/tfepiping/*.js');
const tfepAppJs = glob.sync('./sass/app/js/addon/tfepiping/*.js');
const iCalAdminJs = glob.sync('./sass/admin/js/addon/ical/*.js');
const proAppJs = glob.sync('./sass/app/js/pro/*.js');
const proAdminJs = [
    './sass/admin/js/pro/locationpicker.jquery.js',
    './sass/admin/js/pro/locationpicker-custom.js',
    './sass/admin/js/pro/template-builder.js',
    './sass/admin/js/pro/admin.js'
];

//tourfic free
entryPoints['tourfic/assets/app/js/tourfic-scripts'] = freeAppJs;
minEntryPoints['tourfic/assets/app/js/tourfic-scripts.min'] = freeAppJs;
entryPoints['tourfic/assets/admin/js/tourfic-admin-scripts'] = freeAdminJs;
minEntryPoints['tourfic/assets/admin/js/tourfic-admin-scripts.min'] = freeAdminJs;
//tourfic pro
entryPoints['tourfic-pro/assets/app/js/tourfic-pro'] = proAppJs;
minEntryPoints['tourfic-pro/assets/app/js/tourfic-pro.min'] = proAppJs;
entryPoints['tourfic-pro/assets/admin/js/tourfic-pro-admin'] = proAdminJs;
minEntryPoints['tourfic-pro/assets/admin/js/tourfic-pro-admin.min'] = proAdminJs;

//tourfic vendor addon
entryPoints['tourfic-vendor/admin/assets/js/tourfic-vendor-scripts'] = vendorAdminJs;
minEntryPoints['tourfic-vendor/admin/assets/js/tourfic-vendor-scripts.min'] = vendorAdminJs;
entryPoints['tourfic-vendor/public/assets/js/tourfic-vendor'] = vendorAppJs;
minEntryPoints['tourfic-vendor/public/assets/js/tourfic-vendor.min'] = vendorAppJs;

//tourfic email-piping addon
entryPoints['tourfic-email-piping/assets/admin/js/tourfic-email-piping-scripts'] = tfepAdminJs;
minEntryPoints['tourfic-email-piping/assets/admin/js/tourfic-email-piping-scripts.min'] = tfepAdminJs;
entryPoints['tourfic-email-piping/assets/app/js/tourfic-email-piping-scripts'] = tfepAppJs;
minEntryPoints['tourfic-email-piping/assets/app/js/tourfic-email-piping-scripts.min'] = tfepAppJs;

//tourfic ical addon
entryPoints['tourfic-ical/assets/admin/js/tourfic-ical'] = iCalAdminJs;
minEntryPoints['tourfic-ical/assets/admin/js/tourfic-ical.min'] = iCalAdminJs;

// SASS entry points
const appScss = glob.sync('./sass/app/css/free/tourfic.scss');
const proAppScss = glob.sync('./sass/app/css/pro/tourfic-pro.scss');
const adminScss = glob.sync('./sass/admin/css/free/tourfic-admin.scss');
const proAdminScss = glob.sync('./sass/admin/css/pro/tourfic-pro-admin.scss');
const addonAdminScss = glob.sync('./sass/admin/css/addon/tourfic-addon/tourfic-vendor.scss'); 
const addonAppScss = glob.sync('./sass/app/css/addon/tourfic-vendor.scss'); 
const tfepAdminScss = glob.sync('./sass/admin/css/addon/tourfic-addon/tourfic-email-piping.scss'); 
const tfepAppScss = glob.sync('./sass/app/css/addon/tourfic-email-piping.scss'); 
const CarAppScss = glob.sync('./sass/app/css/free/car/car.scss');
const ApartmentAppScss = glob.sync('./sass/app/css/free/apartment/apartment.scss');
const TourAppScss = glob.sync('./sass/app/css/free/tour/tour.scss');
const HotelAppScss = glob.sync('./sass/app/css/free/hotel/hotel.scss');

StyleEntryPoints['tourfic/assets/app/css/tourfic-style'] = appScss;
StyleEntryPoints['tourfic-pro/assets/app/css/tourfic-pro'] = proAppScss;
StyleEntryPoints['tourfic/assets/admin/css/tourfic-admin'] = adminScss;
StyleEntryPoints['tourfic-pro/assets/admin/css/tourfic-pro-admin'] = proAdminScss;
StyleEntryPoints['/tourfic-vendor/admin/assets/css/tourfic-vendor'] = addonAdminScss;
StyleEntryPoints['/tourfic-vendor/public/assets/css/tourfic-vendor'] = addonAppScss;
StyleEntryPoints['/tourfic-email-piping/assets/admin/css/tourfic-email-piping'] = tfepAdminScss;
StyleEntryPoints['/tourfic-email-piping/assets/app/css/tourfic-email-piping'] = tfepAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-carrentals'] = CarAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-apartment'] = ApartmentAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-tour'] = TourAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-hotel'] = HotelAppScss;


const JSconfig = {
    entry: entryPoints,
    mode: 'development',
    output: {
        path: path.resolve(__dirname, '../'),
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
        path: path.resolve(__dirname, '../'),
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
        path: path.resolve(__dirname, '../'),
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
        path: path.resolve(__dirname, '../'),
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
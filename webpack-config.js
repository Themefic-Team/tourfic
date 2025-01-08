const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// Entrypoints Object
const entryPoints = {};
const StyleEntryPoints = {};

const freeAppJs = glob.sync('./sass-modify/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass-modify/admin/js/free/*.js');
const vendorAdminJs = glob.sync('./sass-modify/admin/js/addon/vendor/*.js');
const vendorAppJs = glob.sync('./sass-modify/app/js/addon/vendor/*.js');
const tfepAdminJs = glob.sync('./sass-modify/admin/js/addon/tfepiping/*.js');
const tfepAppJs = glob.sync('./sass-modify/app/js/addon/tfepiping/*.js');
const iCalAdminJs = glob.sync('./sass-modify/admin/js/addon/ical/*.js');
const proAppJs = glob.sync('./sass-modify/app/js/pro/*.js');
const proAdminJs = ['./sass-modify/admin/js/pro/locationpicker.jquery.js','./sass-modify/admin/js/pro/locationpicker-custom.js','./sass-modify/admin/js/pro/admin.js'];

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

//tourfic vendor addon
entryPoints['tourfic-email-piping/assets/admin/js/tourfic-email-piping-scripts.min'] = tfepAdminJs;
entryPoints['tourfic-email-piping/assets/app/js/tourfic-email-piping-scripts.min'] = tfepAppJs;

//tourfic ical addon
entryPoints['tourfic-ical/assets/admin/js/tourfic-ical.min'] = iCalAdminJs;

// SASS entry points
const appScss = glob.sync('./sass-modify/app/css/free/tourfic.scss');
const proAppScss = glob.sync('./sass-modify/app/css/pro/tourfic-pro.scss');
const adminScss = glob.sync('./sass-modify/admin/css/free/tourfic-admin.scss');
const proAdminScss = glob.sync('./sass-modify/admin/css/pro/tourfic-pro-admin.scss');
const addonAdminScss = glob.sync('./sass-modify/admin/css/addon/tourfic-addon/tourfic-vendor.scss'); 
const addonAppScss = glob.sync('./sass-modify/app/css/addon/tourfic-vendor.scss'); 
const tfepAdminScss = glob.sync('./sass-modify/admin/css/addon/tourfic-addon/tourfic-email-piping.scss'); 
const tfepAppScss = glob.sync('./sass-modify/app/css/addon/tourfic-email-piping.scss'); 
const CarAppScss = glob.sync('./sass-modify/app/css/free/car/car.scss');
const ApartmentAppScss = glob.sync('./sass-modify/app/css/free/apartment/apartment.scss');

StyleEntryPoints['tourfic/assets/app/css/tourfic-style'] = appScss;
StyleEntryPoints['tourfic-pro/assets/app/css/tourfic-pro'] = proAppScss;
StyleEntryPoints['tourfic/assets/admin/css/tourfic-admin'] = adminScss;
StyleEntryPoints['tourfic-pro/assets/admin/css/tourfic-pro-admin'] = proAdminScss;
StyleEntryPoints['/tourfic-vendor/admin/assets/css/tourfic-vendor'] = addonAdminScss;
StyleEntryPoints['/tourfic-vendor/public/assets/css/tourfic-vendor'] = addonAppScss;
StyleEntryPoints['/tourfic-email-piping/assets/admin/css/tourfic-email-piping'] = tfepAdminScss;
StyleEntryPoints['/tourfic-email-piping/assets/app/css/tourfic-email-piping'] = tfepAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-car'] = CarAppScss;
StyleEntryPoints['tourfic/assets/app/css/tourfic-apartment'] = ApartmentAppScss;


const JSconfig = {
    entry: entryPoints,

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
module.exports = [JSconfig, StyleConfig, styleMinConfig];
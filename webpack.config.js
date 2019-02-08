module.exports = [

    {
        entry: {
            "node-theme": "./app/js/node-theme.js",
            "widget-theme": "./app/js/widget-theme.js",
            "settings-theme": "./app/js/settings-theme.js",
            "settings-theme-kit": "./app/js/settings-theme-kit.js",
        },

        output: {
            filename: "./app/bundle/[name].js"
        },

        module: {
            loaders: [
                {
                    test: /\.vue$/,
                    loader: "vue"
                },
                {
                    test: /\.html$/,
                    loader: 'vue-html'
                },
                {
                    test: /\.js$/,
                    loader: 'babel',
                    query: { presets: ['es2015',], },
                },
            ]
        }
    }

];
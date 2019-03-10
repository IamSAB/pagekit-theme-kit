module.exports = [

    {
        entry: {
            "node-theme": "./app/js/node-theme.js",
            "widget-theme": "./app/js/widget-theme.js",
            "settings-theme-node": "./app/js/settings-theme-node.js",
            "settings-theme-widget": "./app/js/settings-theme-widget.js",
            "settings-theme-fieldsets": "./app/js/settings-theme-fieldsets.js",
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

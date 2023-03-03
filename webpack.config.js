let webpack = require("webpack")
let path = require("path")

module.exports = {

    entry: "./resources/assets/js/app.js",

    output:{

        path:path.resolve(__dirname, 'public/assets/js'),
        
        filename:"app.js"

    },
    resolve: {
        alias: {
          'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
        }
    },
    module: {

        rules: [

        {    
            test: /\.vue$/,
            use: [
              'vue-style-loader',
              'css-loader',
              {
                loader: 'sass-loader',
                // Requires sass-loader@^7.0.0
                options: {
                  implementation: require('sass'),
                  fiber: require('fibers'),
                  indentedSyntax: true // optional
                },
                // Requires sass-loader@^8.0.0
                options: {
                  implementation: require('sass'),
                  sassOptions: {
                    fiber: require('fibers'),
                    indentedSyntax: true // optional
                  },
                },
              },
            ],
            loader: 'vue-loader',
            options: {
            loaders: {
                // {{#sass}}
                // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                // the "scss" and "sass" values for the lang attribute to the right configs here.
                // other preprocessors should work out of the box, no loader config like this necessary.
                'scss': [
                'vue-style-loader',
                'css-loader',
                'sass-loader'
                ],
                'sass': [
                'vue-style-loader',
                'css-loader',
                'sass-loader?indentedSyntax'
                ]
                // {{/sass}}
            }
            // other vue-loader options go here
            }
        },
        // {
        //     test: /\.jsx?$/,
        //     exclude: /node_modules/,
        //     loader: "babel-loader",
        //     query: {
        //         presets: ["vue"]
        //     }
        // }

        ]

    }


}
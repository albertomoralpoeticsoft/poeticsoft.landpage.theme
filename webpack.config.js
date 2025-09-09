const path = require('path')

const MiniCssExtractPlugin = require('mini-css-extract-plugin')

const themename = 'poeticsoft-landpage-theme'
const destdir = path.join(__dirname, themename)
const themeplublic = '/wp-content/themes/' + themename

module.exports = env => {  
                                                    
  const input = Object.keys(env)[2] || ''

  const params = input.split('-')
  const type = params[0] || 'apps' // apps | theme

  let unit, mode
  let paths = {
    public: themeplublic,
    cssfilename: 'main.css'
  }

  switch(type) {

    case 'mailmessage': 

      mode = params[1] || 'dev' // dev | prod
      
      paths.entryjs = './src/' + type + '/main.js',
      paths.output = destdir  + '/api'

      break

    default:

      mode = params[1] || 'dev' // dev | prod
      
      paths.entryjs = './src/' + type + '/main.js',
      paths.output = destdir  + '/' + type

      break
  }  

  return {
    context: __dirname,
    stats: 'minimal',
    watch: true,
    name: 'minimal',
    entry: {
      main: paths.entryjs
    },
    output: {
      path: paths.output,
      publicPath: paths.public
    },
    mode: mode == 'prod' ? 'production' : 'development',
    devtool: mode == 'prod' ? false : 'source-map',
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          use: [          
            { 
              loader: 'babel-loader',
              options: {
                presets: [
                  '@babel/preset-env',
                  '@babel/preset-react'
                ]
              }
            }
          ]
        },
        {
          test: /\.s[ac]ss$/i,
          exclude: /node_modules/,
          use: [
            { 
              loader: MiniCssExtractPlugin.loader
            },
            {
              loader: 'css-loader'
            },
            {
              loader: 'sass-loader'
            }
          ]
        },
        {
          test: /\.css$/,
          include: /node_modules/,
          use: [
            { 
              loader: MiniCssExtractPlugin.loader
            },
            'style-loader',
            'css-loader'
          ]
        },
        // Assets
        {
          test: /\.(jpg|jpeg|png|gif|svg|woff|ttf|eot|mp3|woff|woff2|webm|mp4)$/,
          type: 'asset/resource',
          generator: {
            emit: false,
            filename: content => { 

              return content.filename.replace(themename, '')
            }
          }
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: paths.cssfilename
      })
    ],
    resolve: {
      extensions: ['.js'],
      alias: {
        assets: path.join(destdir, 'assets'),
        common: path.join(__dirname, 'src/common'),
      }
    }
  }
}
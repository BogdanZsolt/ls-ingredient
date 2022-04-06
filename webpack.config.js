const currentTask = process.env.npm_lifecycle_event;
const path = require("path");
const postcssPresetEnv = require("postcss-preset-env");

let imagesConfig = {
  test: /\.(jpg|png|jpeg)$/,
  type: "asset/resource",
  generator: {
    filename: "images/[name][ext]",
  },
};

let fontsConfig = {
  test: /\.(woff|woff2|eot|ttf|otf)$/,
  type: "asset/resource",
  generator: {
    filename: "fonts/[name][ext]",
  },
};

let svgConfig = {
  test: /\.svg$/,
  type: "asset/resource",
  generator: {
    filename: "icons/[name][ext]",
  },
};

let cssConfig = {
  test: /\.css$/,
  use: [
    {
      loader: "file-loader",
      options: {
        name: "css/[name].css",
      },
    },
    "extract-loader",
    "css-loader",
    {
      loader: "postcss-loader",
      options: {
        postcssOptions: {
          plugins: [
            postcssPresetEnv({
              browsers: "last 2 versions",
            }),
          ],
        },
      },
    },
  ],
};

let sassConfig = {
  test: /\.(s[ac]|c)ss$/,
  use: [
    {
      loader: "file-loader",
      options: {
        name: "css/[name].css",
      },
    },
    "extract-loader",
    "css-loader",
    {
      loader: "postcss-loader",
      options: {
        postcssOptions: {
          plugins: [
            postcssPresetEnv({
              browsers: "last 2 versions",
            }),
          ],
        },
      },
    },
    "sass-loader",
  ],
};

let config = {
  entry: {
    // index: path.resolve(__dirname, "src/index.js"),
    // admin: path.resolve(__dirname, "src/admin.scss"),
    index: ["./src/index.js", "./src/index.scss", "./src/admin.scss"],
  },
  output: {
    path: path.resolve(__dirname, "./assets"),
    filename: "[name].js",
    clean: true,
  },
  module: {
    rules: [cssConfig, sassConfig, imagesConfig, fontsConfig, svgConfig],
  },
  plugins: [],
};

if (currentTask === "dev") {
  (config.mode = "development"),
    (config.devtool = "source-map"),
    (config.watch = true),
    (config.watchOptions = {
      ignored: /node_modules/,
    });
}

if (currentTask === "build") {
  (config.mode = "production"),
    config.module.rules.push({
      test: /\.js$/,
      exclude: /node_modules/,
      use: {
        loader: "babel-loader",
        options: {
          presets: ["@babel/preset-env"],
          plugins: [
            "@babel/plugin-proposal-class-properties",
            "@babel/transform-runtime",
          ],
        },
      },
    });
}

module.exports = config;

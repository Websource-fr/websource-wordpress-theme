/**
 * Map “page type -> CSS sources + templates”.
 *
 * `base` produces assets/css/theme.css (loaded everywhere): tokens, reset, layout, header, footer, buttons, fields,
 * notices, pagination, editorial content.
 * Component CSS is unlayered (it must win over core and plugin CSS); only the reset and utilities are layered.
 * Each entry of `pages` produces assets/css/pages/<name>.css, loaded only on that page type
 * (see Websource\Theme\Assets::pageType()).
 *  - css       : files of assets/src/ compiled into the file (component layer).
 *  - templates : PHP files or folders scanned by Tailwind for utility classes.
 * Anything already present in theme.css is removed from the page files (see build-css.mjs).
 */

export const base = {
  css: ['base.css', 'layout.css', 'header.css', 'footer.css', 'prose.css', 'wp-blocks.css'],
  templates: ['header.php', 'footer.php', 'searchform.php', 'template-parts/header.php', 'template-parts/footer.php', 'src'],
};

export const pages = {
  home: {
    css: ['product-card.css', 'post-card.css', 'pages/home.css'],
    templates: ['front-page.php', 'template-parts/home', 'template-parts/post-card.php', 'woocommerce/content-product.php'],
  },
  shop: {
    css: ['product-card.css', 'pages/shop.css'],
    templates: ['woocommerce/content-product.php', 'template-parts/shop-sidebar.php'],
  },
  product: {
    css: ['product-card.css', 'pages/product.css'],
    templates: ['woocommerce/content-single-product.php', 'woocommerce/single-product', 'woocommerce/content-product.php'],
  },
  cart: {
    css: ['product-card.css', 'wc-tables.css', 'wc-forms.css', 'wc-vendor.css', 'wc-blocks.css', 'pages/cart.css'],
    templates: ['page.php', 'woocommerce/cart', 'woocommerce/content-product.php'],
  },
  checkout: {
    css: ['wc-tables.css', 'wc-forms.css', 'wc-vendor.css', 'wc-blocks.css', 'pages/checkout.css'],
    templates: ['page.php', 'woocommerce/checkout'],
  },
  account: {
    css: ['wc-tables.css', 'wc-forms.css', 'wc-vendor.css', 'pages/account.css'],
    templates: ['page.php', 'woocommerce/myaccount'],
  },
  post: {
    css: ['pages/post.css'],
    templates: ['single.php', 'comments.php'],
  },
  blog: {
    css: ['post-card.css', 'pages/blog.css'],
    templates: ['index.php', 'template-parts/post-card.php'],
  },
  page: {
    css: ['pages/page.css', 'pages/post.css'],
    templates: ['page.php', 'comments.php'],
  },
  search: {
    css: ['post-card.css', 'pages/search.css'],
    templates: ['search.php', 'template-parts/post-card.php'],
  },
  404: {
    css: ['pages/404.css'],
    templates: ['404.php'],
  },
};

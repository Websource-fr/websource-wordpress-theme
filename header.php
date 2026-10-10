<?php
/**
 * Document head and site header.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-surface focus:p-3"><?php esc_html_e('Skip to content', 'websource'); ?></a>
<div id="page" class="wsx-site">
<header id="header" class="bg-page">
<?php get_template_part('template-parts/header'); ?>
</header>
<main id="wrapper">
<?php
websource_theme_area('wrapper-top', '<div class="wsx-wrapper-top">', '</div>');

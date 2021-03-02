<?php
// Добавление расширенных возможностей
if ( ! function_exists( 'universal_theme_setup' ) ) :
    // Добавление тега Title
    function universal_theme_setup() {
        add_theme_support( 'title-tag' );
    //Добавление пользовательского логотипа
    add_theme_support( 'custom-logo', [
        'width'       => 163,
        'flex-height' => true,
        'header-text' => 'Universal',
        'unlink-homepage-logo' => false, // WP 5.5
    ] );
    //Регистрация меню
    
        register_nav_menus( [
            'header_menu' => 'Меню в шапке',
            'footer_menu' => 'Меню в подвале'
        ] );
    
    }
endif;
add_action( 'after_setup_theme', 'universal_theme_setup' );
// правильный способ подключить стили и скрипты
function enqueue_universal_style() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style( 'universal-theme', get_template_directory_uri() . '/assets/css/universal-theme.css', 'style',null,null);
}

add_action( 'wp_enqueue_scripts', 'enqueue_universal_style' );
<?php
// Добавление расширенных возможностей
if ( ! function_exists( 'universal_theme_setup' ) ) :
    // Добавление тега Title
    function universal_theme_setup() {
        add_theme_support( 'title-tag' );
    // Добавление миниатюр
    add_theme_support( 'post-thumbnails', array( 'post' ) );  
      
    //Добавление пользовательского логотипа
    add_theme_support( 'custom-logo', [
        'width'       => 163,
        'flex-height' => true,
        'header-text' => 'Universal',
        'unfacebook-homepage-logo' => false, // WP 5.5
    ] );
    //Регистрация меню
    
        register_nav_menus( [
            'header_menu' => 'Меню в шапке',
            'footer_menu' => 'Меню в подвале'
        ] );
    
    }
endif;
add_action( 'after_setup_theme', 'universal_theme_setup' );

/**
 * Подключение сайдбара
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function universal_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Сайдбар на главной', 'study-theme' ),
			'id'            => 'main-sidebar',
			'description'   => esc_html__( 'Добавьте виджеты сюда', 'study-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'universal_theme_widgets_init' );


/**
 * Добавление нового виджета Downloader_Widget.
 */
class Downloader_Widget extends WP_Widget {

	// Регистрация виджета используя основной класс
	function __construct() {
		// вызов конструктора выглядит так:
		// __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
		parent::__construct(
			'downloader_widget', // ID виджета, если не указать (оставить ''), то ID будет равен названию класса в нижнем регистре: downloader_widget
			'Полезные файлы',
			array( 'description' => 'Файлы для скачивания', 'classname' => 'widget-downloader', )
		);

		// скрипты/стили виджета, только если он активен
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action('wp_enqueue_scripts', array( $this, 'add_downloader_widget_scripts' ));
			add_action('wp_head', array( $this, 'add_downloader_widget_style' ) );
		}
	}

	/**
	 * Вывод виджета во Фронт-энде
	 *
	 * @param array $args     аргументы виджета.
	 * @param array $instance сохраненные данные из настроек
	 */
	function widget( $args, $instance ) {
		$title = $instance['title'];
        $description = $instance['description'];
        $link = $instance['link'];

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
        if ( ! empty( $description ) ) {
			echo '<p>' . $description . '</p>';
		}
        if ( ! empty( $link ) ) {
			echo '<a target="_blank" class="widget-link" href=">' . $link . '">
            <img class="widget-link-icon" src="' . get_template_directory_uri ( ).'/assets/images/download.svg">
            Скачать</a>';
		}
		
		echo $args['after_widget'];
	}

	/**
	 * Админ-часть виджета
	 *
	 * @param array $instance сохраненные данные из настроек
	 */
	function form( $instance ) {
		$title = @ $instance['title'] ?: 'Полезные файлы';
        $description = @ $instance['description'] ?: 'Описание';
        $link = @ $instance['link'] ?: 'Ссылка';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?= ( 'Описание:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" value="<?php echo esc_attr( $description ); ?>">
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'http://' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
		</p>
		<?php 
	}

	/**
	 * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance новые настройки
	 * @param array $old_instance предыдущие настройки
	 *
	 * @return array данные которые будут сохранены
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';

        return $instance;
	}

	// скрипт виджета
	function add_downloader_widget_scripts() {
		// фильтр чтобы можно было отключить скрипты
		if( ! apply_filters( 'show_my_widget_script', true, $this->id_base ) )
			return;

		$theme_url = get_stylesheet_directory_uri();

		wp_enqueue_script('my_widget_script', $theme_url .'/my_widget_script.js' );
	}

	// стили виджета
	function add_downloader_widget_style() {
		// фильтр чтобы можно было отключить стили
		if( ! apply_filters( 'show_my_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">
			.my_widget a{ display:inline; }
		</style>
		<?php
	}

} 
// конец класса Downloader_Widget

// регистрация Downloader_Widget в WordPress
function register_downloader_widget() {
	register_widget( 'Downloader_Widget' );
}
add_action( 'widgets_init', 'register_downloader_widget' );

// правильный способ подключить стили и скрипты
function enqueue_universal_style() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style( 'universal-theme', get_template_directory_uri() . '/assets/css/universal-theme.css', 'style',null,null);
    wp_enqueue_style( 'Roboto-slab','https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap');
    
}

add_action( 'wp_enqueue_scripts', 'enqueue_universal_style' );

add_filter('widget_tag_cloud_args', 'edit_widget_tag_cloud_args');
function edit_widget_tag_cloud_args ($args) {
    $args['unit'] = 'px';
    $args['smallest'] = '14';
    $args['largest'] = '14';
    $args['number'] = '13';
    return $args;
}

## отключаем создание миниатюр файлов для указанных размеров
add_filter( 'intermediate_image_sizes', 'delete_intermediate_image_sizes' );
function delete_intermediate_image_sizes( $sizes ){
	// размеры которые нужно удалить
	return array_diff( $sizes, [
		'medium_large',
		'large',
		'1536x1536',
		'2048x2048',
	] );
}

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'homepage-thumb', 65, 65, true ); // Кадрирование изображения
    add_image_size( 'article-thumb', 336, 195, true ); // Кадрирование изображения
}


// social widget

/**
 * Добавление нового виджета Social_widget.
 */
class Social_widget extends WP_Widget {

	// Регистрация виджета используя основной класс
	function __construct() {
		// вызов конструктора выглядит так:
		// __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
		parent::__construct(
			'social_widget', // ID виджета, если не указать (оставить ''), то ID будет равен названию класса в нижнем регистре: social_widget
			'Социальный виджет',
			array( 'description' => 'Социальные ссылки', 'classname' => 'social_widget', )
		);

		// скрипты/стили виджета, только если он активен
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action('wp_enqueue_scripts', array( $this, 'add_social_widget_scripts' ));
			add_action('wp_head', array( $this, 'add_social_widget_style' ) );
		}
	}

	/**
	 * Вывод виджета во Фронт-энде
	 *
	 * @param array $args     аргументы виджета.
	 * @param array $instance сохраненные данные из настроек
	 */
	function widget($args, $instance) {
		/* ... */


		extract( $args );
		// these are the widget options
		//$title = apply_filters('widget_title', $instance['title']);

		
		$facebook = $instance['facebook'];
		$youtube = $instance['youtube'];
		$wordpress = $instance['wordpress'];
		$twitter = $instance['twitter'];

		echo $before_widget;
		// Вывод виджета
		
		echo '<center><p class="widget-title">Наши соцсети</p></center>';
		echo '<center><div>';

		// // if ( $title ) {
		//   echo $before_title . $title . $after_title;
		// }
		

		
		if ( ! empty( $facebook ) ) {
			echo '<a target="_blank" class="widget-social" href="'.$facebook.'">
            <img class="widget-svg" src="' . get_template_directory_uri ( ).'/assets/images/facebook.svg"></a>';
		}

		
		if ( ! empty( $youtube ) ) {
			echo '<a target="_blank" class="widget-social" href="'.$youtube.'">
            <img class="widget-svg" src="' . get_template_directory_uri ( ).'/assets/images/youtube.svg"></a>';
		}
		
		
		if ( ! empty( $twitter ) ) {
			echo '<a target="_blank" class="widget-social" href="'.$twitter.'">
            <img class="widget-svg" src="' . get_template_directory_uri ( ).'/assets/images/twitter.svg"></a>';
		}

		
		if ( ! empty( $wordpress ) ) {
			echo '<a target="_blank" class="widget-social" href="'.$wordpress.' ">
            <img class="widget-svg" src="' . get_template_directory_uri ( ).'/assets/images/wordpress.svg"></a>';
		}
				
		echo '</div>';
		echo $after_widget;
	}

	/**
	 * Админ-часть виджета
	 *
	 * @param array $instance сохраненные данные из настроек
	 */
	function form( $instance ) {
		/* ... */
		
		// Check values
		if( $instance) {
			
			$facebook = esc_attr($instance['facebook']);
			$youtube = esc_attr($instance['youtube']);
			$wordpress = esc_attr($instance['wordpress']);
			$twitter = esc_attr($instance['twitter']);
	   } else {
			
			$facebook = '';
			$youtube = '';
			$wordpress = '';
			$twitter = '';
	   }
	   ?>
		

		<!-- facebook -->
		<p>
		<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo $facebook; ?>" />
		</p>

		<!-- instagram -->
		<p>
		<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('youtube', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo $youtube; ?>" />
		</p>

		<!-- wordpress -->
		<p>
		<label for="<?php echo $this->get_field_id('wordpress'); ?>"><?php _e('wordpress', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('wordpress'); ?>" name="<?php echo $this->get_field_name('wordpress'); ?>" type="text" value="<?php echo $wordpress; ?>" />
		</p>

		<!-- twitter -->
		<p>
		<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo $twitter; ?>" />
		</p>
		<?php 
	}

	/**
	 * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance новые настройки
	 * @param array $old_instance предыдущие настройки
	 *
	 * @return array данные которые будут сохранены
	 */
	function update($new_instance, $old_instance) {
		/* ... */
		$instance = $old_instance;
		// Fields
		
		$instance['facebook'] = strip_tags($new_instance['facebook']);
		$instance['youtube'] = strip_tags($new_instance['youtube']);
		$instance['wordpress'] = strip_tags($new_instance['wordpress']);
		$instance['twitter'] = strip_tags($new_instance['twitter']);
		return $instance;
	}

	// скрипт виджета
	function add_social_widget_scripts() {
		// фильтр чтобы можно было отключить скрипты
		if( ! apply_filters( 'show_my_widget_script', true, $this->id_base ) )
			return;

		$theme_url = get_stylesheet_directory_uri();

		wp_enqueue_script('my_widget_script', $theme_url .'/my_widget_script.js' );
	}

	// стили виджета
	function add_social_widget_style() {
		// фильтр чтобы можно было отключить стили
		if( ! apply_filters( 'show_my_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">
			.my_widget a{ display:inline; }
		</style>
		<?php
	}

} 
// конец класса Social_widget

// регистрация Social_widget в WordPress
function register_social_widget() {
	register_widget( 'Social_widget' );
}
add_action( 'widgets_init', 'register_social_widget' );

function register_my_widgets(){
	register_sidebar( array(
		'name' => "Еще один сайдбар",
		'id' => 'onemore-sidebar',
		'description' => 'Еще один сайдбар',
		'before_title' => '<h2>',
		'after_title' => '</h2>'
	) );
}
add_action( 'widgets_init', 'register_my_widgets' );

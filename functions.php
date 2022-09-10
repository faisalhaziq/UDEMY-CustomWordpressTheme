<?php
require get_theme_file_path('/inc/search-route.php');

//Function to add Custom Fields for REST Api
function uni_custom_rest(){
    register_rest_field('post','authorName', array(
        'get_callback' => function(){
            return get_the_author();
        }
    ));
}

add_action('rest_api_init', 'uni_custom_rest');

//Function to duplicate pageBanner on different pages
function pageBanner($args = NULL){
    if(!isset($args['title'])){
        $args['title'] = get_the_title();
    }
    
    if(!isset($args['subtitle'])){
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if(!isset($args['photo'])){
        if (get_field('page_banner_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_image')['sizes']['pageBanner'];
        }else{
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'];?></p>
            </div>
        </div>
    </div>
    <?php
}

function uni_files(){
    wp_enqueue_script('main-uni-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('uni_css', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('uniextra_css', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-uni-js', 'unidata', array(
        'root_url' => get_site_url()
    ));
}

add_action('wp_enqueue_scripts','uni_files');

function uni_features(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('profLandscape', 400, 260, true);
    add_image_size('profPortrait', 480, 600, true);
    add_image_size('pageBanner', 1500, 350, true);
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLoc1', 'Footer Menu Location 1');
    register_nav_menu('footerLoc2', 'Footer Menu Location 2');
}

add_action('after_setup_theme','uni_features');

function uni_adjust_queries($query){
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric',
            )
            ));
    }

    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
        $today = date('Ymd');
        $query->set('posts_per_page', -1);
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }
}

add_action('pre_get_posts','uni_adjust_queries');

?>
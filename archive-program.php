<?php 
    get_header();
    pageBanner(array(
        'title' => 'All Programs',
        'subtitle' => 'There is something for everyone. Look around.'
    ));
   ?>
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo site_url('/programs'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Programs Home</a>
            </p>
        </div>
        <ul class="link-list min-list">
        <?php
        while(have_posts()){
        the_post();?>
        <li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
        <?php
        }
        echo paginate_links();
        ?>
        </ul>
    </div>
   <?php
    get_footer();
?>
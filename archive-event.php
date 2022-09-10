<?php 
    get_header();
    pageBanner(array(
      'title' => 'All Events',
      'subtitle' => 'See what is going on in the world'
    ));
   ?>
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo site_url('/events'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a>
            </p>
        </div>
        <?php
        while(have_posts()){
        the_post();
        get_template_part('template-parts/event');
        }
        echo paginate_links();
        ?>
        
        <p>Looking for a recap of past events? <a href="<?php echo site_url('/past-events');?>">Check out our past events archives.</a></p>
    </div>
   <?php
    get_footer();
?>
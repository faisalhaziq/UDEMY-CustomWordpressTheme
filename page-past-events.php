<?php
get_header();

pageBanner(array(
    'title' => 'Past Events',
    'subtitle' => 'Recap of Past Events.'
));
?>
<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link" href="<?php echo site_url('/events'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a>
        </p>
    </div>
    <?php
    $today = date('Ymd');
    $pastEvents = new WP_Query(array(
        'paged' => get_query_var('paged', 1),
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'compare' => '<',
                'value' => $today,
                'type' => 'numeric',
            )
        )
    ));
    while ($pastEvents->have_posts()) {
        $pastEvents->the_post();
        get_template_part('template-parts/event');
    }
    echo paginate_links(array(
        'total' => $pastEvents->max_num_pages,
    ));
    ?>
</div>
<?php
get_footer();
?>
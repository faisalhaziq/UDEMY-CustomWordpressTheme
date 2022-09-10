<?php

add_action('rest_api_init', 'uniRegSearch');

function uniRegSearch(){
    register_rest_route('university', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'uniSearchResults'
    ));
}

function uniSearchResults($data){
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'event', 'program'),
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'generalInfo' => array(

        ),
        'professors' => array(

        ),
        'programs' => array(

        ),
        'events' => array(

        )
    );

    while($mainQuery->have_posts()){
        $mainQuery->the_post();
        if(get_post_type() == 'post' OR get_post_type() == 'page'){
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'type' => get_post_type(),
                'author' => get_the_author()
            ));
        }

        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'profLandscape')
            ));
        }

        if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));

            $desc = null;

            if(has_excerpt()){
                $desc = get_the_excerpt();
            }else {
                $desc = wp_trim_words(get_the_content(), 18);
            }
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'desc' => $desc
            ));
        }

        if(get_post_type() == 'program'){
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID()
            ));
        }
    }

    if($results['programs']){
        $programsMetaQuery = array(
            'relation' => 'OR'
        );
    
        foreach($results['programs'] as $item){
            array_push($programsMetaQuery, array(
                array(
                    'key' => 'related_program',
                    'compare' => 'LIKE',
                    'value' => '"'.$item['id'].'"'
                )
            ));
        }
    
        $programRelated = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery
        ));
    
        while($programRelated->have_posts()){
            $programRelated->the_post();
            
            if(get_post_type() == 'event'){
                $eventDate = new DateTime(get_field('event_date'));
    
                $desc = null;
    
                if(has_excerpt()){
                    $desc = get_the_excerpt();
                }else {
                    $desc = wp_trim_words(get_the_content(), 18);
                }
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'desc' => $desc
                ));
            }

            if(get_post_type() == 'professor'){
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'profLandscape')
                ));
            }
        }
    
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }
    return $results;
}
?>
<?php

set_post_thumbnail_size( 300, 300 );

/* This is a hack to make sure that wordpress's autop filter doesn't apply to shortcodes */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );

function exploreveg_page_list ($atts) {
    extract( shortcode_atts( array(
		'tag' => '',
	), $atts ) );

    if (! $tag) {
        die('The ev_page_list shortcode requires a tag parameter');
    }

    $query = new WP_Query ( array( 'post_type' => 'page',
                                   'tag'       => $tag,
                                   'orderby'   => 'title',
                                   'order'     => 'ASC' ) );

    $return = '';
    while ( $query->have_posts() ) {
        $query->the_post();
        $return .= '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';

        $content = apply_filters( 'the_content', get_the_content() );
        $content = str_replace( ']]>', ']]&gt;', $content );

        $first_200 = substr( $content, 0, 200 );
        preg_match( '/(?:<p>)?(.+\.)[ <\n]/', $first_200, $matches );
        $return .= '<p>' . $matches[1] . ' <a href="' . get_permalink() . '">Learn more</a>.</p>';
    }

    wp_reset_postdata();

    return $return;
}

add_shortcode( 'ev_page_list', 'exploreveg_page_list' );

function exploreveg_page_include ($atts) {
    extract( shortcode_atts( array(
		'tag' => '',
	), $atts ) );

    if (! $tag) {
        die('The ev_page_include shortcode requires a tag parameter');
    }

    $query = new WP_Query ( array( 'post_type' => 'page',
                                   'tag'       => $tag,
                                   'orderby'   => 'title',
                                   'order'     => 'ASC' ) );

    $return = '';
    while ( $query->have_posts() ) {
        $query->the_post();

        $return .= '<h3 class="subtitle">'. get_the_title() . '</h3>';

        $content = apply_filters( 'the_content', get_the_content() );
        $content = str_replace( ']]>', ']]&gt;', $content );

        $return .= $content;
    }

    wp_reset_postdata();

    return $return;
}

add_shortcode( 'ev_page_include', 'exploreveg_page_include' );

function exploreveg_front_page_blog_post () {
    $query = new WP_Query( array( 'post_type'      => 'post',
                                  'posts_per_page' => 1,
                                  'tag'            => 'ev-front-page',
                                  'orderby'        => 'post_date',
                                  'order'          => 'DESC' ) );


    $return = '';
    while ( $query->have_posts() ) {
        $query->the_post();

        $return .= '<h2>' . get_the_title() . '</h2>';
        $return .= '<p class="byline">Posted on ' . get_the_date() . ' by ' . get_the_author() . '</p>';
        $return .= _exploreveg_clean_excerpt();
    }

    wp_reset_postdata();

    return $return;
}

add_shortcode( 'ev_front_page_blog_post', 'exploreveg_front_page_blog_post' );

function exploreveg_front_page_event () {
    $events = EM_Events::get(
        array(
            'scope'   => 'future',
            'limit'   => 5,
            'order'   => 'ASC',
            'orderby' => 'event_start',
            )
        );

    if ( ! count($events) ) {
        return '<h2>Events</h2><p>There are no upcoming events right now.</p>';
    }

    $return = '';
    $return = $events[0]->output('<h2>#_EVENTNAME</h2>');

    $return .= $events[0]->output('<h3 class="event-date">#_EVENTDATES</h3>');

    global $post;
    $post = get_post( $events[0]->post_id );
    setup_postdata($post);

    $return .= _exploreveg_clean_excerpt();

    wp_reset_postdata();

    return $return;
}

add_shortcode( 'ev_front_page_event', 'exploreveg_front_page_event' );

function exploreveg_blockquote ($atts, $content) {
    extract( shortcode_atts( array(
		'author' => '',
		'image'  => false,
	), $atts ) );

    if (! $author) {
        die('The ev_blockquote shortcode requires an author parameter');
    }

    if (! $content) {
        die('The ev_blockquote shortcode requires a quote');
    }

    $classes = 'sidekick-unit';
    if ($image) {
        $classes .= ' with-image';
    }

    $return = '';
    $return .= '<div class="' . $classes . '">';
    $return .= "\n";
    $return .= '<blockquote>' . $content . "\n";
    $return .= "\n";
    $return .= '<small>' . $author . '</small>';
    $return .= '</blockquote>';
    $return .= '</div>';

    return $return;
}

add_shortcode( 'ev_blockquote', 'exploreveg_blockquote' );

function exploreveg_definition_list ($atts, $content) {
    if (! $content) {
        die('The ev_dl shortcode requires list items');
    }

    return '<dl>' . do_shortcode($content) . '</dl>';
}

add_shortcode( 'ev_dl', 'exploreveg_definition_list' );

function exploreveg_definition_list_item ($atts, $content) {
    extract( shortcode_atts( array(
		'title' => '',
	), $atts ) );


    if (! $title) {
        die('The ev_dl_item shortcode requires a title parameter');
    }

    if (! $content) {
        die('The ev_dl_item shortcode require a body');
    }

    return '<dt>' . $title . '</dt><dd>' . $content . "</dd>\n";
}

add_shortcode( 'ev_dl_item', 'exploreveg_definition_list_item' );

function _exploreveg_clean_excerpt () {
    // I love the Wordpress API!
    global $more;
    $old_more = $more;
    $more = 0;

    $excerpt = get_the_content('');

    $more = $old_more;

    preg_replace( '/^\s+/', '', $excerpt );
    preg_replace( '/\s+$/', '', $excerpt );

    $thumbnail = exploreveg_thumbnail();
    $added_thumbnail = false;

    $clean = '';
    $paras = preg_split( '/\n+/', $excerpt );
    foreach ( $paras as $p ) {
        if ( ! $added_thumbnail ) {
            $clean .= "<p>$thumbnail$p</p>";
            $added_thumbnail = true;
        }
        else {
            $clean .= "<p>$p</p>";
        }
    }

    $clean .= '<a href="' . get_permalink() . '">Continue reading<span class="meta-nav">→</span></a>';

    return $clean;
}

function exploreveg_thumbnail ( $atts=array() ) {
    if ( ! has_post_thumbnail() ) {
        return '';
    }

    extract( shortcode_atts( array(
		'size' => 'thumbnail',
	), $atts ) );

    $return = '';
    $return .= '<a href="' . get_permalink() . '">';
    $return .= get_the_post_thumbnail( null, $size, array( 'class' => 'pull-right' ) );
    $return .= '</a>';

    return $return;
}

add_shortcode( 'ev_thumbnail', 'exploreveg_thumbnail' );

function exploreveg_volunteer_categories ( $atts=array() ) {
    extract( shortcode_atts( array(
		'type' => '',
	), $atts ) );

    if (! $type) {
        die('The ev_volunteer_categories shortcode requires a type parameter');
    }

    $type_term = get_term_by( 'slug', $type, 'volunteer_opportunity_tag' );

    if ( is_wp_error($terms) ) {
        return 'Error: ' . $type_term->get_error_message();
    }

    if ( ! $type_term || count($type_term) == 0 ) {
        return "<p>Could not find a volunteer category matching $type.</p>";
    }

    $terms = get_terms(
        'volunteer_opportunity_tag',
        array(
            'orderby' => 'name',
            'order'   => 'ASC',
            'parent'  => $type_term->term_id,
            'hide_empty' => 0,
            )
        );

    if ( is_wp_error($terms) ) {
        return 'Error: ' . $terms->get_error_message();
    }

    if ( count($terms) == 0 ) {
        return "<p>There are no categories of this type ($type).</p>";
    }

    $return = '<ul>';
    foreach ( $terms as $term ) {
        $return .= '<li>';
        $return .= '<a href="/volunteer/category/' . $term->slug . '">' . $term->name . '</a>';
        $return .= '<p>' . $term->description . '</p>';
        $return .= '</li>';
    }
    $return .= '</ul>';

    return $return;
}

add_shortcode( 'ev_volunteer_categories', 'exploreveg_volunteer_categories' );

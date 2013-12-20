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
    if ($author) {
        $return .= "\n<small>" . $author . '</small>';
    }
    $return .= '</blockquote>';
    $return .= '</div>';

    return $return;
}

add_shortcode( 'ev_blockquote', 'exploreveg_blockquote' );

function exploreveg_aside ($atts, $content) {
    if (! $content) {
        die('The ev_aside shortcode requires content');
    }

    return '<aside class="span3 pull-right side-note">' . $content . '</aside>';
}

add_shortcode( 'ev_aside', 'exploreveg_aside' );

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

function exploreveg_anchor ($atts) {
    extract( shortcode_atts( array(
		'name' => '',
	), $atts ) );

    if (! $name) {
        die('The ev_anchor shortcode requires a name parameter');
    }

    return '<a name="' . $name . '"></a>';
}

add_shortcode( 'ev_anchor', 'exploreveg_anchor' );

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

add_action( 'admin_menu', 'ev_plugin_menu' );

function ev_plugin_menu() {
	add_options_page( 'Exploreveg Theme Options', 'Theme Options', 'manage_options', 'exploreveg-options', 'ev_plugin_options' );
}

function ev_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

    $hidden_field_name = 'ev_option_submit';
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        update_option('exploreveg-facebook', $_POST['exploreveg-facebook']);
        update_option('exploreveg-twitter', $_POST['exploreveg-twitter']);
        update_option('exploreveg-rss', $_POST['exploreveg-rss']);
        update_option('exploreveg-phone', $_POST['exploreveg-phone']);
        update_option('exploreveg-announce-form-id', $_POST['exploreveg-announce-form-id']);

        echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
    }

    $phone_val = get_option('exploreveg-phone');
    $facebook_val = get_option('exploreveg-facebook');
    $twitter_val = get_option('exploreveg-twitter');
    $rss_val = get_option('exploreveg-rss');
    $announce_form_id_val = get_option('exploreveg-announce-form-id');
?>

<div id="icon-options-general" class="icon32"><br /></div><h2>Exploreveg Theme Settings</h2>

<form name="ev-options" method="post" action="">
  <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">

  <table class="form-table">
    <tr valign="top">
      <th scope="row"><label for="facebook">Facebook Page:</label></th>
      <td>
        <input name="exploreveg-facebook" type="text" id="facebook" value="<?php echo $facebook_val ?>" class="regular-text" />
        <br>
        This will be used as the link at the top of the page if one is provided.
      </td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="twitter">Twitter Page:</label></th>
      <td>
        <input name="exploreveg-twitter" type="text" id="twitter" value="<?php echo $twitter_val ?>" class="regular-text" />
        <br>
        This will be used as the link at the top of the page if one is provided.
      </td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="rss">Show RSS icon?</label></th>
      <td>
        <input name="exploreveg-rss" type="checkbox" id="rss" value="1" <?php if ($rss_val) { echo 'checked="checked"'; } ?>" />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="phone">Phone Number:</label></th>
      <td>
        <input name="exploreveg-phone" type="text" id="phone" value="<?php echo $phone_val ?>" />
        <br>
        This will be shown in the footer if one is provided.
      </td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="announce-form-id">Announce List Signup Form ID:</label></th>
      <td>
        <input name="exploreveg-announce-form-id" type="text" id="announce-form--id" value="<?php echo $announce_form_id_val ?>" />
        <br>
        The Contact Form 7 form ID for this form, if one exists.
      </td>
    </tr>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
</form>

<?php
}
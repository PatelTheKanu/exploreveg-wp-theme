<?php
/** content-page.php
 *
 * The template for displaying page content in the page.php template
 *
 * @author		Konstantin Obenland
 * @package		The Bootstrap
 * @since		1.0.0 - 07.02.2012
 */


tha_entry_before(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php tha_entry_top(); ?>
	
	<header>
		<?php the_title( '<h2 id="page-title">', '</h2>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content clearfix">
        <?php
        $breadcrumbs = $post->breadcrumbs;
        if ( !$breadcrumbs ) {
            $parent = $post->post_parent;
            if ($parent) {
                $breadcrumbs = array(
                    array(
                        link => get_permalink($parent),
                        title => get_the_title($parent),
                        ),
                    );
            }
        }

        if ($breadcrumbs) {
            echo '<ul id="breadcrumbs">';
            $x = 0;
            foreach ($breadcrumbs as $crumb) {
                echo '<li>';
                if ($x++ == 0) {
                    echo '<i class="icon-arrow-left"></i>';
                }
                echo '<a href="' . $crumb['link'] . '" title="' . $crumb['title'] . '">' . $crumb['title'] . '</a></li>';
            }
            echo '</ul>';
        }

        if ( has_post_thumbnail() ) {
            $full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
            echo '<a class="thumbnail post-thumbnail pull-right span3" href="' . $full_image_url[0] . '" title="' . the_title_attribute('echo=0') . '">';
            the_post_thumbnail();
            echo '</a>';
        }
		the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'the-bootstrap' ) );
		the_bootstrap_link_pages(); ?>
	</div><!-- .entry-content -->
	<?php edit_post_link( __( 'Edit', 'the-bootstrap' ), '<footer class="entry-meta"><span class="edit-link label">', '</span></footer>' );
	
	tha_entry_bottom(); ?>
</article><!-- #post-<?php the_ID(); ?> -->
<?php tha_entry_after();


/* End of file content-page.php */
/* Location: ./wp-content/themes/the-bootstrap/partials/content-page.php */
<?php
/** content.php
 *
 * The default template for displaying content
 *
 * @author      Konstantin Obenland
 * @package     The Bootstrap
 * @since       1.0.0 - 05.02.2012
 */

global $is_multi_post;

tha_entry_before(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php tha_entry_top(); ?>
    
    <header class="page-header">
    <?php if ( is_sticky() AND is_home() ) : ?>
        <?php the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'the-bootstrap' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark">', '</a></h1>' ); ?>
        <h3 class="entry-format"><?php _e( 'Featured', 'the-bootstrap' ); ?></h3>
    <?php
        else :
            if ($is_multi_post) {
                the_title( '<h3 class="entry-title"><a href="' . get_permalink() .'" title="' . sprintf( esc_attr__( 'Permalink to %s', 'the-bootstrap' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark">', '</a></h3>' );
            }
            else {
                the_title( '<h2 id="page-title"><a href="' . get_permalink() .'" title="' . sprintf( esc_attr__( 'Permalink to %s', 'the-bootstrap' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark">', '</a></h2>' );
            }
        endif;
        
        if ( 'post' == get_post_type() ) : ?>
        <div class="entry-meta">
            <?php the_bootstrap_posted_on(); ?>
        </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php if ( is_search() ) : // Only display Excerpts for Search ?>
    <div class="entry-summary clearfix">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->
    <?php else : ?>
    <div class="entry-content clearfix">
        <?php if ( has_post_thumbnail() ) : ?>
        <a class="thumbnail post-thumbnail pull-right span2" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
             <?php the_post_thumbnail( 'thumbnail' ); ?>
        </a>
        <?php endif;
        the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'the-bootstrap' ) );
        the_bootstrap_link_pages(); ?>
    </div><!-- .entry-content -->
    <?php endif; ?>

    <?php tha_entry_bottom(); ?>
</article><!-- #post-<?php the_ID(); ?> -->
<?php tha_entry_after();


/* End of file content.php */
/* Location: ./wp-content/themes/the-bootstrap/partials/content.php */

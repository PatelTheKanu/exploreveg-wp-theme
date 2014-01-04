<?php
/** sidebar.php
 *
 * @author		Konstantin Obenland
 * @package		The Bootstrap
 * @since		1.0.0	- 05.02.2012
 */

tha_sidebars_before();
if ( get_option('exploreveg-use-custom-sidebar') ) {
    include('sidebar-exploreveg.php');
}
else {
    include('sidebar-standard.php');
}
tha_sidebars_after();

/* End of file sidebar.php */
/* Location: ./wp-content/themes/the-bootstrap/sidebar.php */

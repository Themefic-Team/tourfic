<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Topbar
 * @author Foysal
 */
do_action( 'tf_fd_before_topbar' );

global $TF_FD;
$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$avatar_url   = get_avatar_url( $user_id );
$display_name = $current_user->display_name;
?>
    <header id="page-topbar">
        <h1>Header</h1>
    </header>
<?php
do_action( 'tf_fd_after_topbar' );
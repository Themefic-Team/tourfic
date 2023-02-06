<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Navbar
 * @author Foysal
 */
global $TF_FD;
$tf_fd_menus = $TF_FD->get_tf_fd_menus();


do_action( 'tf_fd_before_navbar' );
?>
    <div class="vertical-menu">

        <div data-simplebar class="h-100">

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
					<?php
					if ( ! empty( $tf_fd_menus ) ) {
						foreach ( $tf_fd_menus as $menu_key => $menu_data ) {
							if ( ! empty( $menu_data['label'] ) && ! empty( $menu_data['url'] ) ) {
								?>
                                <li class="tffd_menu_items tffd_menu_<?php echo $menu_key; ?>">
                                    <a href="<?php echo $menu_data['url']; ?>" class="waves-effect <?php echo !empty( $menu_data['sub_menu'] ) ? 'has-arrow' : ''; ?>">
                                        <i class="ti-home"></i>
                                        <span><?php echo esc_html( $menu_data['label'] ) ?></span>
                                    </a>
									<?php if ( ! empty( $menu_data['sub_menu'] ) ): ?>
                                        <ul class="sub-menu" aria-expanded="false">
											<?php foreach ( $menu_data['sub_menu'] as $sub_menu_key => $sub_menu_data ) { ?>
                                                <li class="tffd_menu_items tffd_menu_<?php echo $sub_menu_key; ?>">
                                                    <a href="<?php echo $sub_menu_data['url']; ?>" class="waves-effect">
                                                        <?php echo esc_html( $sub_menu_data['label'] ) ?>
                                                    </a>
                                                </li>
											<?php } ?>
                                        </ul>
									<?php endif; ?>
                                </li>
								<?php
							}
						}
					}
					?>

                    <li class="menu-title">User Options</li>


                </ul>
            </div>
            <!-- Sidebar -->
        </div>
    </div>
<?php
do_action( 'tf_fd_after_navbar' );
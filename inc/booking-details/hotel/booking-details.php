<div class="tf-booking-header-filter">
    <div class="tf-left-search-filter">
        <div class="tf-bulk-action-form">
            <div class="tf-filter-options">
                <div class="tf-order-status-filter">
                    <select class="tf-tour-filter-options tf-filter-bulk-option">
                        <option value=""><?php _e("Bulk action", "tourfic"); ?></option>
                        <option value="trash"><?php _e("Trash", "tourfic"); ?></option>
                        <option value="processing"><?php _e("Processing", "tourfic"); ?></option>
                        <option value="on-hold"><?php _e("On Hold", "tourfic"); ?></option>
                        <option value="completed"><?php _e("Complete", "tourfic"); ?></option>
                        <option value="cancelled"><?php _e("Cancelled", "tourfic"); ?></option>
                    </select>
                </div>
            </div>
            <button class="tf-order-status-filter-btn">
                <?php _e("Apply", "tourfic"); ?>
            </button>
        </div>

        <div class="tf-filter-options">
            <div class="tf-order-status-filter">
                <select class="tf-tour-filter-options tf-order-payment-status">
                    <option value=""><?php _e("Order status", "tourfic"); ?></option>
                    <option value="processing" <?php echo !empty($_GET['payment']) && "processing"==$_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Processing", "tourfic"); ?></option>
                    <option value="on-hold" <?php echo !empty($_GET['payment']) && "on-hold"==$_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("On Hold", "tourfic"); ?></option>
                    <option value="completed" <?php echo !empty($_GET['payment']) && "completed"==$_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Complete", "tourfic"); ?></option>
                    <option value="cancelled" <?php echo !empty($_GET['payment']) && "cancelled"==$_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Cancelled", "tourfic"); ?></option>
                    <option value="refunded" <?php echo !empty($_GET['payment']) && "refunded"==$_GET['payment'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Refund", "tourfic"); ?></option>
                </select>
            </div>
        </div>

        <div class="tf-filter-options">
            <div class="tf-order-status-filter">
                <select class="tf-tour-checkinout-options">
                    <option value=""><?php _e("Checked in status", "tourfic"); ?></option>
                    <option value="in" <?php echo !empty($_GET['checkinout']) && "in"==$_GET['checkinout'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Checked in", "tourfic"); ?></option>
                    <option value="out" <?php echo !empty($_GET['checkinout']) && "out"==$_GET['checkinout'] ? esc_attr( 'selected' ) : ''; ?>><?php _e("Checked out", "tourfic"); ?></option>
                </select>
            </div>
        </div>

        <div class="tf-filter-options">
            <div class="tf-order-status-filter">
                <select class="tf-tour-filter-options tf-hotel-id-filter-options">
                    <option value=""><?php _e("Hotel name", "tourfic"); ?></option>
                    <?php 
                    $tf_hotel_list = array(
                        'posts_per_page' => - 1,
                        'post_type'      => 'tf_hotel',
                        'post_status'    => 'publish'
                    );
                    $tf_hotel_list_query = new WP_Query( $tf_hotel_list );
                    if ( $tf_hotel_list_query->have_posts() ):
                        while ( $tf_hotel_list_query->have_posts() ) : $tf_hotel_list_query->the_post();
                    ?>
                    <option value="<?php echo get_the_ID(); ?>" <?php echo !empty($_GET['post']) && get_the_ID()==$_GET['post'] ? esc_attr( 'selected' ) : ''; ?>><?php echo get_the_title(); ?></option>
                    <?php 
                        endwhile;
                    endif;
                    wp_reset_query();
                    ?>
                </select>
            </div>
        </div>
    </div>
    <form class="tf-right-search-filter">
        <input type="number" value="<?php echo !empty($_GET['post']) ? esc_attr( $_GET['post'] ) : ''; ?>" placeholder="Search by Hotel ID" id="tf-searching-key">
        <button class="tf-search-by-id" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z" stroke="#87888B" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </form>
</div>

<div class="tf-order-table-responsive">
    <table class="wp-list-table table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td id="cb">
                    <div class="tf-checkbox-listing">
                        <input id="cb-select-all-1" type="checkbox">
                    </div>
                </td>
                <th id="order_id">
                    <?php _e("ID", "tourfic"); ?>
                </th>
                <th id="odetails">
                    <?php _e("Hotel name", "tourfic"); ?>
                </th>
                <th id="cdetails">
                    <?php _e("Customer details", "tourfic"); ?>
                </th>
                <th id="odate">
                    <?php _e("Booking date", "tourfic"); ?>
                </th>
                <th id="check_status">
                    <?php _e("Checked in status", "tourfic"); ?>
                </th>
                <th id="ostatus">
                    <?php _e("Order status", "tourfic"); ?>
                </th>
                <th id="action">
                    <?php _e("Action", "tourfic"); ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $tf_key = 1;
            foreach($hotel_orders_result as $hotel){ ?>
            <tr>
                <th class="check-column">
                    <div class="tf-checkbox-listing">
                        <input type="checkbox" name="order_id[]" value="<?php echo esc_html( $hotel['id'] ); ?>">
                    </div>
                </th>
                <td>
                    <a href="<?php echo admin_url(); ?>edit.php?post_type=tf_hotel&amp;page=tf_hotel_booking&amp;order_id=<?php echo esc_attr($hotel['order_id']); ?>&amp;book_id=<?php echo esc_attr($hotel['id']); ?>&amp;action=preview">
                        <?php echo esc_html( $hotel['order_id'] ); ?>
                    </a>
                </td>
                <td>
                    <?php echo get_the_title($hotel['post_id']); ?>
                </td>
                <td>
                    <?php 
                    $billing_info = json_decode($hotel['billing_details']);
                    $billing_details = "";
                    $billing_first_name = !empty($billing_info->billing_first_name) ? $billing_info->billing_first_name : '';
                    $billing_last_name = !empty($billing_info->billing_last_name) ? $billing_info->billing_last_name : '';
                    $customer_name        = $billing_first_name . ' ' . $billing_last_name;
                    $customer_email       = !empty($billing_info->billing_email) ? $billing_info->billing_email : '';
                    if ( $customer_name ) {
                        $billing_details .= $customer_name . '<br>';
                    }
                    if ( $customer_email ) {
                        $billing_details .= '<span>' . $customer_email . '</span>';
                    }
                    echo wp_kses_post($billing_details);
                    ?>
                </td>
                <td>
                    <?php echo date('F d, Y',strtotime($hotel['order_date'])); ?>
                </td>
                <td>
                    <?php 
                    if( !empty($hotel['checkinout']) ){
                        if("in"==$hotel['checkinout']){
                            echo wp_kses_post('<span class="checkinout checkin">Checked in</span>');
                        }
                        if("out"==$hotel['checkinout']){
                            echo wp_kses_post('<span class="checkinout checkout">Checked out</span>');
                        }
                        if("not"==$hotel['checkinout']){
                            echo wp_kses_post('<span class="checkinout checkout">Not checked in</span>');
                        }
                    }else{
                        echo wp_kses_post('<span class="checkinout checkout">Not checked in</span>');
                    }
                    ?>
                </td>
                <td style="text-transform: capitalize;">
                    <?php echo esc_html( $hotel['ostatus'] ); ?>
                </td>
                <td>
                    <?php 
                    $actions_details = '<a href="'.admin_url().'edit.php?post_type=tf_hotel&amp;page=tf_hotel_booking&amp;order_id='.$hotel['order_id'].'&amp;book_id='.$hotel['id'].'&amp;action=preview" class="tf_booking_details_view"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.82924 16.1427L8.31628 17.238C8.46106 17.5641 8.69734 17.8412 8.99647 18.0356C9.29559 18.23 9.6447 18.3335 10.0015 18.3334C10.3582 18.3335 10.7073 18.23 11.0065 18.0356C11.3056 17.8412 11.5419 17.5641 11.6866 17.238L12.1737 16.1427C12.3471 15.754 12.6387 15.43 13.007 15.2167C13.3777 15.0029 13.8065 14.9119 14.232 14.9566L15.4237 15.0834C15.7784 15.1209 16.1364 15.0547 16.4543 14.8929C16.7721 14.731 17.0362 14.4803 17.2144 14.1714C17.3929 13.8626 17.4779 13.5086 17.4592 13.1525C17.4405 12.7963 17.3188 12.4532 17.1089 12.1649L16.4033 11.1955C16.1521 10.8477 16.0178 10.4291 16.02 10.0001C16.0199 9.57224 16.1554 9.15537 16.407 8.80934L17.1126 7.8399C17.3225 7.55154 17.4442 7.20847 17.4629 6.85231C17.4816 6.49615 17.3966 6.1422 17.2181 5.83341C17.0399 5.52444 16.7758 5.27382 16.458 5.11194C16.1401 4.95005 15.7821 4.88386 15.4274 4.92138L14.2357 5.04823C13.8102 5.09292 13.3814 5.00185 13.0107 4.78804C12.6417 4.57362 12.35 4.24788 12.1774 3.85749L11.6866 2.76212C11.5419 2.43606 11.3056 2.15901 11.0065 1.96458C10.7073 1.77015 10.3582 1.66669 10.0015 1.66675C9.6447 1.66669 9.29559 1.77015 8.99647 1.96458C8.69734 2.15901 8.46106 2.43606 8.31628 2.76212L7.82924 3.85749C7.65668 4.24788 7.36497 4.57362 6.99591 4.78804C6.62526 5.00185 6.19647 5.09292 5.77091 5.04823L4.57554 4.92138C4.22081 4.88386 3.86282 4.95005 3.54497 5.11194C3.22711 5.27382 2.96305 5.52444 2.7848 5.83341C2.60632 6.1422 2.52128 6.49615 2.54002 6.85231C2.55876 7.20847 2.68046 7.55154 2.89035 7.8399L3.59591 8.80934C3.84753 9.15537 3.98302 9.57224 3.98295 10.0001C3.98302 10.4279 3.84753 10.8448 3.59591 11.1908L2.89035 12.1603C2.68046 12.4486 2.55876 12.7917 2.54002 13.1479C2.52128 13.504 2.60632 13.858 2.7848 14.1667C2.96323 14.4756 3.22732 14.726 3.54513 14.8879C3.86294 15.0498 4.22084 15.1161 4.57554 15.0788L5.76721 14.9519C6.19276 14.9072 6.62155 14.9983 6.99221 15.2121C7.36265 15.4259 7.65571 15.7517 7.82924 16.1427Z" stroke="#1D2327" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.99998 12.5001C11.3807 12.5001 12.5 11.3808 12.5 10.0001C12.5 8.61937 11.3807 7.50008 9.99998 7.50008C8.61926 7.50008 7.49998 8.61937 7.49998 10.0001C7.49998 11.3808 8.61926 12.5001 9.99998 12.5001Z" stroke="#1D2327" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg></a>';
                    echo $actions_details;
                    ?>
                </td>
            </tr>
            <?php 
            if ( !defined( 'TF_PRO' ) && $tf_key == 15) { ?>
                <tr class="pro-row" style="text-align: center; background-color: #ededf8">
                    <td colspan="8" style="text-align: center;">
                        <a href="https://tourfic.com/" target="_blank">
                            <h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;"><?php _e( 'Upgrade to Pro Version to See More', 'tourfic' ); ?></h3>
                        </a>
                    </td>
                </tr>
            <?php } $tf_key++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8">
                    <ul class="tf-booking-details-pagination">
                        <?php if(!empty($paged) && $paged>=2){ ?>
                            <li><a href="<?php echo tf_booking_details_pagination( $paged-1 ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M15.8333 10.0001H4.16663M4.16663 10.0001L9.99996 15.8334M4.16663 10.0001L9.99996 4.16675" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg><?php _e("Previous", "tourfic"); ?></a></li>
                        <?php } 
                        if(!empty($total_pages) && $total_pages > 1){
                        for ($i=1; $i<=$total_pages; $i++) {
                            if ($i == $paged) {  
                        ?>
                            <li class="active">
                                <a href="<?php echo tf_booking_details_pagination( $i ); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } else{ ?>
                            <li>
                            <a href="<?php echo tf_booking_details_pagination( $i ); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } } }
                        if(!empty($total_pages) && !empty($paged) && $paged < $total_pages){
                        ?>
                            <li><a href="<?php echo tf_booking_details_pagination( $paged+1 ); ?>"><?php _e("Next", "tourfic"); ?> <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.16669 10.0001H15.8334M15.8334 10.0001L10 4.16675M15.8334 10.0001L10 15.8334" stroke="#1D2327" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg></a></li>
                        <?php } ?>
                    </ul>
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="tf-preloader-box">
    <div class="tf-loader-preview">
        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
    </div>
</div>
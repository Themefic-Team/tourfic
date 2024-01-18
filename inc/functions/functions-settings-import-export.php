<?php 
//don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Import settings
 */
add_action( 'wp_ajax_tf_import', 'tf_import_callback' );
function tf_import_callback(){

    $imported_data = stripslashes( $_POST['tf_import_option'] );
    $imported_data = unserialize( $imported_data );
    
    // QR CODE Company Logo
    if(!empty($imported_data["qr_logo"])){
        
        // Download the image file
        $qr_logo_image_data = file_get_contents( $imported_data["qr_logo"] );
    
        // Create a unique filename for the image
        $qr_logo_filename   = basename( $imported_data["qr_logo"] );
        $qr_logo_upload_dir = wp_upload_dir();
        $qr_logo_image_path = $qr_logo_upload_dir['path'] . '/' . $qr_logo_filename;

        // Save the image file to the uploads directory
        file_put_contents( $qr_logo_image_path, $qr_logo_image_data );
        // Check if the image was downloaded successfully.
        if (file_exists($qr_logo_image_path)) {
            // Create the attachment for the uploaded image
            $qr_logo_attachment = array(
                'guid'           => $qr_logo_upload_dir['url'] . '/' . $qr_logo_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $qr_logo_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $qr_logo_attachment_id = wp_insert_attachment( $qr_logo_attachment, $qr_logo_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $qr_logo_attachment_data = wp_generate_attachment_metadata( $qr_logo_attachment_id, $qr_logo_image_path );
            wp_update_attachment_metadata( $qr_logo_attachment_id, $qr_logo_attachment_data );

            $imported_data["qr_logo"] = wp_get_attachment_url($qr_logo_attachment_id);
        }
    }

    // QR CODE Watermark
    if(!empty($imported_data["qr_background"])){
        
        // Download the image file
        $qr_background_image_data = file_get_contents( $imported_data["qr_background"] );
      
        // Create a unique filename for the image
        $qr_background_filename   = basename( $imported_data["qr_background"] );
        $qr_background_upload_dir = wp_upload_dir();
        $qr_background_image_path = $qr_background_upload_dir['path'] . '/' . $qr_background_filename;

        // Save the image file to the uploads directory
        file_put_contents( $qr_background_image_path, $qr_background_image_data );
        if (file_exists($qr_background_image_path)) {
            // Create the attachment for the uploaded image
            $qr_background_attachment = array(
                'guid'           => $qr_background_upload_dir['url'] . '/' . $qr_background_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $qr_background_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $qr_background_attachment_id = wp_insert_attachment( $qr_background_attachment, $qr_background_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $qr_background_attachment_data = wp_generate_attachment_metadata( $qr_background_attachment_id, $qr_background_image_path );
            wp_update_attachment_metadata( $qr_background_attachment_id, $qr_background_attachment_data );

            $imported_data["qr_background"] = wp_get_attachment_url($qr_background_attachment_id);
        }
    }

    // Front End Dashboard Logo
    if(!empty($imported_data["fd_logo"])){
        
        // Download the image file
        $fd_logo_image_data = file_get_contents( $imported_data["fd_logo"] );
        
        // Create a unique filename for the image
        $fd_logo_filename   = basename( $imported_data["fd_logo"] );
        $fd_logo_upload_dir = wp_upload_dir();
        $fd_logo_image_path = $fd_logo_upload_dir['path'] . '/' . $fd_logo_filename;

        // Save the image file to the uploads directory
        file_put_contents( $fd_logo_image_path, $fd_logo_image_data );
        
        if (file_exists($fd_logo_image_path)) {
            // Create the attachment for the uploaded image
            $fd_logo_attachment = array(
                'guid'           => $fd_logo_upload_dir['url'] . '/' . $fd_logo_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $fd_logo_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $fd_logo_attachment_id = wp_insert_attachment( $fd_logo_attachment, $fd_logo_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $fd_logo_attachment_data = wp_generate_attachment_metadata( $fd_logo_attachment_id, $fd_logo_image_path );
            wp_update_attachment_metadata( $fd_logo_attachment_id, $fd_logo_attachment_data );

            $imported_data["fd_logo"] = wp_get_attachment_url($fd_logo_attachment_id);
        }
    }

    // Front End Dashboard Minified Logo
    if(!empty($imported_data["fd_logo_minified"])){
        
        // Download the image file
        $fd_logo_minified_image_data = file_get_contents( $imported_data["fd_logo_minified"] );
        
        // Create a unique filename for the image
        $fd_logo_minified_filename   = basename( $imported_data["fd_logo_minified"] );
        $fd_logo_minified_upload_dir = wp_upload_dir();
        $fd_logo_minified_image_path = $fd_logo_minified_upload_dir['path'] . '/' . $fd_logo_minified_filename;

        // Save the image file to the uploads directory
        file_put_contents( $fd_logo_minified_image_path, $fd_logo_minified_image_data );
        
        if (file_exists($fd_logo_minified_image_path)) {
            // Create the attachment for the uploaded image
            $fd_logo_minified_attachment = array(
                'guid'           => $fd_logo_minified_upload_dir['url'] . '/' . $fd_logo_minified_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $fd_logo_minified_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $fd_logo_minified_attachment_id = wp_insert_attachment( $fd_logo_minified_attachment, $fd_logo_minified_image_path );      
            
            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $fd_logo_minified_attachment_data = wp_generate_attachment_metadata( $fd_logo_minified_attachment_id, $fd_logo_minified_image_path );
            wp_update_attachment_metadata( $fd_logo_minified_attachment_id, $fd_logo_minified_attachment_data );

            $imported_data["fd_logo_minified"] = wp_get_attachment_url($fd_logo_minified_attachment_id);
        }
    }

    // Front End Dashboard Mobile Logo
    if(!empty($imported_data["fd_logo_mobile"])){
        
        // Download the image file
        $fd_logo_mobile_image_data = file_get_contents( $imported_data["fd_logo_mobile"] );
       
        // Create a unique filename for the image
        $fd_logo_mobile_filename   = basename( $imported_data["fd_logo_mobile"] );
        $fd_logo_mobile_upload_dir = wp_upload_dir();
        $fd_logo_mobile_image_path = $fd_logo_mobile_upload_dir['path'] . '/' . $fd_logo_mobile_filename;

        // Save the image file to the uploads directory
        file_put_contents( $fd_logo_mobile_image_path, $fd_logo_mobile_image_data );
        
        if (file_exists($fd_logo_mobile_image_path)) {
            // Create the attachment for the uploaded image
            $fd_logo_mobile_attachment = array(
                'guid'           => $fd_logo_mobile_upload_dir['url'] . '/' . $fd_logo_mobile_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $fd_logo_mobile_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $fd_logo_mobile_attachment_id = wp_insert_attachment( $fd_logo_mobile_attachment, $fd_logo_mobile_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $fd_logo_mobile_attachment_data = wp_generate_attachment_metadata( $fd_logo_mobile_attachment_id, $fd_logo_mobile_image_path );
            wp_update_attachment_metadata( $fd_logo_mobile_attachment_id, $fd_logo_mobile_attachment_data );

            $imported_data["fd_logo_mobile"] = wp_get_attachment_url($fd_logo_mobile_attachment_id);
        }
    }

    // Itinerary Company Logo
    if(!empty($imported_data['itinerary-builder-setings']['company_logo'])){
        
        // Download the image file
        $itinerary_company_image_data = file_get_contents( $imported_data['itinerary-builder-setings']['company_logo'] );
        
        // Create a unique filename for the image
        $itinerary_company_filename   = basename( $imported_data['itinerary-builder-setings']['company_logo'] );
        $itinerary_company_upload_dir = wp_upload_dir();
        $itinerary_company_image_path = $itinerary_company_upload_dir['path'] . '/' . $itinerary_company_filename;

        // Save the image file to the uploads directory
        file_put_contents( $itinerary_company_image_path, $itinerary_company_image_data );
        
        if (file_exists($itinerary_company_image_path)) {
            // Create the attachment for the uploaded image
            $itinerary_company_attachment = array(
                'guid'           => $itinerary_company_upload_dir['url'] . '/' . $itinerary_company_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $itinerary_company_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $itinerary_company_attachment_id = wp_insert_attachment( $itinerary_company_attachment, $itinerary_company_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $itinerary_company_attachment_data = wp_generate_attachment_metadata( $itinerary_company_attachment_id, $itinerary_company_image_path );
            wp_update_attachment_metadata( $itinerary_company_attachment_id, $itinerary_company_attachment_data );

            $imported_data['itinerary-builder-setings']["itinerary_company"] = wp_get_attachment_url($itinerary_company_attachment_id);
        }
    }

    // Itinerary Talk to Expert Image
    if(!empty($imported_data['itinerary-builder-setings']['expert_logo'])){
        
        // Download the image file
        $expert_logo_image_data = file_get_contents( $imported_data['itinerary-builder-setings']['expert_logo'] );
        
        // Create a unique filename for the image
        $expert_logo_filename   = basename( $imported_data['itinerary-builder-setings']['expert_logo'] );
        $expert_logo_upload_dir = wp_upload_dir();
        $expert_logo_image_path = $expert_logo_upload_dir['path'] . '/' . $expert_logo_filename;

        // Save the image file to the uploads directory
        file_put_contents( $expert_logo_image_path, $expert_logo_image_data );
        
        if (file_exists($expert_logo_image_path)) {
            // Create the attachment for the uploaded image
            $expert_logo_attachment = array(
                'guid'           => $expert_logo_upload_dir['url'] . '/' . $expert_logo_filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $expert_logo_filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            // Insert the attachment
            $expert_logo_attachment_id = wp_insert_attachment( $expert_logo_attachment, $expert_logo_image_path );                       

            // Include the necessary file for media_handle_sideload().
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate the attachment metadata
            $expert_logo_attachment_data = wp_generate_attachment_metadata( $expert_logo_attachment_id, $expert_logo_image_path );
            wp_update_attachment_metadata( $expert_logo_attachment_id, $expert_logo_attachment_data );

            $imported_data['itinerary-builder-setings']["expert_logo"] = wp_get_attachment_url($expert_logo_attachment_id);
        }
    }

    update_option( 'tf_settings', $imported_data );
    wp_send_json_success($imported_data);
    die();
}
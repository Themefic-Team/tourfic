### Filter hook

#### General
* tf_global_css - **args:** `$output`

#### Hotel
* tf_hotel_supports - **file:** `Hotel_CPT.php`
* tf_hotel_labels - **file:** `functions-hotel.php`
* tf_hotel_post_type_args - **file:** `functions-hotel.php`
* tf_hotel_slug - **file **`Hotel_CPT.php`
* tf_hotel_location_labels - `not found`
* tf_hotel_location_args - **file:** `functions-hotel.php`
* tf_hotel_location_slug - **file:** `functions-hotel.php`
* tf_hotel_feature_labels - **file:** `Post_Type.php` - **dynamic:** `*_labels`
* tf_hotel_feature_args - **file:** `Post_Type.php` - **dynamic:** `*_args`
* tf_hotel_feature_slug - **file:** `Hotel_CPT.php`
* tf_hotel_type_labels - **file:** `Post_Type.php` - **dynamic:** `*_labels`
* tf_hotel_type_args - **file:** `Post_Type.php` - **dynamic:** `*_args`
* tf_hotel_type_slug - **file:** `Hotel_CPT.php`
* tf_hotel_css - **file:** `Enqueue.php`

#### Tour
* tf_tours_supports - **file:** `Tour_CPT.php`
* tf_tour_post_type_args - **file:** `functions-tours.php`
* tf_tours_slug - **file:** Tour_CPT.php 
* tf_tour_destination_labels - `not found`
* tour_destination_args - **file:** `functions-tour.php`
* tf_tour_destination_slug - **file:** `Tour_CPT.php`
* tf_tour_attraction_labels - **file:** `functions-tours.php`
* tour_attraction_args - **file:** `functions-tours.php`
* tf_tour_attraction_slug - **file:** `Tour_CPT.php`
* tf_tour_activities_labels - `not found`
* tour_activities_args - **file:** `functions-tours.php`
* tf_tour_activities_slug - **file:** `Tour_CPT.php`
* tf_tour_features_labels - **file:** `Post_Type.php` - **dynamic:** `*_labels`
* tf_tour_features_args - **file:** `Post_Type.php` - **dynamic:** `*_args`
* tf_tour_features_slug - **file:** `Tour_CPT.php`
* tf_tour_type_labels - `not found`
* tour_type_args - **file:** `functions-tour.php`
* tf_tour_type_slug - **file:** `Tour_CPT.php`
* tf_tour_css - **file:** `Enqueue.php`

#### Apartment
* tf_apartment_supports - **file:** `Apartment_CPT.php`
* tf_apartment_labels - **file:** `functions-apartment.php`
* tf_apartment_args - `not found`
* tf_apartment_slug - **file:** `Apartment_CPT.php`
* tf_apartment_location_labels - `not found`
* apartment_location_args - **file:** `functions-apartment.php`
* tf_apartment_location_slug - **file:** `Apartment_CPT.php`
* tf_apartment_feature_labels - **file:** `Post_Type.php` - **dynamic:** `*_labels`
* tf_apartment_feature_args - **file:** `Post_Type.php` - **dynamic:** `*_args`
* tf_apartment_feature_slug - **file:** `Apartment_CPT.php`
* tf_apartment_type_labels - `not found`
* tf_apartment_type_args - **file:** `functions-apartment.php`
* tf_apartment_type_slug - **file:** `Apartment_CPT.php`
* tf_apartment_css - **file:** `Enqueue.php` 

#### Backend Booking
* tf_{*}_backend_booking_form_title - **file:** `TF_Backend_Booking.php` - **dynamic:** `tour/hotel/apartment`
* tf_{*}_backend_booking_form_class - **file:** `TF_Backend_Booking.php` - **dynamic:** `tour/hotel/apartment`

#### Enquiry
* tf_hotel_enquiry_page_title - **file:** `Hotel_Enquiry.php`
* tf_hotel_enquiry_menu_title - **file:** `Hotel_Enquiry.php`
* tf_hotel_enquiry_page_heading - **file:** `Hotel_Enquiry.php`
* tf_tour_enquiry_page_title - **file:** `Tour_Enquiry.php`
* tf_tour_enquiry_menu_title - **file:** `Tour_Enquiry.php`
* tf_tour_enquiry_page_heading - **file:** `Tour_Enquiry.php`
* tf_apartment_enquiry_page_title - **file:** `Apartment_Enquiry.php`
* tf_apartment_enquiry_menu_title - **file:** `Apartment_Enquiry.php`
* tf_apartment_enquiry_page_heading - **file:** `Apartment_Enquiry.php`


### Action hook

#### Backend Booking
* tf_before_{*}_backend_booking_form - **file:** `TF_Backend_Booking.php` - **dynamic:** `tour/hotel/apartment`
* tf_after_{*}_backend_booking_form - **file:** `TF_Backend_Booking.php` - **dynamic:** `tour/hotel/apartment`

#### Enquiry
* tf_before_enquiry_details - **file:** `Hotel_Enquiry.php, Tour_Enquiry.php, Apartment_Enquiry.php`
* tf_before_hotel_enquiry_details - **file:** `Hotel_Enquiry.php`
* tf_after_hotel_enquiry_details - **file:** `Hotel_Enquiry.php`
* tf_before_tour_enquiry_details - **file:** `Tour_Enquiry.php`
* tf_after_tour_enquiry_details - **file:** `Tour_Enquiry.php`
* tf_before_apartment_enquiry_details - **file:** `Apartment_Enquiry.php`
* tf_after_apartment_enquiry_details - **file:** `Apartment_Enquiry.php`


<?php



/**







 * Jobify Child Theme







 *







 * Place any custom functionality/code snippets here.







 *







 * @since Jobify Child 1.0.0







 */















function jobify_child_styles() {







    wp_enqueue_style( 'jobify-child', get_stylesheet_uri() );







}

add_action( 'wp_enqueue_scripts', 'jobify_child_styles', 20 );



if(strpos($_SERVER['REQUEST_URI'], 'post-a-job') > 0 || strpos($_SERVER['REQUEST_URI'], 'checkout') > 0){

    //do nothing

}else{

    wc_setcookie('custom_add_to_cart_flag',1,  time()-3600);

    wc_setcookie('chosen_package_is_user_package',0,  time()-3600);

    wc_setcookie('chosen_package_id',0,  time()-3600);

}



function custom_add_to_cart_redirect() {



    wc_setcookie( 'chosen_package_id', $_REQUEST['add-to-cart'] );



    wc_setcookie( 'chosen_package_is_user_package', 1 );

    

   // wc_setcookie('custom_add_to_cart_flag',1);



   // global $woocommerce;



   // $woocommerce->cart->empty_cart();



  //  return get_site_url().'/post-a-job'; 



}



add_filter( 'woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect' );







add_filter( 'woocommerce_add_to_cart_validation', 'only_one_in_cart' );



  



function only_one_in_cart( $cart_item_data ) {



    global $woocommerce;



    $woocommerce->cart->empty_cart();



    return $cart_item_data;



}



//! empty( $_COOKIE['chosen_package_id'])

if (isset($_COOKIE['custom_add_to_cart_flag'] ) ){

    add_filter('wcpl_enable_paid_job_listing_submission','disable_package_step');



}







function disable_package_step($is_choosen){



    return false;



}







function proceed_to_checkout(){



    $job_manager = WP_Job_Manager_Form_Submit_Job::instance();



    $job_id = $job_manager->get_job_id();



    $package_id = $_COOKIE['chosen_package_id'];



    $package = get_product( $package_id );

    

    unset($_COOKIE['custom_add_to_cart_flag']);



    // Continue = change job status then show next screen



    if ( ! empty( $_POST['continue'] ) ) {



            $job = get_post( $job_id );







            if ( in_array( $job->post_status, array( 'preview', 'expired' ) ) ) {



                    // Reset expiry



                    delete_post_meta( $job->ID, '_job_expires' );







                    // Update job listing



                    $update_job                  = array();



                    $update_job['ID']            = $job->ID;



                    $update_job['post_status']   = 'pending_payment';



                    $update_job['post_date']     = current_time( 'mysql' );



                    $update_job['post_date_gmt'] = current_time( 'mysql', 1 );



                    $update_job['post_author']   = get_current_user_id();







                    wp_update_post( $update_job );



            }







//            $this->step ++;



    }







    // Give job the package attributes



    update_post_meta( $job_id, '_job_duration', $package->get_duration() );



    update_post_meta( $job_id, '_featured', $package->is_featured() ? 1 : 0 );



    update_post_meta( $job_id, '_package_id', $package_id );







    if ( 'listing' === $package->package_subscription_type ) {



            update_post_meta( $job_id, '_job_expires', '' ); // Never expire automatically



    }







    // Add package to the cart



    WC()->cart->add_to_cart( $package_id, 1, '', '', array(



            'job_id' => $job_id



    ) );







    woocommerce_add_to_cart_message( $package_id );







    // Clear cookie



    wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );



    wc_setcookie( 'chosen_package_is_user_package', '', time() - HOUR_IN_SECONDS );







//    do_action( 'wcpl_process_package_for_job_listing', $package_id, $is_user_package, $job_id );







    // Redirect to checkout page



    wp_redirect( get_permalink( woocommerce_get_page_id( 'checkout' ) ) );



    exit;



}





if (isset($_COOKIE['custom_add_to_cart_flag'] ) ){

    add_filter('submit_job_steps','check_package_step');

}

//echo "hello world";

//echo $_COOKIE['chosen_package_id'];

function check_package_step($steps){



    if ( ! empty( $_COOKIE['chosen_package_id'] ) && $_COOKIE['custom_add_to_cart_flag'] == 1 ){



//        $steps['wc-pay']['handler'] = 'choose_package_handler_check';



        $steps['preview']['handler'] = 'proceed_to_checkout';



    }



    return $steps;



}







function archipro_login_redirects( $redirect_to, $request, $user ) {



	if ( isset( $user->roles ) && is_array( $user->roles ) ) {



                if(in_array( 'administrator', $user->roles)){



                    return admin_url();



                }elseif ( in_array( 'employer', $user->roles )





                        || in_array('customer', $user->roles)



                        ) {



			return home_url('/my-account');



		}elseif(in_array('candidate', $user->roles)){

                    return home_url('/candidate-dashboard');

                }



	}



        return $redirect_to;



}



add_filter( 'login_redirect', 'archipro_login_redirects', 10, 3 );







add_action('woocommerce_before_my_account','post_a_job_button',9);







function post_a_job_button(){

    $user = wp_get_current_user();

    if(in_array('candidate', $user->roles)){

        return false;

    }

    ?>



<div class="callout container">



    <div class="callout-description">



            <p>POST an ad today, receive applicants today. Use credit card or Paypal.</p>



    </div>







    <div class="callout-action">



        <a href="<?php echo home_url('post-a-job') ?>" class="button">POST A JOB NOW!</a>



    </div>



</div>



<?php



}





/*

function wp_job_manager_notify_new_user( $user_id, $password ) {



    $user = get_userdata( $user_id );



    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n";



    $message .= sprintf(__('Password: %s'), $password) . "\r\n\r\n";



    



    $message .= wp_login_url() . "\r\n";



    $blogname = get_bloginfo('name');



    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);







}*/







add_filter('submit_job_form_fields','archipro_job_change_form_fields',20);







function archipro_job_change_form_fields($fields){



//    $fields['job']['job_title']['label'] = "Custom Label";



    $fields['job']['job_description']['label'] = "Job Description";



    unset($fields['job']['job_region']);



    unset($fields['company']['company_description']);



    return $fields;



}







function listing_published_send_email($post_id) {



   $post = get_post($post_id);



   $author = get_userdata($post->post_author);







   $message = "



      Hi ".$author->display_name.",



      Your listing, ".$post->post_title." has just been approved at ".get_permalink( $post_id ).". Well done!



   ";



   wp_mail($author->user_email, "Your job listing is now online, please check it!", $message);



}



add_action('publish_job_listing', 'listing_published_send_email');



add_filter('job_manager_indeed_get_jobs_args','archipro_refine_search');

function archipro_refine_search($args){

    // This file defines $keyword variable

    include_once get_stylesheet_directory().'/includes/indeed-query-keywords.php';

    

    //This file defines $valid_keywords variable.

    include_once get_stylesheet_directory().'/includes/indeed-valid-keywords.php';

    

    if(!array_key_exists($args['q'], $valid_keywords)){

        $args['q'] = md5('no results to show');

        return $args;

    }

    

    $query = $args['q'];

    if(array_key_exists($query, $keywords)){

        

        $additions = $keywords[$query];

        

        // Combining And

        $and_query = '';

        if(!empty($additions['and'])){

            $and_keywords = explode(',', $additions['and']);

            $and_query = implode(' ',$and_keywords);

        }

        

        // Combining OR

        $or_query =  '';

        if(!empty($additions['or'])){

            $or_keywords = explode(',', $additions['or']);

            $or_query_string = implode(' or ', $or_keywords);

            $or_query = '('.$or_query_string.')';

        }

        

        // Combining Not

        $not_query = '';

        if(!empty($additions['not'])){

            $not_keywords = archi_parse_keywords_ary(explode(',', $additions['not']));

            $not_query = ' -'.implode(' -', $not_keywords);

        }

        

        $query .= ' '.$and_query;

        $query .= ' '.$or_query;

        $query.= $not_query;

        

    }

    $args['q'] = $query;

    return $args;

}



function archi_parse_keywords_ary($keywords){

    $parsed_ary = array();

    foreach($keywords as $keyword){

        $parsed_ary[] = '"'.$keyword.'"';

    }

    return $parsed_ary;

}



function arc_ninja_forms_register_example(){

  add_action( 'ninja_forms_process', 'arc_ninja_forms_register_employer' );

    add_action('ninja_forms_pre_process','arc_ninja_forms_check');

    add_action('ninja_forms_post_process','arc_ninja_register_employer_redirect');

}

add_action( 'init', 'arc_ninja_forms_register_example' );



function arc_ninja_forms_check(){

    global $ninja_forms_processing;

    $form_id = $ninja_forms_processing->get_form_ID();

    if($form_id == 8){

        $user_email = $ninja_forms_processing->get_field_value(24);

        $user_id = username_exists( $user_email );

        if ( !$user_id && email_exists($user_email) == false ) {

            //Do nothing

        }else{

            $ninja_forms_processing->add_error('user_exists', 'User already exists with this email address');

        }

    }

}



function arc_ninja_register_employer_redirect(){

    global $ninja_forms_processing;

    $form_id = $ninja_forms_processing->get_form_ID();

    if($form_id == 8){

        wp_redirect( site_url('/my-account') );

    }

}



function arc_ninja_forms_register_employer(){

  global $ninja_forms_processing;

  $form_id = $ninja_forms_processing->get_form_ID();

  if($form_id != 8){

      return false;

  }

  //Get all the user submitted values

  $all_fields = $ninja_forms_processing->get_all_fields();

  

  if( is_array( $all_fields ) ){

    $user_email = $all_fields['24'];

    $user_name = $all_fields['24'];

    $first_name = $ninja_forms_processing->get_field_value(20);

    $last_name = $ninja_forms_processing->get_field_value(21);

    $company = $ninja_forms_processing->get_field_value(35);

    $city = $ninja_forms_processing->get_field_value(22);

    $state = $ninja_forms_processing->get_field_value(23);

    $phone = $ninja_forms_processing->get_field_value(25);

    

    $user_id = username_exists( $user_name );

    if ( !$user_id and email_exists($user_email) == false ) {

//            $password = wp_generate_password( $length=12, $include_standard_special_chars=false );

            $password = $all_fields['29'];

//            $user_id = wp_create_user( $user_name, $password, $user_email );

            $userdata = array(

                'user_login'  =>  $user_name,

                'user_url'    =>  '',

                'user_pass'   =>  $password,

                'user_email' => $user_email,

                'display_name' => $first_name.' '.$last_name,

                'first_name' => $first_name,

                'last_name' => $last_name,

                'role' => 'employer',

            );

            $user_id = wp_insert_user( $userdata ) ;

            update_user_meta($user_id, 'billing_first_name', $first_name);

            update_user_meta($user_id, 'billing_last_name', $last_name);

            update_user_meta($user_id, 'billing_company', $company);

            update_user_meta($user_id, 'billing_city', $city);

            update_user_meta($user_id,'billing_state',$state);

            update_user_meta($user_id,'billing_phone',$phone);

            update_user_meta($user_id,'billing_email',$user_email);

            

            //Signin the created user

            $creds = array(

                'user_login'    => $user_name,

                'user_password' => $password,

                'remember'      => false

            );



            $user = wp_signon( $creds, false );

            

    }else{

        $ninja_forms_processing->add_error('user_exists', 'User already exists with this email address');

    }

  

  }

}

/****-- change role---****/

function wpa_120656_convert_paying_customer( $order_id ) {



$order = new WC_Order( $order_id );



if ( $order->user_id > 0 ) {

    update_user_meta( $order->user_id, 'paying_customer', 1 );

    $user = new WP_User( $order->user_id );



    // Remove role

    $user->remove_role( 'customer' ); 



    // Add role

    $user->add_role( 'employer' );

}

}

add_action( 'woocommerce_order_status_completed', 'wpa_120656_convert_paying_customer' );



	

/**

 * Redirect the Continue Shopping URL

 */

function custom_continue_shopping_redirect_url ( $url ) {

	$url = "https://archipro.com/shop"; // Add your link here

	return $url;

}

add_filter('woocommerce_continue_shopping_redirect', 'custom_continue_shopping_redirect_url');



 /* Remove password strength check.

 */

function iconic_remove_password_strength() {

    wp_dequeue_script( 'wc-password-strength-meter' );

}

add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );



include_once get_stylesheet_directory().'/plugin-hooks/job-manager.php';

include_once get_stylesheet_directory().'/plugin-hooks/general-actions.php';

include_once get_stylesheet_directory().'/plugin-hooks/job-manager-resumes.php';



include_once get_stylesheet_directory().'/old-archipro/actions-hooks.php';





add_action( 'init', 'candidates_cpt' );



function candidates_cpt() {



register_post_type( 'candidate-resumes', array(

  'labels' => array(

    'name' => 'Candidate-resumes',

    'singular_name' => 'candidate-resumes',

    'all_items'           =>  'All candidate-resumes',

	'add_new'        => 'Add New candidate-resumes',

   ),

  

  'public' => true,

  'menu_position' => 20,

  'supports' => array( 'editor','title'),

  'has_archive'   => true

));

}

flush_rewrite_rules( false );





add_action( 'init', 'clients_cpt' );



function clients_cpt() {



register_post_type( 'client-resumes', array(

  'labels' => array(

    'name' => 'Client-resumes',

    'singular_name' => 'Client-resumes',

    'all_items'           =>  'All client-resumes',

	'add_new'        => 'Add New client-resumes',

   ),

  

  'public' => true,

  'menu_position' => 22,

  'supports' => array( 'editor','title')

));

}

flush_rewrite_rules( false );











// Add the custom columns to the book post type:

add_filter( 'manage_candidate-resumes_posts_columns', 'set_custom_candidate_columns' );

function set_custom_candidate_columns($columns) {

   // unset( $columns['title'] );

	// unset( $columns['date'] );

    

     $columns['email'] = __( 'Email', 'jobify' );

	 $columns['upload_resume'] = __( 'Resume', 'jobify' );

	 $columns['notes'] = __( 'Notes', 'jobify' );

	 $columns['result'] = __( 'Result', 'jobify' );

	  $columns['result'] = __( 'Result', 'jobify' );

	 



    return $columns;

}



// Add the data to the custom columns for the candidate post type:

add_action( 'manage_candidate-resumes_posts_custom_column' , 'custom_candidate_column', 10, 2 );

function custom_candidate_column( $column, $post_id ) {

    switch ( $column ) {



        case 'email' :

            echo get_post_meta( $post_id , 'email' , true ); 

            break;

			

		case 'upload_resume' :

            $res= get_post_meta( $post_id , 'upload_resume' , true ); ?>

            <a href="<?php the_field('upload_resume'); ?>">Download Resume</a>	<?php

            break;

			

		case 'notes' :

            echo get_post_meta( $post_id , 'notes' , true ); 

            break;	

			

	   case 'result' :

            echo get_post_meta( $post_id , 'results' , true ); 

            break;

			



    }

}







// Add the custom columns to the client post type:

add_filter( 'manage_client-resumes_posts_columns', 'set_custom_clients_columns' );

function set_custom_clients_columns($columns) {

   // unset( $columns['title'] );

   

     $columns['email'] = __( 'Email', 'jobify' );

	 $columns['upload_resume'] = __( 'Resume', 'jobify' );

	 $columns['notes'] = __( 'Notes', 'jobify' );

	 $columns['result'] = __( 'Result', 'jobify' );



    return $columns;

}



// Add the data to the custom columns for the candidate post type:

add_action( 'manage_client-resumes_posts_custom_column' , 'custom_clients_column', 10, 2 );

function custom_clients_column( $column, $post_id ) {

    switch ( $column ) {



        case 'email' :

            echo get_post_meta( $post_id , 'client_email' , true ); 

            break;

			

		case 'upload_resume' :

            $res= get_post_meta( $post_id , 'upload_resume' , true ); ?>

            <a href="<?php the_field('upload_resume'); ?>">Download Resume</a>	<?php 

            break;

			

		case 'notes' :

            echo get_post_meta( $post_id , 'client_notes' , true ); 

            break;	

			

	   case 'result' :

            echo get_post_meta( $post_id , 'client_result' , true ); 

            break;



    }

}



add_filter('gettext','custom_enter_title');



function custom_enter_title( $input ) {



    global $post_type;



    if( is_admin() && 'Enter title here' == $input && 'candidate-resumes' == $post_type )

        return 'Enter Name';



    return $input;

}



add_filter('gettext','custom_enter_title2');



function custom_enter_title2( $input ) {



    global $post_type;



    if( is_admin() && 'Enter title here' == $input && 'client-resumes' == $post_type )

        return 'Enter Name';



    return $input;

}



add_action( 'add_meta_boxes', 'my_meta_box_add' );

function my_meta_box_add()

{

      $types = array( 'candidate-resumes', 'client-resumes' );

      foreach( $types as $type ) {
      add_meta_box( 'my-meta-box-id', 'Type Of Resume', 'my_meta_box_cb', $type , 'normal', 'high' );
}


}



function my_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );

   $check1 = ( $values['candidate_check'][0] == 'on') ? 'on' : '';
   $check2 = ( $values['client_check'][0] == 'on') ? 'on' : '';
	
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <p>
        <input type="checkbox" id="candidate_check" name="candidate_check" <?php checked( $check1, 'on' ); ?> />
        <label for="candidate_check">Candidate</label>
    </p>
	 <p>
        <input type="checkbox" id="client_check" name="client_check" <?php checked( $check2, 'on' ); ?> />
        <label for="client_check">Client</label>
    </p>
    <?php    
}

add_action( 'save_post', 'my_meta_box_save' );
function my_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;
     
    // now we can actually save the data
    $allowed = array( 
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );
     
   // This is purely my personal preference for saving check-boxes
    $chk1 = isset( $_POST['candidate_check'] ) && $_POST['candidate_check'] ? 'on' : 'off';
    update_post_meta( $post_id, 'candidate_check', $chk1 );
	$chk2 = isset( $_POST['client_check'] ) && $_POST['client_check'] ? 'on' : 'off';
    update_post_meta( $post_id, 'client_check', $chk2 );
}

/*===============start city field==============*/
add_filter( 'submit_job_form_fields', 'frontend_add_salary_field' );

function frontend_add_salary_field( $fields ) {
  $fields['job']['job_salary'] = array(
    'label'       => __( 'City', 'job_manager' ),
    'type'        => 'text',
    'required'    => true,
    'placeholder' => 'e.g. "city"',
    'priority'    => 2
  );
  return $fields;
}

add_filter( 'job_manager_job_listing_data_fields', 'admin_add_salary_field' );
function admin_add_salary_field( $fields ) {
  $fields['_job_salary'] = array(
    'label'       => __( 'City', 'job_manager' ),
    'type'        => 'text',
    'placeholder' => 'e.g. "city"',
    'description' => ''
  );
  return $fields;
}

add_action( 'single_job_listing_meta_end', 'display_job_salary_data' );
function display_job_salary_data() {
  global $post;

  $salary = get_post_meta( $post->ID, '_job_salary', true );

  if ( $salary ) {
    echo '<li>' . __( 'City:' ) . esc_html( $salary ) . '</li>';
  }
}








?>
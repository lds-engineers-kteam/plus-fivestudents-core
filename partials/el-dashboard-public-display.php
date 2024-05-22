<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       web-hike.com
 * @since      1.0.0
 *
 * @package    El_Dashboard
 * @subpackage El_Dashboard/public/partials
 */


//add_action('login_redirect', 'redirect_my_page');

/*function add_bootstrap_cdn_attributes( $html, $handle ) {
		if ( 'bootstrap' === $handle ) {
			return str_replace( "media='all'", 'media="all" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"', $html );
		}
		return $html;
	}
add_filter( 'style_loader_tag', 'add_bootstrap_cdn_attributes', 10, 2 );*/

/**
	 * Login form for the public-facing side of the site.
	 *
	 * @since    1.0.0
*/
function login_form( $args = array() ) {
	
	$defaults = array(
		'echo'           => true,
		// Default 'redirect' value takes the user back to the request URI.
		'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'form_id'        => 'loginform',
		'label_username' => __( 'Username or Email Address' ),
		'label_password' => __( 'Password' ),
		'label_remember' => __( 'Remember Me' ),
		'label_log_in'   => __( 'Log In' ),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => true,
		'value_username' => '',
		// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
		'value_remember' => false,
	);
 
	/**
	 * Filters the default login form output arguments.
	 *
	 * @since 3.0.0
	 *
	 * @see wp_login_form()
	 *
	 * @param array $defaults An array of default login form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );
 
	/**
	 * Filters content to display at the top of the login form.
	 *
	 * The filter evaluates just following the opening form tag element.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_top = apply_filters( 'login_form_top', '', $args );
 
	/**
	 * Filters content to display in the middle of the login form.
	 *
	 * The filter evaluates just following the location where the 'login-password'
	 * field is displayed.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_middle = apply_filters( 'login_form_middle', '', $args );
 
	/**
	 * Filters content to display at the bottom of the login form.
	 *
	 * The filter evaluates just preceding the closing form tag element.
	 *
	 * @since 3.0.0
	 *
	 * @param string $content Content to display. Default empty.
	 * @param array  $args    Array of login form arguments.
	 */
	$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );
	$loginfailed = plus_get_request_parameter("loginfailed", 0);
	if($loginfailed){
		$login_form_bottom .='<br/><div class="alert alert-danger">Email ou mot de passe invalide. Veuillez vérifier et recommencer.</div>';
	}
	$form = '
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
<div class="col-sm-4">
</div><div class="col-sm-4 login-form">
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
			' . $login_form_top . '
			<h1>Log in</h1>
			<div class="form-group">
				<input type="text" class="form-control" name="log" id="' . esc_attr( $args['id_username'] ) . '" aria-describedby="Username/Email" placeholder="' . esc_html( $args['label_username'] ) . '" value="' . esc_attr( $args['value_username'] ) . '">								
			</div>
			
			<div class="form-group">	
				<input type="password" class="form-control" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" placeholder="' . esc_html( $args['label_password'] ) . '" value="">
			</div>
			
			' . $login_form_middle . '
			
			<div class="login-submit">
				<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="btn btn-primary form-control" value="' . esc_attr( $args['label_log_in'] ) . '" />
				<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
			</div>
			' . $login_form_bottom . '
		</form></div>
	  <div class="col-sm-4">
</div>
    </div>
  </div>

</section>';
 
	if ( $args['echo'] ) {
		echo $form;
	} else {
		return $form;
	}
	
}



function login_form_check(){
	
	if ( !is_admin() && is_user_logged_in()  && is_page('login') ) {
		if(plus_is_admin_user()){
			return '<script> location.href="'. site_url().'/wp-admin"; </script>';
		} else {
			return '<script> location.href="'. site_url().'/"; </script>';
		}
	}else{
		return login_form();
	}
}
//Login form Shortcode


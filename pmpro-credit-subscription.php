<?php
/*
Plugin Name: PMPro Credit Subscription
Plugin URI: http://www.paidmembershipspro.com/
Description: Allows for crediting users based on subscription amounts
Version: .1
Author: Stranger Studios, Harsha Venkatesh
Author URI: http://www.strangerstudios.com
 * 
 */

add_action( 'show_user_profile', 'pmprocs_credit_subscription_fields' );
add_action( 'edit_user_profile', 'pmprocs_credit_subscription_fields' );
 
function pmprocs_credit_subscription_fields( $user ) { 
	
	global $profileuser;
	
	$role = get_user_role();

	?>
 
        <h3>Other Discounts and Payment Items</h3>
 
        <table class="form-table">
 
                <tr>
                        <th><label for="pmpro_subscription_credit">Credit:</label></th>
 
                        <td>
					    <?php
					    
					    //only admins are permitted to assign discounts.
					    if(($role == 'administrator'))
					    {
					    ?>
                                <input type="text" name="pmpro_subscription_credit" id="pmpro_subscription_credit" value="<?php echo esc_attr(get_user_meta($profileuser->ID, 'pmpro_subscription_credit',true)); ?>" class="regular-text" /><br />
                                <span class="description">Edit discount for user</span>
				   <?php }
				     
						else
						{
							echo get_user_meta($user->ID,'pmpro_subscription_credit',true );
						}
				   ?>
                        </td>
                </tr>
 
        </table>
<?php }
 
add_action( 'personal_options_update', 'pmprocs_credit_subscription_update_fields' );
add_action( 'edit_user_profile_update', 'pmprocs_credit_subscription_update_fields' );
 
function pmprocs_credit_subscription_update_fields($user_id )
{
	$role = get_user_role();
	
	if(!($role == 'administrator'))
	{
		return false;
	}
	
	update_user_meta($user_id, 'pmpro_subscription_credit', $_REQUEST['pmpro_subscription_credit'] );	
}

//Add the credit value to the PMPro member accounts page
function pmprocs_display_on_account_page()
{
	global $current_user;
	get_currentuserinfo();
	echo "Credit: $".get_user_meta($current_user->ID, 'pmpro_subscription_credit', true);	
}

add_action('pmpro_member_links_bottom',  'pmprocs_display_on_account_page');

//The callback function should accept one parameter (an object containing user data and meta values) and return a value to output to the CSV.

function pmprocs_members_list_csv_extra_columns($columns)
{
	$columns["credit"] = "pmprocs_extra_column_credit";
	
	return $columns;
}
add_filter("pmpro_members_list_csv_extra_columns", "pmprocs_members_list_csv_extra_columns", 10, 2);

function pmprocs_extra_column_credit($user)
{
	return $user->metavalues->pmpro_subscription_credit;;
}

/* 
 * Get the user's role. If you call the function without passing parameter $id, 
 * then it will return role name for current user logged in user
 * 
 * Taken from: http://wpeden.com/tipsntuts/how-to-get-logged-in-users-role-in-wordpress/
 */
function get_user_role($id=null)
{
	global $current_user;
	if(!$id) 
	{	
		$id = $current_user->ID;
	}
	
	if ( is_user_logged_in() )
	{
		$user = new WP_User( $id );
		if ( !empty( $user->roles ) && is_array( $user->roles ) ) 
		{
			foreach ( $user->roles as $role )
				return $role;
		}
	}
}
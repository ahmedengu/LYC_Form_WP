<?php

/*
Plugin Name: LYC
Plugin URI: http://github.com/ahmedengu/LYC_Form_WP
Description: Use tag [LYC_FORM], available attributes title, show_mail, from | Between the tags you can put the message with replaceable keywords [CODE], [NAME]  | Example:  [LYC_FORM   title="Email Subject" show_mail="1" from="my@mail.com"]  Dear [NAME],   Your code: [CODE]  [/LYC_FORM]
Version: 1.0
Author: Ahmedengu
Author URI: http://github.com/ahmedengu
*/

function html_form_code() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';


	echo '<p>';
	echo 'FULL Name *<br />';
	echo '<input type="text" name="l_name"  value="' . ( isset( $_POST["l_name"] ) ? esc_attr( $_POST["l_name"] ) : '' ) . '" required/>';
	echo '</p>';


	echo '<p>';
	echo 'Certificate Name  *<br />';
	echo '<i>Name you would like to write it down in your certificate</i><br />';
	echo '<input type="text" name="l_cert"  value="' . ( isset( $_POST["l_cert"] ) ? esc_attr( $_POST["l_cert"] ) : '' ) . '" required/>';
	echo '</p>';


	echo '<p>';
	echo 'Gender  *<br />';
	echo '<input type="radio" name="l_gender" value="Male" ' . ( ( isset( $_POST['l_gender'] ) && $_POST['l_gender'] == 'Male' ) ? 'checked' : '' ) . ' required/>Male<br>';
	echo '<input type="radio" name="l_gender" value="Female" ' . ( ( isset( $_POST['l_gender'] ) && $_POST['l_gender'] == 'Female' ) ? 'checked' : '' ) . ' />Female<br>';
	echo '</p>';

	echo '<p>';
	echo 'Birthday *<br />';
	echo '<span style="display: flex"><input type="number" name="l_mm" placeholder="MM" max="13" min="0" value="' . ( isset( $_POST["l_mm"] ) ? esc_attr( $_POST["l_mm"] ) : '' ) . '" required/>';
	echo '/ <input type="number" name="l_dd" placeholder="DD" max="32" min="0" value="' . ( isset( $_POST["l_dd"] ) ? esc_attr( $_POST["l_dd"] ) : '' ) . '" required/>';
	echo '/ <input type="number" name="l_yyyy" placeholder="YYYY" max="2016" min="1900" value="' . ( isset( $_POST["l_yyyy"] ) ? esc_attr( $_POST["l_yyyy"] ) : '' ) . '" required/></span>';
	echo '</p>';

	echo '<p>';
	echo 'University *<br />';
	echo '<input type="text" name="l_university"  value="' . ( isset( $_POST["l_university"] ) ? esc_attr( $_POST["l_university"] ) : '' ) . '" required/>';
	echo '</p>';

	echo '<p>';
	echo 'College *<br />';
	echo '<input type="text" name="l_college"  value="' . ( isset( $_POST["l_college"] ) ? esc_attr( $_POST["l_college"] ) : '' ) . '" required/>';
	echo '</p>';

	echo '<p>';
	echo 'Department<br />';
	echo '<input type="text" name="l_department"  value="' . ( isset( $_POST["l_department"] ) ? esc_attr( $_POST["l_department"] ) : '' ) . '"/>';
	echo '</p>';

	echo '<p>';
	echo 'Email *<br />';
	echo '<input type="email" name="l_email"  value="' . ( isset( $_POST["l_email"] ) ? esc_attr( $_POST["l_email"] ) : '' ) . '" required/>';
	echo '</p>';


	echo '<p>';
	echo 'Mobile(+20) *<br />';
	echo '<input type="text" name="l_mobile"  value="' . ( isset( $_POST["l_mobile"] ) ? esc_attr( $_POST["l_mobile"] ) : '' ) . '" required/>';
	echo '</p>';

	echo '<p>';
	echo 'Academic Status *<br />';
	echo '<input type="radio" name="l_academic" value="Graduate" ' . ( ( isset( $_POST['l_academic'] ) && $_POST['l_academic'] == 'Graduate' ) ? 'checked' : '' ) . ' required/>Graduate<br>';
	echo '<input type="radio" name="l_academic" value="Undergraduate" ' . ( ( isset( $_POST['l_academic'] ) && $_POST['l_academic'] == 'Undergraduate' ) ? 'checked' : '' ) . ' />Undergraduate<br>';
	echo '<input type="radio" name="l_academic" value="Other" ' . ( ( isset( $_POST['l_academic'] ) && $_POST['l_academic'] == 'Other' ) ? 'checked' : '' ) . ' />Other:<br>';
	echo '<input type="text" name="l_academic_other"  value="' . ( ( isset( $_POST["l_academic_other"] ) ) ? esc_attr( $_POST["l_academic_other"] ) : '' ) . '"/>';
	echo '</p>';

	echo '<p>';
	echo 'Membership status *<br />';
	echo '<input type="radio" name="l_member" value="Member" ' . ( ( isset( $_POST['l_member'] ) && $_POST['l_member'] == 'Member' ) ? 'checked' : '' ) . ' required/>Member<br>';
	echo '<input type="radio" name="l_member" value="Non Member" ' . ( ( isset( $_POST['l_member'] ) && $_POST['l_member'] == 'Non Member' ) ? 'checked' : '' ) . ' />Non Member<br>';
	echo '</p>';

	echo '<p><input type="submit" name="l_submitted" value="Send"/></p>';
	echo '</form>';
}


function process( $atts, $content ) {

	if ( isset( $_POST['l_submitted'] ) ) {
		$errors = validate();
		if ( strlen( $errors ) ) {
			echo '<p style="color: red">';
			echo $errors;
			echo '</p>';

			return true;
		}
		global $wpdb;


		$code   = generateCode( $wpdb );
		$insert = $wpdb->insert( 'lyc_form', array(
			'name'       => $_POST['l_name'],
			'cert'       => $_POST['l_cert'],
			'gender'     => $_POST['l_gender'],
			'birthday'   => $_POST['l_yyyy'] . '-' . $_POST['l_mm'] . '-' . $_POST['l_dd'],
			'university' => $_POST['l_university'],
			'college'    => $_POST['l_college'],
			'department' => $_POST['l_department'],
			'email'      => $_POST['l_email'],
			'mobile'     => $_POST['l_mobile'],
			'academic'   => $_POST['l_academic'] . ( isset( $_POST['l_academic_other'] ) ? ': ' . $_POST['l_academic_other'] : '' ),
			'member'     => $_POST['l_member'],
			'code'       => $code
		) );
		if ( $insert == false ) {
			echo 'Faild to insert';

			return true;
		}

		if ( sendMail( $atts, $content, $code, $_POST['l_email'], $_POST['l_name'] ) ) {
			echo '<div>';
			echo '<p>Registered</p>';
			echo '</div>';

			return false;
		} else {
			echo 'Failed to send the email.';
		}

	}

	return true;

}

/**
 * @param $wpdb
 *
 * @return string
 */
function generateCode( $wpdb ) {
	do {
		$arr  = array( 'L', 'Y', 'C' );
		$rand = array_rand( $arr, 1 );
		$code = codeRand( $wpdb, $arr[ $rand ] );
	} while ( $code == false );

	return $code;
}

/**
 * @param $wpdb
 *
 * @return bool|string
 */
function codeRand( $wpdb, $pre ) {
	$num = $wpdb->get_var( "SELECT COUNT(*) FROM `lyc_form` WHERE `code` LIKE '$pre%'" );
	if ( $num < 999 ) {
		return "$pre" . sprintf( '%03d', ( $num + 1 ) );
	} else {
		return false;
	}
}

function validate() {
	$errors = checkRequired();
	$errors = checkRegex( $errors );
	$errors = checkEmail( $errors );
	$errors = checkMobile( $errors );
	$errors = checkRadio( $errors );
	$errors = checkBirthday( $errors );

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkRegex( $errors ) {
	if ( strlen( $errors ) == 0 ) {
		$regex = array(
			array( "name", "/([a-zA-Z]* ){2}([a-zA-Z]* ?)*/", "Name should be english and at least 3 words" ),
			array( "cert", "/([a-zA-Z]* ){2}([a-zA-Z]* ?)*/", "Certificate should be english and at least 3 words" )
		);
		for ( $i = 0; $i < count( $regex ); $i ++ ) {
			if ( ! ( preg_match( $regex[ $i ][1], $_POST[ 'l_' . $regex[ $i ][0] ] ) ) ) {
				$errors .= $regex[ $i ][2] . ' <br>';
			}
		}
	}

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkBirthday( $errors ) {
	if ( strlen( $errors ) == 0 && ! preg_match( "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['l_yyyy'] . '-' . $_POST['l_mm'] . '-' . $_POST['l_dd'] ) ) {
		$errors = 'Birthday not valid <br>';
	}

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkRadio( $errors ) {
	if ( strlen( $errors ) == 0 ) {
		if ( $_POST['l_gender'] != 'Male' && $_POST['l_gender'] != 'Female' ) {
			$errors .= 'Gender values is Male or Female<br>';
		}
		if ( $_POST['l_academic'] != 'Graduate' && $_POST['l_academic'] != 'Undergraduate' && $_POST['l_academic'] != 'Other' ) {
			$errors .= 'academic values is Graduate , Undergraduate or Other<br>';
		}
		if ( $_POST['l_academic'] == 'Other' && ( ! isset( $_POST["l_academic_other"] ) || strlen( $_POST["l_academic_other"] ) < 2 ) ) {
			$errors .= 'Other value is required<br>';
		}
		if ( ( $_POST['l_academic'] == 'Graduate' || $_POST['l_academic'] == 'Undergraduate' ) && ( isset( $_POST["l_academic_other"] ) && strlen( $_POST["l_academic_other"] ) > 1 ) ) {
			$errors .= 'Choose Graduate , Undergraduate or Other only <br>';
		}
		if ( $_POST['l_member'] != 'Member' && $_POST['l_member'] != 'Non Member' ) {
			$errors .= 'member values is Member or Non Member<br>';
		}
	}

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkMobile( $errors ) {
	if ( strlen( $errors ) == 0 && ! preg_match( "/01(0|1|2)\d{8}/", $_POST['l_mobile'] ) ) {
		$errors = 'Mobile is not valid <br>';
	}

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkEmail( $errors ) {
	if ( strlen( $errors ) == 0 && filter_var( $_POST['l_email'], FILTER_VALIDATE_EMAIL ) == false ) {
		$errors = 'Email is not valid <br>';
	}

	return $errors;
}

/**
 * @param $errors
 *
 * @return string
 */
function checkRequired() {
	$required = [
		'name',
		'cert',
		'gender',
		'mm',
		'dd',
		'yyyy',
		'university',
		'college',
		'email',
		'mobile',
		'academic',
		'member'
	];
	$errors   = "";
	for ( $i = 0; $i < count( $required ); $i ++ ) {
		if ( ! isset( $_POST[ 'l_' . $required[ $i ] ] ) || strlen( $_POST[ 'l_' . $required[ $i ] ] ) < 2 ) {
			$errors .= $required[ $i ] . ' is required <br>';
		}
	}

	return $errors;
}

/**
 * @return bool
 */
function sendMail( $atts, $content, $code, $to, $name ) {
	$content = str_replace( '[CODE]', $code, $content );
	$content = str_replace( '[NAME]', sanitize_text_field( $name ), $content );
	$from    = ( isset( $atts['from'] ) ) ? $atts['from'] : 'lyc@ieeeaast.org';
	$headers = array( "From: LYC <$from>", "Content-Type: text/html; charset=UTF-8" );
	$wp_mail = wp_mail( sanitize_email( $to ), $atts['title'], $content, $headers );

	if ( isset( $atts['show_mail'] ) ) {
		echo $content . '<br>From: ' . $from . '<br>';
	}

	return $wp_mail;
}


function LYC_shortcode( $atts = [], $content = null, $tag = '' ) {
	ob_start();
	if ( process( $atts, $content ) ) {
		html_form_code();
	}

	return ob_get_clean();
}

add_shortcode( 'LYC_FORM', 'LYC_shortcode' );


function LYC_INSTALL() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE `lyc_form` ( `name` TEXT NOT NULL , `cert` TEXT NOT NULL , `gender` TEXT NOT NULL , `birthday` TEXT NOT NULL , `university` TEXT NOT NULL , `college` TEXT NOT NULL , `department` TEXT NOT NULL , `email` TEXT NOT NULL , `mobile` TEXT NOT NULL , `academic` TEXT NOT NULL , `member` TEXT NOT NULL , `id` INT NOT NULL AUTO_INCREMENT , `code` VARCHAR(20) NOT NULL , PRIMARY KEY (`id`)) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}


register_activation_hook( __FILE__, 'LYC_INSTALL' );

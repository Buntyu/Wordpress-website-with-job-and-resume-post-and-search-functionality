<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Unfortunately WordPress supports PHP 5.2 even though
 * everyone should now be using at least PHP 5.3.
 *
 * To be compatible with PHP 5.2 below you will find functions
 * that are required in order to make this plugin work correctly.
 */

if( version_compare( phpversion(), '5.4', '<' ) ) {
	add_action( 'admin_notices', 'jmfe_old_php_notice' );
	add_action( 'admin_init', 'jmfe_old_php_notice_ignore' );
}

// PHP 5.2 does not have array_replace_recursive
if ( ! function_exists( 'array_replace_recursive' ) ) {

	function array_replace_recursive( $array, $array1 ) {

		if ( ! function_exists( 'compat_array_recurse' ) ) {

			function compat_array_recurse( $array, $array1 ) {

				foreach ( $array1 as $key => $value ) {
					// create new key in $array, if it is empty or not an array
					if ( ! isset( $array[ $key ] ) || ( isset( $array[ $key ] ) && ! is_array( $array[ $key ] ) ) ) {
						$array[ $key ] = array();
					}

					// overwrite the value in the base array
					if ( is_array( $value ) ) {
						$value = compat_array_recurse( $array[ $key ], $value );
					}
					$array[ $key ] = $value;
				}

				return $array;
			}

		}

		// handle the arguments, merge one by one
		$args  = func_get_args();
		$array = $args[ 0 ];
		if ( ! is_array( $array ) ) {
			return $array;
		}
		for ( $i = 1; $i < count( $args ); $i ++ ) {
			if ( is_array( $args[ $i ] ) ) {
				$array = compat_array_recurse( $array, $args[ $i ] );
			}
		}

		return $array;
	}
}

if ( ! function_exists( 'jmfe_old_php_notice' ) ) {

	function jmfe_old_php_notice() {

		$user_id = get_current_user_id();

		if ( ! get_option( 'jmfe_old_php_notice' ) ) {

			echo '<div class="error"><p>';
			$notice = "";
			// Server running PHP 5.2
			if( version_compare( phpversion(), '5.3', '<' ) ){
				$notice = sprintf( __( 'Your server is using a <strong>VERY OLD, unsupported, and no longer maintained</strong> version of PHP, version 5.2 or older. <a href="%1$s" target="_blank">EOL (End of Life)</a> for PHP 5.2 was about <strong>%2$s ago</strong>!! This means there may be bugs, and security vulnerabilities that have not, and will never be patched for this version of PHP!<br /><br />It is <strong>strongly</strong> recommended that you contact your web hosting provider and request to upgrade to PHP 5.4 or newer ... or <a href="%3$s">Hide this Notice Forever!</a> (but don\'t say I didn\'t warn you)<br/><br /><a href="%4$s" target="_blank">Contact me</a> for an exclusive sMyles Plugins customer promo code discount for any shared <strong>SSD (Solid State Drive)</strong> hosting packages!  Data centers in Florida USA, Arizona USA, Montreal Canada, and France.  Your site will run faster than it ever has, or your money back!', 'wp-job-manager-field-editor' ), 'http://php.net/eol.php', human_time_diff( '1294272000', current_time( 'timestamp' ) ), '?jmfe_old_php_notice=0', 'https://plugins.smyl.es/contact' );
			}
			// Server running PHP 5.3
			if( version_compare( phpversion(), '5.4', '<' ) && version_compare( phpversion(), '5.2', '>' ) ) {
				$notice = sprintf( __( 'Your server is using an <strong>OLD, unsupported, and no longer maintained</strong> version of PHP, version 5.3. <a href="%1$s" target="_blank">EOL (End of Life)</a> for PHP 5.3 was about <strong>%2$s ago</strong>!! This means there may be bugs, and security vulnerabilities that have not, and will never be patched for this version of PHP!<br /><br />It is <strong>strongly</strong> recommended that you contact your web hosting provider and request to upgrade to PHP 5.4 or newer ... or <a href="%3$s">Hide this Notice Forever!</a> (but don\'t say I didn\'t warn you)<br/><br /><a href="%4$s" target="_blank">Contact me</a> for an exclusive sMyles Plugins customer promo code discount for any shared <strong>SSD (Solid State Drive)</strong> hosting packages!  Data centers in Florida USA, Arizona USA, Montreal Canada, and France.  Your site will run faster than it ever has, or your money back!', 'wp-job-manager-field-editor' ), 'http://php.net/eol.php', human_time_diff( '1406865600', current_time( 'timestamp' ) ), '?jmfe_old_php_notice=0', 'https://plugins.smyl.es/contact' );
			}
			echo "{$notice}</p></div>";

		}

	}

}

if ( ! function_exists( 'jmfe_old_php_notice_ignore' ) ) {

	function jmfe_old_php_notice_ignore() {

		if ( isset( $_GET[ 'jmfe_old_php_notice' ] ) && '0' == $_GET[ 'jmfe_old_php_notice' ] ) {
			add_option( 'jmfe_old_php_notice', 'true' );
		}

	}

}

if ( ! function_exists( 'str_getcsv' ) ) {

	function str_getcsv( $input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n' ) {

		if ( is_string( $input ) && ! empty( $input ) ) {
			$output = array();
			$tmp    = preg_split( "/" . $eol . "/", $input );
			if ( is_array( $tmp ) && ! empty( $tmp ) ) {
				while ( list( $line_num, $line ) = each( $tmp ) ) {
					if ( preg_match( "/" . $escape . $enclosure . "/", $line ) ) {
						while ( $strlen = strlen( $line ) ) {
							$pos_delimiter       = strpos( $line, $delimiter );
							$pos_enclosure_start = strpos( $line, $enclosure );
							if (
								is_int( $pos_delimiter ) && is_int( $pos_enclosure_start )
								&& ( $pos_enclosure_start < $pos_delimiter )
							) {
								$enclosed_str           = substr( $line, 1 );
								$pos_enclosure_end      = strpos( $enclosed_str, $enclosure );
								$enclosed_str           = substr( $enclosed_str, 0, $pos_enclosure_end );
								$output[ $line_num ][ ] = $enclosed_str;
								$offset                 = $pos_enclosure_end + 3;
							} else {
								if ( empty( $pos_delimiter ) && empty( $pos_enclosure_start ) ) {
									$output[ $line_num ][ ] = substr( $line, 0 );
									$offset                 = strlen( $line );
								} else {
									$output[ $line_num ][ ] = substr( $line, 0, $pos_delimiter );
									$offset                 = (
										! empty( $pos_enclosure_start )
										&& ( $pos_enclosure_start < $pos_delimiter )
									)
										? $pos_enclosure_start
										: $pos_delimiter + 1;
								}
							}
							$line = substr( $line, $offset );
						}
					} else {
						$line = preg_split( "/" . $delimiter . "/", $line );

						/*
						 * Validating against pesky extra line breaks creating false rows.
						 */
						if ( is_array( $line ) && ! empty( $line[ 0 ] ) ) {
							$output[ $line_num ] = $line;
						}
					}
				}

				return $output;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

}

if( ! function_exists( 'hex2bin' ) ) {
	function hex2bin( $str ) {

		$sbin = "";
		$len  = strlen( $str );
		for( $i = 0; $i < $len; $i += 2 ) {
			$sbin .= pack( "H*", substr( $str, $i, 2 ) );
		}

		return $sbin;
	}
}

if( ! function_exists( 'date_create_from_format' ) ) {

	function date_create_from_format( $dformat, $dvalue ) {

		$schedule        = $dvalue;
		$schedule_format = str_replace( array('Y', 'm', 'd', 'H', 'i', 'a'), array('%Y', '%m', '%d', '%I', '%M', '%p'), $dformat );
		// %Y, %m and %d correspond to date()'s Y m and d.
		// %I corresponds to H, %M to i and %p to a
		$ugly         = strptime( $schedule, $schedule_format );
		$ymd          = sprintf(
		// This is a format string that takes six total decimal
		// arguments, then left-pads them with zeros to either
		// 4 or 2 characters, as needed
				'%04d-%02d-%02d %02d:%02d:%02d',
				$ugly['tm_year'] + 1900,  // This will be "111", so we need to add 1900.
				$ugly['tm_mon'] + 1,      // This will be the month minus one, so we add one.
				$ugly['tm_mday'],
				$ugly['tm_hour'],
				$ugly['tm_min'],
				$ugly['tm_sec']
		);
		$new_schedule = new DateTime( $ymd );

		return $new_schedule;

	}

}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey (http://benramsey.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */

if( ! function_exists( 'array_column' ) ) {
	/**
	 * Returns the values from a single column of the input array, identified by
	 * the $columnKey.
	 *
	 * Optionally, you may provide an $indexKey to index the values in the returned
	 * array by the values from the $indexKey column in the input array.
	 *
	 * @param array $input     A multi-dimensional array (record set) from which to pull
	 *                         a column of values.
	 * @param mixed $columnKey The column of values to return. This value may be the
	 *                         integer key of the column you wish to retrieve, or it
	 *                         may be the string key name for an associative array.
	 * @param mixed $indexKey  (Optional.) The column to use as the index/keys for
	 *                         the returned array. This value may be the integer key
	 *                         of the column, or it may be the string key name.
	 *
	 * @return array
	 */
	function array_column( $input = NULL, $columnKey = NULL, $indexKey = NULL ) {

		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc   = func_num_args();
		$params = func_get_args();

		if( $argc < 2 ) {
			trigger_error( "array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING );

			return NULL;
		}

		if( ! is_array( $params[0] ) ) {
			trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype( $params[0] ) . ' given',
				E_USER_WARNING
			);

			return NULL;
		}

		if( ! is_int( $params[1] )
		    && ! is_float( $params[1] )
		    && ! is_string( $params[1] )
		    && $params[1] !== NULL
		    && ! (is_object( $params[1] ) && method_exists( $params[1], '__toString' ))
		) {
			trigger_error( 'array_column(): The column key should be either a string or an integer', E_USER_WARNING );

			return FALSE;
		}

		if( isset($params[2])
		    && ! is_int( $params[2] )
		    && ! is_float( $params[2] )
		    && ! is_string( $params[2] )
		    && ! (is_object( $params[2] ) && method_exists( $params[2], '__toString' ))
		) {
			trigger_error( 'array_column(): The index key should be either a string or an integer', E_USER_WARNING );

			return FALSE;
		}

		$paramsInput     = $params[0];
		$paramsColumnKey = ($params[1] !== NULL) ? (string) $params[1] : NULL;

		$paramsIndexKey = NULL;
		if( isset($params[2]) ) {
			if( is_float( $params[2] ) || is_int( $params[2] ) ) {
				$paramsIndexKey = (int) $params[2];
			} else {
				$paramsIndexKey = (string) $params[2];
			}
		}

		$resultArray = array();

		foreach( $paramsInput as $row ) {
			$key    = $value = NULL;
			$keySet = $valueSet = FALSE;

			if( $paramsIndexKey !== NULL && array_key_exists( $paramsIndexKey, $row ) ) {
				$keySet = TRUE;
				$key    = (string) $row[ $paramsIndexKey ];
			}

			if( $paramsColumnKey === NULL ) {
				$valueSet = TRUE;
				$value    = $row;
			} elseif( is_array( $row ) && array_key_exists( $paramsColumnKey, $row ) ) {
				$valueSet = TRUE;
				$value    = $row[ $paramsColumnKey ];
			}

			if( $valueSet ) {
				if( $keySet ) {
					$resultArray[ $key ] = $value;
				} else {
					$resultArray[] = $value;
				}
			}

		}

		return $resultArray;
	}

}
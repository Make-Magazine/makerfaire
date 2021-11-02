<?php
/**
 * Class SB_Instagram_Connected_Account
 *
 * Used for parsing data from connected accounts and getting
 * data related to an account using searches.
 *
 * @since 5.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SB_Instagram_Connected_Account {

	/**
	 * @var array
	 *
	 * @since 5.10
	 */
	public $account;

	/**
	 * @since 5.12.4
	 */
	const ALGORITHM = 'AES-256-CBC';

	public function __construct( $search_term_or_account, $search_type = 'user' ) {
		if ( is_array( $search_term_or_account ) ) {
			$this->account = $search_term_or_account;
		} else {
			$this->account = SB_Instagram_Connected_Account::lookup( $search_term_or_account, $search_type );
		}
	}

	/**
	 * @return array
	 *
	 * @since 5.10
	 */
	public function get_account_data() {
		return $this->account;
	}

	/**
	 * Returns data for a connected account based on a search by term
	 * or type (business, user)
	 *
	 * @param $search_term string
	 * @param string $search_type string
	 *
	 * @return array|bool|mixed
	 *
	 * @since 5.10
	 */
	public static function lookup( $search_term, $search_type = 'user' ) {
		$options = sbi_get_database_settings();
		$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

		if ( is_array( $search_term ) ) {
			return false;
		}

		if ( $search_type === 'business' ) {
			if ( $search_term === '' ) {
				$business_accounts = array();
				$access_tokens_found = array();
				foreach ( $connected_accounts as $connected_account ) {
					if ( isset( $connected_account['type'] )
					     && $connected_account['type'] === 'business'
					     && ! in_array( $connected_account['access_token'], $access_tokens_found, true ) ) {
						$business_accounts[] = $connected_account;
						$access_tokens_found[] = $connected_account['access_token'];
					}
				}
				return $business_accounts;
			} else {
				foreach ( $connected_accounts as $connected_account ) {
					if ( isset( $connected_account['type'] )
					     && $connected_account['type'] === 'business' ) {
						return $connected_account;
					}
				}


			}

		} else {
			if ( isset( $connected_accounts[ $search_term ] ) ) {
				return $connected_accounts[ $search_term ];
			} else {
				foreach ( $connected_accounts as $connected_account ) {
					if ( strpos( $connected_account['access_token'], '.' ) === false ) {
						if ( strtolower( $connected_account['username'] ) === trim( strtolower( $search_term ) ) ) {
							return $connected_account;
						} elseif ( $connected_account['access_token'] === trim( strtolower( $search_term ) )  ) {
							return $connected_account;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Generates a local version of the avatar image file
	 * and stores related information for easy retrieval and
	 * management
	 *
	 * @param $username string
	 * @param $profile_picture string
	 *
	 * @return bool
	 *
	 * @since 5.10
	 */
	public static function create_local_avatar( $username, $profile_picture ) {
		$options = sbi_get_database_settings();
		if ( !$options['sb_instagram_disable_resize'] ) {
			if ( sbi_create_local_avatar( $username, $profile_picture ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Encrypt string (access token) with an included initialization vector
	 *
	 * @param string $access_token
	 * @param string $initialization_vector
	 *
	 * @return string
	 *
	 * @since 5.12.4
	 */
	public static function encrypt_access_token( $access_token ) {
		$encryption = new SB_Instagram_Data_Encryption();

		return $encryption->encrypt( $access_token );
	}

	/**
	 * Attempt to decrypt access token
	 *
	 * @param string $access_token
	 * @param string $initialization_vector
	 *
	 * @return string
	 *
	 * @since 5.12.4
	 */
	public static function decrypt_access_token( $access_token ) {
		$encryption = new SB_Instagram_Data_Encryption();

		return $encryption->decrypt( $access_token );
	}

	/**
	 * Encrypt access tokens in a connected account and return it
	 *
	 * @param array $connected_account
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @since 5.12.4
	 */
	public static function encrypt_connected_account_tokens( $connected_account ) {
		if ( ! self::decrypt_access_token( $connected_account['access_token'] ) ) {
			$encrypted_access_token = self::encrypt_access_token( $connected_account['access_token'] );
			$connected_account['access_token'] = $encrypted_access_token;

			if ( isset( $connected_account['page_access_token'] ) ) {
				$encrypted_page_access_token = self::encrypt_access_token( $connected_account['page_access_token'] );

				$connected_account['page_access_token'] = $encrypted_page_access_token;
			}

			$connected_account['wp_user'] = get_current_user_id();
		}

		return $connected_account;
	}

	/**
	 * Encrypt all access tokens in all connected accounts. Used for
	 * a one-time update.
	 *
	 * @return array
	 * @throws Exception
	 *
	 * @since 5.12.4
	 */
    public static function encrypt_all_access_tokens() {
	    $options = sbi_get_database_settings();
	    $connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	    $updated = array();
	    foreach ( $connected_accounts as $key => $connected_account ) {
		    $updated[ $key ] = $connected_account;

		    if ( ! self::decrypt_access_token( $connected_account['access_token'] ) ) {
			    $encrypted_access_token = self::encrypt_access_token( $connected_account['access_token'] );
			    $updated[ $key ]['access_token'] = $encrypted_access_token;

			    if ( isset( $connected_account['page_access_token'] ) ) {
				    $encrypted_page_access_token = self::encrypt_access_token( $connected_account['page_access_token'] );

				    $updated[ $key ]['page_access_token'] = $encrypted_page_access_token;
			    }

			    $updated[ $key ]['wp_user'] = get_current_user_id();
		    }
	    }

	    $options['connected_accounts'] = $updated;

	    update_option( 'sb_instagram_settings', $options );

	    return $connected_accounts;
    }

    public static function get_all_connected_accounts() {
	    $options = sbi_get_database_settings();
	    $connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	    return $connected_accounts;
    }

	public static function update_connected_accounts( $connected_accounts ) {
		$options = sbi_get_database_settings();

		$options['connected_accounts'] = $connected_accounts;

		update_option( 'sb_instagram_settings', $options );

		return $connected_accounts;
	}
}
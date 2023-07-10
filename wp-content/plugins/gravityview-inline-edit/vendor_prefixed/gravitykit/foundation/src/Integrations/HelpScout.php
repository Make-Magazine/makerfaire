<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 11-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Integrations;

use GravityKit\GravityEdit\Foundation\Core as FoundationCore;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityEdit\Foundation\Licenses\Framework as LicensesFramework;
use GravityKit\GravityEdit\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;

class HelpScout {
	const HS_BEACON_KEY = 'e899c3af-bfb9-479a-9579-38e758664fb7';

	const HASH_KEY = 't4MTtLRuIH74gBuQ/2OVpj0NscYAjdg9nY1rw67PiT8=';

	/**
	 * @since 1.0.0
	 *
	 * @var TrustedLogin Class instance.
	 */
	private static $_instance;

	private function __construct() {
		add_filter( 'gk/foundation/inline-scripts', [ $this, 'enqueue_beacon_script' ] );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return TrustedLogin
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Outputs inline JS code that initializes the HS beacon.
	 *
	 * @since 1.0.0
	 *
	 * @param array $scripts
	 *
	 * @return array
	 */
	public function enqueue_beacon_script( $scripts ) {
		if ( ! $this->should_display_beacon() ) {
			return $scripts;
		}

		$beacon_configuration = json_encode( $this->get_beacon_configuration() );

		$js = <<<JS
!function(e,t,n){function a(){var e=t.getElementsByTagName('script')[0],n=t.createElement('script');n.type='text/javascript',n.async=!0,n.src='https://beacon-v2.helpscout.net',e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],'complete'===t.readyState)return a();e.attachEvent?e.attachEvent('onload',a):e.addEventListener('load',a,!1)}(window,document,window.Beacon||function(){});
var beaconConfig = {$beacon_configuration}; for (var param in beaconConfig) { window.Beacon( param, beaconConfig[param]); }
JS;

		$scripts[] = [
			'script' => $js,
		];

		return $scripts;
	}

	/**
	 * Determines if the HS beacon should be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function should_display_beacon() {
		$is_enabled = SettingsFramework::get_instance()->get_plugin_setting( FoundationCore::ID, 'support_port' );

		if ( ! $is_enabled ) {
			return false;
		}

		/**
		 * @TODO: Possibly implement additional checks as it's done in GravityView:
		 *
		 * If the user doesn't have the `gravityview_support_port` capability, returns false; then
		 * If global setting is "hide", returns false; then
		 * If user preference is not set, return global setting; then
		 * If user preference is set, return that setting.
		 */

		$page      = Arr::get( $_REQUEST, 'page' );
		$post_type = Arr::get( $_REQUEST, 'post_type' );

		$display_beacon = false;

		if ( in_array( $page, [ SettingsFramework::ID, LicensesFramework::ID ], true ) ) {
			$display_beacon = true;
		}

		/**
		 * @filter `gk/foundation/integrations/helpscout/display` Toggles whether HS beacon should be displayed. Return "true" to short-circuit all other checks.
		 *
		 * @since  1.0.0
		 *
		 * @param bool        $display_beacon Whether to display the beacon.
		 * @param string|null $page           Current page ($_REQUEST['page']).
		 * @param string|null $post_type      Current post type ($_REQUEST['post_type']).
		 */
		return apply_filters( 'gk/foundation/integrations/helpscout/display', $display_beacon, $page, $post_type );
	}

	/**
	 * Returns HS beacon configuration options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_beacon_configuration() {
		$foundation_settings = SettingsFramework::get_instance()->get_plugin_settings( FoundationCore::ID );

		$current_user = wp_get_current_user();

		$beacon_configuration = [
			'init'         => self::HS_BEACON_KEY,
			'config'       => [
				'color'       => '#4d9bbe',
				'poweredBy'   => false,
				'docsEnabled' => true,
				'topArticles' => true,
				'iconImage'   => 'question',
				'zIndex'      => 10000 + 10, // Above #adminmenuwrap, which is 9990 and modal content, which is 10000 + 1.
				'labels'      => $this->get_beacon_label_translations(),
				'messaging'   => [
					'chatEnabled' => ( ! is_multisite() && current_user_can( 'manage_options' ) ) ||
					                 ( is_multisite() && current_user_can( 'manage_network_options' ) && CoreHelpers::is_network_admin() ),
				],
			],
			// Each session data parameter is limited to 10k characters.
			'session-data' => [
				'WP Version'  => mb_substr( get_bloginfo( 'version', 'display' ), 0, 10000 ),
				'PHP Version' => mb_substr( PHP_VERSION . ' on ' . esc_html( Arr::get( $_SERVER, 'SERVER_SOFTWARE' ) ), 0, 10000 ),
			],
			'prefill'      => [
				'name'  => mb_substr( $current_user->display_name, 0, 80 ),
				'email' => mb_substr( $current_user->user_email, 0, 80 ),
			],
			'identify'     => [
				'email'                 => mb_substr( $current_user->user_email, 0, 80 ),
				'name'                  => mb_substr( $current_user->display_name, 0, 80 ),
				'signature'             => hash_hmac( 'sha256', mb_substr( $current_user->user_email, 0, 80 ), self::HASH_KEY ),
				'affiliate_id'          => mb_substr( Arr::get( $foundation_settings, 'affiliate_id', '' ) ?: '', 0, 255 ),
				'is_super_admin'        => is_super_admin(),
				'alt_emails'            => mb_substr( sprintf( 'Admin: %s / GV Support: %s', get_bloginfo( 'admin_email' ), Arr::get( $foundation_settings, 'support_email' ) ), 0, 255 ),
				'wordpress_version'     => mb_substr( get_bloginfo( 'version', 'display' ), 0, 255 ),
				'php_version'           => mb_substr( PHP_VERSION . ' on ' . esc_html( $_SERVER['SERVER_SOFTWARE'] ), 0, 255 ),
				'no_conflict_mode'      => Arr::get( $foundation_settings, 'no_conflict_mode' ) ? 'Disabled' : 'Enabled',
				'gravityview_version'   => mb_substr( class_exists( '\GV\Plugin' ) ? \GV\Plugin::$version : 'Not Installed', 0, 255 ),
				'gravity_forms_version' => mb_substr( class_exists( '\GFForms' ) ? \GFForms::$version : 'Not Installed', 0, 255 ),
				'locale'                => get_user_locale(),
				'is_support_contact'    => ( $current_user->user_email === Arr::get( $foundation_settings, 'support_email' ) ),
			],
			'suggest'      => [],
		];

		/**
		 * @filter `gk/foundation/integrations/helpscout/configuration` Modified HS beacon configuration.
		 *
		 * @since  1.0.0
		 *
		 * @param array $beacon_configuration Beacon configuration.
		 */
		return apply_filters( 'gk/foundation/integrations/helpscout/configuration', $beacon_configuration );
	}

	/**
	 * Returns translated labels. These combines default HS translations with custom GravityView translations.
	 *
	 * @since 1.0.0
	 *
	 * @see   https://github.com/gravityview/GravityView/blob/3a999a0ce8d96cd63386c575fd3199730ded6491/includes/admin/class-gravityview-support-port.php#L137-L156
	 * @see   https://developer.helpscout.com/beacon-2/web/javascript-api/#translate-options
	 *
	 * @return array
	 */
	public function get_beacon_label_translations() {
		$translations = [
			// Answers
			'suggestedForYou'                 => __( 'Instant Answers', 'gk-gravityedit' ),
			'getInTouch'                      => __( 'Get in touch', 'gk-gravityedit' ),
			'searchLabel'                     => __( 'Search GravityKit Docs', 'gk-gravityedit' ),
			'tryAgain'                        => __( 'Try again', 'gk-gravityedit' ),
			'defaultMessageErrorText'         => __( 'There was a problem sending your message. Please try again in a moment.', 'gk-gravityedit' ),
			'beaconButtonClose'               => __( 'Close', 'gk-gravityedit' ),
			'beaconButtonChatMinimize'        => __( 'Minimize chat', 'gk-gravityedit' ),
			'beaconButtonChatOpen'            => __( 'Open chat', 'gk-gravityedit' ),
			// Ask
			'answer'                          => __( 'Answer', 'gk-gravityedit' ),
			'ask'                             => __( 'Ask', 'gk-gravityedit' ),
			'messageButtonLabel'              => __( 'Email', 'gk-gravityedit' ),
			'noTimeToWaitAround'              => __( 'No time to wait around? We usually respond within a few hours', 'gk-gravityedit' ),
			'chatButtonLabel'                 => __( 'Chat', 'gk-gravityedit' ),
			'chatButtonDescription'           => __( 'We\'re online right now, talk with our team in real-time', 'gk-gravityedit' ),
			'wereHereToHelp'                  => __( 'Start a conversation', 'gk-gravityedit' ),
			'whatMethodWorks'                 => __( 'What channel do you prefer?', 'gk-gravityedit' ),
			'previousMessages '               => __( 'Previous Conversations', 'gk-gravityedit' ),
			// Search Results
			'cantFindAnswer'                  => __( 'Can\'t find the answer?', 'gk-gravityedit' ),
			'relatedArticles'                 => __( 'Related Articles', 'gk-gravityedit' ),
			'nothingFound'                    => __( 'Hmm…', 'gk-gravityedit' ),
			'docsSearchEmptyText'             => __( 'We couldn\'t find any articles that match your search.', 'gk-gravityedit' ),
			'tryBroaderTerm'                  => __( 'Try a broader term, or', 'gk-gravityedit' ),
			'docsArticleErrorText'            => __( 'There was a problem loading this article. Please double-check your internet connection and try again.', 'gk-gravityedit' ),
			'docsSearchErrorText'             => __( 'Your search timed out. Please double-check your internet connection and try again.', 'gk-gravityedit' ),
			'escalationQuestionFeedback'      => __( 'Did this answer your question?', 'gk-gravityedit' ),
			'escalationQuestionFeedbackNo'    => __( 'No', 'gk-gravityedit' ),
			'escalationQuestionFeedbackYes'   => __( 'Yes', 'gk-gravityedit' ),
			'escalationSearchText'            => __( 'Browse our help docs for an answer to your question', 'gk-gravityedit' ),
			'escalationSearchTitle'           => __( 'Keep searching', 'gk-gravityedit' ),
			'escalationTalkText'              => __( 'Talk with a friendly member of our support team', 'gk-gravityedit' ),
			'escalationTalkTitle'             => __( 'Talk to us', 'gk-gravityedit' ),
			'escalationThanksFeedback'        => __( 'Thanks for the feedback', 'gk-gravityedit' ),
			'escalationWhatNext'              => __( 'What next?', 'gk-gravityedit' ),
			// Send A Message
			'sendAMessage'                    => __( 'Send a message', 'gk-gravityedit' ),
			'firstAFewQuestions'              => __( 'Let\'s begin with a few questions', 'gk-gravityedit' ),
			'howCanWeHelp'                    => __( 'How can we help?', 'gk-gravityedit' ),
			'responseTime'                    => __( 'We usually respond in a few hours', 'gk-gravityedit' ),
			'history'                         => __( 'History', 'gk-gravityedit' ),
			'uploadAnImage'                   => __( 'Upload an image', 'gk-gravityedit' ),
			'attachAFile'                     => __( 'Attach a screenshot or file', 'gk-gravityedit' ),
			'continueEditing'                 => __( 'Continue writing…', 'gk-gravityedit' ),
			'lastUpdated'                     => __( 'Last updated', 'gk-gravityedit' ),
			'you'                             => __( 'You', 'gk-gravityedit' ),
			'nameLabel'                       => __( 'Your Name', 'gk-gravityedit' ),
			'subjectLabel'                    => __( 'Subject', 'gk-gravityedit' ),
			'emailLabel'                      => __( 'Email address', 'gk-gravityedit' ),
			'messageLabel'                    => __( 'How can we help?', 'gk-gravityedit' ),
			'messageSubmitLabel'              => __( 'Send a message', 'gk-gravityedit' ),
			'next'                            => __( 'Next', 'gk-gravityedit' ),
			'weAreOnIt'                       => __( 'Message sent!', 'gk-gravityedit' ),
			'messageConfirmationText'         => __( 'You\'ll receive an email reply within a few hours.', 'gk-gravityedit' ),
			'viewAndUpdateMessage'            => __( 'You can view and update your message in', 'gk-gravityedit' ),
			'mayNotBeEmpty'                   => __( 'May not be empty', 'gk-gravityedit' ),
			'customFieldsValidationLabel'     => __( 'Please complete all fields', 'gk-gravityedit' ),
			'emailValidationLabel'            => __( 'Please enter a valid email address', 'gk-gravityedit' ),
			'attachmentErrorText'             => __( 'There was a problem uploading your attachment. Please try again in a moment.', 'gk-gravityedit' ),
			'attachmentSizeErrorText'         => strtr(
				_x( 'The maximum file size is [size]', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[size]' => size_format( 10485760 ) ] // 10MB in bytes.
			),
			//Previous messages
			'addReply'                        => __( 'Add a reply', 'gk-gravityedit' ),
			'addYourMessageHere'              => __( 'Add your message here…', 'gk-gravityedit' ),
			'sendMessage'                     => __( 'Send message', 'gk-gravityedit' ),
			'received'                        => __( 'Received', 'gk-gravityedit' ),
			'waitingForAnAnswer'              => __( 'Waiting for an answer', 'gk-gravityedit' ),
			'previousMessageErrorText'        => __( 'There was a problem retrieving your previous messages. Please double-check your Internet connection and try again.', 'gk-gravityedit' ),
			'justNow'                         => __( 'Just Now', 'gk-gravityedit' ),
			// Chat
			'chatHeadingTitle'                => __( 'Chat with our team', 'gk-gravityedit' ),
			'chatHeadingSublabel'             => __( 'We\'ll be with you soon', 'gk-gravityedit' ),
			'chatEndCalloutHeading'           => __( 'All done!', 'gk-gravityedit' ),
			'chatEndCalloutMessage'           => __( 'A copy of this conversation will land in your inbox shortly.', 'gk-gravityedit' ),
			'chatEndCalloutLink'              => __( 'Return home', 'gk-gravityedit' ),
			'chatEndUnassignedCalloutHeading' => __( 'Sorry about that', 'gk-gravityedit' ),
			'chatEndUnassignedCalloutMessage' => __( 'It looks like nobody made it to your chat. We\'ll send you an email response as soon as possible.', 'gk-gravityedit' ),
			'chatEndWaitingCustomerHeading'   => __( 'Sorry about that', 'gk-gravityedit' ),
			'chatEndWaitingCustomerMessage'   => __( 'Your question has been added to our email queue and we\'ll get back to you shortly.', 'gk-gravityedit' ),
			'ending'                          => __( 'Ending…', 'gk-gravityedit' ),
			'endChat'                         => __( 'End chat', 'gk-gravityedit' ),
			'chatEnded'                       => strtr(
				_x( '[name] ended the chat', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space before the translation.
			),
			'chatConnected'                   => strtr(
				_x( 'Connected to [name]', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space after the translation.
			),
			'chatbotName'                     => __( 'Help Bot', 'gk-gravityedit' ),
			'chatbotGreet'                    => __( 'Hi there! You can begin by asking your question below. Someone will be with you shortly.', 'gk-gravityedit' ),
			'chatbotPromptEmail'              => __( 'Got it. Real quick, what\'s your email address? We\'ll use it for any follow-up messages.', 'gk-gravityedit' ),
			'chatbotConfirmationMessage'      => __( 'Thanks! Someone from our team will jump into the chat soon.', 'gk-gravityedit' ),
			'chatbotGenericErrorMessage'      => __( 'Something went wrong sending your message, please try again in a few minutes.', 'gk-gravityedit' ),
			'chatbotInactivityPrompt'         => __( 'Since the chat has gone idle, I\'ll end this chat in a few minutes.', 'gk-gravityedit' ),
			'chatbotInvalidEmailMessage'      => __( 'Looks like you\'ve entered an invalid email address. Want to try again?', 'gk-gravityedit' ),
			'chatbotAgentDisconnectedMessage' => strtr(
				_x( '[name] has disconnected from the chat. It\'s possible they lost their internet connection, so I\'m looking for someone else to take over. Sorry for the delay!', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space before the translation.
			),
			'chatAvailabilityChangeMessage'   => __( 'Our team\'s availability has changed and there\'s no longer anyone available to chat. Send us a message instead and we\'ll get back to you.', 'gk-gravityedit' ),
			// Transcript Email
			'emailHeading'                    => strtr(
				_x( 'Today\'s chat with [name]', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space after the translation.
			),
			'emailGreeting'                   => strtr(
				_x( 'Hey [name]', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space after the translation.
			),
			'emailCopyOfDiscussion'           => __( 'Here\'s a copy of your discussion', 'gk-gravityedit' ),
			'emailContinueConversation'       => __( 'If you\'ve got any other questions, feel free to hit reply and continue the conversation.', 'gk-gravityedit' ),
			'emailJoinedLineItem'             => strtr( _x( '[name] joined the chat', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space before the translation.
			),
			'emailEndedLineItem'              => strtr(
				_x( '[name] ended the chat', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[name]' => '' ] // HS has a blank space before the translation.
			),
			'emailYou'                        => __( 'You', 'gk-gravityedit' ),
		];

		foreach ( $translations as &$translation ) {
			$translation = mb_substr( $translation, 0, 160 ); // Maximum characters for a translation value.
		}

		return $translations;
	}
}

<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor my Subscription Widget
 *
 * Elementor widget that lists the omeda subscriptions
 *
 * @since 1.0.0
 */
class Elementor_mySubscription_Widget extends \Elementor\Widget_Base {
	private $noActive;
	private $digitalAccess;
	const VERSION = '1.3.2';

	public function __construct($data = array(), $args = null) {
		parent::__construct($data, $args);

		//JS for form submission		
		/*
		wp_enqueue_script('make-omeda-script', plugin_dir_url(dirname(__FILE__)) . 'js/omeda.js', array('jquery', 'jquery-ui-widget'), self::VERSION, true);
		wp_localize_script('make-omeda-script', 'make_ajax_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'ajaxnonce' => wp_create_nonce('omeda_ajax')
		));*/
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve mySubscription_Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mysubs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve mySubscription_Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__('My Subscription listing', 'elementor-make-widget');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve mySubscription_Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the mySubscription_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['make-category'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the mySubscription_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['make', 'omeda', 'subscription'];
	}

	/**
	 * Register mySubscription_Widget widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', 'elementor-make-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__('Title', 'elementor-make-widget'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('Enter your title', 'elementor-make-widget'),
			]
		);

		$this->add_control(
			'email',
			[
				'label' => esc_html__('Email', 'elementor-make-widget'),
				'description' => "Select a specific type of user to test that view, set to default to display for current user",
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('User Email', 'elementor-make-widget'),
					'webmaster@make.co' => esc_html__('No Subscription', 'elementor-make-widget'),
					'TMC104@GMAIL.COM' => esc_html__('Payment Due', 'elementor-make-widget'),
					'alicia@make.co' => esc_html__('Active Subscription', 'elementor-make-widget'),
					'tim@cometoconnect.com' => esc_html__('Expired Subscription', 'elementor-make-widget'),
					'steam.jazzy.0w@icloud.com' => esc_html__('Cancelled Subscription', 'elementor-make-widget'),
					'dana@thelabellas.com' => esc_html__('Gift Subscription Recipient', 'elementor-make-widget'),
					'KOA.ROSA@GMAIL.COM' => esc_html__('Active Subscription - Gift Donor', 'elementor-make-widget'),
					'pjo@pobox.com' => esc_html__('No Subscription - Gift Donor', 'elementor-make-widget'),
					'MICHAEL@MFRANCE.NET' => esc_html__('Multiple Expired Subscriptions', 'elementor-make-widget'),
					'david.casselbury@jhuapl.edu' => esc_html__('Multiple Active Accounts', 'elementor-make-widget'),
					'jnewby@ix.netcom.com' =>  'Digital Access exp on omeda',
					'llanford@gmail.com' => 'Stacked order'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Style', 'elementor-make-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__('Icon Alignment', 'elementor-make-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__('After', 'elementor-make-widget'),
					'before' => esc_html__('Before', 'elementor-make-widget'),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render mySubscription_Widget widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		global $current_user;
		if (!is_user_logged_in()) { ?>
			<div class=“clear”></div>
			<div class=“page-content container”>
				<div class=“row”>
					<div class=“content col-md-12 text-center”>
						<h1>You must be logged in to access your Subscription information</h1>
					</div>
				</div>
			</div>
			<?php } else {
	
			//retrieve widget settings
			$settings = $this->get_settings_for_display();

			//retrieve logged in user information
			$user = wp_get_current_user();

			$omeda_postal_id = '';
			$customer_array = array();

			$user_email = $user->user_email;
			if ($settings['email'] != '') {
				$user_email = $settings['email'];
			}

			//only admins can set a test email
			if (in_array('administrator', $current_user->roles)) {
				if(isset($_GET['test_email']) && $_GET['test_email']!=''){
					$user_email = $_GET['test_email'];
				}
			}

			/*                   Subscription Lookup By Email
			This service returns all subscription information stored for all customers
			with the given Email Address and optional Product Id. Note, this includes
			both current subscription and deactivated subscriptions.
			https://training.omeda.com/knowledge-base/subscription-lookup-by-email/

			Returns:
				- Omeda Customer ID,
				- API URL for customer information
				- Subscription object
		*/
			//echo '<b>Calling customer by email API '.$sub_by_email_api.'</b><br/>';
			$sub_by_email_api = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/email/' . $user_email . '/subscription/product/7/*';
			$header = array("x-omeda-appid: 0387143E-E0DB-4D2F-8441-8DAB0AF47954");

			$subscriptionJson = json_decode(MakeBasicCurl($sub_by_email_api, $header));

			//no customers found, let's try by entered postal id
			if (!isset($subscriptionJson->Customers)) {
				$customers = array();

				if ($omeda_postal_id != '') {
					//call Customer Lookup By PostalAddressId
					$url = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/' . $omeda_postal_id . '/postaladdressid/*';
					$customerInfo  = json_decode(MakeBasicCurl($url, $header));

					//did we find a customer?
					if (isset($customerInfo->Customer)) {
						//pull any subscriptions
						$subscriptions = json_decode(MakeBasicCurl($customerInfo->Subscriptions, $header));
						$customers[] = (object) array('Url' => $customerInfo->Customer, 'Subscriptions' => (array)$subscriptions->Subscriptions);
					}
				}
			} else {
				// check if customer found at omeda, otherwise skip
				$customers = $subscriptionJson->Customers;
			}

			//loop through all customers associated with this email
			foreach ($customers as $customer) {
				$customer_id = $customer->OmedaCustomerId;

				//pull customer information
				if (isset($customer->Url)) {
					/*                   Customer Lookup By Customer Id
					The response will include basic Customer information and various
					links to look up additional Customer information such as Demographics,
					Addresses, etc for a single Customer record.
					https://training.omeda.com/knowledge-base/customer-lookup-by-customer-id/
				*/
					//echo '<b>Calling customer specific API '.$customer->Url.'</b><br/>';
					$customerInfo = json_decode(MakeBasicCurl($customer->Url, $header));
					$custEncryptID = (isset($customerInfo->EncryptedCustomerId) ? $customerInfo->EncryptedCustomerId : 0);

					/*                   Address Lookup By Customer Id
					This API provides the ability look up a Customer’s Address by the Customer Id.
					The response will return all active addresses stored for a given customer.
					https://training.omeda.com/knowledge-base/customer-lookup-by-customer-id/
				*/
					if (isset($customerInfo->Addresses)) {
						//echo '<b>Calling customer address API '.$customerInfo->Addresses.'</b><br/>';
						$customer_address = json_decode(MakeBasicCurl($customerInfo->Addresses, $header));

						//save addresses for this customer
						$address_array = array();
						foreach ($customer_address->Addresses as $address) {
							$address_array[] = (array) $address;
						} //end customer address loop
					} //end check if customer address url set
				} //end check if customer url set

				// loop through all subscriptions for this customer
				foreach ($customer->Subscriptions as $customer_sub) {
					if (isset($customer_sub->Status)) {
						//was this subscription gifted?
						$donorName = '';
						if (isset($customer_sub->DonorId)) {
							//pull donor information
							$donor_api  = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/' . $customer_sub->DonorId . '/*';
							//echo '<b>Calling donor API '.$donor_api.'</b><br/>';
							$donorInfo  = json_decode(MakeBasicCurl($donor_api, $header));
							$donorName = (isset($donorInfo->FirstName) ? $donorInfo->FirstName : '') . ' ' .
								(isset($donorInfo->LastName) ? $donorInfo->LastName : '');
						}

						// the customer array contains all information regarding the customer.
						// each row is specific to a subscription
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]  = $this->set_sub_fields((array) $customer_sub);
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['customer_id']   = $customer_id;   //customer id associated with this subscriptiobn
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['FirstName']     = $customerInfo->FirstName;  //customer basic information
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['LastName']	    = $customerInfo->LastName;  //customer basic information
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['address_array'] = $address_array; //addresses associated with this subscription
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['donorName']     = $donorName;	   //donor information if any,
						$customer_array['subscriptions'][$customer_sub->ShippingAddressId]['custEncryptID'] = $custEncryptID;
					}
				} //end customer subscription loop

				//now let's see if this customer has given any gifts
				$giftAPI = 'https://ows.omeda.com/webservices/rest/brand/MK/customer/' . $customer_id . '/gift/*';

				//echo '<b>Calling gift API '.$giftAPI.'</b><br/>';
				$gift_array = json_decode(MakeBasicCurl($giftAPI, $header));

				if (isset($gift_array->GiftRecipients) && !empty($gift_array->GiftRecipients)) {
					foreach ($gift_array->GiftRecipients as $giftRecipients) {
						//only process gifts with a subscription attached
						if (isset($giftRecipients->Subscriptions)) {
							//var_dump($giftRecipients->Subscriptions);
							//save addresses for this customer
							$address_array = array();
							foreach ($giftRecipients->Addresses as $address) {
								//only write the primary address
								if ($address->StatusCode == 1) {
									$address_array[] = (array) $address;
								}
							} //end customer address loop

							$emails = (isset($giftRecipients->Emails) ? (array) $giftRecipients->Emails : array());
							//loop through subscriptions for each gift recipien`t
							foreach ($giftRecipients->Subscriptions as $gift_sub) {
								if (isset($gift_sub->Status)) {
									// the customer array contains all information regarding the customer.
									// each row is specific to a subscription
									$customer_array['gifts'][$gift_sub->ShippingAddressId]  = $this->set_sub_fields((array) $gift_sub);
									$customer_array['gifts'][$gift_sub->ShippingAddressId]['FirstName']     = $giftRecipients->FirstName;  //customer basic information
									$customer_array['gifts'][$gift_sub->ShippingAddressId]['LastName']	    = $giftRecipients->LastName;  //customer basic information
									$customer_array['gifts'][$gift_sub->ShippingAddressId]['address_array'] = $address_array; //addresses associated with this subscription
									$customer_array['gifts'][$gift_sub->ShippingAddressId]['Emails'] 		= $emails; //addresses associated with this subscription
								}
							}
						}
					}
				} //end check for gift recipients*/

			} //end customer loop

			//allow admins to test any email			
			if (in_array('administrator', $current_user->roles)) {
				global $wp;
				$current_slug = add_query_arg( array(), $wp->request );

				echo '<div class="account-form-wrapper" style="text-align:center">                            
						<div class="account-form">							
							<form action="/'.$current_slug.'">
								<b>Admin only</b> Enter in an email to see what user sees.
								<input id="test_email" name="test_email" value="" />
								<input type="submit" value="Submit">
								'.(isset($_GET['test_email'])?'<br/><i>Testing Email: '.$_GET['test_email'].'</i>':'') .'
							</form>
						</div>
					</div>
					<br/>';
			}
			
			/*
			only show the Make subscription box,
			1) if the customer array is empty
				or
			2) if the customer array is not empty and
				there are no subscriptions (gift giver with no subscription)
		*/
			$this->digitalAccess = FALSE;
			if (empty($customer_array) || isset($customer_array['subscriptions'])) { ?>
				<script>
					jQuery(function() {
						jQuery("#tabs").tabs();
						var target = window.location.hash;
						if (target) {
							jQuery(target).click();
						}
					});
				</script>

				<div id="tabs">
					<ul>
						<li><a href="#tabs-1">Subscription Information</a></li>
						<li><a href="#tabs-2">Digital Magazine</a></li>
					</ul>
					<div id="tabs-1">
						<div class="dashboard-box make-elementor-expando-box subscriptions-wrapper">
							<h4 class="open"><?php echo ($settings['title'] != '' ? $settings['title'] : 'My Make: Magazine Subscriptions'); ?></h4>
							<ul class="open">
								<li>
									<?php
									$mb_exp_date = "";
									if(function_exists('getMembership')) {
										$mb_exp_date = getMembership($user_email);
									} else {
										// $mb_exp_date returns blank if the membership is not one of the premium types (Multi-Seat Membership, Premium Subscriber, School Maker Faire)
										$mb_exp_date = json_decode(basicCurl("https://make.co/wp-json/makecommunity/v2/membership?email=" . $user_email))->expiration;
									}
									

									$return = '';
									//if no customer information was found, prompt them to enter in a postal id
									if (empty($customer_array) && empty($mb_exp_date)) {
										$return .= '<div class="subscriptions-wrapper">
														<div class="subscription-item-wrapper">
															<div class="subscription-item disclaimer">';
										$return .= 				"<p>I'm sorry, we couldn't find any active subscriptions using email " . $user_email . ($omeda_postal_id ? ' or Account Number ' . $omeda_postal_id : '') . '</p><br/><br/>';
										
										$return .= '</div>
											</div>
										</div>';
									}

									//process subscription array
									if (!empty($customer_array['subscriptions'])) {
										//We only want to display Active or pending subscriptions. If none are found, display the most recent sub based on exp date
										$subscriptions = $this->cleanSubs($customer_array['subscriptions']);
										
										//build output
										foreach ($subscriptions as $subscription) {
											$return .= $this->buildSubOutput($subscription, $mb_exp_date);
										} //end subscription loop
										//end check if subscription array is set
									} else if(!empty($mb_exp_date)) {												
										//if mb_exp_date is set, then we have an active Premium membership
										$subscription=array(
											'RequestedVersionCode'=>'B',
											'Status'=> 1,
											'IssueExpirationDate'=>$mb_exp_date
										);
										$return .= $this->buildSubOutput($subscription, $mb_exp_date);										
									}

									echo $return;
									?>
								</li>
							</ul>
						</div>

						<?php
						//Check if customer has given any gifts
						if (isset($customer_array['gifts']) && !empty($customer_array['gifts'])) {
							$return = '';
						?>
							<div class="dashboard-box make-elementor-expando-box subscriptions-wrapper">
								<h4 class="open"><?php echo 'My Magazine Gift(s)'; ?></h4>
								<ul class="open">
									<li>
										<?php
										$gift_subs = $this->cleanSubs($customer_array['gifts']);
										foreach ($gift_subs as $gift) {
											$return .= $this->buildSubOutput($gift, $mb_exp_date);
										}
										echo $return;
										?>
									</li>
								</ul>
							</div>
						<?php
						} // end gift check
						?>
					</div>
					<div id="tabs-2">
						<?php
						if ($this->digitalAccess) {
						?>

							<article style="height:100vh;overflow:hidden;">
								<iframe id="bluetoad-iframe" src="https://make.co/wp-content/themes/make-community/blue-toad-login.php" height="700"></iframe>
							</article>

						<?php
						} else {
							echo "I'm sorry, this account does not have access to the digital magazine";
						}
						?>
					</div>
				</div>

			<?php
			}

			if (empty($customer_array) || empty($customer_array['subscriptions']) || $this->noActive) {
			?>
				<div class="subscriptions-wrapper">
					<div class="subscription-item sub-offer">
						<a href="https://subscribe.makezine.com/loading.do?omedasite=Make_subscribe&amp;PK=M2GNWB3" target="_none"><img src="https://make.co/wp-content/universal-assets/v2/images/magazine-nav-subscribe-single.webp"></a>
						<div>
							Ready for a creative escape? <i><strong>Make:</strong></i> is here to help! Now, with our limited community offer you can save big and jump into the world of DIY and global innovations. Don't miss out on 66% savings off the cover price - <a href="https://subscribe.makezine.com/loading.do?omedasite=Make_subscribe&amp;PK=M2GNWB3" target="_none">get your subscription today</a>!
						</div>
					</div>
				</div>
			<?php
			}
			?>
			<div class="subscriptions-wrapper">
				<div class="subscription-item-wrapper">
					<div class="subscription-item disclaimer">
						<p>Your first volume will arrive within 6-8 weeks in the U.S. If you need additional help or have questions, our customer representatives are available to chat over the phone from 8 am - 4:30 pm Central Time <a href="tel:847-559-7395">(847-559-7395)</a>, or you may also send an email with your Account # included to make@omeda.com – we'll be more than happy to offer assistance!</p>
					</div>
				</div>
			</div>
			<div class="subscriptions-wrapper">
				<div class="subscription-item-wrapper">
					<div class="subscription-item disclaimer">
						<div><b>To reset your password:</b>
						<ul>
							<li>Please first log out by hovering over your avatar and clicking on 'Log out' from the menu.</li>
							<li>Click the 'Log In' link.</li>
							<li>On the log in screen, click on 'Reset your Password' below the password box.</li>
							<li>Enter your email and you will receive a link in your email to reset your password.</li>
						</ul></div>
					</div>
				</div>
			</div>
<?php   } // end if logged in
	} //end render function

	protected function cleanSubs($subArray) {
		$this->noActive = FALSE;

		$subscriptions = array();
		//ensure the subscriptions are sorted with the most recent subscription on top, based on exp date
		$exp_date = array_column($subArray, "IssueExpirationDate");
		array_multisort($exp_date, SORT_DESC, $subArray);

		//check for any active or pending subscriptions
		$active  = array_search(1, array_column($subArray, 'Status'));
		$pending = array_search(2, array_column($subArray, 'Status'));

		//we only want to display active or pending subscriptions
		if ($active === false && $pending === false) {
			//	If there are no active or pending subscriptions found,
			//	display the most recent subscription based on postal ID
			//remove all but the most recent subscription
			$subscriptions[] = array_shift($subArray);
			$this->noActive = TRUE;
		} else {
			//loop through the subscriptions and only output the active and pending subscriptions
			foreach ($subArray as $subscription) {
				//save only the active and pending orders
				if (isset($subscription['Status']) && ($subscription['Status'] == 1 || $subscription['Status'] == 2)) {
					$subscriptions[] = $subscription;
				}
			}
		}
		return $subscriptions;
	}

	protected function set_sub_fields($customer_sub) {
		$subscriptions = array(
			'RequestedVersionCode'	=> $customer_sub['RequestedVersionCode'],
			'Status' 			=> $customer_sub['Status'],
			'AutoRenewalCode'	=> (isset($customer_sub['AutoRenewalCode']) ? $customer_sub['AutoRenewalCode'] : ''),
			'IssueExpirationDate' => ($customer_sub['IssueExpirationDate'] ? $customer_sub['IssueExpirationDate'] : ''),
			'IssuesRemaining'	=> (isset($customer_sub['IssuesRemaining']) ? $customer_sub['IssuesRemaining'] : 0),
			'LastPaymentDate'	=> (isset($customer_sub['LastPaymentDate']) ? $customer_sub['LastPaymentDate'] : ''),
			'LastPaymentAmount' => (isset($customer_sub['LastPaymentAmount']) ? $customer_sub['LastPaymentAmount'] : ''),
			'OrderDate'			=> (isset($customer_sub['OrderDate']) ? $customer_sub['OrderDate'] : ''),
			'ShippingAddressId' => $customer_sub['ShippingAddressId'],
			'PaymentStatus'		=> (isset($customer_sub['PaymentStatus']) ? $customer_sub['PaymentStatus'] : ''),
			'Amount'			=> (isset($customer_sub['Amount']) ? $customer_sub['Amount'] : 0),
			'Receive'			=> (isset($customer_sub['Receive']) ? $customer_sub['Receive'] : ''),
			'LastIssueEarnedDate' => (isset($customer_sub['LastIssueEarnedDate']) ? $customer_sub['LastIssueEarnedDate'] : '')
		);
		return $subscriptions;
	}

	function buildSubOutput($subscription, $mb_exp_date) {
		$return = '';

		//Build the output
		$name = isset($subscription['FirstName']) ? prettifyString($subscription['FirstName']) . ' ' : "";
		$name .= isset($subscription['LastName']) ? prettifyString($subscription['LastName']) . '<br/>' : "";
		//show the address associated with this subscription
		if (isset($subscription['address_array']) && $subscription['address_array'] != '') {
			$address = (isset($address_info['Company']) ? $address_info['Company'] . '<br/>' : '');   
			foreach ($subscription['address_array'] as $address_info) {
				//use the address assigned to this postal id
				if ($address_info['Id'] == $subscription['ShippingAddressId']) {
					$address .= prettifyString($address_info['Street']) . '<br/>';
					$address .= (isset($address_info['ApartmentMailStop']) 	!= '' ? prettifyString($address_info['ApartmentMailStop']) . '<br/>' : '');
					$address .= (isset($address_info['ExtraAddress']) 		!= '' ? prettifyString($address_info['ExtraAddress']) . '<br/>' : '');
					if (strlen($address_info['PostalCode']) > 5) {
						$address_info['PostalCode'] = substr_replace($address_info['PostalCode'], "-", 5, 0);
					}
					$region = isset($address_info['Region']) ? prettifyString($address_info['Region']) : "";
					$address .= prettifyString($address_info['City']) . ', ' . $region . ' ' . $address_info['PostalCode'] . '<br/>';
					$address .= prettifyString($address_info['Country']) . '<br/><br/>';
				}
			}
		}
		
		//determine supscription type
		//error_log(print_r($subscription, TRUE));
		if ($subscription['RequestedVersionCode'] != '') {
			switch ($subscription['RequestedVersionCode']) {
				case "P":
					$subscription_type = "Print";
					break;
				case "D":
				case "A":
					//if the mb_exp_date is set, this means they have an active premium subscription on make.co
					if($mb_exp_date!=''){
						$subscription_type = "Premium";
					}else{
						$subscription_type = "Digital";
					}
					
					break;
				case "B":
					$subscription_type = "Premium";
					break;
				default:
					$subscription_type = $subscription['RequestedVersionCode'];
					break;
			}
		}

		//determine subscription Status
		$subscription_status = '';
		if ($subscription['Status'] != '') {
			switch ($subscription['Status']) {
				case 1:
					$subscription_status = "Active";
					break;
				case 2:
					$subscription_status = "Pending";
					break;
				case 3:
					$subscription_status = "Expired";
					break;
				case 4:
					$subscription_status = "Cancelled";
					break;
				case 5:
					$subscription_status = "Graced";
					break;
				case 6:
					$subscription_status = "Standing Order";
					break;
				default:
					$subscription_status = $subscription['Status'];
					break;
			}
		}

		/*
		Payment Status Codes
			value	description	what it means
			1	Paid on invoice.	Customer paid after being invoiced.
			2	Paid with order.	Customer paid at the time of his order.
			3	Credit.	Customer owes an outstanding balance on the subscription.
			6	Free.	Customer is being granted a free subscription, but isn’t necessarily qualified by the publisher.
			7	Controlled.	Customer was selected by publisher to receive subscription for free.*/
		if (isset($subscription['PaymentStatus']) && $subscription['PaymentStatus'] == 3) {
			$subscription_status = 'Balance due: $' . $subscription['Amount'];
		}

		//renewal type
		$auto_renew = '';
		/*
		// 0 = Not Auto Renewal, 5 = Auto Charge, 6 = Auto Bill Me on Invoice
		if($subscription['AutoRenewalCode'] !='' ){
			switch ($subscription['AutoRenewalCode']){
				case 0: $auto_renew = "(account not set up for auto renewal)"; break;
				case 5: $auto_renew .= "(account will auto renew)"; break;
				case 6: $auto_renew .= "(account will be billed with an invoice)"; break;
			}
		}*/

		//expiration date
		if($subscription['IssueExpirationDate'] == "Never") {
			$subscription['IssueExpirationDate'] = "2099/06/04"; // we need this to be a date to format it like one
		}
		$exp_date = (isset($subscription['IssueExpirationDate']) ? date_format(date_create($subscription['IssueExpirationDate']), "Y/m/d") : '');

		
		// if the subscription isn't a gift, let's check memberpress data to see if they have a further out expiration date
		if(empty($subscription['donorName'])) {
			if( !empty($mb_exp_date) ) {
				if($mb_exp_date == "Never") {
					$exp_date = $mb_exp_date;
					$this->digitalAccess = TRUE;
				} else if ( date_format(date_create($mb_exp_date), "Y/m/d") >= $exp_date ) {
					$exp_date = date_format(date_create($mb_exp_date), "Y/m/d");					
					// if expiration date is in the future, ensure the status says "Active"
					$date_now = date("(Y/m/d");

					if($exp_date >= $date_now) {
						$subscription_status = "Active";
						$this->digitalAccess = TRUE;
					}
				}
			}
		}

		//last payment date
		$last_pay_date = $last_pay_amt = '';
		if (isset($subscription['LastPaymentDate']) && $subscription['LastPaymentDate'] != '') {
			$last_pay_date = date_format(date_create($subscription['LastPaymentDate']), "Y/m/d");
			$last_pay_amt  = $subscription['LastPaymentAmount'];
		}

		//Order date
		$order_date = '';
		if (isset($subscription['OrderDate']) && $subscription['OrderDate'] != '') {
			$order_date = date_format(date_create($subscription['OrderDate']), "Y/m/d");
		}

		$issues_remaining = '';
		if (isset($subscription['IssuesRemaining']) && $subscription['IssuesRemaining'] != '') {
			$issues_remaining = $subscription['IssuesRemaining'];
		}
		$subAddressID = isset($subscription['ShippingAddressId']) ? $subscription['ShippingAddressId'] : "";
		$return .= '<div class="subscription-item-wrapper"><div class="subscription-item open">';
		$return .= 	   '<div class="sub-shippingAdID" title="Postal ID">' . $subAddressID . '</div>
						<div class="sub-type" title="Subscription Type">' . $subscription_type . '</div>
						<div class="sub-status sub-' . strtok(strtolower($subscription_status), " ") . '" title="Subscription Status">' . $subscription_status . '</div>
						<div class="sub-name" title="Name">' . $name . '</div>';

		if (isset($subscription['PaymentStatus']) && $subscription['PaymentStatus'] == 3 && $subscription['custEncryptID'] != 0) {
			$return .= '<div class="sub-issuesRemaining" title="Pay bill now"><a href="https://subscribe.makezine.com/loading.do?omedasite=Make_bill_pay&r=' . $subscription['custEncryptID'] . '" target="_blank">Pay Now</a></div>';
		}

		//if they have access to a digital sub, add this link
		$issueFudge = ($mb_exp_date == "") ? 91 : 0;

		$receive = ((isset($subscription['Receive']) && $subscription['Receive'] == "1") || (isset($subscription['LastIssueEarnedDate']) && strtotime($subscription['LastIssueEarnedDate'] . " + " . $issueFudge . " days") >= strtotime('now') ));
		//active digital or print/digital ("both") subscription
		if ($receive && ($subscription['RequestedVersionCode'] == 'B' || $subscription['RequestedVersionCode'] == 'D')) {
			$this->digitalAccess = TRUE;
		}

		//Begin additional information section
		$return .= 	   '<div class="sub-additional-info">';

		//only show mailing address if this is a print or print and digital subscription
		if(isset($address)) {}
		if (($subscription['RequestedVersionCode'] == 'B' || $subscription['RequestedVersionCode'] == 'P') && isset($address) && $address!='') {
			$return .= 	'<div class="sub-address" title="Shipping Address"><b>Mailing Address:</b>' . $address . '</div>';
		}

		if ($exp_date != '') {
			$return .= '<div class="sub-expiration" title="Expiration Date"><b>Expire Date:</b> ' . $exp_date . '</div>';
		}
		if ($last_pay_date != '') {
			$return .= 	   '<div class="sub-lastPaymentDate" title="Last Payment Date"><b>Last Payment Date:</b> ' . $last_pay_date . '</div>
							<div class="sub-lastPaymentAmount" title="Last Payment Amount"><b>Last Payment Amount:</b> $' . $last_pay_amt . '</div>';
		}

		//add Payment due but only show it when it drops down from the main screen
		if (isset($subscription['PaymentStatus']) && $subscription['PaymentStatus'] == 3 && $subscription['custEncryptID'] != 0) {
			$return .= '<div class="sub-addinfo-issuesRemaining" title="Pay Bill Now"><a href="https://subscribe.makezine.com/loading.do?omedasite=Make_bill_pay&r=' . $subscription['custEncryptID'] . '" target="_blank">Pay Now</a></div>';
		}
		$return .= 	   '</div>';
		//end additional information section

		//was this subscription a gift?
		if (isset($subscription['donorName']) && $subscription['donorName'] != '') {
			$return .= '<div class="sub-gift"><i style="color:#eb002a" class="fas fa-gift"></i> Lucky you! This subscription was gifted to you by ' . prettifyString($subscription['donorName']) . '.</div><br/><br/>';
		}
		$return .= 	   '<div class="more-info" title="See More">...</div>
					</div></div>';

		return $return;
	}
}

function prettifyString($string) {
	return ucwords(strtolower($string));
}
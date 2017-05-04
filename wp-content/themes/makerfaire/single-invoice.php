<?php
/*
 *	Single invoice template
 */
//* Define running total global variable
$running_total = 0;

//* Set variables
$invoice_id = get_the_title();
$billing_company_name = get_field('billing_company_name');
$billing_contact_name = get_field('billing_contact_name');
$billing_address      = get_field('billing_address');
$billing_address2     = get_field('billing_address2');
$billing_city         = get_field('billing_city');
$billing_state        = get_field('billing_state');
$billing_zip          = get_field('billing_zip');
$billing_country      = get_field('billing_country');
$billing_email        = get_field('billing_email');
$billing_phone_num    = get_field('billing_phone_num');
$invoice_date         = get_field('invoice_date');
$origEntryID          = get_field('original_entry_id');
$entry    = GFAPI::get_entry($origEntryID);
$sponsorName = (is_array($entry) && isset($entry['151'])?$entry['151']:$billing_company_name);//field 151 from $origEntryID

add_filter( 'gform_field_value_billing_company_name', 'gf_filter_billing_company_name' );
function gf_filter_billing_company_name() {
	return esc_attr( get_field( 'billing_company_name' ) );
}
add_filter( 'gform_field_value_billing_email', 'gf_filter_billing_email' );
function gf_filter_billing_email() {
	return esc_attr( get_field( 'billing_email' ) );
}
add_filter( 'gform_field_value_billing_address', 'gf_filter_billing_address' );
function gf_filter_billing_address() {
	return esc_attr( get_field( 'billing_address' ) );
}
add_filter( 'gform_field_value_billing_address2', 'gf_filter_billing_address2' );
function gf_filter_billing_address2() {
	return esc_attr( get_field( 'billing_address2' ) );
}
add_filter( 'gform_field_value_billing_city', 'gf_filter_billing_city' );
function gf_filter_billing_city() {
	return esc_attr( get_field( 'billing_city' ) );
}
add_filter( 'gform_field_value_billing_state', 'gf_filter_billing_state' );
function gf_filter_billing_state() {
	return esc_attr( get_field( 'billing_state' ) );
}
add_filter( 'gform_field_value_billing_zip', 'gf_filter_billing_zip' );
function gf_filter_billing_zip() {
	return esc_attr( get_field( 'billing_zip' ) );
}
add_filter( 'gform_field_value_billing_country', 'gf_filter_billing_country' );
function gf_filter_billing_country() {
	return esc_attr( get_field( 'billing_country' ) );
}

add_filter( 'gform_field_value_invoice_amount', 'gf_filter_amount' );
function gf_filter_amount() {
	global $running_total;
	return esc_attr( number_format( $running_total, 2 ) );
}
add_filter( 'gform_submit_button', 'add_paragraph_below_submit', 10, 2 );
function add_paragraph_below_submit( $button, $form ) {
  if(isset($form['form_type']) && $form['form_type']=='Invoice'){
    $button = "<button class='button gform_next_button' id='gform_submit_button_{$form['id']}'><span>Pay Now</span></button>";
    return $button .= " PayPal or Credit Card";
  }else{
    return $button;
  }
}
get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="container">
	<div class="row">
    <br/><br/>
		<div class="content col-md-12">
			<div class="invoice">
        <div class="row">
          <div class="col-sm-6"><img src="http://makerfaire.com/wp-content/uploads/2016/01/mf_logo.jpg" width="300px"/></div>
          <div class="col-sm-6"><h2>INVOICE</h2></div>
        </div>

        <br/><br/>
        <div class="row">
          <div class="col-sm-6">
            <div class="row">
              <div class="col-sm-3"><b>Bill to:</b></div>
              <div class="col-sm-9"><?php echo $billing_company_name;?></div>
            </div>
            <div class="row">
              <div class="col-sm-3"><b>Contact:</b></div>
              <div class="col-sm-9"><?php echo $billing_contact_name;?></div>
            </div>
            <br/>
            <div class="row">
              <div class="col-sm-3">&nbsp;</div>
              <div class="col-sm-9">
                <?php echo $billing_address .'<br/>'.
                           (!empty($billing_address2)?$billing_address2.'<br/>':'').
                           $billing_city.', '.$billing_state.' '.$billing_zip.'<br/>'.
                           $billing_country;
                ?>
              </div>
            </div>
            <br/>
            <div class="row">
              <div class="col-sm-3">&nbsp;</div>
              <div class="col-sm-9"><?php echo $billing_email;?></div>
            </div>
            <div class="row">
              <div class="col-sm-3">&nbsp;</div>
              <div class="col-sm-9"><?php echo $billing_phone_num;?></div>
            </div>
          </div>
          <div class="col-sm-6">
                        <div class="row">
              <div class="col-sm-3"><b>Invoice number:</b></div>
              <div class="col-sm-9"><?php echo $invoice_id; ?></div>
            </div>
            <?php if($invoice_date!=''){?>
            <div class="row">
              <div class="col-sm-3"><b>Invoice Date:</b></div>
              <div class="col-sm-9"><?php echo $invoice_date;?></div>
            </div>
            <?php } ?>
            <div class="row">
              <div class="col-sm-3"><b>Due Date:</b></div>
              <div class="col-sm-9">Due Upon Receipt</div>
            </div>
            <br/>
            <div class="row">
              <div class="col-sm-3"><b>Deliver to:</b></div>
              <div class="col-sm-9">Exhibit Space Onsite</div>
            </div>
            <div class="row">
              <div class="col-sm-3"><b>Sponsor:</b></div>
              <div class="col-sm-9"><?php echo $sponsorName;?></div>
            </div>
          </div>
        </div>
        <br/><Br/>

        <?php
        if ( have_rows( 'invoice_services' ) ) {
          //* Start the table if services are listed ?>
          <table class="services">
            <tr>
              <th>Item</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Amount</th>
            </tr>

            <?php
            while ( have_rows( 'invoice_services' ) ) {
              the_row(); ?>
              <?php
                //* Set repeater variables
                $service_name     = get_sub_field( 'invoice_service_name' );
                $service_amount   = get_sub_field( 'invoice_service_amount');
                $service_quantity = get_sub_field( 'invoice_service_quantity' );
                if($service_quantity!=0){
                  $service_total    = (is_numeric($service_quantity) && is_numeric($service_amount)?($service_quantity*$service_amount):0);
              ?>
              <?php //* Output a details row for each service ?>
              <tr class="service">
                <td class="service_name"><?php echo $service_name; ?></td>
                <td><?php echo $service_quantity; ?></td>
                <td><?php echo '$' . number_format($service_amount,2); ?></td>
                <td><?php echo '$' . number_format($service_total,2); ?></td>
              </tr>

              <?php
                  global $running_total;
                  $running_total += get_sub_field( 'invoice_service_amount' ) * get_sub_field( 'invoice_service_quantity' );
                }
              ?>
        <?php } ?>
          </table>
        <?php
        }else{
          echo 'No services listed';
        } ?>

        <!-- Payment Form -->
        <?php echo do_shortcode( '[gravityform id="152" name="Invoice" title="false" description="false"]' ); ?>
        <?php
          $subject = "Special Billing Options - ". $billing_company_name.'- '.$origEntryID;

          $invoiceLink = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $body = "[Add your billing requests and details above this line.]%0D%0A
            %0D%0A
            Sponsor Name: ".$sponsorName."%0D%0A
            Invoice Number: ".$invoice_id."%0D%0A
            Invoice: ".$invoiceLink;
        ?>
        <a href="mailto:ar@makermedia.com,sponsorrelations@makermedia.com?bcc=kate@makermedia.com&subject=<?php echo $subject;?>&body=<?php echo $body;?>">Special Billing Options</a>
        <br/><br/>
      </div><!-- /invoice -->
		</div><!--Content-->
	</div>
</div><!--Container-->

<?php get_footer(); ?>
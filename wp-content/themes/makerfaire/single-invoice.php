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
$billing_email        = get_field('billing_email');
$billing_phone_num    = get_field('billing_phone_num');
$invoice_date         = get_field('invoice_date');
$origEntryID          = get_field('original_entry_id');

add_filter( 'gform_field_value_client_name', 'gf_filter_client_name' );
function gf_filter_client_name() {
	return esc_attr( get_field( 'invoice_client_name' ) );
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
              <div class="col-sm-9"><?php echo $billing_address;?></div>
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
                <td><?php echo $service_name; ?></td>
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
        <a href="mailto:sponsorrelations@makermedia.com?subject=Special Billing Options <?php echo $billing_company_name.' '.$origEntryID;?>">Special Billing Options</a>
        <br/><br/>
      </div><!-- /invoice -->
		</div><!--Content-->
	</div>
</div><!--Container-->

<?php get_footer(); ?>
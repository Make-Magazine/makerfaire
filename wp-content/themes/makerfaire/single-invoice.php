<?php
/*
 *	Single invoice template
 */
//* Define running total global variable
$running_total = 0;

//* Set variables
$invoice_id = get_the_title();
$name       = get_field( 'invoice_client_name' );
$email      = get_field( 'invoice_client_email' );


add_filter( 'gform_field_value_client_name', 'gf_filter_client_name' );
function gf_filter_client_name() {
	return esc_attr( get_field( 'invoice_client_name' ) );
}
add_filter( 'gform_field_value_invoice_amount', 'gf_filter_amount' );
function gf_filter_amount() {
	global $running_total;
	return esc_attr( number_format( $running_total, 2 ) );
}

get_header(); ?>

<div class="clear"></div>
<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
</div><!-- .post-thumbnail -->
<div class="container">
	<div class="row">
		<div class="content col-md-12">
			<div class="invoice">
        <h2 class="text-center">Invoice</h2>
        <div class="container">
          <div class="row">
            <div class="col-sm-6"><img src="http://makerfaire.com/wp-content/uploads/2016/01/mf_logo.jpg" width="200px"/></div>
            <div class="col-sm-6">
              Invoice number <?php echo $invoice_id; ?><br/>
              Invoice Date: [Order Acceptance Date]<br/>
              Due Date: Due Upon Receipt
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              Bill to:    [Billing Company Name]			<br/>
              Contact: [Billing Contact Name]<br/>
              [Billing Address]<br/>
              [Billing Email]<br/>
              [Billing Phone #]
            </div>
            <div class="col-sm-6">
              Deliver to: Exhibit Space Onsite
          </div>
        </div>


        <?php if ( have_rows( 'invoice_services' ) ): //* Start the table if services are listed ?>
          <table class="services">
            <tr>
              <th>Item</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Amount</th>
            </tr>

            <?php while ( have_rows( 'invoice_services' ) ) : the_row(); ?>
              <?php
                //* Set repeater variables
                $service_name     = get_sub_field( 'invoice_service_name' );
                $service_amount   = get_sub_field( 'invoice_service_amount');
                $service_quantity = get_sub_field( 'invoice_service_quantity' );
                $service_total    = (is_numeric($service_quantity) && is_numeric($service_amount)?($service_quantity*$service_amount):0);
              ?>
              <?php //* Output a details row for each service ?>
              <tr class="service">
                <td class="name"><?php echo $service_name; ?></td>
                <td class="quantity"><?php echo $service_quantity; ?></td>
                <td class="amount"><?php echo '$' . number_format($service_amount,2); ?></td>
                <td><?php echo '$' . number_format($service_total,2); ?></td>
              </tr>

              <?php
                global $running_total;
                $running_total += get_sub_field( 'invoice_service_amount' ) * get_sub_field( 'invoice_service_quantity' );
              ?>
            <?php endwhile; ?>
          </table>
        <?php else : echo 'No services listed'; ?>
        <?php endif; ?>
      </div>

      <div class="payment-form">
        <?php echo do_shortcode( '[gravityform id="151" name="Invoice" title="false" description="false"]' ); ?>
      </div>
		</div><!--Content-->
	</div>
</div><!--Container-->

<?php get_footer(); ?>
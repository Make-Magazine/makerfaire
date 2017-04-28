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
        <h2>Invoice Number <?php echo $invoice_id; ?></h2>
        <p>Bill to: <strong><?php echo $name; ?></strong></p>

        <?php if ( have_rows( 'invoice_services' ) ): //* Start the table if services are listed ?>
          <table class="services">
            <tr>
              <th>Service</th>
              <th>Price</th>
              <th>Quantity</th>
            </tr>

            <?php while ( have_rows( 'invoice_services' ) ) : the_row(); ?>
              <?php
                //* Set repeater variables
                $service_name = get_sub_field( 'invoice_service_name' );
                $service_amount = '$' . number_format( get_sub_field( 'invoice_service_amount' ), 2 );
                $service_quantity = get_sub_field( 'invoice_service_quantity' );
              ?>
              <?php //* Output a details row for each service ?>
              <tr class="service">
                <td class="name"><?php echo $service_name; ?></td>
                <td class="amount"><?php echo $service_amount; ?></td>
                <td class="quantity"><?php echo $service_quantity; ?></td>
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
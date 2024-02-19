<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php esc_html_e( 'MapifyPro Notification', 'crowd' ) ?></title>
    <style>
@media only screen and (max-width: 620px) {
  table.body h1 {
    font-size: 28px !important;
    margin-bottom: 10px !important;
  }

  table.body p,
table.body ul,
table.body ol,
table.body td,
table.body span,
table.body a {
    font-size: 16px !important;
  }

  table.body .wrapper,
table.body .article {
    padding: 10px !important;
  }

  table.body .content {
    padding: 0 !important;
  }

  table.body .container {
    padding: 0 !important;
    width: 100% !important;
  }

  table.body .main {
    border-left-width: 0 !important;
    border-radius: 0 !important;
    border-right-width: 0 !important;
  }

  table.body .btn table {
    width: 100% !important;
  }

  table.body .btn a {
    width: 100% !important;
  }

  table.body .img-responsive {
    height: auto !important;
    max-width: 100% !important;
    width: auto !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }

  .ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
    line-height: 100%;
  }

  .apple-link a {
    color: inherit !important;
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    text-decoration: none !important;
  }

  #MessageViewBody a {
    color: inherit;
    text-decoration: none;
    font-size: inherit;
    font-family: inherit;
    font-weight: inherit;
    line-height: inherit;
  }

  .btn-primary table td:hover {
    background-color: #34495e !important;
  }

  .btn-primary a:hover {
    background-color: #34495e !important;
    border-color: #34495e !important;
  }
}
</style>
  </head>
  <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"></span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" width="100%" bgcolor="#f6f6f6">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;" width="580" valign="top">
          <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border-radius: 3px; width: 100%;" width="100%">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;" valign="top">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><?php esc_html_e( 'Dear CrowdMaps admin,', 'crowd' ) ?></p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><?php esc_html_e( 'A user has submitted the following CrowdMaps location:', 'crowd' ) ?></p>
                        
						<table border="0" cellspacing="0" cellpadding="5">
							<tr>
								<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;width:130px;"><?php esc_html_e( 'Map', 'crowd' ) ?></th>
								<td style="vertical-align:top;padding:0 0 5px 0;"><?php echo esc_html( $map->get_title() ) ?></td>
							</tr>
							<tr>
								<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;"><?php esc_html_e( 'Location Name', 'crowd' ) ?></th>
								<td style="vertical-align:top;padding:0 0 5px 0;"><?php echo esc_html( $data['title'] ) ?></td>
							</tr>
							<tr>
								<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;"><?php esc_html_e( 'Edit Map Location', 'crowd' ) ?></th>
								<td style="vertical-align:top;padding:0 0 5px 0;"><?php echo sprintf( '<a href="%1$s" target="_blank">%1$s</a>', add_query_arg( array( 'post'=>$post_id, 'action'=>'edit' ), admin_url( 'post.php' ) ) ) ?></td>
							</tr>
							
							<?php if ( $map->get_mode() == 'google_maps' ) : ?>
								<tr>
									<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;"><?php esc_html_e( 'Coordinates', 'crowd' ) ?></th>
									<td style="vertical-align:top;padding:0 0 5px 0;"><?php printf( '<a href="http://maps.google.com/?q=%1$s" target="_blank">%1$s</a>', implode( ',', $data['location_coords'] ) ) ?></td>
								</tr>	
							<?php endif ?>

							<tr>
								<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;"><?php esc_html_e( 'Address', 'crowd' ) ?></th>
								<td style="vertical-align:top;padding:0 0 5px 0;"><?php echo esc_html( sprintf( '%s %s', $data['address'], $data['address_2'] ) ) ?></td>
							</tr>
							<tr>
								<th style="text-align:left;vertical-align:top;padding:0 20px 5px 0;"><?php esc_html_e( 'User Email', 'crowd' ) ?></th>
								<td style="vertical-align:top;padding:0 0 5px 0;"><?php echo esc_html( $user->user_email ) ?></td>
							</tr>
						</table>

					</td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            <!-- START FOOTER -->
            <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                <tr>
                  <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;" valign="top" align="center">
                    <br> <?php esc_html_e( 'You receive this because you are the admin of', 'crowd' ) ?> <a href="<?php echo site_url() ?>" style="text-decoration: underline; color: #999999; font-size: 12px; text-align: center;"><?php echo site_url() ?></a>.
                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #999999; font-size: 12px; text-align: center;" valign="top" align="center">
				  <?php esc_html_e( 'Powered by', 'crowd' ) ?> <a href="https://mapifypro.com/" style="color: #999999; font-size: 12px; text-align: center; text-decoration: none;">MapifyPro</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          </div>
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
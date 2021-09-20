<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor\Notice;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Assets;
class FrontendNavigator
{
    public static function display_form()
    {
        ?>

		<form action="" method="post">
			<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['frontend_navigator'])) {
                update_option('dce_frontend_navigator', $_POST['frontend_navigator']);
                Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
            }
        }
        ?>
			<?php 
        $option = get_option('dce_frontend_navigator');
        ?>

			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">
						<div>
							<label for="frontend_navigator"><?php 
        _e('Frontend Navigator', 'dynamic-content-for-elementor');
        ?></label>
						</div>
					</th>
					<td>
					<div>
					<select name="frontend_navigator">
						<option value="active" <?php 
        if ($option === 'active') {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Active only for administrators', 'dynamic-content-for-elementor');
        ?></option>
						<option value="active-visitors" <?php 
        if ($option === 'active-visitors') {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Active for all roles and visitors', 'dynamic-content-for-elementor');
        ?></option>
						<option value="inactive" <?php 
        if ($option === 'inactive') {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Inactive', 'dynamic-content-for-elementor');
        ?></option>
					</select>
				</div>
				</td>
				</tr>
			</table>

			<?php 
        submit_button('');
        ?>
		</form>
		<?php 
    }
}

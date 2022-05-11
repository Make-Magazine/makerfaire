<div id="sbspf_modal_overlay">
	<div class="sbspf_modal">
		<div class="sbspf_modal_message">
            <div class="sby_api_needed">
                <strong class="sbspf_emphasis"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-key fa-w-16"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" class=""></path></svg> <?php _e ( 'API Key Recommended', $text_domain ); ?></strong>
                <h3><?php _e ( 'Why do I need an API Key for some features?', $text_domain ); ?></h3>
                <p><?php _e ( 'In order to create your YouTube feeds, the plugin makes API requests to YouTube. Our Smash Balloon YouTube App has a limit on how many requests can be made in a day. To prevent hitting this daily limit and interrupting your feeds, a personal API Key is required for certain features that make heavy use of the YouTube API.', $text_domain ); ?></p>
                <p class="sbspf_submit">
                    <a href="JavaScript:void(0);" class="button button-secondary sbspf_dismiss_button" data-action="sby_dismiss_api_key_notice"><?php esc_html_e( 'Dismiss', $text_domain); ?></a>
                </p>
                <a href="JavaScript:void(0);" class="sbspf_modal_close sbspf_dismiss_button" data-action="sby_dismiss_api_key_notice"><i class="fa fa-times"></i></a>

            </div>
            <div class="sby_after_connection">
                <p><?php _e ( 'Now that your account has connected you may receive a security alert from Google .', $text_domain ); ?></p>
                <p class="sbspf_submit">
                    <a href="JavaScript:void(0);" class="button button-secondary sbspf_dismiss_at_warning_button" data-action="sby_dismiss_at_warning_notice"><?php esc_html_e( 'Dismiss', $text_domain); ?></a>
                </p>
                <a href="JavaScript:void(0);" class="sbspf_modal_close sbspf_dismiss_at_warning_button" data-action="sby_dismiss_at_warning_notice"><i class="fa fa-times"></i></a>

            </div>
		</div>

	</div>
</div>
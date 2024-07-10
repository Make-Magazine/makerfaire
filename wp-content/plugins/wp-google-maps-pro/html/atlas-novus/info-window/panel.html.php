<div class="wpgmza-panel-info-window wpgmza-panel-view">
	<div class="wpgmza-panel-actions">
		<svg class='wpgmza-close' width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M1.17157 27.1716C-0.390528 28.7337 -0.390528 31.2663 1.17157 32.8284L26.6274 58.2843C28.1895 59.8464 30.7222 59.8464 32.2843 58.2843C33.8464 56.7222 33.8464 54.1895 32.2843 52.6274L9.65685 30L32.2843 7.37258C33.8464 5.81049 33.8464 3.27783 32.2843 1.71573C30.7222 0.153632 28.1895 0.153632 26.6274 1.71573L1.17157 27.1716ZM64 26L4 26V34L64 34V26Z" />
			<title><?php _e("Close", "wp-google-maps"); ?></title>
		</svg>

		<div class="wpgmza-panel-actions-right">

			<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class='wpgmza-nearby wpgmza-hidden'>
				<g clip-path="url(#maskLeft)">
					<circle cx="30" cy="30" r="27.5" stroke-width="5" class='circle-left'/>
				</g>
				<g clip-path="url(#maskRight)">
					<circle cx="30" cy="30" r="27.5" stroke-width="5" stroke-dasharray="8 6" class='circle-right'/>
				</g>
				<path d="M30 44.2353L21.3397 31L38.6603 31L30 44.2353Z" class='mark-point'/>
				
				<circle cx="30" cy="26" r="7.5" stroke-width="5" class='mark-dot'/>
				
				<defs>
					<clipPath id="maskLeft">
						<rect width="30" height="60"/>
					</clipPath>
					<clipPath id="maskRight">
						<rect width="30" height="60" transform="translate(30)"/>
					</clipPath>
				</defs>

				<title><?php _e("Find Nearby", "wp-google-maps"); ?></title>
			</svg>

			<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class='wpgmza-share wpgmza-hidden'>
				<circle cx="51.5" cy="51.5" r="5.5" stroke-width="6" class='circle'/>
				<circle cx="51.5" cy="8.5" r="5.5" stroke-width="6" class='circle'/>
				<circle cx="8.5" cy="30.5" r="5.5" stroke-width="6" class='circle'/>
				<line x1="16.3523" y1="26.1565" x2="43.8309" y2="11.5458" stroke-width="6" stroke-linecap="square" class='line'/>
				<line x1="17.0603" y1="33.7538" x2="43.7565" y2="47.9485" stroke-width="6" stroke-linecap="square" class='line'/>

				<title><?php _e("Share Location", "wp-google-maps"); ?></title>
			</svg>




			<svg class='wpgmza-directions wpgmza-directions-button wpgmza-hidden' width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect y="30" width="42.4264" height="42.4264" rx="5" transform="rotate(-45 0 30)"/>
				<path d="M21 40V28H41" stroke-width="5" stroke-linejoin="round" class='line'/>
				<path d="M47.2159 26.7687C47.6398 27.1641 47.6398 27.8359 47.2159 28.2313L39.9321 35.0245C39.2927 35.6208 38.25 35.1674 38.25 34.2932L38.25 20.7068C38.25 19.8326 39.2927 19.3792 39.932 19.9755L47.2159 26.7687Z" class='arrow'/>

				<title><?php _e("Get Directions", "wp-google-maps"); ?></title>
			</svg>
		</div>

	</div>
	
	<div class="wpgmza-gallery-container">
		<img class="wpgmza-marker-listing-pic" data-name='pic'/>
	</div>
			
	<div data-name="title" class="wpgmza-title"></div>
	<div data-name="categories" class="wpgmza-categories"></div>
	<div class="wpgmza-address">
		<svg width="43" height="60" viewBox="0 0 43 60" fill="none" xmlns="http://www.w3.org/2000/svg" class='wpgmza-mark'>
			<path d="M21.25 60L2.84696 31.875L39.653 31.875L21.25 60Z"/>
			<circle cx="21.25" cy="21.25" r="15.25" stroke-width="12"/>
		</svg>
		<span data-name="address"></span>
	</div>
	<div data-name="description" class="wpgmza-description"></div>
	<div data-name="user-distance" class="wpgmza-distance-from-location"></div>

	<div data-name="link" class='wpgmza-infowindow-link'><a></a></div>

	<div data-name="custom_fields_html" class="wpgmza-custom-fields"></div>

	<div class="wpgmza-rating-container"></div>
</div>
<?php
/**
 * Template Name: Newsletter Subscribe Landing Page
 *
 * @package    makeblog
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */
get_header('version-2'); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="newsletter-landing-page">

      <div class="nlp-top">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <h2>Maker Faire Updates</h2>
            <p>Enter your email to keep up with the Greatest Show (&amp; Tell) on Earth</p>
            <input type="email" id="nlp-input" class="form-control" placeholder="Enter your email address" data-toggle="tooltip" data-placement="right" title="Please enter your email" />

          </div>

        </div>

      </div>

      <div class="nlp-bottom">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <form id="nlp-form" class="nlp-form form-inline" action="https://secure.whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid" value="6B5869DC547D3D4690C43FE9E066FBC6" /><!-- Confirmation -->
              <input type="hidden" name="custom_list_makerfaire" value="yes" />
              <input type="hidden" name="cmd" value="subscribe" />
              <input type="hidden" id="email" name="email" value="" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="goto" value="" />
              <input type="hidden" name="custom_source" value="landing-page_signup" />
              <input type="hidden" name="custom_incentive" value="none" />
              <input type="hidden" name="custom_url" value="makezine.com/join" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="custom_host" value="makezine.com" />

              <div class="form-group">
                <label class="list-radio">
                  <div class="pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                    <input type="checkbox" id="list_6B5869DC547D3D46B52F3516A785F101_yes" name="custom_list_makermedia" value="yes" />
                    <span for="list_6B5869DC547D3D46B52F3516A785F101_yes" class="newcheckbox"></span>
                  </div>
                  <h4>Make: Community</h4><p>News and information from and about makers</p>
                </label>
              </div>
              <hr />

              <h4>Flagship Maker Faires</h4><p>Event-specific updates from our annual flagship Faires</p>

              <div class="form-group">
                <label class="list-radio">
                  <div class="pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                    <input type="checkbox" id="list_6B5869DC547D3D4679B07245D96C075A_yes" name="custom_list_flagshipfaires_chi" value="yes" />
                    <span for="list_6B5869DC547D3D4679B07245D96C075A_yes" class="newcheckbox"></span>
                  </div>
                  <h5>Chicago</h5>
                </label>
              </div>
                
              <div class="form-group">
                <label class="list-radio">
                  <div class="pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                    <input type="checkbox" id="list_6B5869DC547D3D461285274DDB064BAC_yes" name="custom_list_flagshipfaires_ba" value="yes" />
                    <span for="list_6B5869DC547D3D461285274DDB064BAC_yes" class="newcheckbox"></span>
                  </div>
                  <h5>Bay Area</h5>
                </label>
              </div>
                
              <div class="form-group">
                <label class="list-radio">
                  <div class="pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                    <input type="checkbox" id="list_6B5869DC547D3D4641ADFD288D8C7739_yes" name="custom_list_flagshipfaires_ny" value="yes" />
                    <span for="list_6B5869DC547D3D4641ADFD288D8C7739_yes" class="newcheckbox"></span>
                  </div>
                  <h5>New York</h5>
                </label>
              </div>
                
              <hr />

              <div class="form-group">
                <label class="list-radio">
                  <div class="pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                    <input type="checkbox" id="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" name="custom_list_makerfaire" value="yes" />
                    <span for="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" class="newcheckbox"></span>
                  </div>
                  <h4>Local Maker Faires</h4><p>Find out about faires near you</p>
                </label>
              </div>

              <div class="form-group">
                <label class="list-name-sml">
                  <div class="pull-right">
                    <select  name="country" class="select form-control">
                      <option selected="selected"  value="">Select Country</option>
                      <option value="US">United States</option>
                      <option value="AF">Afghanistan</option>
                      <option value="AX">Aland Islands</option>
                      <option value="AL">Albania</option>
                      <option value="DZ">Algeria</option>
                      <option value="AS">American Samoa</option>
                      <option value="AD">Andorra</option>
                      <option value="AO">Angola</option>
                      <option value="AI">Anguilla</option>
                      <option value="AQ">Antarctica</option>
                      <option value="AG">Antigua And Barbuda</option>
                      <option value="AR">Argentina</option>
                      <option value="AM">Armenia</option>
                      <option value="AW">Aruba</option>
                      <option value="AU">Australia</option>
                      <option value="AT">Austria</option>
                      <option value="AZ">Azerbaijan</option>
                      <option value="BS">Bahamas</option>
                      <option value="BH">Bahrain</option>
                      <option value="BD">Bangladesh</option>
                      <option value="BB">Barbados</option>
                      <option value="BY">Belarus</option>
                      <option value="BE">Belgium</option>
                      <option value="BZ">Belize</option>
                      <option value="BJ">Benin</option>
                      <option value="BM">Bermuda</option>
                      <option value="BT">Bhutan</option>
                      <option value="BO">Bolivia</option>
                      <option value="BA">Bosnia And Herzegovina</option>
                      <option value="BW">Botswana</option>
                      <option value="BV">Bouvet Island</option>
                      <option value="BR">Brazil</option>
                      <option value="IO">British Indian Ocean Territory</option>
                      <option value="BN">Brunei Darussalam</option>
                      <option value="BG">Bulgaria</option>
                      <option value="BF">Burkina Faso</option>
                      <option value="BI">Burundi</option>
                      <option value="KH">Cambodia</option>
                      <option value="CM">Cameroon</option>
                      <option value="CA">Canada</option>
                      <option value="CV">Cape Verde</option>
                      <option value="KY">Cayman Islands</option>
                      <option value="CF">Central African Republic</option>
                      <option value="TD">Chad</option>
                      <option value="CL">Chile</option>
                      <option value="CN">China</option>
                      <option value="CX">Christmas Island</option>
                      <option value="CC">Cocos (Keeling) Islands</option>
                      <option value="CO">Colombia</option>
                      <option value="KM">Comoros</option>
                      <option value="CG">Congo</option>
                      <option value="CD">Congo, The Democratic Republic Of The</option>
                      <option value="CK">Cook Islands</option>
                      <option value="CR">Costa Rica</option>
                      <option value="CI">Cote D'Ivoire</option>
                      <option value="HR">Croatia</option>
                      <option value="CU">Cuba</option>
                      <option value="CY">Cyprus</option>
                      <option value="CZ">Czech Republic</option>
                      <option value="DK">Denmark</option>
                      <option value="DJ">Djibouti</option>
                      <option value="DM">Dominica</option>
                      <option value="DO">Dominican Republic</option>
                      <option value="EC">Ecuador</option>
                      <option value="EG">Egypt</option>
                      <option value="SV">El Salvador</option>
                      <option value="GQ">Equatorial Guinea</option>
                      <option value="ER">Eritrea</option>
                      <option value="EE">Estonia</option>
                      <option value="ET">Ethiopia</option>
                      <option value="FK">Falkland Islands (Malvinas)</option>
                      <option value="FO">Faroe Islands</option>
                      <option value="FJ">Fiji</option>
                      <option value="FI">Finland</option>
                      <option value="FR">France</option>
                      <option value="GF">French Guiana</option>
                      <option value="PF">French Polynesia</option>
                      <option value="TF">French Southern Territories</option>
                      <option value="GA">Gabon</option>
                      <option value="GM">Gambia</option>
                      <option value="GE">Georgia</option>
                      <option value="DE">Germany</option>
                      <option value="GH">Ghana</option>
                      <option value="GI">Gibraltar</option>
                      <option value="GR">Greece</option>
                      <option value="GL">Greenland</option>
                      <option value="GD">Grenada</option>
                      <option value="GP">Guadeloupe</option>
                      <option value="GU">Guam</option>
                      <option value="GT">Guatemala</option>
                      <option value="GG">Guernsey</option>
                      <option value="GN">Guinea</option>
                      <option value="GW">Guinea-Bissau</option>
                      <option value="GY">Guyana</option>
                      <option value="HT">Haiti</option>
                      <option value="HM">Heard Island And Mcdonald Islands</option>
                      <option value="VA">Holy See (Vatican City State)</option>
                      <option value="HN">Honduras</option>
                      <option value="HK">Hong Kong</option>
                      <option value="HU">Hungary</option>
                      <option value="IS">Iceland</option>
                      <option value="IN">India</option>
                      <option value="ID">Indonesia</option>
                      <option value="IR">Iran, Islamic Republic Of</option>
                      <option value="IQ">Iraq</option>
                      <option value="IE">Ireland</option>
                      <option value="IM">Isle Of Man</option>
                      <option value="IL">Israel</option>
                      <option value="IT">Italy</option>
                      <option value="JM">Jamaica</option>
                      <option value="JP">Japan</option>
                      <option value="JE">Jersey</option>
                      <option value="JO">Jordan</option>
                      <option value="KZ">Kazakhstan</option>
                      <option value="KE">Kenya</option>
                      <option value="KI">Kiribati</option>
                      <option value="KP">Korea, Democratic People'S Republic Of</option>
                      <option value="KR">Korea, Republic Of</option>
                      <option value="KW">Kuwait</option>
                      <option value="KG">Kyrgyzstan</option>
                      <option value="LA">Lao People'S Democratic Republic</option>
                      <option value="LV">Latvia</option>
                      <option value="LB">Lebanon</option>
                      <option value="LS">Lesotho</option>
                      <option value="LR">Liberia</option>
                      <option value="LY">Libyan Arab Jamahiriya</option>
                      <option value="LI">Liechtenstein</option>
                      <option value="LT">Lithuania</option>
                      <option value="LU">Luxembourg</option>
                      <option value="MO">Macao</option>
                      <option value="MK">Macedonia, The Former Yugoslav Republic</option>
                      <option value="MG">Madagascar</option>
                      <option value="MW">Malawi</option>
                      <option value="MY">Malaysia</option>
                      <option value="MV">Maldives</option>
                      <option value="ML">Mali</option>
                      <option value="MT">Malta</option>
                      <option value="MH">Marshall Islands</option>
                      <option value="MQ">Martinique</option>
                      <option value="MR">Mauritania</option>
                      <option value="MU">Mauritius</option>
                      <option value="YT">Mayotte</option>
                      <option value="MX">Mexico</option>
                      <option value="FM">Micronesia, Federated States Of</option>
                      <option value="MD">Moldova, Republic Of</option>
                      <option value="MC">Monaco</option>
                      <option value="MN">Mongolia</option>
                      <option value="MS">Montserrat</option>
                      <option value="MA">Morocco</option>
                      <option value="MZ">Mozambique</option>
                      <option value="MM">Myanmar</option>
                      <option value="NA">Namibia</option>
                      <option value="NR">Nauru</option>
                      <option value="NP">Nepal</option>
                      <option value="NL">Netherlands</option>
                      <option value="AN">Netherlands Antilles</option>
                      <option value="NC">New Caledonia</option>
                      <option value="NZ">New Zealand</option>
                      <option value="NI">Nicaragua</option>
                      <option value="NE">Niger</option>
                      <option value="NG">Nigeria</option>
                      <option value="NU">Niue</option>
                      <option value="NF">Norfolk Island</option>
                      <option value="MP">Northern Mariana Islands</option>
                      <option value="NO">Norway</option>
                      <option value="OM">Oman</option>
                      <option value="PK">Pakistan</option>
                      <option value="PW">Palau</option>
                      <option value="PS">Palestinian Territory, Occupied</option>
                      <option value="PA">Panama</option>
                      <option value="PG">Papua New Guinea</option>
                      <option value="PY">Paraguay</option>
                      <option value="PE">Peru</option>
                      <option value="PH">Philippines</option>
                      <option value="PN">Pitcairn</option>
                      <option value="PL">Poland</option>
                      <option value="PT">Portugal</option>
                      <option value="PR">Puerto Rico</option>
                      <option value="QA">Qatar</option>
                      <option value="RE">Reunion</option>
                      <option value="RO">Romania</option>
                      <option value="RU">Russian Federation</option>
                      <option value="RW">Rwanda</option>
                      <option value="SH">Saint Helena</option>
                      <option value="KN">Saint Kitts And Nevis</option>
                      <option value="LC">Saint Lucia</option>
                      <option value="PM">Saint Pierre And Miquelon</option>
                      <option value="VC">Saint Vincent And The Grenadines</option>
                      <option value="WS">Samoa</option>
                      <option value="SM">San Marino</option>
                      <option value="ST">Sao Tome And Principe</option>
                      <option value="SA">Saudi Arabia</option>
                      <option value="SN">Senegal</option>
                      <option value="CS">Serbia And Montenegro</option>
                      <option value="SC">Seychelles</option>
                      <option value="SL">Sierra Leone</option>
                      <option value="SG">Singapore</option>
                      <option value="SK">Slovakia</option>
                      <option value="SI">Slovenia</option>
                      <option value="SB">Solomon Islands</option>
                      <option value="SO">Somalia</option>
                      <option value="ZA">South Africa</option>
                      <option value="GS">South Georgia And The South Sandwich&#8230;</option>
                      <option value="ES">Spain</option>
                      <option value="LK">Sri Lanka</option>
                      <option value="SD">Sudan</option>
                      <option value="SR">Suriname</option>
                      <option value="SJ">Svalbard And Jan Mayen</option>
                      <option value="SZ">Swaziland</option>
                      <option value="SE">Sweden</option>
                      <option value="CH">Switzerland</option>
                      <option value="SY">Syrian Arab Republic</option>
                      <option value="TW">Taiwan</option>
                      <option value="TJ">Tajikistan</option>
                      <option value="TZ">Tanzania</option>
                      <option value="TH">Thailand</option>
                      <option value="TL">Timor-Leste</option>
                      <option value="TG">Togo</option>
                      <option value="TK">Tokelau</option>
                      <option value="TO">Tonga</option>
                      <option value="TT">Trinidad And Tobago</option>
                      <option value="TN">Tunisia</option>
                      <option value="TR">Turkey</option>
                      <option value="TM">Turkmenistan</option>
                      <option value="TC">Turks And Caicos Islands</option>
                      <option value="TV">Tuvalu</option>
                      <option value="UG">Uganda</option>
                      <option value="UA">Ukraine</option>
                      <option value="AE">United Arab Emirates</option>
                      <option value="GB">United Kingdom</option>
                      <option value="US">United States</option>
                      <option value="UM">United States Minor Outlying Islands</option>
                      <option value="UY">Uruguay</option>
                      <option value="UZ">Uzbekistan</option>
                      <option value="VU">Vanuatu</option>
                      <option value="VE">Venezuela</option>
                      <option value="VN">Viet Nam</option>
                      <option value="VG">Virgin Islands, British</option>
                      <option value="VI">Virgin Islands, U.S.</option>
                      <option value="WF">Wallis And Futuna</option>
                      <option value="EH">Western Sahara</option>
                      <option value="YE">Yemen</option>
                      <option value="ZM">Zambia</option>
                      <option value="ZW">Zimbabwe</option>
                    </select>
                  </div>
                  <h5>Country</h5>
                </label>
              </div>

              <div class="form-group">
                <label class="list-name-sml">
                  <div class="pull-right">
                    <input placeholder="Enter Postal Code" type="text" maxlength="10" id="zip" class="form-control" value="" name="zip" />
                  </div>
                  <h5>Postal Code</h5>
                </label>
              </div>

              <hr />

              <input class="btn-cyan" type="submit" value="Submit" />
              <div class="clearfix"></div>
            </form>
            <script>
              jQuery(document).on('submit', '#nlp-form', function (e) {
                e.preventDefault();
                // First check if any checkboxes are checked
                var anyBoxesChecked = false;
                jQuery('#nlp-form input[type="checkbox"]').each(function() {
                  if (jQuery(this).is(":checked")) {
                    anyBoxesChecked = true;
                  }
                });
                if (anyBoxesChecked == false) {
                  jQuery('.pull-right[data-toggle="tooltip"]').tooltip()
                  jQuery('.pull-right[data-toggle="tooltip"]').tooltip('show')
                  return false;
                }
                // Now get the email into the form and send
                else {
                  var nlpEmail = jQuery('#nlp-input').val();
                  jQuery('#nlp-form #email').val(nlpEmail);
                  if (jQuery('#nlp-form #email').val() == '') {
                    jQuery('#nlp-input').tooltip()
                    jQuery('#nlp-input').tooltip('show')
                    return false;
                  }
                  else {
                    jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('#nlp-form').serialize());
                    jQuery('.nl-thx-p1').hide();
                    jQuery('.nl-thx-p2').show();
                    jQuery('.fancybox-thx').trigger('click');
                  }
                }
              });
            </script>

          </div>

        </div>

      </div>

    </div>

  <?php endwhile; else: ?>
  
    <div class="container">
      <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
    </div>  
  
  <?php endif; ?>

<?php get_footer(); ?>
<?php
/**
 * Plugin class logic goes here
 */
	


class SeedProd_WordPress_Notification_Bar{

    
    /**
     *  Extend the base construct and add plugin specific hooks
     */
    function __construct(){
    	global $seed_wnb;
    	if($seed_wnb)
    		$options = get_option('seed_wnb_settings_1');


    	if(!empty($options)){
	        
	        if(isset($options['enabled'])){
		        add_action( 'wp_enqueue_scripts', array(&$this,'render_notification_bar') );
		        add_action( 'wp_head', array(&$this,'render_css') );
	    	}
	        add_action( 'admin_enqueue_scripts', array(&$this,'render_notification_bar') );
	        add_action( 'admin_head', array(&$this,'render_admin_css') );
    	}

    }

    function render_notification_bar($hook){
    	global $seed_wnb;
        $options = $seed_wnb->get_options();

        if($hook == 'settings_page_seed_wnb' || ! is_admin() && isset($options['enabled'])){
        	$button_target = '_self';
        	if(!empty($options['button_target'])){
        		$button_target = '_blank';
        	}

            wp_enqueue_script( 'seed-wnb-js', plugins_url('inc/js/seed_wnb.js',dirname(__FILE__)), array('jquery') );
            $data = array( 
                    'msg' => strip_tags($options['msg']),
                    'button_link' => $options['button_link'],
                    'button_label' => $options['button_label'],
                    'button_target' => $button_target,
                );
    		wp_localize_script( 'seed-wnb-js', 'seed_wnb_js_localize', $data );
            wp_enqueue_style( 'seed-wnb-css', plugins_url('inc/css/seed_wnb.css',dirname(__FILE__)), false );
        }
    }

    function render_css(){
    	global $seed_wnb;
    	$options = $seed_wnb->get_options();

        if(is_admin_bar_showing()){
        	$options = $seed_wnb->get_options();
            ?>
            <style type="text/css">
            #wnb-bar{
                top:28px;
            }
            #wpwrap{
                padding-top:33px !important;
            }
            </style>
            <?php
        } 
        if(isset($options['position'])){
        	$position = 'fixed';
        }else{
        	$position = 'absolute';
        }
        if(isset($options['enabled']) && $options['bg_color']){
        	$options = $seed_wnb->get_options();
        	$bg_color = $options['bg_color'];
        	$css="
        	@Color: {$bg_color};
			@DarkColor: darken(@Color, 7%);
			.lightordark (@c) when (lightness(@c) >= 50%) {
			  color: black;
			}
			.lightordark (@c) when (lightness(@c) < 50%) {
			  color: white;
			}
			.lightordark2 (@c) when (lightness(@c) >= 50%) {
			  color: white;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			  background-color:black;
			}
			.lightordark2 (@c) when (lightness(@c) < 50%) {
			  color: black;
			  text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.25);
			  background-color:white;
			}
			#bg_color{
				.lightordark (@Color);
			}
			.wnb-bar-button{
        		.lightordark2 (@Color);
        	}
			#wnb-bar{
			.lightordark (@Color);
			position:{$position};
			background-color: @Color;
			background-image: -moz-linear-gradient(top, @Color, @DarkColor);
			background-image: -ms-linear-gradient(top, @Color, @DarkColor);
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@Color), to(@DarkColor));
			background-image: -webkit-linear-gradient(top, @Color, @DarkColor);
			background-image: -o-linear-gradient(top, @Color, @DarkColor);
			background-image: linear-gradient(top, @Color, @DarkColor);
			background-repeat: repeat-x;
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='@{Color}', endColorstr='@{DarkColor}', GradientType=0);
			}";
			require_once('lib/seed-wnb-lessc.inc.php');	
        	$less = new seed_wnb_lessc();
			$style = $less->parse($css);
			echo "<style type='text/css'>".$style."</style>";
		}
	}
	function render_admin_css(){
    	global $seed_wnb;

        if((is_admin_bar_showing() && $_GET['page'] === 'seed_wnb')){
        	$options = $seed_wnb->get_options();
            ?>
            <style type="text/css">
            #wnb-bar{
                top:28px;
            }
            #wpwrap{
                padding-top:33px !important;
            }
            </style>
            <?php
        }  
        if(isset($options['position'])){
        	$position = 'fixed';
        }else{
        	$position = 'absolute';
        }
        if($_GET['page'] === 'seed_wnb' && $options['bg_color']){
        	$options = $seed_wnb->get_options();
        	$bg_color = $options['bg_color'];
        	$css="
        	@Color: {$bg_color};
			@DarkColor: darken(@Color, 7%);
			.lightordark (@c) when (lightness(@c) >= 50%) {
			  color: black;
			  text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.25);
			  background-color:white;
			}
			.lightordark (@c) when (lightness(@c) < 50%) {
			  color: white;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			  background-color:black;
			}
			.lightordark2 (@c) when (lightness(@c) >= 50%) {
			  color: white;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			  background-color:black;
			}
			.lightordark2 (@c) when (lightness(@c) < 50%) {
			  color: black;
			  text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.25);
			  background-color:white;
			}
			#bg_color{
				.lightordark (@Color);
			}
			.wnb-bar-button{
        		.lightordark2 (@Color);
        	}
			#wnb-bar{
			.lightordark (@Color);
			position:{$position};
			background-color: @Color;
			background-image: -moz-linear-gradient(top, @Color, @DarkColor);
			background-image: -ms-linear-gradient(top, @Color, @DarkColor);
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@Color), to(@DarkColor));
			background-image: -webkit-linear-gradient(top, @Color, @DarkColor);
			background-image: -o-linear-gradient(top, @Color, @DarkColor);
			background-image: linear-gradient(top, @Color, @DarkColor);
			background-repeat: repeat-x;
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='@{Color}', endColorstr='@{DarkColor}', GradientType=0);
			}";
			require_once('lib/seed-wnb-lessc.inc.php');	
        	$less = new seed_wnb_lessc();
			$style = $less->parse($css);
			echo "<style type='text/css'>".$style."</style>";
		}

    } 				
} // End of Class	
$seedprod_wnb = new SeedProd_WordPress_Notification_Bar();


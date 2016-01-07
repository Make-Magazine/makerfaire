<?php
/**
 * Template Name: Meet the Makers page
 */
 get_header();

 $par_post = get_post($post->post_parent);
 $slug = $par_post->post_name;

 function mf_get_topics( ) { 	
     global $faire;
        //change to pull topics by taxonomy not category
        $cats_tags = get_terms('makerfaire_category',array('hide_empty'=>false));        
 	$output = '<ul class="columns list-unstyled">';
 	foreach ($cats_tags as $cat) {
 		  if ($cat->slug != 'uncategorized') {
   			// $atts['faire'] has been deprecated and will be removed once the production server has been updated.
 		// Why? Include both if $atts['faire_url'] needed JE 8.27.14
 			$output .= '<li><a href="topics/' .  $cat->slug . '?faire='.$faire.'">' . esc_html( $cat->name ) . '</a></li>';
 		}
 	}
 	$output .= '</ul>';
 	return $output;
 }
 ?>

 <div id="wrapper" class="quora">
 <main id="main" role="main">
 
	    
<!-- The header section with a fullwidth image--> 
<?php
 
  $search_criteria['field_filters'][] = array( 'key' => '304', 'value' => 'Featured Maker');
  $search_criteria['field_filters'][] = array( 'key' => '303', 'value' => 'Accepted');

  $faireArray  = $faireName = '';
  $faire_forms = get_post_meta($post->ID, 'faire-forms', true);
  $faireArray  = explode(',',$faire_forms);
  
  $faire     = get_post_meta($post->ID, 'faire', true);
  $results = $wpdb->get_results('SELECT * FROM wp_mf_faire where faire= "'.strtoupper($faire).'"');
  $faireName = $results[0]->faire_name;
  
  $entries = GFAPI::get_entries($faireArray, $search_criteria, null, array('offset' => 0, 'page_size' => 60));  
  
  $randEntryKey = array_rand($entries); 
  $randEntry = $entries[$randEntryKey];
  $randEntryId = $randEntry['id'];
  
  $randPhoto = $randEntry['22']; 
  //find out if there is an override image for this page
  $overrideImg = findOverride($randEntry['id'],'mtm');  
  if($overrideImg!='') $randPhoto = $overrideImg;
?> 
      
<!-- The slider -->
<div class="featured-holder">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1>Featured <?php echo $faireName;?> Makers: </h1>
				<div class="gallery-holder">
					<div class="cycle-gallery carousel-gallery">
                                                                              
						<div class="mask">
							<div class="slideset">
			      				<div class="slide">
   		                			<a href="/maker/entry/<?php echo $randEntry['id']; ?>">
   		                				<span class="maker-slider-btn">Learn More About This Maker</span>
   		                				<img class="img-responsive cycle-gallery-slide" src="<?php echo legacy_get_resized_remote_image_url($randPhoto,1134,442); ?>" alt="Slide Show from Maker Faire <?php echo $faireName;?>"></a>
									<a href="/maker/entry/<?php echo $randEntry['id']; ?>">
									<div class="text-holder">
				   						<strong class="title">Featured Maker Story</strong>
				   						<p><mark><?php echo $randEntry['151']; ?>: </mark><?php echo $randEntry['16']; ?></p>
									</div></a>
			      				</div>
                              	<?php for ($i = 0; $i < count($entries); $i++) { if ($i == $randEntryKey) { continue; }  ?>
	                                <div class="slide">
	   		                  			<a href="/maker/entry/<?php echo $entries[$i]['id']; ?>">
	   		                  				<span class="maker-slider-btn">Learn More About This Maker</span>
                                                                        <?php
                                                                        //find out if there is an override image for this page
                                                                        $overrideImg = findOverride($entries[$i]['id'],'mtm');
                                                                        $projPhoto = ($overrideImg==''?$entries[$i]['22']:$overrideImg);?>
	   		                  				<img class="img-responsive cycle-gallery-slide" src="<?php echo legacy_get_resized_remote_image_url($projPhoto,1134,442); ?>" alt="Slide Show from Maker Faire <?php echo $faireName;?>"></a>
					  					<a href="/maker/entry/<?php echo $entries[$i]['id']; ?>">
					  					<div class="text-holder">
					     					<strong class="title">Featured Maker Story</strong>
					     					<p><mark><?php echo $entries[$i]['151']; ?>: </mark><?php echo $entries[$i]['16']; ?></p>
					  					</div></a>
				        			</div>
                              	<?php } // end for ?>
							</div>
						</div>
                                              <div class="top-buttons"><a class="btn-prev" href="#"><i class="icon-arrow-left"></i></a>
						<a class="btn-next" href="#"><i class="icon-arrow-right"></i></a></div>
					</div>
					<div class="carousel">
						<div class="mask">
							<div class="slideset">
                                <div class="slide">
				     				<a href="#"><img class="cycle-gallery-thumb" src="<?php echo legacy_get_resized_remote_image_url($randPhoto,95,95); ?>" alt="Slide gallery thumbnail"></a>
				 				</div>
                              	<?php for ($i = 0; $i < count($entries); $i++) { if ($i == $randEntryKey) { continue; }  ?>
					 				<div class="slide">
                                                                            <?php
                                                                                //find out if there is an override image for this page
                                                                                $overrideImg = findOverride($entries[$i]['id'],'mtm');
                                                                                $projPhoto = ($overrideImg==''?$entries[$i]['22']:$overrideImg);?>
					     				<a href="#"><img class="cycle-gallery-thumb" src="<?php echo legacy_get_resized_remote_image_url($projPhoto,95,95); ?>" alt="Slide gallery thumbnail"></a>
					 				</div>
                             	<?php } // end for ?>
							</div>
						</div>
						<a class="btn-prev" href="#"><i class="icon-arrow-left"></i></a>
						<a class="btn-next" href="#"><i class="icon-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--End of slider -->
  
<div class="search-box">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-8 text-center padbottom">
				<strong>Looking for a specific Maker? Search by Keyword:</strong>
			</div>
			<div class="col-sm-6 col-md-4 text-center">
				<div class="form-group visible-xs-inline-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block">
        	       	<form role="search" method="get" class="form-search" id="searchform" action="search/">
						<input type="text"  name="s_term" id="s_term" class="form-control evilquora" />
                                                <input type="hidden"  name="faire" value="<?php echo $faire;?>" />
						<button type="submit" id="searchsubmit" value="Search"><i class="icon-search"></i></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="browse-box">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				Browse by Topic 
				<?php echo mf_get_topics();?>
			</div>
		</div>
	</div>
</div>
        
<?php the_content(); ?>

<!-- Sponsor carousel section-->                 
<div class="sponsors-wrap">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 sponsor-carousel-holder">
				<div class="head-box">
					<div class="row">
						<div class="col-xs-12 col-sm-8">
							<div class="title">
								<h1><?php echo $faireName;?> Maker Faire Sponsors</h1>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="open-close">
								<a class="opener" href="#"><span class="selected">Silversmith</span><i class="icon-arrow-right"></i></a>
								<div class="slide">
									<ul class="tabset">
                                                                                <li class="active"><a href="#tab2">Goldsmith</a></li>
										<li><a href="#tab1">Silversmith</a></li>
										<li><a href="#tab3">Coppersmith</a></li>
		                                <!-- <li><a href="#tab4">Media</a></li>
		                                <li><a href="#tab4">Presenting</a></li> -->
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="sponsor-carousel">
					<div class="mask">
						<div class="slideset">
                                                    <div class="slide">
                                                        <?php echo mf_sponsor_list('Goldsmith Sponsor',$slug) ?>
                                                    </div>
						    <div class="slide">
                                                      <?php echo mf_sponsor_list('Silversmith Sponsor',$slug) ?>
                                                    </div>
						    <div class="slide">
					              <?php echo mf_sponsor_list('Coppersmith Sponsor',$slug) ?>	
						    </div>
                            <!--
                            <div class="slide">
					              <?php // echo mf_sponsor_list('Media Sponsor') ?>
						    </div>
                            <div class="slide">
					              <?php // echo mf_sponsor_list('Presenting Sponsor') ?>
						    </div>
						    -->
						</div>
					</div>
				</div>
				<div class"col-xs-12 visible-xs-12">
					<a class="pull-right" href="../sponsors/">Become a sponsor</a></mark>
				</div>
			</div>
		</div>
	</div>
</div><!--end of Sponsor carusel section--> 
        
<?php echo do_shortcode('[show_instagram]'); ?>
             
</main>  
</div> <!-- end of wrapper -->         
<?php get_footer(); ?>

<script>// twitter function
    (function(v,n){"function"===typeof define&&define.amd?define([],n):"object"===typeof exports?module.exports=n():n()})(this,function(){function v(a){return a.replace(/<b[^>]*>(.*?)<\/b>/gi,function(a,f){return f}).replace(/class=".*?"|data-query-source=".*?"|dir=".*?"|rel=".*?"/gi,"")}function n(a){a=a.getElementsByTagName("a");for(var c=a.length-1;0<=c;c--)a[c].setAttribute("target","_blank")}function m(a,c){for(var f=[],g=new RegExp("(^| )"+c+"( |$)"),h=a.getElementsByTagName("*"),b=0,k=h.length;b<
    k;b++)g.test(h[b].className)&&f.push(h[b]);return f}var A="",k=20,B=!0,t=[],w=!1,u=!0,q=!0,x=null,y=!0,C=!0,z=null,D=!0,E=!1,r=!0,F={fetch:function(a){void 0===a.maxTweets&&(a.maxTweets=20);void 0===a.enableLinks&&(a.enableLinks=!0);void 0===a.showUser&&(a.showUser=!0);void 0===a.showTime&&(a.showTime=!0);void 0===a.dateFunction&&(a.dateFunction="default");void 0===a.showRetweet&&(a.showRetweet=!0);void 0===a.customCallback&&(a.customCallback=null);void 0===a.showInteraction&&(a.showInteraction=!0);
        void 0===a.showImages&&(a.showImages=!1);void 0===a.linksInNewWindow&&(a.linksInNewWindow=!0);if(w)t.push(a);else{w=!0;A=a.domId;k=a.maxTweets;B=a.enableLinks;q=a.showUser;u=a.showTime;C=a.showRetweet;x=a.dateFunction;z=a.customCallback;D=a.showInteraction;E=a.showImages;r=a.linksInNewWindow;var c=document.createElement("script");c.type="text/javascript";c.src="//cdn.syndication.twimg.com/widgets/timelines/"+a.id+"?&lang="+(a.lang||"en")+"&callback=twitterFetcher.callback&suppress_response_codes=true&rnd="+
        Math.random();document.getElementsByTagName("head")[0].appendChild(c)}},callback:function(a){var c=document.createElement("div");c.innerHTML=a.body;"undefined"===typeof c.getElementsByClassName&&(y=!1);a=[];var f=[],g=[],h=[],b=[],p=[],e=0;if(y)for(c=c.getElementsByClassName("tweet");e<c.length;){0<c[e].getElementsByClassName("retweet-credit").length?b.push(!0):b.push(!1);if(!b[e]||b[e]&&C)a.push(c[e].getElementsByClassName("e-entry-title")[0]),p.push(c[e].getAttribute("data-tweet-id")),f.push(c[e].getElementsByClassName("p-author")[0]),
        g.push(c[e].getElementsByClassName("dt-updated")[0]),void 0!==c[e].getElementsByClassName("inline-media")[0]?h.push(c[e].getElementsByClassName("inline-media")[0]):h.push(void 0);e++}else for(c=m(c,"tweet");e<c.length;)a.push(m(c[e],"e-entry-title")[0]),p.push(c[e].getAttribute("data-tweet-id")),f.push(m(c[e],"p-author")[0]),g.push(m(c[e],"dt-updated")[0]),void 0!==m(c[e],"inline-media")[0]?h.push(m(c[e],"inline-media")[0]):h.push(void 0),0<m(c[e],"retweet-credit").length?b.push(!0):b.push(!1),e++;
        a.length>k&&(a.splice(k,a.length-k),f.splice(k,f.length-k),g.splice(k,g.length-k),b.splice(k,b.length-k),h.splice(k,h.length-k));c=[];e=a.length;for(b=0;b<e;){if("string"!==typeof x){var d=g[b].getAttribute("datetime"),l=new Date(g[b].getAttribute("datetime").replace(/-/g,"/").replace("T"," ").split("+")[0]),d=x(l,d);g[b].setAttribute("aria-label",d);if(a[b].innerText)if(y)g[b].innerText=d;else{var l=document.createElement("p"),G=document.createTextNode(d);l.appendChild(G);l.setAttribute("aria-label",
            d);
            g[b]=l}else g[b].textContent=d}d="";B?(r&&(n(a[b]),q&&n(f[b])),q&&(d+='<h2 class="user" style="margin-bottom:0px;">'+v(f[b].innerHTML)+"</h2>"),u&&(d+='<h2 class="timePosted">'+g[b].getAttribute("aria-label")+"</h2>",d+='<p class="tweet">'+v(a[b].innerHTML)+"</p>")):a[b].innerText?(q&&(d+='<p class="user">'+f[b].innerText+"</p>"),d+='<p class="tweet">'+a[b].innerText+"</p>",u&&(d+='<p class="timePosted">'+g[b].innerText+"</p>")):(q&&(d+='<p class="user">'+f[b].textContent+"</p>"),d+='<p class="tweet">'+a[b].textContent+
        "</p>",u&&(d+='<p class="timePosted">'+g[b].textContent+"</p>"));
            D&&(d+='<p class="interact" style="display: none;"><a href="https://twitter.com/intent/tweet?in_reply_to='+p[b]+'" class="twitter_reply_icon"'+(r?' target="_blank">':">")+'Reply</a><a href="https://twitter.com/intent/retweet?tweet_id='+p[b]+'" class="twitter_retweet_icon"'+(r?' target="_blank">':">")+'Retweet</a><a href="https://twitter.com/intent/favorite?tweet_id='+p[b]+'" class="twitter_fav_icon"'+(r?' target="_blank">':">")+"Favorite</a></p>");E&&void 0!==
            h[b]&&(l=h[b],void 0!==l?(l=l.innerHTML.match(/data-srcset="([A-z0-9%_\.-]+)/i)[0],l=decodeURIComponent(l).split('"')[1]):l=void 0,d+='<div class="media"><img src="'+l+'" alt="Image from tweet" /></div>');c.push(d);b++}if(null===z){a=c.length;f=0;g=document.getElementById(A);for(h="<div>";f<a;)h+="<div>"+c[f]+"</div>",f++;g.innerHTML=h+"</div>"}else z(c);w=!1;0<t.length&&(F.fetch(t[0]),t.splice(0,1))}};return window.twitterFetcher=F});

    var config1 = {
        "id": '322225978648698880',
        "domId": 'recent-twitter',
        "maxTweets": 1,
        "enableLinks": true
    };
    twitterFetcher.fetch(config1);
	// Search function
      jQuery(".searchsubmit").click(function(){
        var uemail = $('.email_input_field').val();
        window.location = "http://example.com/automated/action.jsp?action=register&errorPage=/automated/action.jsp&gid=12345678&uemail="+uemail+"&user.CustomAttribute.NewsletterPopUp=Global&user.CustomAttribute.NewsletterOptIn=True";
        return false;
      });
</script>
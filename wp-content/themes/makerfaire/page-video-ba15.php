<?php
/*
* Template name: Video Archive BA2015
*/
get_header(); 
$par_post = get_post($post->post_parent);
$slug = $par_post->post_name;
?>
<div class="clear"></div>
<div class="container live-page">
	<div class="row" style="margin-bottom:0px;padding-bottom:0px;">
			<div class="col-xs-12 col-sm-7">
				<h1><?php echo get_the_title(); ?></h1>
			</div>
			<div class="col-sm-2 hidden-xs">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/robot.png" width="auto" alt="makey robot" />
			</div>
			<div class="col-sm-3 social hidden-xs">
				<div class="social-foot-col">
					<div class="social-profile-icons">
						<a class="sprite-facebook-32" href="//www.facebook.com/sharer/sharer.php?u=http://makerfaire.com/bay-area-2015/live" title="Facebook" target="_blank">
							<div class="social-profile-cont">
								<span class="sprite"></span>
							</div>
						</a>
						<a class="sprite-twitter-32" href="//twitter.com/home?status=http://makerfaire.com/bay-area-2015/live" title="Twitter" target="_blank">
							<div class="social-profile-cont">
								<span class="sprite"></span>
							</div>
						</a>
						<a class="sprite-pinterest-32" href="//www.pinterest.com/makemagazine/maker-faire/" title="Pinterest" target="_blank">
							<div class="social-profile-cont">
								<span class="sprite"></span>
							</div>
						</a>

					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
				</article>
				<?php endwhile; ?>
				<?php else: ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
				<?php endif; ?>
				<div class="clearfix">&nbsp;</div>
			</div>
		</div>
	</div>
<?php //build array of video's 
    $videoArray =   array(
                        array('title'=>'Learn about the latest developments in Arduino open-source microcontroller from Massimo Banzi, co-founder of the Arduino Project.',
                              'youtubeID' => 'k_Elf1XBsQ4'),
                        array('title'=>'Large scale art requires a special set of skills and circumstances to produce, and to show. It also elicits a particular level of attention from an audience. Come listen to a panel of artists and curators (FiveTonCrane, Burning Man, Flux Foundation, Zach Coffin, Maker Faire) who have gone deep into the science of building and showing monumental art.',
                              'youtubeID' => 'algOJYY1i_g'),
                        array('title'=>'The talk will outline everything that goes into making a portable, precise milling machine: from wiring harnesses, HDPE frames, packaging, to the process by which we went from making everything in-house to using outside manufacturers.',
                                'youtubeID' => '9agSwwtZYA8'),
                        array('title'=>'Since Maker Faire was founded in 2006, new maker tools and tech have taken hold, crowdfunding was born, makerspaces have taken off. Dale will share his thoughts on the Maker Movement: where we\'ve come and what\'s next.',
                              'youtubeID'=> 'jM7GbCke8Yo'),
                        array('title'=>'Failing in life is inevitable, but sometimes as a maker you fail in epic proportions. Come listen to dedicated makers Jimmy Diresta, Bob Clagett, David Picciuto, and Make: editor Mike Senese tell tales of when things go very wrong — and what you learn from the process.',
                              'youtubeID' => 'bJDhtP6qb-I'),
                        array('title' => 'Eben will share the highlights of the first four years of the Raspberry Pi project. From their accidental announcement to the launch of Pi 2 and their five-millionth unit, Eben will draw a few lessons for other maker-oriented hardware projects.',
                              'youtubeID' => 'JGMJcU87kYU'),
                        array('title' => 'Space exploration is limited by the cost of launching materials off earth. Biology could solve this problem making possible the dream of producing bespoke tools, food, smart fabrics and even replacement organs on demand on earth or beyond!',
                              'youtubeID' => '5HfQReYgMlo'),
                        array('title' => 'Six years after its birth, Kickstarter is home to a thriving community of makers, one that\'s growing and maturing in interesting ways. Established companies and young creators alike are part of Kickstarter\'s dense fabric.',
                              'youtubeID' => 'HjmT-YYSO_w'),
                        array('title' => 'Known for the Eyewriter, Project Daniel, the Brainwriter and introducing collaborative initiatives to teach kids with cerebral palsy to walk, Not Impossible\'s Elliot Kotek wants to inspire you to be part of a community of do-ers and dynamos making the world a more "human" place.',
                              'youtubeID' => 'bchESLbH8D8'),
                        array('title' => 'John Collins, world record holder, shows off a collection of paper airplanes that has wowed audiences on four continents. See the world record plane fly across the room.',
                              'youtubeID' => 'Z5msJXVv918'),
                        array('title' => 'The OpenROV team talks about the history of underwater exploration, the current landscape of vehicles and devices, as well as glimpse into the very near future.',
                              'youtubeID' => 'XWx6pOh62io'),
                        array('title' => 'Cutting edge technology has never ceased to entertain users through accomplishing "magic". Let\'s look back at the history of tech so we can inform the future with inventions that resonate through magic and whimsy.',
                              'youtubeID' => 'yF10LI-3DbI'),
                        array('title' => 'A panel of Maker Faire veterans will discuss Maker Faires of the past, sharing favorite moments and ways that Maker Faire has changed their lives. Moderated by Lenore of Evil Mad Scientist Laboratories.',
                              'youtubeID' => 'FofQ0kbIT3U'),
                        array('title' => 'Join Autodesk CEO Carl Bass as he talks about how kids can use the latest hardware and software tools to make new things in new ways.',
                              'youtubeID' => 'xy3wwfoa3RA'),
                        array('title' => 'Since those early Maker Faires of 8-bit Arduinos and 3D printers much has changed, from the the wide availability of powerful smartphone-class electronics to the rise of polished crowdfunded campaigns. So what’s now at the DIY bleeding edge?',
                              'youtubeID' => 'TKDv37fHZu0'),
                        array('title' => 'Big & Bad is all relative in robotics. Don\'t judge a robot by its size. Join Robogames founder David Caulkins with roboticists Mark Setrakian (Robot Combat League) and Gui Cavalcanti (MegaBots) and artist Christan Ristow (Hand of Man) to discuss the nuances of what is "big and bad."',
                              'youtubeID' => 'MCokwPXncxY'),
                        array('title' => 'The Maker Movement obviously impacts all facets of our lives - including the home. Brit Morin will talk about the most interesting inevitables (and possibilities) that will soon hit our tables (and bathrooms and closets and kitchens).',
                              'youtubeID' => '4imNbsPazVc'),
                        array('title' => 'Rise of a Nation of Makers',
                              'youtubeID' => 'jLGaddMZP_M'),
                        array('title' => 'For a Maker culture to thrive it needs a healthy habitat: affordable workspaces, tools and supplies, job training and entrepreneurship services, makerspaces, and celebrations. Four people actively engaged in building Maker Cities discuss state of the art initiatives, policies and recipes towards better Maker habitat.',
                              'youtubeID' => 'zwa8ZRvY90g'),
                        array('title' => 'Jennifer George, Rube\'s Granddaughter, will discuss the making of her best selling book, "The Art of Rube Goldberg." Jennifer will also share memories of growing up with the Pulitzer Prize winning cartoonist and the legacy of invention that she\'s building into a brand.',
                              'youtubeID' => '69gE5pG6MZ4'),
                        array('title' => 'Executive Editor of Make: magazine Mike Senese will survey the various practical and beneficial ways consumer drones are being employed today: tracking endangered species, searching for missing persons, disaster area mapping and surveying, and more.',
                              'youtubeID' => 'f0Swa5pkuYk'),
                        array('title' => 'Charles & Ray Eames\' philosophy of learning-by-doing process was inquisitive, and thorough, resulting in projects that ranged from architecture, exhibits, and furniture to toys, films, and photography. Join Llisa Demetrios, granddaughter of Charles & Ray, in conversation with Exploratorium\'s Karen Wilkinson, on the Eames\' maker design process.',
                              'youtubeID' => 'MD_xJYrRCEQ'),
                        array('title' => 'Yobie Benjamin has been involved in two crowdfunded IoT startups, Avegant and Skully Systems and advises several other startups. He will explore the challenges of getting your product to market and the challenges of contract manufacturing.',
                              'youtubeID' => 'cZ6U8Ts_GZM'),
                        array('title' => 'Robotics are the future. From sci-fi to research labs, robots have captured our imagination about inventing a better world. Surgical roboticist Carol Reiley will share insights into the world of robotics as a researcher and as a maker. Join the robot revolution!',
                              'youtubeID' => 'unbi3q-RxXY')
        
                    );
    $output = '<h2>Make: Center Stage Videos from Maker Faire Bay Area 2015</h2>';
    
    
    $videoCount = 0;
    foreach ($videoArray as $video){
        //check if we need a new row
        if($videoCount % 3 == 0) {
            $output.= '<div class="row" style="margin-bottom: 30px;"><!-- New row of 3 video\'s -->';
        }
        $videoCount++;
        $output .= '<div class="col-xs-4"><!-- Video '.$videoCount.' -->
                        <a href="#'.$video['youtubeID'].'" role="button" class="modal-archive" data-toggle="modal">
                        <span class="video-icon text-center" aria-hidden="true"></span>
                        <img class="img-responsive" src="http://img.youtube.com/vi/'.$video['youtubeID'].'/maxresdefault.jpg" alt="" /></a>
                        <p>'.$video['title'].'</p>
                    </div> <!-- close .col-xs-4-->
                    <!-- Start Video Modal -->
                    <div class="modal" id="'.$video['youtubeID'].'">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                              <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
                            </div>
                            <div class="modal-body embed-youtube">
                                <iframe src="https://www.youtube.com/embed/'.$video['youtubeID'].'" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <div class="modal-footer">
                              <a href="#" data-dismiss="modal" class="btn">Close</a>
                            </div>
                          </div><!-- close .modal-content -->
                        </div> <!-- close .modal-dialog -->
                    </div><!-- close .modal -->';
        if($videoCount % 3 == 0) {           
            $output.= '</div><!-- close .row-->';
        }        
    }
    //add final close div if needed
    if($videoCount % 3 != 0) {
            $output.= '</div><!-- close .row-->';
    }
    
?>
	<div class="flag-border"></div>
	<div class="live-archive">
		<div class="container live-page">
			<div class="row padtop">
                            <div class="content col-xs-12">
                                <?php echo $output;?>				
                            </div>    <!-- end .content col-xs-12 -->        
				<div class="content col-xs-12">
					<h2>Make: Editors Report from Maker Faire Bay Area 2015</h2>
                                        <div class="row" style="margin-bottom: 30px;"><!-- New row of 3 video's -->

						<div class="col-xs-4"><!-- Video 1 -->
							<a href="#myModal34" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/hVHPsIiBVpk/maxresdefault.jpg" alt="" /></a>
							<p>Arduino Founder Massimo Shares the future of Arduino.cc, including a manufacturing partnership with Adafruit, and the start of a new brand to protect manufacturers from litigation.</p>
						</div>
						<!-- Video 1 Modal -->
						<div class="modal" id="myModal34">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/hVHPsIiBVpk?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4"> <!-- video 2 -->
							<a href="#myModal2" role="button" class="modal-archive" data-toggle="modal">
								<span class="video-icon text-center" aria-hidden="true"></span>
								<img class="img-responsive" src="http://img.youtube.com/vi/Xgwqy2lJ3_g/maxresdefault.jpg" alt="Video Thumbnail" />
							</a>
							<p>Microsoft's Tony Goodhew chats about the Microsoft booth.</p>
						</div>
						<!-- Video2 Modal -->
						<div class="modal" id="myModal2">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/Xgwqy2lJ3_g?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>


						<div class="col-xs-4"> <!-- Video 3 -->
							<a href="#myModal3" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/81kJuLI94O0/maxresdefault.jpg" alt="" /></a>
							<p>Celebrate the universal love of high-fiving with this sensor-equipped high-five rig.</p>
						</div>
						<!-- Video3 Modal -->
						<div class="modal" id="myModal3">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/81kJuLI94O0?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div> <!-- End Row -->
                                        
					<div class="row" style="margin-bottom: 30px;">

						<div class="col-xs-4">
							<a href="#myModal34" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/hVHPsIiBVpk/maxresdefault.jpg" alt="" /></a>
							<p>Arduino Founder Massimo Shares the future of Arduino.cc, including a manufacturing partnership with Adafruit, and the start of a new brand to protect manufacturers from litigation.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal34">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/hVHPsIiBVpk?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal2" role="button" class="modal-archive" data-toggle="modal">
								<span class="video-icon text-center" aria-hidden="true"></span>
								<img class="img-responsive" src="http://img.youtube.com/vi/Xgwqy2lJ3_g/maxresdefault.jpg" alt="Video Thumbnail" />
							</a>
							<p>Microsoft's Tony Goodhew chats about the Microsoft booth.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal2">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/Xgwqy2lJ3_g?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>


						<div class="col-xs-4">
							<a href="#myModal3" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/81kJuLI94O0/maxresdefault.jpg" alt="" /></a>
							<p>Celebrate the universal love of high-fiving with this sensor-equipped high-five rig.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal3">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/81kJuLI94O0?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>

					<!-- new row 2-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal4" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/fxHyAgybbZs/maxresdefault.jpg" alt="" /></a>
							<p>We talk to Jason Krinder of Beagleboard.org about the new Beaglebone Green. The new board is a clone of the Beaglebone Black produced by Seeed Studio based on the original open hardware design.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal4">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/fxHyAgybbZs?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal5" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/H3sWm-OOjYk/maxresdefault.jpg" alt="" /></a>
							<p>Karen Diggs' company Kraut Source sells fermenting kits. All of her products are sustainably made and expertly crafted. They each help you create any fermented food product you can think of.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal5">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/H3sWm-OOjYk?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal6" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/eJxZtn2gdT0/maxresdefault.jpg" alt="" /></a>
							<p>Succulent Wine Cork From UC Davis making people aware of the drought.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal6">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/eJxZtn2gdT0?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>
					<!-- new row 3-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal7" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/rLC8G7fM9PA/maxresdefault.jpg" alt="" /></a>
							<p>We talk to Gagan Luthra from Cypress Semiconductor about Cypress PSoC, the differences between it and a standard microcontroller, their development kits and the Cypress PSoC Challenge.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal7">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/rLC8G7fM9PA?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal8" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/JGBzHCmAPI0/maxresdefault.jpg" alt="" /></a>
							<p>Learn how Laney College created a giant plywood geodesic structure.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal8">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/JGBzHCmAPI0?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal9" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/gFCPnohdz-c/maxresdefault.jpg" alt="" /></a>
							<p>Contestants - i.e. kids - build their own cardboard buttons and tap them for points, while trying to avoid the whack of a mole - it's Whack-a-Mole, but in reverse.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal9">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=fxHyAgybbZs&index=31&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>
					<!-- new row 4-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal10" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/bHa97MYju9I/maxresdefault.jpg" alt="" /></a>
							<p>During MakerCon we caught up with Anouk Wipprecht in the Maker Media Lab, who was furiously working to complete her latest work — the Bubble Gum Dress — in time for Maker Faire Bay Area.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal10">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=H3sWm-OOjYk&index=30&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal11" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/hVHPsIiBVpk/maxresdefault.jpg" alt="" /></a>
							<p>SF Bizzar.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal11">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=Kw37VfXIbCg&index=2&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal12" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/2HW1YUT1CMw/maxresdefault.jpg" alt="" /></a>
							<p>The KaLEDoscope inserts the crowd into the performance.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal12">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=rLC8G7fM9PA&index=28&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>
					<!-- new row 5-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal13" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/BaJczW6HgUU/maxresdefault.jpg" alt="" /></a>
							<p>When one Rubens' tube isn't enough, you need a Rubens' organ!</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal13">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/BaJczW6HgUU?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal14" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/b2fRyiC8JKg/maxresdefault.jpg" alt="" /></a>
							<p>Robby Cuthbert shows off his unique sculptures and furniture that rely on tension to hold together.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal14">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=gFCPnohdz-c&index=26&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal15" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/85PVwyqkOdk/maxresdefault.jpg" alt="" /></a>
							<p>We caught up w/ Hector from DF Robot to learn about their booth and how they help make making easier.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal15">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=bHa97MYju9I&index=25&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>
					<!-- new row 6-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal16" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/0bFHzlEYdAg/maxresdefault.jpg" alt="" /></a>
							<p>Maker Kids, a makerspace for kids in Toronto, brought all the fixins for kids to build their own Rube Goldberg contraptions , made to fire paintballs.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal16">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/0bFHzlEYdAg?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal17" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/8hsorBt59NE/maxresdefault.jpg" alt="" /></a>
							<p>Zachary from the Bot Bash Party service shows us their flea-weight battling robots, and unveils their plans for a Bot Bash Summer Program.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal17">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=2HW1YUT1CMw&index=23&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal18" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/2DxGPWJBB_M/maxresdefault.jpg" alt="" /></a>
							<p>The Mosaic</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal18">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/2DxGPWJBB_M?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>
					</div>
					<!-- new row 7-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal19" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/-Z6qYhwIN6U/maxresdefault.jpg" alt="" /></a>
							<p>The self-aligning hand router formerly known as Taktia is renamed Shaper, while getting 2-depth and a slick new case.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal19">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=b2fRyiC8JKg&index=21&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal20" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/inJNN6WibxM/maxresdefault.jpg" alt="" /></a>
							<p>Megabots are chasing the dream of bringing giant humanoid combat robots to life. Their early build, shown here, moves about on tank treads and fires giant paintballs.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal20">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=85PVwyqkOdk&index=20&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal21" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/0UVn3QaI3zc/maxresdefault.jpg" alt="" /></a>
							<p>We talk to Pebble about their new watch, the Pebble Time, and the dueling sumo bots you can control with it.
	We also see Seeed Studio's Xadow-based Smartstrap for the very first time in public.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal21">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=0bFHzlEYdAg&index=19&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>	
					</div>
					<!-- new row 8-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal22" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/ByOAkOnS7O4/maxresdefault.jpg" alt="" /></a>
							<p>Charles "Chuck" Swiger, the winner of the first-ever PSoC Challenge. The contest challenged Makers to design IoT projects using the newly released PSoC 4 BLE Pioneer Kit. Ten Maker prujects competed for one grand prize, $2,500. A few travel here to Maker Faire Bay Area.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal22">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/ByOAkOnS7O4?list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal23" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/njlz6NfXxjM/maxresdefault.jpg" alt="" /></a>
							<p>We talk to Zach Shelby from ARM about the Internet of Things and he shows off his in-house team's project , an mbed connected espresso machine, as well as an internet connected wine rack made by  a partner company.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal23">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=2DxGPWJBB_M&index=17&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal24" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/HyWn4_22cN8/maxresdefault.jpg" alt="" /></a>
							<p>We check in with Sheau-Lan from the Sprout team to see how the Sprout is inspiring makers to learn and create.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal24">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=-Z6qYhwIN6U&index=16&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

					</div>
					<!-- new row 9-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal25" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/4enXl57BjG8/maxresdefault.jpg" alt="" /></a>
							<p>Ramon shows off his modern day player piano. Some new technology grafted to the existing player mechanisms allow Ramon to play a multitude of songs, often drawing a crowd.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal25">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=inJNN6WibxM&index=15&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal26" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/fgquWO_D-_8/maxresdefault.jpg" alt="" /></a>
							<p>We talk to George Yakoleve from Facebook about Parse for IoT, a new SDK supporting both Arduio and Raspberry Pi, as well as targeting selected RTOS platforms.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal26">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=0UVn3QaI3zc&index=14&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal27" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/2dcPd_H9gnQ/maxresdefault.jpg" alt="" /></a>
							<p>We talked to Kristin Salomon and Paul Rothman from littleBits about the littleBits module system, BitLabs, and the towering analog arcade machine they've brought here to the Faire.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal27">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=ByOAkOnS7O4&index=13&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

					</div>
					<!-- new row  10-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal28" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/jOUALurj5P4/maxresdefault.jpg" alt="" /></a>
							<p>We talk to Atmel's Wizard of Make, Bob Martin, about the Arduino Zero.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal28">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=njlz6NfXxjM&index=12&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal29" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/Zhu4JWEMlwg/maxresdefault.jpg" alt="" /></a>
							<p>Sketchup and Materialise team up to allow for automatic, 3D-printable .stl generation for your sketchup designs.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal29">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=HyWn4_22cN8&index=11&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal30" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/ER6FJgY-iFI/maxresdefault.jpg" alt="" /></a>
							<p>Instead of taking it home from Maker Faire, Will Pemble put his backyard roller coaster on Ebay.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal30">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=4enXl57BjG8&index=10&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

					</div>
					<!-- new row  11-->
					<div class="row" style="margin-bottom: 30px;">
						<div class="col-xs-4">
							<a href="#myModal31" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/Kw37VfXIbCg/maxresdefault.jpg" alt="" /></a>
							<p>Bart Pascoli of Makers Italia talks about the current state of the maker movement in Italy - from Arduino to Motorcycles to growing plants in space.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal31">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=jOUALurj5P4&index=7&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

						<div class="col-xs-4">
							<a href="#myModal32" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/eGMLwIjoWog/maxresdefault.jpg" alt="" /></a>
							<p>Scratch Artists DJ Qbert and Yoga Frog talk about how Intel Edison is helping artists through technology by reducing the tools they need to use for a performance.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal32">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=Zhu4JWEMlwg&index=6&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

					    <div class="col-xs-4">
							<a href="#myModal33" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/NfmZjG3LoIg/maxresdefault.jpg" alt="" /></a>
							<p>Scratch Artist DJ QBert controls a tesla coil using his Edison-powered turntable setup.</p>
						</div>
						<!-- Video Modal -->
						<div class="modal" id="myModal33">
							<div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						          <h4 class="modal-title">Maker Faire Bay Area 2015</h4>
						        </div>
						        <div class="modal-body embed-youtube">
									<iframe src="https://www.youtube.com/embed/watch?v=ER6FJgY-iFI&index=5&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
						        </div>
						        <div class="modal-footer">
						          <a href="#" data-dismiss="modal" class="btn">Close</a>
						        </div>
						      </div>
						    </div>
						</div>

					</div>
					<!-- new row  12-->
					<div class="row" style="margin-bottom: 30px;">
						<!-- <div class="col-xs-4">
							<a href="#myModal34" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/hVHPsIiBVpk/maxresdefault.jpg" alt="" /></a>
							<p>Arduino Founder Massimo Shares the future of Arduino.cc, including a manufacturing partnership with Adafruit, and the start of a new brand to protect manufacturers from litigation.</p>
						</div> -->
						<!-- Video Modal -->
						<!-- <div id="myModal34" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3 id="myModalLabel34" style="color:#000">Maker Faire Bay Area 2015</h3>
							</div>
							<div class="modal-body">
								<script>
									jQuery('#myModal34').on('show', function () { 
									 jQuery('div.modal-body').html('<iframe src="https://www.youtube.com/embed/watch?v=hVHPsIiBVpk&index=24&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');  
									});
									jQuery('#myModal34').on('hide', function () {
									 jQuery('div.modal-body').html('');
									});
								</script>
							</div>
						</div> -->
						<!-- <div class="col-xs-4">
							<a href="#myModal32" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/eGMLwIjoWog/maxresdefault.jpg" alt="" /></a>
							<p>Scratch Artists DJ Qbert and Yoga Frog talk about how Intel Edison is helping artists through technology by reducing the tools they need to use for a performance.</p>
						</div> -->
						<!-- Video Modal -->
						<!-- <div id="myModal32" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3 id="myModalLabel" style="color:#000">Maker Faire Bay Area 2015</h3>
							</div>
							<div class="modal-body">
								<script>
									jQuery('#myModal32').on('show', function () { 
									 jQuery('div.modal-body').html('<iframe src="https://www.youtube.com/embed/watch?v=eGMLwIjoWog&index=2&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');  
									});
									jQuery('#myModal32').on('hide', function () {
									 jQuery('div.modal-body').html('');
									});
								</script>
							</div>
						</div> -->
					              <!--   <div class="col-xs-4">
							<a href="#myModal33" role="button" class="modal-archive" data-toggle="modal">
							<span class="video-icon text-center" aria-hidden="true"></span>
							<img class="img-responsive" src="http://img.youtube.com/vi/NfmZjG3LoIg/maxresdefault.jpg" alt="" /></a>
							<p>Scratch Artist DJ QBert controls a tesla coil using his Edison-powered turntable setup.</p>
						</div> -->
						<!-- Video Modal -->
						<!-- <div id="myModal33" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						 --><!-- Video Modal -->
								<!-- <h3 id="myModalLabel33" style="color:#000">Maker Faire Bay Area 2015</h3>
							</div>
							<div class="modal-body">
								<script>
									jQuery('#myModal33').on('show', function () { 
									 jQuery('div.modal-body').html('<iframe src="https://www.youtube.com/embed/watch?v=NfmZjG3LoIg&index=1&list=PLwhkA66li5vC06gyQNvo6I6nd9AXjN5us" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');  
									});
									jQuery('#myModa33').on('hide', function () {
									 jQuery('div.modal-body').html('');
									});
								</script>
							</div>
						</div> -->
					</div>
					<div class="row">
						<div class="col-xs-12">
							<h4 class="text-right"><strong><a href="http://makezine.com/video/" target="_blank"><span class="icon-rocket"><img src="http://makerfaire.com/wp-content/uploads/2015/05/rocket@2x.png" alt="Maker Faire Rocket logo" width="16" height="16" /></span> See more Make: videos</a></strong></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	
<div class="flag-border"></div>
<!-- Sponsor carusel section-->
<div class="quora">              
	<div class="sponsors-wrap">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 sponsor-carousel-holder">
					<div class="head-box">
						<div class="row">
							<div class="col-xs-12 col-sm-8">
								<div class="title">
									<h1>Bay Area Maker Faire Sponsors</h1>
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
						<a class="pull-right" href="/bay-area-2015/sponsors/">Become a sponsor</a></mark>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--end of Sponsor carusel section--> 
<!--End  Container-->
<?php get_footer(); ?>
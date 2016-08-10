

<?php
global $wp_query;
$user = $wp_query->query_vars['makerid'];
/* $user_data = null;

  if ($user < 1) {
  $user_data = get_user_by('login', $wp_query->query_vars['makerid']);
  $user = $user_data->ID;
  } else {
  $user_data = get_userdata($user);
  } */
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$select_query = sprintf("select * from
    (SELECT wp_mf_entity.lead_id, wp_mf_maker_to_entity.maker_type, `wp_mf_maker`.`First Name` as first_name, `wp_mf_maker`.`Last Name` as last_name,
           `wp_mf_maker`.`Bio`, `wp_mf_maker`.`Photo`, `wp_mf_maker`.`Email`, `wp_mf_maker`.`TWITTER`, `wp_rg_lead`.`form_id`, `wp_mf_maker`.`maker_id`, wp_mf_entity.category
      FROM `wp_mf_maker`, wp_mf_maker_to_entity, wp_mf_entity, wp_mf_faire,wp_rg_lead, wp_auth0_user, wp_users  
      where wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id
      and   wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id
      and  wp_mf_maker.Email = wp_users.user_email
      AND   wp_auth0_user.wp_id=wp_users.id
      AND   wp_mf_entity.status = 'Accepted'
      AND   wp_mf_maker_to_entity.maker_type != 'contact'
      and   `wp_users`.`display_name` = '" . $user . "'
      AND   FIND_IN_SET (`wp_rg_lead`.`form_id`,wp_mf_faire.form_ids)> 0
      and   wp_rg_lead.id = `wp_mf_maker_to_entity`.`entity_id`
      and   wp_rg_lead.status = 'active'
      ORDER BY `wp_mf_maker`.`maker_id` ASC, wp_mf_maker_to_entity.maker_type ASC)
    AS tmp_table GROUP by `maker_id`
  ");
$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($select_query);


// Init the entities header
$makers = array();

// Loop through the posts
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

  //Check for null makers
  if (!isset($row['lead_id']))
    continue;

  // REQUIRED: The maker ID
  $maker['id'] = $row['maker_id'];

  // REQUIRED: The maker name
  $maker['first_name'] = $row['first_name'];
  $maker['last_name'] = $row['last_name'];
  $maker['description'] = $row['Bio'];
  $maker['email'] = $row['Email'];
  $maker['image'] = $row['Photo'];
  $maker['twitter'] = $row['TWITTER'];

  $maker['name'] = $row['first_name'] . ' ' . $row['last_name'];
  $maker['child_id_refs'] = array(); //array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );
  $maker['category_id_refs'] = explode(',', $row['category']); //array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );
  //add the sponsor category 333 if using a sponsor form
  //look for the word sponsor in the form name
  $form = GFAPI::get_form($row['form_id']);
  $formTitle = $form['title'];
  $formType = $form['form_type'];

  //If the form is a sponsor set to null otherwise use 222.  See Manual categories in /category/index.php
  if ((strpos($formType, 'Sponsor') === false)) {
    $maker['category_id_refs'][] = '222';
    array_push($makers, $maker);
  }

  // No longer have these
  // Maker Thumbnail and Large Images
  //$maker_image = isset($entry['217']) ? $entry['217']  : null;
  //$maker['thumb_img_url'] = esc_url( legacy_get_resized_remote_image_url( $maker_image, '80', '80' ) );
  //$maker['large_image_url'] = esc_url( legacy_get_resized_remote_image_url( $maker_image, '600', '600' ) );;
  // Maker bio information
  //$maker['description'] =isset($entry['234']) ? $entry['234']  : null;
  // Maker Video link
  //$maker_video = isset($entry['32']) ? $entry['32']  : null;
  //$maker['youtube_url'] = ( ! empty( $maker_video ) ) ? esc_url( $maker_video ) : null;
  // Maker Website link
  //$maker_website = isset($entry['27']) ? $entry['27']  : null;
  //$maker['website_url'] = ( ! empty( $maker_website ) ) ? esc_url( $maker_website ) : null;
  // Put the maker into our list of makers
}

get_header('makerportfolio');
?>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/portfolio-style.css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<?php ?>
<script>
  jQuery(document).ready(function () {
    var my_posts = jQuery("[rel=tooltip]");

    var size = jQuery(window).width();
    for (i = 0; i < my_posts.length; i++) {
      the_post = jQuery(my_posts[i]);

      if (the_post.hasClass('invert') && size >= 767) {
        the_post.tooltip({placement: 'left'});
        the_post.css("cursor", "pointer");
      } else {
        the_post.tooltip({placement: 'rigth'});
        the_post.css("cursor", "pointer");
      }
    }
  });
</script>
<div class="container">
  <hr class="">
  <div class="container target">
    <div class="row">
      <div class="col-sm-10">
        <h1 class=""><?= $user ?></h1>

        <button type="button" class="btn btn-success">Book me!</button>  <button type="button" class="btn btn-info">Send me a message</button>
        <br>
      </div>
      <div class="col-sm-2"><a href="/users" class="pull-right"><img title="profile image" class="img-circle img-responsive" src="<?= $maker['image'] ?>"></a>

      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-3">
        <!--left col-->
        <ul class="list-group">
          <li class="list-group-item text-muted" contenteditable="false">Profile</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Joined</strong></span> 2.13.2014</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Last seen</strong></span> Yesterday</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Real name</strong></span> <?= $maker['name'] ?></li>

        </ul>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="watch-card">
              <div class="artist-collage col-md-12">
                <div class="col-md-6"><img src="<?= $maker['image'] ?>" alt="artist-image" width="150" height="150"></div>
                <div class="col-md-6 collage-rhs">
                  <div class="col-md-12"><img src="<?= $maker['image'] ?>" alt="artist-image" width="150" height="84"></div>
                  <div class="col-md-12"><img src="<?= $maker['image'] ?>" alt="artist-image" width="150" height="84"></div>        
                </div>
              </div>
              <div class="listing-tab col-md-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="myTabs" role="tablist">
                  <li role="presentation" class="active"><a href="#videos" aria-controls="videos" role="tab" data-toggle="tab">Videos</a></li>
                  <li role="presentation"><a href="#projects" aria-controls="projects" role="tab" data-toggle="tab">Projects</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="videos">
                    <ul>
                      <li><a href="#">Show and Tell 1</a>    <span>4:31</span></li>
                      <li><a href="#">Show and Tell 2</a>    <span>4:31</span></li>
                      <li><a href="#">Show and Tell 3</a>    <span>4:31</span></li>

                    </ul>

                  </div>
                  <div role="tabpanel" class="tab-pane" id="projects">
                    <ul>
                      <li>Project 1</li>
                      <li>Project 1</li>
                      <li>Project 1</li>

                    </ul>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">Yes / No?

          </div>
          <div class="panel-body"><i style="color:green" class="fa fa-check-square"></i> Yes, I am insured and bonded.

          </div>
        </div>


        <div class="panel panel-default">
          <div class="panel-heading">Website <i class="fa fa-link fa-1x"></i>

          </div>
          <div class="panel-body"><a href="#" class="">here</a>

          </div>
        </div>

        <ul class="list-group">
          <li class="list-group-item text-muted">Activity <i class="fa fa-dashboard fa-1x"></i>

          </li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Shares</strong></span> 125</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Likes</strong></span> 13</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Posts</strong></span> 37</li>
          <li class="list-group-item text-right"><span class="pull-left"><strong class="">Followers</strong></span> 78</li>
        </ul>
        <div class="panel panel-default">
          <div class="panel-heading">Social Media</div>
          <div class="panel-body">	<i class="fa fa-facebook fa-2x"></i>  <i class="fa fa-github fa-2x"></i> 
            <i class="fa fa-twitter fa-2x"></i> <i class="fa fa-pinterest fa-2x"></i>  <i class="fa fa-google-plus fa-2x"></i>

          </div>
        </div>
      </div>
      <!--/col-3-->
      <div class="col-sm-9" style="" contenteditable="false">
        <div class="panel panel-default">
          <div class="panel-heading"><?= $user ?>'s Bio</div>
          <div class="panel-body"> <?= $maker['description'] ?>

          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">Video

          </div>
          <div class="panel-body">
            <div class="col-md-4">
              <h4>Lorem Ipsum</h4>
              <p>
                Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.
              </p><img src="http://farm7.staticflickr.com/6043/6311448642_841c424a65.jpg" class="img-responsive center-block" alt="tv">

              <p>
                <small>Image: <a href="http://www.flickr.com/photos/photography_and_design/6311448642/sizes/l/" target="_blank">Jonas' Design</a><br>
                  <a href="http://tinyurl.com/tbvalid8" target="_blank">HTML 5 Valid</a></small>
              </p>

            </div><!--.col -->

            <div class="col-md-8">
              <div class="vid">
                <iframe width="560" height="315" src="//www.youtube.com/embed/ac7KhViaVqc" allowfullscreen=""></iframe>
              </div><!--./vid -->

            </div><!--.col -->

          </div><!--./row -->
        </div>
        <div class="panel panel-default target">
          <div class="panel-heading" contenteditable="false">MakerFaire Projects</div>
          <div class="panel-body">
            <div class="row">
              <div class="col-md-4">
                <div class="thumbnail">
                  <img alt="300x200" src="<?= esc_url(legacy_get_resized_remote_image_url($maker['image'], '600', '200')) ?>">
                  <div class="caption">
                    <h3>
                      Project 1 MakerFaire Bay Area 2015
                    </h3>
                    <p>
                      My Project.
                    </p>
                    <p>

                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="thumbnail">
                  <img alt="300x200" src="http://lorempixel.com/600/200/city">
                  <div class="caption">
                    <h3>
                      Project 2 Hackster.io Project
                    </h3>
                    <p>
                      See it
                    </p>
                    <p>

                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="thumbnail">
                  <img alt="300x200" src="http://lorempixel.com/600/200/sports">
                  <div class="caption">
                    <h3>
                      Instructables Project 1
                    </h3>
                    <p>
                      See it
                    </p>
                    <p>

                    </p>
                  </div>
                </div>

              </div>

            </div>

          </div>

        </div>
        <div class="panel panel-default">
          <div class="panel-heading"><?= $user ?>'s Bio</div>
          <div class="panel-body"> <?= $maker['description'] ?>

          </div>
        </div></div>


      <div id="push"></div>
    </div>


  </div>
</div> <!-- End basic profile -->
<div class="container">
  <div class="row"> <div class="fb-profile">
      <img align="left" class="fb-image-lg" src="<?= esc_url(legacy_get_resized_remote_image_url($maker['image'], '850', '280')) ?>" alt="Profile image example"/>
      <img align="left" class="fb-image-profile thumbnail" src="<?= $maker['image'] ?>" alt="Profile image example"/>
      <div class="fb-profile-text">
        <h1><?= $maker['name'] ?></h1>
        <p><?= $maker['description'] ?></p>
      </div>
    </div></div>
  <div class="row">
    <div class="col-md-offset-2 col-md-8 col-lg-offset-3 col-lg-6">
      <div class="well profile">
        <div class="col-sm-12">
          <div class="col-xs-12 col-sm-8">
            <h2><?= $maker['name'] ?></h2>
            <p><strong>About: </strong> <?= $maker['description'] ?> </p>
            <p><strong>Hobbies: </strong> None </p>
            <p><strong>Awards: </strong>
              <span class="tags">Blueribbon Bay Area MakerFaire 2015</span> 
              <span class="tags">Blueribbon World MakerFaire 2015</span> 
              <span class="tags">Blueribbon Bay Area MakerFaire 2016</span> 

            </p>
          </div>             
          <div class="col-xs-12 col-sm-4 text-center">
            <figure>
              <img src="<?= $maker['image'] ?>" alt="" class="img-circle img-responsive">
              <figcaption class="ratings">
                <p>Ratings
                  <a href="#">
                    <span class="fa fa-star"></span>
                  </a>
                  <a href="#">
                    <span class="fa fa-star"></span>
                  </a>
                  <a href="#">
                    <span class="fa fa-star"></span>
                  </a>
                  <a href="#">
                    <span class="fa fa-star"></span>
                  </a>
                  <a href="#">
                    <span class="fa fa-star-o"></span>
                  </a> 
                </p>
              </figcaption>
            </figure>
          </div>
        </div>            
        <div class="col-xs-12 divider text-center">
          <div class="col-xs-12 col-sm-4 emphasis">
            <h2><strong> 20,7K </strong></h2>                    
            <p><small>Followers</small></p>
            <button class="btn btn-success btn-block"><span class="fa fa-plus-circle"></span> Follow </button>
          </div>
          <div class="col-xs-12 col-sm-4 emphasis">
            <h2><strong>245</strong></h2>                    
            <p><small>Following</small></p>
            <button class="btn btn-info btn-block"><span class="fa fa-user"></span> View Profile </button>
          </div>
          <div class="col-xs-12 col-sm-4 emphasis">
            <h2><strong>43</strong></h2>                    
            <p><small>Snippets</small></p>
            <div class="btn-group dropup btn-block">
              <button type="button" class="btn btn-primary"><span class="fa fa-gear"></span> Options </button>
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu text-left" role="menu">
                <li><a href="#"><span class="fa fa-envelope pull-right"></span> Send an email </a></li>
                <li><a href="#"><span class="fa fa-list pull-right"></span> Add or remove from a list  </a></li>
                <li class="divider"></li>
                <li><a href="#"><span class="fa fa-warning pull-right"></span>Report this user for spam</a></li>
                <li class="divider"></li>
                <li><a href="#" class="btn disabled" role="button"> Unfollow </a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>                 
    </div>
  </div>
  <div class="row">
    <ul class="timeline">
      <li>
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record" rel="tooltip" title="May 20, 2016" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-heading">
            <img class="img-responsive" src="http://lorempixel.com/1600/500/sports/2" />

          </div>
          <div class="timeline-body">
            <p> Project 1
            </p>
          </div>

          <div class="timeline-footer">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">See it</a>
          </div>
        </div>
      </li>

      <li  class="timeline-inverted">
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record invert" rel="tooltip" title="Nov 20, 2015" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-heading">
            <img class="img-responsive" src="http://lorempixel.com/1600/500/sports/2" />

          </div>
          <div class="timeline-body">
            <p>
              New York Makerfaire Project</p>
          </div>

          <div class="timeline-footer">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">See It</a>
          </div>
        </div>
      </li>
      <li>
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record" rel="tooltip" title="Nov 1, 2015" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-heading">
            <img class="img-responsive" src="http://lorempixel.com/1600/500/sports/2" />

          </div>
          <div class="timeline-body">
            <p>Hackster.io Project</p>
          </div>

          <div class="timeline-footer">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">See it</a>
          </div>
        </div>
      </li>

      <li  class="timeline-inverted">
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record invert" rel="tooltip" title="Sep 18, 2015" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-body">
            <p>Instructables Project</p>

          </div>

          <div class="timeline-footer">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">See it</a>
          </div>
        </div>
      </li>
      <li>
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record" rel="tooltip" title="Mar 20, 2015" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-heading">

            <p> Subscribed to Makezine.</p>
          </div>
          <div class="timeline-body">
            <p>
              First Issue was 48
            </p>
          </div>

          <div class="timeline-footer">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">View that issue</a>
          </div>
        </div>
      </li>

      <li  class="timeline-inverted">
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record invert" rel="tooltip" title="Jan 1, 2015" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-heading">
            <img class="img-responsive" src="http://lorempixel.com/1600/500/sports/2" />

          </div>
          <div class="timeline-body">
            <p>MakerFaire Project for Bay Area 2015 was Accepted.</p>
          </div>

          <div class="timeline-footer primary">
            <a><i class="glyphicon glyphicon-thumbs-up"></i></a>
            <a><i class="glyphicon glyphicon-share"></i></a>
            <a class="pull-right">See it</a>
          </div>
        </div>
      </li>
      <li>
        <div class="timeline-badge primary"><a><i class="glyphicon glyphicon-record invert" rel="tooltip" title="Jan 1, 2014" id=""></i></a></div>
        <div class="timeline-panel">
          <div class="timeline-body">
            <p>Joined as a Maker
            </p>
          </div>

          <div class="timeline-footer primary">
            <p>Jan 1, 2014</p>
          </div>
        </div>
      </li>

      <li class="clearfix" style="float: none;"></li>
    </ul>
  </div>
</div>
<!-- End Timeline Row -->



<?php get_footer();
?>
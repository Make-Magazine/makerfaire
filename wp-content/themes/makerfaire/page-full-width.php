<?php
/*
Template name: Full browser width, no container
*/
get_header(); ?>

<div class="clear"></div>

<div id="media-center-page">

  <header>

    <div class="container">

      <div class="row">

        <div class="col-xs-12 col-md-5">

          <h1><?php echo get_the_title(); ?></h1>

        </div>

        <div class="col-xs-12 col-md-7">

          <div class="row">

            <div class="col-xs-5 mcp-connect">
              <p>Connect with us</p>
              <div class="social-network-container">
                <ul class="social-network social-circle">
                  <li><a href="//www.facebook.com/makerfaire" class="icoFacebook" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
                  <li><a href="//twitter.com/makerfaire" class="icoTwitter" title="Twitter" target="_blank"><i class="fa fa-twitter" target="_blank"></i></a></li>
                  <li><a href="//www.pinterest.com/makemagazine/maker-faire/" class="icoPinterest" title="Pinterest" target="_blank"><i class="fa fa-pinterest-p" target="_blank"></i></a></li>
                  <li><a href="//www.youtube.com/user/MakerFaire" class="icoYoutube" title="Youtube" target="_blank"><i class="fa fa-youtube-play" target="_blank"></i></a></li>
                </ul>
              </div>
              <a href="mailto:pr@makerfaire.com">pr@makerfaire.com</a>
            </div>

            <div class="col-xs-7 mcp-newsletter">  
              <p>Sign Up For Maker Faire Media Alerts</p>
              <p><small>Timely, pertinent information sent only when needed.</small></p>
              
            </div>

          </div>

        </div>

      </div>

    </div>

  </header>


  <div class="container">

    <div class="row">

      <div class="content col-xs-12">

        <div class="tabbable">

          <ul class="nav nav-pills nav-stacked col-md-3">
            <li class="active">
              <a href="#a" data-toggle="tab">Welcome</a>
            </li>
            <li>
              <a href="#b" data-toggle="tab">Media Registration</a>
            </li>
            <li>
              <a href="#c" data-toggle="tab">Fast Facts</a>
            </li>
            <li>
              <a href="#d" data-toggle="tab">Latest News</a>
            </li>
            <li>
              <a href="#e" data-toggle="tab">Newsroom</a>
            </li>
            <li>
              <a href="#f" data-toggle="tab">Photos</a>
            </li>
            <li>
              <a href="#g" data-toggle="tab">Videos</a>
            </li>
            <li>
              <a href="#h" data-toggle="tab">Logos / Promotion</a>
            </li>
          </ul>

          <article class="tab-content col-md-9">
            <div class="tab-pane active" id="a">Lorem ipsum dolor sit amet, charetra varius rci quis tortor imperdiet venenatis quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero.</div>
            <div class="tab-pane" id="b">Secondo sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. Aliquam in felis sit amet augue.</div>
            <div class="tab-pane" id="c">Thirdamuno, ipsum dolor sit amet, consectetur adipiscing elit. Duis elementum auctor accumsan. Duis pharetra
            varius quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae. </div>
            <div class="tab-pane" id="d">dddddddd</div>
            <div class="tab-pane" id="e">eeeeeeee</div>
            <div class="tab-pane" id="f">ffffffff</div>
            <div class="tab-pane" id="g">gggggggg</div>
            <div class="tab-pane" id="h">hhhhhhhh</div>
          </article>

  <!--           <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

              <article <?php post_class(); ?>>

                <?php the_content(); ?>

              </article>

            <?php endwhile; ?>

            <?php else: ?>

              <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

            <?php endif; ?> -->

        </div>

      </div><!--Content-->

    </div>

  </div><!--Container-->

</div><!--#media-center-page-->

<?php get_footer(); ?>
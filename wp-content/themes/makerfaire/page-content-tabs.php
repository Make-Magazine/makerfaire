<?php
/*
Template name: Content Tabs
*/
get_header(); ?>

<div class="clear"></div>

<div id="media-center-page">

  <header>

    <div class="container">

      <div class="row">

        <div class="col-xs-12 col-sm-3 col-md-5">

          <h1><?php echo get_the_title(); ?></h1>

        </div>

        <div class="col-xs-12 col-sm-9 col-md-7">

          <?php the_field('header_right_side_content'); ?>

        </div>

      </div>

    </div>

  </header>


  <div class="mcp-body">

    <div class="container">

      <div class="row">

        <div class="content col-xs-12">

          <div class="tabbable">

            <ul class="nav nav-tabs nav-stacked nav-tabs-responsive navbar-collapse col-xs-12 col-sm-3">

              <?php if( have_rows('tabs') ):

                $counter = 1;

                while( have_rows('tabs') ): the_row();

                  $tab_title = get_sub_field('tab_title');
                  $replace_these = array("/", "&");
                  $tab_title2 = (str_replace($replace_these, '', $tab_title));
                  $tab_url = (str_replace(' ', '-', strtolower($tab_title2)));

                  if ($counter == 1) { ?>

                    <li class="active">
                      <a href="#<?php echo $tab_url ?>" data-toggle="tab"><span><?php echo $tab_title; ?></span></a>
                    </li>

                  <?php
                  } else { ?>

                    <li>
                      <a href="#<?php echo $tab_url ?>" data-toggle="tab"><span><?php echo $tab_title; ?></span></a>
                    </li>

                  <?php
                  }

                  $counter++;

                endwhile;

              endif; ?>

            </ul>

            <article class="tab-content col-xs-12 col-sm-9">

              <?php if( have_rows('tabs') ):

                $counter = 1;

                while( have_rows('tabs') ): the_row(); 

                  $tab_content = get_sub_field('tab_content');
                  $tab_title = get_sub_field('tab_title');
                  $replace_these = array("/", "&");
                  $tab_title2 = (str_replace($replace_these, '', $tab_title));
                  $tab_url = (str_replace(' ', '-', strtolower($tab_title2)));

                  if ($counter == 1) { ?>

                    <div class="tab-pane active" id="<?php echo $tab_url ?>">
                      <?php echo $tab_content; ?>
                    </div>

                  <?php
                  } else { ?>

                    <div class="tab-pane" id="<?php echo $tab_url ?>">
                      <?php echo $tab_content; ?>
                    </div>

                  <?php
                  }

                  $counter++;

                endwhile;

              endif; ?>

            </article>


          </div>

        </div><!--Content-->

      </div>

    </div>

    <div id="mcp-background-color"></div>

  </div><!--Container-->

</div><!--#media-center-page-->
<script src="https://rawgit.com/flatlogic/bootstrap-tabcollapse/master/bootstrap-tabcollapse.js"></script>
<script>
  jQuery('.nav-tabs-responsive').tabCollapse({
    tabsClass: 'hidden-sm hidden-xs',
    accordionClass: 'visible-sm visible-xs nav-tabs-responsive'
  });
</script>
<script>
  //Script to add url navigation to the bootstrap tabs: jquery.stickytabs.js
  (function ( $ ) {
      $.fn.stickyTabs = function( options ) {
          var context = this

          var settings = $.extend({
              getHashCallback: function(hash, btn) { return hash },
              selectorAttribute: "href",
              backToTop: false,
              initialTab: $('li.active > a','h4 > a:not(.collapsed)',context)
          }, options );

          // Show the tab corresponding with the hash in the URL, or the first tab.
          var showTabFromHash = function() {
            var hash = settings.selectorAttribute == "href" ? window.location.hash : window.location.hash.substring(1);
            if (hash != '') {
                
                if ($( window ).width() < 991) {
                  var selector = hash ? 'a[' + settings.selectorAttribute +'="' + hash + '-collapse"]' : settings.initialTab;
                  $(selector).click();
                } else {
                  var selector = hash ? 'a[' + settings.selectorAttribute +'="' + hash + '"]' : settings.initialTab;
                  $(selector, context).tab('show');
                }
                setTimeout(backToTop, 1);
            }
          }

          // We use pushState if it's available so the page won't jump, otherwise a shim.
          var changeHash = function(hash) {
            if (history && history.pushState) {
              history.pushState(null, null, window.location.pathname + window.location.search + '#' + hash);
            } else {
              scrollV = document.body.scrollTop;
              scrollH = document.body.scrollLeft;
              window.location.hash = hash;
              document.body.scrollTop = scrollV;
              document.body.scrollLeft = scrollH;
            }
          }

          var backToTop = function() {
            if (settings.backToTop === true) {
              window.scrollTo(0, 0);
            }
          }

          // Set the correct tab when the page loads
          showTabFromHash();

          // Set the correct tab when a user uses their back/forward button
          $(window).on('hashchange', showTabFromHash);

          // Change the URL when tabs are clicked
          $('a', context).on('click', function(e) {
            var hash = this.href.split('#')[1];
            if (typeof hash != 'undefined' && hash != '') {
                var adjustedhash = settings.getHashCallback(hash, this);
                changeHash(adjustedhash);
                setTimeout(backToTop, 1);
            }
          });

          return this;
      };
  }( jQuery ));
  jQuery( document ).ready(function() {
    jQuery('.nav-tabs-responsive').stickyTabs();
  });
</script>
<?php get_footer(); ?>
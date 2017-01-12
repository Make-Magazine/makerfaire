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

            <ul class="nav nav-tabs nav-stacked navbar-collapse col-xs-12 col-sm-3">

              <?php if( have_rows('tabs') ):

                $counter = 1;

                while( have_rows('tabs') ): the_row(); 

                  $tab_title = get_sub_field('tab_title');

                  if ($counter == 1) { ?>

                    <li class="active">
                      <a href="#mcp-tab<?php echo $counter; ?>" data-toggle="tab"><span><?php echo $tab_title; ?></span></a>
                    </li>

                  <?php
                  } else { ?>

                    <li>
                      <a href="#mcp-tab<?php echo $counter; ?>" data-toggle="tab"><span><?php echo $tab_title; ?></span></a>
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

                  if ($counter == 1) { ?>

                    <div class="tab-pane active" id="mcp-tab<?php echo $counter; ?>">
                      <?php echo $tab_content; ?>
                    </div>

                  <?php
                  } else { ?>

                    <div class="tab-pane" id="mcp-tab<?php echo $counter; ?>">
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
  jQuery('.nav-tabs').tabCollapse({
    tabsClass: 'hidden-sm hidden-xs',
    accordionClass: 'visible-sm visible-xs'
  });
</script>
<?php get_footer(); ?>
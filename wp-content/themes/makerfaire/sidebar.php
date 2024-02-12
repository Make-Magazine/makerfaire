<?php
/* Retrieve the faire slug.  The Entries page are not under a specific faire parent, so the slug is set on that page */
global $slug;
?>
<div class="col-md-4">
      <?php
            $goldSmith   = mf_sponsor_carousel( 'Goldsmith Sponsor',$slug );
            $silverSmith = mf_sponsor_carousel( 'Silversmith Sponsor',$slug );
            $copperSmith = mf_sponsor_carousel( 'Coppersmith Sponsor',$slug );
        if($goldSmith!=FALSE || $silverSmith!=FALSE || $copperSmith!=FALSE){
        ?>
    <div class="sidebar-bordered sponsored">
        <?php if($goldSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Goldsmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $goldSmith; ?>
            </div>
        </div>
        <?php }?>
        <?php if($silverSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Silversmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $silverSmith; ?>
            </div>
        </div>
        <?php }?>
        <?php if($copperSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Coppersmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $copperSmith; ?>
            </div>
        </div>
        <?php }?>
    </div>
        <?php }?>


</div>
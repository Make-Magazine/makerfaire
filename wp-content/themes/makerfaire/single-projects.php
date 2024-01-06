<?php get_header(); ?>

<div class="clear"></div>

<div STYLE="width:95%; margin: 0 auto;" class="container">

	<div class="row">

		<div class="content">

			<?php
            $faireData = get_field("faire_information");
            $exhibit_photo = get_field("exhibit_photo");
            $exhibit_social = get_field("exhibit_social");
            //var_dump($exhibit_social);
            ?>
            <h3><?php echo $faireData["faire_name"] ." ".$faireData["faire_year"];?></h3>
            
            <div style="float:left; width:50%">
                <h2><?php echo get_field("title");?></h2>
                <h4>City, State, Country</h4>
                <p><?php echo get_field("exhibit_description");?></p>            
            </div>
            <div style="float:right; width:50%">
                <img style="max-height:360px" src="<?php echo $exhibit_photo;?>" />
            </div>
            <div class="clear"></div>
            <div style="float:left; width:50%">            
                <p><?php echo get_field("exhibit_video_link");?></p>            
            </div>
            <div style="float:right; width:50%; background:darkblue;color:white;">
                <?php
                $exhibit_inspiration = get_field("exhibit_inspiration");
                if($exhibit_inspiration!=''){
                    echo '<h3>WHAT INSPIRED YOU TO MAKE THIS</h3>';
                    echo '<p>'.$exhibit_inspiration.'</p>';
                }
                ?>
                
            </div>
            <div class="clear"></div>
            
            
            <!-- Maker Data -->
            <?php
            $maker_data = get_field("maker_data");
            foreach($maker_data as $maker){
                //var_dump($maker);
                ?>
                <div style="width:30%; float:left">
                    <img style="max-height:360px" src="<?php echo $maker["maker_photo"]["url"];?>" />
                </div>
                <div style="width:70%; float:right">
                    <h4><?php echo $maker["maker_or_group_name"];?></h4>
                    <p><?php echo $maker["maker_bio"]?></p>
                    <div><?php echo $maker["maker_website"]?></div>
                    <?php if(is_array($maker['maker_social'])){
                        ?>
                        <ul>
                        <?php
                        foreach($maker["maker_social"] as $social){
                            ?>
                            <li><?php echo $social["maker_social_link"];?></li>
                            <?php
                        }
                        ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
                <div class="clear"></div>
                <?php
            }
            
            
            
            //echo '<div>'.$exhibit_social
            
				$faire = ( isset( $_GET['faire'] ) && ! empty( $_GET['faire'] ) ) ? sanitize_title( $_GET['faire'] ) : '';				
			?>

		</div><!--Content-->		

	</div>

</div><!--Container-->

<?php get_footer(); ?>
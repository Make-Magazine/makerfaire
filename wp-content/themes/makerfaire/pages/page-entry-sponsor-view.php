<?php
/*
 * This is the public facing entry page view
 *
 */
?>
<div class="container-fluid sponsor-entry">
    <div class="sponsor-head">
        <h1><?php echo $project_title; ?></h1>
        <span class="divider-separator"></span>
        <h2 class="entry-type">
            <?php if ($displayFormType == true) { echo $formType; } ?>
        </h2>
        <div class="entry-location">
            <?php if (isset($location) && $location == true) { echo "Find us at " . $location; } ?>
        </div>
    </div>
    <div class="sponsor-body">
        <div class="sponsor-image">
            <img class="img-responsive dispPhoto" src="<?php echo $project_photo; ?>" />
        </div>
        <div class="sponsor-content">
            <div id="project_short" class="lead">
                <p><?php echo nl2br($project_short); ?></p>
                <h3>Contact</h3>
                <?php if (!empty($project_website)) {
                    ?> <a href="<?php echo $project_website; ?>" class="sponsor-website" target="_blank" ><i class="fa fa-globe"></i><?php echo $project_website; ?></a><?php
                } ?>
                <?php if ($dispMakerInfo) { 
                    foreach ($makers as $key => $maker) { 
                        echo $maker['social']; 
                    }
                } ?>
            </div>

        </div>
    </div>
</div>
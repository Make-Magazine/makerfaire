<!-- Marker Separation Tab -->
<div id="tabs-marker-separation">
    <div class="heading">
        <?php _e("Marker Separation", "wp-google-maps"); ?>
    </div>

    <!-- Helper -->
    <div class="general-heading-notice">
        <p>
            <?php _e('This feature will group nearby or overlapping markers together using a placeholder marker. When the placeholder marker is clicked, the group will open, separating the markers on screen. This is intended for when you have several markers at the same address but would like the user to be able to view data from all the markers.', 'wp-google-maps'); ?>
        </p>
    </div>

    <!-- Enable -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e('Enable Separation', 'wp-google-maps'); ?>
        </div>

        <div class='switch'>
            <input name='wpgmza_near_vicinity_control_enabled' 
                    class='cmn-toggle cmn-toggle-round-flat' 
                    type='checkbox' 
                    id='wpgmza_near_vicinity_control_enabled' 
                    value='yes'/>
            
            <label for='wpgmza_near_vicinity_control_enabled'></label>
        </div>
    </div>

    <!-- Marker Icon -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e('Placeholder Marker', 'wp-google-maps'); ?>
        </div>
        <div>
			<div id="marker-separator-placeholder-icon-picker-container"></div>
        </div>
    </div>

    <!-- Radius -->
    <div class="tab-row has-hint">
        <div class="title">
            <?php esc_html_e('Affect Radius', 'wp-google-maps'); ?>
        </div>

        <div>
            <input name='wpgmza_near_vicinity_aff_radius' id='wpgmza_near_vicinity_aff_radius' placeholder='50' type="number" min="1" step="1" value="50" />
            <?php _e("Meters", "wp-google-maps"); ?>
        </div>
    </div>

    <!-- Radius Hint --> 
    <div class="tab-row">
        <div class="title"></div>

        <small class="hint">
            <?php esc_html_e("Markers within this threshold of one another will be grouped together", "wp-google-maps"); ?>
        </small>
    </div>

    <!-- Legacy Removed -->

    <!-- Shape --> 
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e("Shape", "wp-google-maps"); ?>
        </div>

        <select name="marker_separator_algorithm">
            <option value="circle">
                <?php _e('Circle', 'wp-google-maps'); ?>
            </option>
            <option value="spiral">
                <?php _e('Spiral', 'wp-google-maps'); ?>
            </option>
            <option value="hexagon">
                <?php _e('Hexagon', 'wp-google-maps'); ?>
            </option>
            <option value="line">
                <?php _e('Line', 'wp-google-maps'); ?>
            </option>
            <option value="grid">
                <?php _e('Grid', 'wp-google-maps'); ?>
            </option>
        </select>
    </div>

    <!-- Animate -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e("Animate Separation", "wp-google-maps"); ?>
        </div>

        <div class='switch'>
            <input name='marker_separator_animate' 
                    class='cmn-toggle cmn-toggle-round-flat' 
                    type='checkbox' 
                    id='marker_separator_animate' 
                    value='yes'/>
            
            <label for='marker_separator_animate'></label>
        </div>
    </div>

    <!-- Animate - Duration -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e("Animation Duration", "wp-google-maps"); ?>
        </div>

        <div>
            <input name='marker_separator_animation_duration' type="number" value="0.5" min="0" step="0.01"/>
			<?php
				esc_html_e('Seconds', 'wp-google-maps');
            ?>
        </div>
    </div>

    <!-- Animate - Stagger -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e("Stagger Animation", "wp-google-maps"); ?>
        </div>

        <div class='switch'>
            <input name='marker_separator_stagger_animation' 
                    class='cmn-toggle cmn-toggle-round-flat' 
                    type='checkbox' 
                    id='marker_separator_stagger_animation' 
                    value='yes'/>
            
            <label for='marker_separator_stagger_animation'></label>
        </div>
    </div>

    <!-- Animate - Stagger Interval -->
    <div class="tab-row">
        <div class="title">
            <?php esc_html_e("Stagger Interval", "wp-google-maps"); ?>
        </div>

        <div>
            <input name='marker_separator_stagger_interval' type="number" value="0.5" min="0" step="0.01"/>
			<?php
				esc_html_e('Seconds', 'wp-google-maps');
            ?>
        </div>
    </div>

    <!-- Group Size -->
    <div class="tab-row has-hint">
        <div class="title">
            <?php esc_html_e("Maximum Group Size", "wp-google-maps"); ?>
        </div>

        <div>
            <input name="marker_separator_maximum_group_size" type="number" min="2" max="64" step="1" value="16" />
            <small class="hint"><?php esc_html_e("Setting this too high may result in unnecessarily increased load times.", "wp-google-maps"); ?></small>
        </div>
    </div>

    <!-- Group Size Hint --> 
    <div class="tab-row">
        <div class="title"></div>

        <small class="hint">
            <?php 
                esc_html_e("We recommend using a group size no larger than the maximum number of overlapping markers you anticipate, and that you do not use a higher number than necessary.", "wp-google-maps");
            ?>
        </small>
    </div>
</div>
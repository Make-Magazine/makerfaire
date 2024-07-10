<div class="wpgmza-marker-icon-editor">
    <div class="wpgmza-marker-icon-editor-actions">
        <div class="wpgmza-button wpgmza-marker-icon-editor-close wpgmza-shadow" data-action="close" title="<?php _e("Close", "wp-google-maps"); ?>"></div>
    </div>
    <!-- Split Editor -->
    <div class="wpgmza-marker-icon-editor-split-view">
        <!-- Column: Small -->
        <div class="wpgmza-marker-icon-editor-split-view-column small">
            <!-- Editor Canvas -->
            <div class="wpgmza-marker-icon-editor-panel">
                <div class="wpgmza-marker-icon-editor-panel-inner">
                    <div class='wpgmza-marker-icon-editor-preview-heading'>
                        <?php _e("Preview", "wp-google-maps"); ?>
                        <div class="wpgmza-marker-icon-editor-preview-style-control" title="<?php _e("Change preview window style", "wp-google-maps"); ?>">
                            <div class="wpgmza-marker-icon-editor-preview-style" data-style='dark'></div>
                            <div class="wpgmza-marker-icon-editor-preview-style" data-style='light'></div>
                            <div class="wpgmza-marker-icon-editor-preview-style" data-style='transparent'></div>
                            <div class="wpgmza-marker-icon-editor-preview-style" data-style='map'></div>
                        </div>
                    </div>

                    <div class="wpgmza-marker-icon-editor-preview">
                        <canvas width="100" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Editor Size -->
            <div class="wpgmza-marker-icon-editor-panel wpgmza-icon-shape-split-row wpgmza-icon-output-resolution" data-render-mode="shape">
                <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Resolution", "wp-google-maps"); ?></span>
                <select>
                    <option value='default'><?php _e('Default', 'wp-google-maps'); ?></option>
                    <option value='retina'><?php _e('Retina', 'wp-google-maps'); ?></option>
                </select>
            </div>

            <!-- Editor actions -->
            <div class="wpgmza-marker-icon-editor-panel wpgmza-marker-icon-editor-split-view-action-wrapper">
                <div class="wpgmza-marker-icon-editor-actions">
                    <div class="wpgmza-button" data-action="use" data-busy="<?php _e("Saving...", "wp-google-maps"); ?>"><?php _e("Use Icon", "wp-google-maps"); ?></div>
                </div>
            </div>
        </div>

        <!-- Column: Fill -->
        <div class="wpgmza-marker-icon-editor-split-view-column fill">
            <!-- Scrollable Section -->
            <div class="wpgmza-marker-icon-configurator">
                <!-- Editor Shape Templates -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row wpgmza-marker-icon-editor-panel-heading-controller">
                        <span><?php _e("Templates", "wp-google-maps"); ?></span>

                        <select data-history-control='wpgmza-marker-icon-editor-templates'>
                            <option value='system'>Bundled</option>
                            <option value='storage'>History</option>
                        </select>
                    </div>
                    <div class="wpgmza-marker-icon-editor-templates" data-history-control-listener></div>
                    <div data-switch-mode='image'>Switch to image mode</div>
                </div>

                <!-- Editor Icon List -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="image">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row wpgmza-marker-icon-editor-panel-heading-controller">
                        <span><?php _e("Image", "wp-google-maps"); ?></span>

                        <select data-history-control='wpgmza-marker-icon-editor-list'>
                            <option value='system'>Bundled</option>
                            <option value='storage'>History</option>
                        </select>
                    </div>
                    <div class="wpgmza-marker-icon-editor-list" data-history-control-listener></div>
                    <div data-switch-mode='shape'>Back to shape mode</div>
                </div>

                <!-- Shape Config: Fill -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Fill", "wp-google-maps"); ?></span>
                    </div>

                    <!-- Fill style selector -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row">
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Style", "wp-google-maps"); ?></span>
                        <select data-control="colorMode" data-content-toggle-trigger='colorMode'>
                            <option value='solid'><?php _e('Solid', 'wp-google-maps'); ?></option>
                            <option value='gradient-linear'><?php _e('Linear Gradient', 'wp-google-maps'); ?></option>
                            <option value='gradient-radial'><?php _e('Radial Gradient', 'wp-google-maps'); ?></option>
                        </select>
                    </div>

                    <!-- Solid Color -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row" data-content-toggle-listener='colorMode' data-content-toggle-condition='solid'>
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Color", "wp-google-maps"); ?></span>
                        <input data-control="color" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#ea4335">
                    </div>

                    <!-- Gradient A Color -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row" data-content-toggle-listener='colorMode' data-content-toggle-condition='gradient-linear|gradient-radial'>
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Color A", "wp-google-maps"); ?></span>
                        <input data-control="colorGradientA" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#ea4335">
                    </div>

                    <!-- Gradient B Color -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row" data-content-toggle-listener='colorMode' data-content-toggle-condition='gradient-linear|gradient-radial'>
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Color B", "wp-google-maps"); ?></span>
                        <input data-control="colorGradientB" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#cb2115">
                    </div>

                    <!-- Gradient Angle -->
                    <div class="wpgmza-icon-shape-control-wrapper" data-content-toggle-listener='colorMode' data-content-toggle-condition='gradient-linear'>
                        <span><?php _e("Angle", "wp-google-maps"); ?></span>

                        <div class="wpgmza-icon-shape-slider-stack">
                            <input data-control="colorGradientAngle" data-type='deg' data-suffix="deg" type="range" min="0" max="360" value="90">
                            <input type='text' data-linked>
                            <span data-suffix></span>
                        </div>
                    </div>
                </div>

                <!-- Shape Config: Shadow -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Shadow", "wp-google-maps"); ?></span>
                    </div>

                    <!-- Shadow Color -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row">
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Color", "wp-google-maps"); ?></span>
                        <input data-control="shadowColor" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#000000">
                    </div>

                    <!-- Shadow Blur Slider -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row">
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Blur", "wp-google-maps"); ?></span>

                        <div class="wpgmza-icon-shape-slider-stack wpgmza-icon-shape-split-column-align-end">
                            <input data-control="shadowBlur" data-type='pixel' data-suffix='px' data-style='micro' type="range" min="0" max="10" value="0">
                            <input type='text' data-linked>
                            <span data-suffix></span>
                        </div>
                    </div>
                </div>

                <!-- Shape Config: Stroke -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Stroke", "wp-google-maps"); ?></span>
                    </div>

                    <!-- Stroke Color -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row">
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Color", "wp-google-maps"); ?></span>
                        <input data-control="strokeColor" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#FFFFFF">
                    </div>

                    <!-- Stroke Weight Slider -->
                    <div class="wpgmza-icon-shape-control-wrapper wpgmza-icon-shape-split-row">
                        <span class='wpgmza-icon-shape-split-column-fill'><?php _e("Weight", "wp-google-maps"); ?></span>

                        <div class="wpgmza-icon-shape-slider-stack wpgmza-icon-shape-split-column-align-end">
                            <input data-control="strokeWeight" data-type='pixel' data-suffix='px' data-style='micro' type="range" min="0" max="10" value="0">
                            <input type='text' data-linked>
                            <span data-suffix></span>
                        </div>
                    </div>
                </div>

                <!-- Shape Config: Body -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Body", "wp-google-maps"); ?></span>
                    </div>

                    <div class="wpgmza-icon-shape-control-group">
                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Width", "wp-google-maps"); ?></span>

                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="width" data-shape='body' data-type='percentage' data-suffix="%" type="range" min="10" max="100" value="100" data-lock='body.dimensions'>
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>

                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Height", "wp-google-maps"); ?> <span data-lock-trigger='body.dimensions' data-locked='true' title="<?php esc_html_e('Lock width/height sliders', 'wp-google-maps'); ?>"></span></span>
                            
                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="height" data-shape='body' data-type='percentage' data-suffix="%" type="range" min="10" max="100" value="100" data-lock='body.dimensions'>
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>

                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Roundness", "wp-google-maps"); ?></span>

                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="roundness" data-shape='body' data-type='percentage' data-suffix="%" type="range" min="0" max="100" value="100">
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shape Config: Tail -->
                <div class="wpgmza-marker-icon-editor-panel" data-render-mode="shape">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Tail", "wp-google-maps"); ?></span>
                    </div>

                    <div class="wpgmza-icon-shape-control-group">
                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Thickness", "wp-google-maps"); ?></span>
                            
                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="thickness" data-shape='tail' data-type='percentage' data-suffix="%" type="range" min="0" max="100" value="100">
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>

                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Length", "wp-google-maps"); ?></span>

                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="length" data-shape='tail' data-type='percentage' data-suffix="%" type="range" min="0" max="100" value="100">
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>

                        <div class="wpgmza-icon-shape-control-wrapper">
                            <span><?php _e("Roundness", "wp-google-maps"); ?></span>

                            <div class="wpgmza-icon-shape-slider-stack">
                                <input data-control="roundness" data-shape='tail' data-type='percentage' data-suffix="%" type="range" min="0" max="100" value="100">
                                <input type='text' data-linked>
                                <span data-suffix></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Generic Config: Adjustments --> 
                <div class="wpgmza-marker-icon-editor-panel">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Adjustments", "wp-google-maps"); ?></span>
                    </div>

                    <div class="wpgmza-marker-icon-editor-controls">
                        <!-- Effect Sliders -->
                        <div class="wpgmza-icon-effect-mode-sliders">
                            <!-- Effect : Hue Rotate -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Hue Rotate", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="hue-rotate" data-suffix="deg" type="range" min="0" max="360" value="0">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>

                            <!-- Effect : Brightness -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Brightness", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="brightness" data-suffix="%" type="range" min="0" max="100" value="100">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>

                            <!-- Effect : Saturate -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Saturate", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="saturate" data-suffix="%" type="range" min="0" max="200" value="100">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>

                            <!-- Effect : Contrast -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Contrast", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="contrast" data-suffix="%" type="range" min="0" max="200" value="100">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>

                            <!-- Effect : Opacity -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Opacity", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="opacity" data-suffix="%" type="range" min="0" max="100" value="100">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>

                            <!-- Effect : Invert -->
                            <div class="wpgmza-icon-effect-mode-slider-group">
                                <span><?php _e("Invert", "wp-google-maps"); ?></span>
                                <div class="wpgmza-icon-shape-slider-stack">
                                    <input data-control="invert" data-suffix="%" type="range" min="0" max="100" value="0">
                                    <input type='text' data-linked>
                                    <span data-suffix></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Generic Config: Overlays -->
                <div class="wpgmza-marker-icon-editor-panel">
                    <div class="wpgmza-marker-icon-editor-panel-heading-row">
                        <span><?php _e("Overlay", "wp-google-maps"); ?></span>
                    </div>

                    <!-- Layer controls -->
                    <div class="wpgmza-marker-icon-editor-layer">
                        <!-- Layer mode -->
                        <div class="wpgmza-icon-layer-mode-wrapper">
                            <span><?php _e("Type", "wp-google-maps"); ?></span>
                            <select>
                                <option value="text"><?php _e("Text", "wp-google-maps"); ?></option>
                                <option value="icon"><?php _e("Icon", "wp-google-maps"); ?></option>
                            </select>
                        </div>

                        <!-- Text layer controls -->
                        <div class="wpgmza-icon-layer-control" data-mode="text">
                            <div class="layer-input-wrapper">
                                <span><?php _e("Content", "wp-google-maps"); ?></span>
                                <input type="text" data-control="content">
                            </div>
                        </div>

                        <!-- Icon layer controls -->
                        <div class="wpgmza-icon-layer-control" data-mode="icon">
                            <div class="layer-input-wrapper">
                                <span><?php _e("Icon", "wp-google-maps"); ?></span>
                                <input type="text" class="icon-picker" data-control="icon" placeholder="Start typing..." autocomplete="off">
                            </div>
                        </div>

                        <!-- Shared layer controls -->
                        <div class="wpgmza-icon-layer-control">
                            <div class="layer-input-wrapper">
                                <span><?php _e("Size", "wp-google-maps"); ?></span>
                                <input type="number" data-control="size" min="0" max="100" value="20">
                            </div>

                            <div class="layer-input-wrapper">
                                <span><?php _e("Offset", "wp-google-maps"); ?></span>
                                <div class="grouped-input-stack">
                                    <span>x</span>
                                    <input type="number" data-control="xOffset" value="0">
                                    <span>y</span>
                                    <input type="number" data-control="yOffset" value="0">
                                </div>
                            </div>

                            <div class="layer-input-wrapper">
                                <span><?php _e("Color", "wp-google-maps"); ?></span>
                                <div class="light-toggle-stack">
                                    <input data-control="overlayColor" data-support-palette="false" data-support-alpha="false" class="wpgmza-color-input" data-anchor="right" data-container=".wpgmza-marker-icon-editor" value="#FFFFFF">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
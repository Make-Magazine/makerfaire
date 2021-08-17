<?php
/**
 * DFP Ad Block
 *
 * Initializes all of the ads for Maker Faire.
 *
 */
global $post; ?>


<!-- Start GPT Tag -->
<script async src='https://securepubads.g.doubleclick.net/tag/js/gpt.js'></script>
<script>
  window.googletag = window.googletag || {cmd: []};
  googletag.cmd.push(function() {
    var mapping1 = googletag.sizeMapping()
                            .addSize([1000, 200], [[970, 90], [970, 250], [940, 250], [728, 90]])
                            .addSize([800, 200], [[728, 90]])
                            .addSize([0, 0], [[320, 50],[300,50],[300,100]])
                            .build();

    var mapping2 = googletag.sizeMapping()
                            .addSize([1000, 200], [[300, 250], [300, 600]])
                            .addSize([0, 0], [[300, 250]])
                            .build();

    googletag.defineSlot('/3829728/make_leader_1', [[970,90],[970,250],[940,250],[728,90],[320,50],[300,50],[300,100]], 'div-gpt-ad-2739897-1')
             .defineSizeMapping(mapping1)
             .addService(googletag.pubads());
    googletag.defineSlot('/3829728/make_leader_2', [[970,90],[970,250],[940,250],[728,90],[320,50],[300,50],[300,100]], 'div-gpt-ad-2739897-6')
             .defineSizeMapping(mapping1)
             .addService(googletag.pubads());
    googletag.defineSlot('/3829728/make_mpu_1', [[300,250],[300,600]], 'div-gpt-ad-2739897-2')
             .defineSizeMapping(mapping2)
             .addService(googletag.pubads());
    googletag.defineSlot('/3829728/make_mpu_2', [[300,250]], 'div-gpt-ad-2739897-3')
             .addService(googletag.pubads());
    googletag.defineSlot('/3829728/make_mpu_3', [[300,250],[300,600]], 'div-gpt-ad-2739897-4')
             .defineSizeMapping(mapping2)
             .addService(googletag.pubads());
    googletag.defineSlot('/3829728/make_mpu_4', [[300,250]], 'div-gpt-ad-2739897-5')
             .addService(googletag.pubads());

    googletag.pubads().setTargeting('PostID', [''])
	     .setTargeting('Tags', [''])
             .setTargeting('Section', [''])
             .setTargeting('Category', ['']);
    googletag.pubads().collapseEmptyDivs();
    googletag.pubads().setCentering(true);
    googletag.enableServices();
  });
</script>
<!-- End GPT Tag -->
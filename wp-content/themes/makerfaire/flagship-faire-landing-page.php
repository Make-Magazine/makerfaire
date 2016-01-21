<?php /* Template Name: Flagship Faire Landing Page */  ?>
<?php get_header(); ?>
<?php  $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full' ); ?>
</style>
<div class="flagship-faire-wrp" id="flagship-faire-wrp">
  <div class="container">
    <div class="col-md-12">
      <img src="<?php echo get_post_meta($post->ID, 'flagship-faire-badge-img-url', true); ?>"
        width="255" height="255" class="flagship-badge hidden-xs hidden-sm"
        alt="Featured Faire badge image">
    </div>
    <div class="row">
      <div class="col-md-12 flagship-faire-box">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
</div>
<script>
(function() { // Random background image from 'flagship-background' custom fields:
  var img = [];
  <?php foreach (get_post_meta($post->ID, 'flagship-background', false) as $i) {
    echo 'img.push(\'' . $i . '\');'; // push all meta images src to js array
  } ?>
  document.getElementById('flagship-faire-wrp').style.backgroundImage = 'url(' + img[Math.floor(Math.random() * img.length)] + ')';
})();
</script>
<?php get_footer(); ?>

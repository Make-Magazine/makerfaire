<?php /* Template Name: Flagship Faire Landing Page */  ?>
<?php get_header(); ?>
<?php  $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full' ); ?>
</style>
<div class="flagship-faire-wrp" style="background-image: url('<?php echo $feat_image; ?>');">
  <div class="container">
    <div class="col-md-12">
      <img src="<?php echo get_post_meta($post->ID, 'flagship-faire-badge-img-url', true); ?>"
        width="255" height="255" class="flagship-badge"
        alt="Featured Faire badge image">
    </div>
    <div class="row">
      <div class="col-md-12 flagship-faire-box">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>

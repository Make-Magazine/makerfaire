
<?php

/*
Template Name: 404
*/

get_header( 'universal' );

?>
<style>
   body.error404 {
    background-color: #0a5b79;
}
   div.caption404 {
    border: 14px solid rgba(255,255,255,.4);
    border-radius: 40px;
    margin-bottom: 20px;
    margin-top: 50px;
    padding: 40px;
}
p.headline404 {
    color: #fff;
    font-size: 25px;
}
p.body404 {
    color: #fff;
    font-size: 20px;
    font-weight: lighter;
}
</style>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8">
      <div class="caption404">
        <p class="headline404">This is not the page you're looking for.</p>
        <p class="body404">
           It must have moved or mysteriously departed.<br><Br>
           Let's go back to the <a href='https://makerfaire.com'>beginning.</a>
        </p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-offset-1 col-sm-offset-4">
      <img src="<?php echo get_template_directory_uri(). '/images/404-makey.png' ?>" alt="Makey 404" />
    </div>
  </div>
</div>

<?php get_footer(); ?>
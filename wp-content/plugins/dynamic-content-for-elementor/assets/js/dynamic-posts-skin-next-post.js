var Widget_DCE_Dynamicposts_nextpost_Handler = function ($scope, $) {
    var elementSettings = dceGetElementSettings($scope);
    var id_scope = $scope.attr('data-id');

    var use_template = elementSettings[dceDynamicPostsSkinPrefix+'nextpost_use_template']
    var template_id = elementSettings[dceDynamicPostsSkinPrefix+'nextpost_content_template_id'];

    eval('var list_posts = dce_listPosts_'+id_scope+';');
    var list_post_associate = [];

    var totalPosts = list_posts.length;

    $scope.removeClass('elementor-widget-empty');

    // Gli effetti allo scroll
    var introSection,
        introTitle,
        introContent,
        introSectionHeight = 480, //introSection.height(),
        scrtop = 0,
        //change scaleSpeed if you want to change the speed of the scale effect
        scaleSpeed = 0.3,
        bgsScaleSpeed = 10,
        translateSpeed = 200,
        //change opacitySpeed if you want to change the speed of opacity reduction effect
        opacitySpeed = 1;
    var initEffectScroll = function(){

        triggerAnimation();

    };
    //bind the scale event to window scroll if window width > $MQ (unbind it otherwise)
    function triggerAnimation(){
            $(window).on('scroll', function(){
                //The window.requestAnimationFrame() method tells the browser that you wish to perform an animation- the browser can optimize it so animations will be smoother
                window.requestAnimationFrame(animateIntro);

            });

    }
    //assign a scale transformation to the introSection element and reduce its opacity
    function animateIntro () {

        introSection = $scope.find('.dce-page.dce-current .dce-big-image');
        introTitle = $scope.find('.dce-page.dce-current .dce-big-image .dce-item_title, .dce-page.dce-current .dce-big-image .dce-byline');
        introContent = $scope.find('.dce-page.dce-current .dce-content');

        scrtop = $(window).scrollTop()-$scope.offset().top;
        var scrollPercentage = (scrtop/introSectionHeight),
            scaleValue = 1 - scrollPercentage*scaleSpeed;
            bgScaleValue = (scrollPercentage*bgsScaleSpeed) + 50;
            translateValue = (1 * ( scrollPercentage*translateSpeed ));
            translateValue1 = (-1 * ( scrollPercentage*((0.5)*100) ));

        if(typeof introSection !== 'undefined'){
            //check if the introSection is still visible
            if( scrtop < introSectionHeight ) {
                if(scrtop <= 0){
                    scaleValue = 1;
                    translateValue = 0;
                    bgScaleValue = 50;
                }
                introSection.css({
                    '-moz-transform': 'scale(' + scaleValue + ') translateZ(0)',
                    '-webkit-transform': 'scale(' + scaleValue + ') translateZ(0)',
                    '-ms-transform': 'scale(' + scaleValue + ') translateZ(0)',
                    '-o-transform': 'scale(' + scaleValue + ') translateZ(0)',
                    'transform': 'scale(' + scaleValue + ') translateZ(0)',
                    'background-position': '50% ' + bgScaleValue + '%'
                });

                introTitle.css({
                    '-moz-transform': 'translateY(' + translateValue + 'px) translateZ(0)',
                    '-webkit-transform': 'translateY(' + translateValue + 'px) translateZ(0)',
                    '-ms-transform': 'translateY(' + translateValue + 'px) translateZ(0)',
                    '-o-transform': 'translateY(' + translateValue + 'px) translateZ(0)',
                    'transform': 'translateY(' + translateValue + 'px) translateZ(0)',
                });

                introContent.css({
                    '-moz-transform': 'translateY(' + translateValue1 + 'px) translateZ(0)',
                    '-webkit-transform': 'translateY(' + translateValue1 + 'px) translateZ(0)',
                    '-ms-transform': 'translateY(' + translateValue1 + 'px) translateZ(0)',
                    '-o-transform': 'translateY(' + translateValue1 + 'px) translateZ(0)',
                    'transform': 'translateY(' + translateValue1 + 'px) translateZ(0)',
                });
            }
        }
    }

    /* jQuery Setup
    ************************************************************************/
    jQuery.ajaxSetup({
      cache: false
    });

    /* ArticleAnimator Object
    ************************************************************************/
    var ArticleAnimator = ArticleAnimator || {
      canScroll:          true,
      initialLoad:        true,
      animationDuration:  700,
      postCount:          (totalPosts-1),
      currentPostIndex:   0,
      postCache:          {},
      pageTemplate:       null,
    };

    ArticleAnimator.load = function(){
      this.currentPostIndex = getURLIndex();
      this.makeSelections();
      $body.append( this.$current );
      $body.append( this.$next );

      var self = this;
      this.createPost({ type: 'current' }, function(){



        self.createPost({ type: 'next' }, function(){

          /* Selections. */
          self.refreshCurrentAndNextSelection();

          /* Push initial on to stack */
          history.pushState(pageState(), "", "#" + self.postCache[self.currentPostIndex].slug);

          /* Bind to some events. */
          self.bindGotoNextClick();
          self.bindPopstate();
          self.bindWindowScroll();


        });
      });
    };

    ArticleAnimator.makeSelections = function(){
      this.$page         = $('.dce-page');
      this.pageTemplate  = elementToTemplate( this.$page.clone() );
      this.$current      = this.currentElementClone();
      this.$next         = this.nextElementClone();
    };

    ArticleAnimator.getPost = function(index, callback){
      callback = callback || $.noop;

      if ( this.postCache[index] ){
        callback( this.postCache[index] );
        return;
      }
      //http://localhost:8888/demosite/wp-json/wp/v2/posts/10545
      var self = this;
      var urlJson = 'http://localhost:8888/demosite/wp-json/wp/v2/'+list_posts[index].type+'/'+ list_posts[index].id;

      $.getJSON(urlJson, function(d){

        // image
        d.image = list_posts[index].image;
        d.date = list_posts[index].date;
        d.title = list_posts[index].title;
        d.author = list_posts[index].author;
        d.authorimage = list_posts[index].authorimage;
        d.terms = list_posts[index].terms;

        list_post_associate[d.slug] = index;
        self.postCache[index] = d;
            callback(d);
      });
    };

    ArticleAnimator.nextPostIndex = function(index){
      return (index === this.postCount) ? 0 : index + 1
    };

    ArticleAnimator.createPost = function(opts, callback){
      opts      = opts || {};
      var self  = this;
      var type  = opts['type'] || 'next';

      if ( opts['fromTemplate'] ){
        $body.append( this.nextElementClone() );
        this['$' + type] = $('.dce-' + type);
      }

      var index = (type == 'next') ? this.nextPostIndex( this.currentPostIndex) : this.currentPostIndex;

      this.getPost(index, function(d){
        self.contentizeElement(self['$' + type], d);
        callback && callback();
      });

    };

    ArticleAnimator.contentizeElement = function($el, d){
      $el.find('.dce-big-image').css({ backgroundImage: "url(" + d.image + ")" });
      $el.find('.dce-title').html(d.title);
      $el.find('.dce-description').html(d.title);
      //
      if(use_template){
          jQuery.ajax({
                url: dceAjaxPath.ajaxurl,
                dataType: "html",
                type: 'POST',
                data: {
                    'action': 'modale_action',
                    'post_href': d.link,
                    'template_id': template_id
                },
                error: function () {
                    erroreModale();
                },

                success: function (data, status, xhr) {

                    var $result = jQuery(data).filter('.content-p');

                    $result.find('.titolo-nativo').remove();

                    $el.find('.dce-content .dce-text').html($result);

                    var element_el = $result.find('.elementor-element');
                    element_el.each(function (i) {
                        var el = jQuery(this).data('widget_type');
                        elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                    });

                },

          });
      }else{
        $el.find('.dce-content .dce-text').html(d.content.rendered);
      }
      $el.find('.dce-byline .dce-item_date').html(d.date);
      $el.find('.dce-byline .dce-author').html(d.author);
      $el.find('.dce-byline .dce-author-avatar img').attr('src',d.authorimage);
      var terms = d.terms.split(',');
      terms.forEach(function(item) {
        $el.find('.dce-byline .dce-termstaxonomy').append('<span>'+item+'</span>');
      });
      $scope.find('.dce-nota-nextpost').remove();
    };

    ArticleAnimator.animatePage = function(callback){
      var self              = this;
      var translationValue  = this.$next.get(0).getBoundingClientRect().top;
      this.canScroll        = false;

      this.$current.addClass('fade-up-out');

      this.$next.removeClass('dce-content-hidden dce-next')
           .addClass('easing-upward')
           .css({ "transform": "translate3d(0, -"+ translationValue +"px, 0)" });

      setTimeout(function(){
          scrollTop();
          self.$next.removeClass('easing-upward');
          self.$current.remove();

          self.$next.css({ "transform": "" });
          self.$current = self.$next.addClass('dce-current');

          self.canScroll = true;
          self.currentPostIndex = self.nextPostIndex( self.currentPostIndex );

          callback();
      }, self.animationDuration );
    };

    ArticleAnimator.bindGotoNextClick = function(){
      var self  = this;
      var e     = 'ontouchstart' in window ? 'touchstart' : 'click';

      this.$next.find('.dce-big-image').on(e, function(e){
        e.preventDefault();
        $(this).unbind(e);

        self.animatePage(function(){
          self.createPost({ fromTemplate: true, type: 'next' });
          self.bindGotoNextClick();
          history.pushState( pageState(), '', "#" + self.postCache[self.currentPostIndex].slug);
        });
      });
    };

    ArticleAnimator.bindPopstate = function(){
      var self = this;
      $window.on('popstate', function(e){

        if( !history.state || self.initialLoad ){
          self.initialLoad = false;
          return;
        }

        self.currentPostIndex = history.state.index;
        self.$current.replaceWith( history.state.current );
        self.$next.replaceWith( history.state.next );

        self.refreshCurrentAndNextSelection();
        self.createPost({ type: 'next' });
        self.bindGotoNextClick();
      });
    };

    ArticleAnimator.bindWindowScroll = function(){
      var self = this;
      $window.on('mousewheel', function(ev){
        if ( !self.canScroll )
          ev.preventDefault()
      });
    };

    ArticleAnimator.refreshCurrentAndNextSelection = function(){
      this.$current      = $('.dce-page.dce-current');
      this.$next         = $('.dce-page.dce-next');
    };

    ArticleAnimator.nextElementClone = function(){
      return this.$page.clone().removeClass('dce-hidden').addClass('dce-next dce-content-hidden');
    };

    ArticleAnimator.currentElementClone = function(){
      return this.$page.clone().removeClass('dce-hidden').addClass('dce-current');
    };

    /* Helper Functions.
    ************************************************************************/
    function elementToTemplate($element){
      return $element.get(0).outerHTML;
    }

    function scrollTop(){
        var offsetScope = $body.offset();
      $(document.body).add($html).scrollTop(offsetScope.top);
    }

    function pageState(){
        return {
          index: ArticleAnimator.currentPostIndex,
          current: elementToTemplate(ArticleAnimator.$current),
          next: elementToTemplate(ArticleAnimator.$next)
        };
    }

    function getURLIndex(){
      return parseInt( (history.state && history.state.index) || list_post_associate[window.location.hash.replace('#', "")] || ArticleAnimator.currentPostIndex );
    }

    // -------------- init
    /* A couple of selections. */
    $body         = $scope.find('.dce-nextpost-wrapper');
    $window       = $(window);
    $html         = $(document.documentElement);

    /* Let's get it started. */
    ArticleAnimator.load();
    initEffectScroll();
};

jQuery(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.nextpost', Widget_DCE_Dynamicposts_nextpost_Handler);

});

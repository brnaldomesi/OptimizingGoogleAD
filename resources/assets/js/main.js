var App = (function () {
  'use strict';

  //Basic Config
  var config = {
    assetsPath: 'assets',
    imgPath: 'img',
    jsPath: 'js',
    libsPath: 'lib',
    leftSidebarSlideSpeed: 200,
    leftSidebarToggleSpeed: 300,
    enableLeftSidebar: true,
    enableSwipe: true,
    swipeTreshold: 100,
    scrollTop: true,
    openRightSidebarClass: 'open-right-sidebar',
    openLeftSidebarClass: 'open-left-sidebar',
    disabledLeftSidebarClass: 'be-left-sidebar-disabled',
    offCanvasLeftSidebarClass: 'be-offcanvas-menu',
    offCanvasLeftSidebarMobileClass: 'be-offcanvas-menu-sm',
    topHeaderMenuClass: 'be-top-menu',
    closeRsOnClickOutside: true,
    removeLeftSidebarClass: 'be-nosidebar-left',
    collapsibleSidebarClass: 'be-collapsible-sidebar',
    collapsibleSidebarCollapsedClass: 'be-collapsible-sidebar-collapsed',
    openLeftSidebarOnClick: true,
    transitionClass: 'be-animate',
    openSidebarDelay: 400
  };

  var colors = {};
  var scrollers = {};
  var body,
      wrapper,
      topNavbar,
      leftSidebar,
      rightSidebar,
      asideDiv,
      notificationsDiv,
      toggleLeftSidebarButton,
      openSidebar;

  //Perfect scrollbar variables
  var ps_be_scroller_notifications,
      ps_left_sidebar,
      ps_be_scroller_left_sidebar,
      ps_sub_menu_scroller,
      ps_chat_scroll,
      ps_todo_scroll,
      ps_settings_scroll,
      ps_messages_scroll,
      ps_aside_scroll;

  //Get the template css colors into js vars
  function getColor( c ){
    var tmp = $("<div>", { class: c }).appendTo("body");
    var color = tmp.css("background-color");
    tmp.remove();

    return color;
  }

  // Refresh scroller
  function updateScroller(ps_object){
    if( typeof ps_object !== 'undefined' ) {
      ps_object.update();
    }
  }

  // Destroy scroller
  function destroyScroller(ps_object){
    ps_object.destroy();
  }

  // Initialize scroller
  function initScroller(domObject) {
    if( typeof domObject[0] !== 'undefined' ) {
      return new PerfectScrollbar(domObject[0], {
        wheelPropagation: false
      });
    }
  }

  //Core private functions
  function leftSidebarInit(){

    var firstAnchor = $(".sidebar-elements > li > a", leftSidebar);
    var anchor = $(".sidebar-elements li a", leftSidebar);
    var lsc = $(".left-sidebar-scroll", leftSidebar);
    var lsToggle = $(".left-sidebar-toggle", leftSidebar);
    var openLeftSidebarOnClick = config.openLeftSidebarOnClick ? true : false;

    // Collapsible sidebar toggle functionality
    function toggleSideBar(){
      var button = $(".be-toggle-left-sidebar");

      button.on("click", function(){
        if( wrapper.hasClass( config.collapsibleSidebarCollapsedClass ) ) {
          wrapper.removeClass( config.collapsibleSidebarCollapsedClass );
          $("li.open", leftSidebar).removeClass("open");
          $("li.active", leftSidebar).parents(".parent").addClass("active open");
          leftSidebar.trigger( "shown.left-sidebar.collapse" );
          if (typeof ps_be_scroller_left_sidebar === "undefined" || !ps_be_scroller_left_sidebar || !ps_be_scroller_left_sidebar.nodeName) {
            ps_be_scroller_left_sidebar = initScroller($(".be-scroller", leftSidebar));
          }
          destroyScroller(ps_be_scroller_left_sidebar);
          // Destroy Perfect Scrollbar collapsed instance
          if (typeof ps_sub_menu_scroller !== "undefined") {
            destroyScroller(ps_sub_menu_scroller);
          }
        } else {
          wrapper.addClass( config.collapsibleSidebarCollapsedClass );
          $("li.active", leftSidebar).parents(".parent").removeClass("open");
          $("li.open", leftSidebar).removeClass("open");
          leftSidebar.trigger( "hidden.left-sidebar.collapse" );
        }
      });
    }

    // Tooltip sidebar funcionality
    function tooltipSidebar(){
      var menu = $(".sidebar-elements > li > a", leftSidebar);

      for(var i = 0; i <= menu.length; i++ ){
        var _self = menu[i];
        var title = $("> span", _self).text();

        $(_self).attr({
          'data-toggle': 'tooltip',
          'data-placement': 'right',
          'title': title
        });

        $(_self).tooltip({
          trigger: 'manual'
        });
      }

      menu.on('mouseenter', function(){
        if(!$.isSm() && wrapper.hasClass(config.collapsibleSidebarCollapsedClass)){
          $(this).tooltip('show');
        }
      });

      menu.on('mouseleave', function(){
        $(this).tooltip('hide');
      });
    }

    // Collapsed sidebar submenu title
    function syncSubMenu( item ){
      var elements;

      if( typeof item !== "undefined" ) {
        elements = item;
      } else {
        elements = $(".sidebar-elements > li", leftSidebar);
      }

      $.each( elements, function(){
        var title = $(this).find("> a span").html();
        var ul = $(this).find("> ul");
        var subEls = $("> li", ul);
        title = $('<li class="title">' + title + '</li>');
        var subContainer = $('<li class="nav-items"><div class="be-scroller"><div class="content"><ul></ul></div></div></li>');

        if( !ul.find("> li.title").length ){
          ul.prepend( title );
          subEls.appendTo( subContainer.find(".content ul") );
          subContainer.appendTo( ul );
        }
      });
    }

    // Return boolean whether the sidebar is collapsed or not
    function isCollapsed(){
      return wrapper.hasClass( config.collapsibleSidebarCollapsedClass );
    }

    // Return true if the collapsible left sidebar is enabled
    function isCollapsible(){
      return wrapper.hasClass( config.collapsibleSidebarClass );
    }

    // Close submenu function
    function closeSubMenu( subMenu, event ){
      var target = $( event.currentTarget );
      var li = $( subMenu ).parent();
      var openChildren = $( 'li.open', li );

      var clickOutside = !target.closest( leftSidebar ).length;
      var slideSpeed = config.leftSidebarSlideSpeed;
      var isFirstLevel = target.parents().eq( 1 ).hasClass( 'sidebar-elements' );

      // If left sidebar is collapsed, is not small device
      // and the trigger element is first level
      // or click outside the left sidebar
      if ( !$.isSm() && isCollapsed() && ( isFirstLevel || clickOutside ) ){

        li.removeClass( 'open' );
        subMenu.removeClass( 'visible' );
        openChildren.removeClass( 'open' ).removeAttr( 'style' );

      } else { // If not execute classic slide interaction

        subMenu.slideUp({ duration: slideSpeed, complete: function(){
          li.removeClass( 'open' );
          $( this ).removeAttr( 'style' );

          // Close opened child submenus
          openChildren.removeClass( 'open' ).removeAttr( 'style' );
          if( wrapper.hasClass("be-fixed-sidebar") && !$.isSm() ){
            updateScroller(ps_left_sidebar);
          }
        }});

      }
    }

    // Open submenu function
    function openSubMenu( anchor, event ){
      var _el = $( anchor );
      var li = $( _el ).parent();
      var subMenu = $( _el ).next();

      var slideSpeed = config.leftSidebarSlideSpeed;
      var isFirstLevel = $( event.currentTarget ).parents().eq( 1 ).hasClass( 'sidebar-elements' );

      // Get the open sub menus
      var openSubMenus = li.siblings( '.open' );

      // If there are open sub menus close them
      if( openSubMenus ) {
        closeSubMenu( $( '> ul', openSubMenus ), event );
      }

      // If left sidebar is collapsed, is not small device
      // and the trigger element is first level
      if ( !$.isSm() && isCollapsed() && isFirstLevel ){
        li.addClass( 'open' );
        subMenu.addClass( 'visible' );

        //Renew Perfect Scroller instance
        if (typeof ps_sub_menu_scroller !== "undefined") {
          destroyScroller(ps_sub_menu_scroller);
        }
        if (typeof ps_sub_menu_scroller === "undefined" || !ps_sub_menu_scroller || !ps_sub_menu_scroller.nodeName) {
          ps_sub_menu_scroller = initScroller(li.find('.be-scroller'));
        }

        $(window).resize(function () {
          waitForFinalEvent(function(){
            if( !$.isXs() ){
              if (typeof ps_sub_menu_scroller !== "undefined") {
                updateScroller(ps_sub_menu_scroller);
              }
            }
          }, 500, "am_check_phone_classes");
        });

      } else { // If not execute classic slide interaction

        subMenu.slideDown({ duration: slideSpeed, complete: function(){
          li.addClass( 'open' );
          $( this ).removeAttr( 'style' );
          if( wrapper.hasClass("be-fixed-sidebar") && !$.isSm() ){
            updateScroller(ps_left_sidebar);
          }
        }});

      }
    }

    // Execute if collapsible sidebar is enabled
    if ( isCollapsible() ){
      /*Create sub menu elements*/
        syncSubMenu();
        toggleSideBar();
        tooltipSidebar();

      if( !openLeftSidebarOnClick ){

        /*Open sub-menu on hover*/
        firstAnchor.on('mouseover',function( event ){
          if( isCollapsed() ) {
            openSubMenu( this, event );
          }
        });

        /*Open sub-menu on click (fix for touch devices)*/
        firstAnchor.on('touchstart',function( event ){
          var anchor = $( this );
          var li = anchor.parent();
          var subMenu = anchor.next();

          if( isCollapsed() && !$.isSm() ) {

            if( li.hasClass('open') ) {
              closeSubMenu( subMenu, event );
            } else {
              openSubMenu( this, event );
            }

            if( $( this ).next().is( 'ul' ) ) {
              event.preventDefault();
            }
          }
        });

        /*Sub-menu delay on mouse leave*/
        firstAnchor.on('mouseleave',function( event ){
          var _self = $( this );
          var _li = _self.parent();
          var subMenu = _li.find( '> ul' );

          if( !$.isSm() && isCollapsed() ){

            //If mouse is over sub menu attach an additional mouseleave event to submenu
            if ( subMenu.length > 0 ){

              setTimeout(function(){
                if( subMenu.is( ':hover' ) ){

                  subMenu.on( 'mouseleave', function(){
                    setTimeout(function(){
                      if( !_self.is( ':hover' ) ){
                        closeSubMenu( subMenu, event );
                        subMenu.off( 'mouseleave' );
                      }
                    }, 300);
                  });

                }else{
                  closeSubMenu( subMenu, event );
                }
              }, 300);

            }else{
              _li.removeClass( 'open' );
            }
          }
        });
      }

      /*Close sidebar on click outside*/
      $( document ).on("mousedown touchstart",function( event ){
        if ( !$( event.target ).closest( leftSidebar ).length && !$.isSm() ) {
          closeSubMenu( $( "ul.visible", leftSidebar), event );
        }
      });
    }

    /*Open sub-menu functionality*/
      anchor.on("click",function( event ){
        var $el = $(this), $open;
        var $li = $el.parent();
        var $subMenu = $el.next();
        var isFirstLevel = $el.parents().eq(1).hasClass('sidebar-elements');

        // Get the open menus
        $open = $li.siblings(".open");

        if( $li.hasClass('open') ){
          closeSubMenu( $subMenu, event );
        }else{
          openSubMenu( this, event );
        }

        //If current element has children stop link action
        if( $el.next().is('ul') ){
          event.preventDefault();
        }
      });

    /*Calculate sidebar tree active & open classes*/
    if ( wrapper.hasClass( config.collapsibleSidebarCollapsedClass ) ){
      $("li.active", leftSidebar).parents(".parent").addClass("active");
    } else {
      $("li.active", leftSidebar).parents(".parent").addClass("active open");
    }

    /* Add classes if top menu is present */
    if( topNavbar.find('.container-fluid > .navbar-collapse').length && leftSidebar.length ) {
      wrapper.addClass( config.offCanvasLeftSidebarClass ).addClass( config.offCanvasLeftSidebarMobileClass );
      wrapper.addClass( config.topHeaderMenuClass );
      toggleLeftSidebarButton = $('<a class="nav-link be-toggle-left-sidebar" href="#"><span class="icon mdi mdi-menu"></span></a>');
      $('.be-navbar-header', topNavbar).prepend(toggleLeftSidebarButton);
    }

    /*Scrollbar plugin init when left sidebar is fixed*/
      if( wrapper.hasClass("be-fixed-sidebar") ){
        if( !$.isSm() || wrapper.hasClass( config.offCanvasLeftSidebarClass ) ) {
          if (typeof ps_left_sidebar === "undefined" || !ps_left_sidebar || !ps_left_sidebar.nodeName) {
            ps_left_sidebar = initScroller(lsc);
          }
        }

        /*Update scrollbar height on window resize*/
        $(window).resize(function () {
          waitForFinalEvent(function(){
            if( $.isSm() && !wrapper.hasClass( config.offCanvasLeftSidebarClass ) ) {
              destroyScroller(ps_left_sidebar);
            } else {
              if( lsc.hasClass('ps') ) {
                updateScroller(ps_left_sidebar);
              } else {
                if (typeof ps_left_sidebar === "undefined" || !ps_left_sidebar || !ps_left_sidebar.nodeName) {
                  ps_left_sidebar = initScroller(lsc);
                }
              }
            }
          }, 500, "be_update_scroller");
        });
      }

    /*Toggle sidebar on small devices*/
      lsToggle.on('click',function( e ){
        var spacer = $(this).next('.left-sidebar-spacer'), toggleBtn = $(this);

        toggleBtn.toggleClass('open');
        spacer.slideToggle(config.leftSidebarToggleSpeed, function(){
          $(this).removeAttr('style').toggleClass('open');
        });

        e.preventDefault();
      });

    /*Off canvas menu*/
      function leftSidebarOffCanvas() {

        /*Open Sidebar with toggle button*/
        toggleLeftSidebarButton.on("click", function(e){
          if ( openSidebar && body.hasClass(config.openLeftSidebarClass) ) {
            body.removeClass(config.openLeftSidebarClass);
            sidebarDelay();
          } else {
            body.addClass( config.openLeftSidebarClass + " " + config.transitionClass);
            openSidebar = true;
          }
          e.preventDefault();
        });

        /*Close sidebar on click outside*/
        $( document ).on("mousedown touchstart",function( e ){
          if ( !$( e.target ).closest( leftSidebar ).length && !$( e.target ).closest( toggleLeftSidebarButton ).length && body.hasClass( config.openLeftSidebarClass ) ) {
            body.removeClass( config.openLeftSidebarClass );
            sidebarDelay();
          }
        });
      }

      // Left sidebar off-canvas
      if ( wrapper.hasClass(config.offCanvasLeftSidebarClass) ) {
        leftSidebarOffCanvas();
      }
  }

  function rightSidebarInit(){

    var rsScrollbar = $(".be-scroller", rightSidebar);
    var rsChatScrollbar = $(".be-scroller-chat", rightSidebar);
    var rsTodoScrollbar = $(".be-scroller-todo", rightSidebar);
    var rsSettingsScrollbar = $(".be-scroller-settings", rightSidebar);

    function oSidebar(){
      body.addClass( config.openRightSidebarClass  + " " + config.transitionClass );
      openSidebar = true;
    }

    function cSidebar(){
      body.removeClass( config.openRightSidebarClass ).addClass( config.transitionClass );
      sidebarDelay();
    }

    if( rightSidebar.length > 0 ){
      /*Open-Sidebar when click on topbar button*/
      $('.be-toggle-right-sidebar').on("click", function(e){
        if( openSidebar && body.hasClass( config.openRightSidebarClass ) ){
          cSidebar();
        }else if( !openSidebar ){
          oSidebar();
        }

        e.preventDefault();
      });

      /*Close sidebar on click outside*/
      $( document ).on("mousedown touchstart",function( e ){
        if ( !$( e.target ).closest( rightSidebar ).length && body.hasClass( config.openRightSidebarClass ) && (config.closeRsOnClickOutside || $.isSm()) ) {
          cSidebar();
        }
      });
    }

    if ((typeof ps_chat_scroll === "undefined" || !ps_chat_scroll || !ps_chat_scroll.nodeName) && rsChatScrollbar.length) {
      ps_chat_scroll = initScroller(rsChatScrollbar);
    }

    if ((typeof ps_todo_scroll === "undefined" || !ps_todo_scroll || !ps_todo_scroll.nodeName) && rsTodoScrollbar.length) {
      ps_todo_scroll = initScroller(rsTodoScrollbar);
    }

    if ((typeof ps_settings_scroll === "undefined" || !ps_settings_scroll || !ps_settings_scroll.nodeName) && rsSettingsScrollbar.length) {
      ps_settings_scroll = initScroller(rsSettingsScrollbar);
    }

    /*Update scrollbar height on window resize*/
    $(window).resize(function () {
      waitForFinalEvent(function(){
        updateScroller(ps_chat_scroll);
        updateScroller(ps_todo_scroll);
        updateScroller(ps_settings_scroll);
      }, 500, "be_rs_update_scroller");
    });

    /*Update scrollbar when click on a tab*/
    $('a[data-toggle="tab"]', rightSidebar).on('shown.bs.tab', function (e) {
      updateScroller(ps_chat_scroll);
      updateScroller(ps_todo_scroll);
      updateScroller(ps_settings_scroll);
    });
  }

  function sidebarDelay(){
    openSidebar = true;
    setTimeout(function(){
      openSidebar = false;
    }, config.openSidebarDelay );
  }

  function sidebarSwipe(){
    /*Open sidedar on swipe*/
    wrapper.swipe( {
      allowPageScroll: "vertical",
      preventDefaultEvents: false,
      fallbackToMouseEvents: false,
      swipeLeft: function(e){
        if( !openSidebar && rightSidebar.length > 0 ){
          body.addClass( config.openRightSidebarClass + " " + config.transitionClass );
          openSidebar = true;
        }
      },
      threshold: config.swipeTreshold
    });
  }

  function chatWidget(){
    var chat = $(".be-right-sidebar .tab-chat");
    var contactsEl = $(".chat-contacts", chat);
    var conversationEl = $(".chat-window", chat);
    var messagesContainer = $(".chat-messages", conversationEl);
    var messagesList = $(".content ul", messagesContainer);
    var messagesScroll = $(".be-scroller-messages", messagesContainer);
    var chatInputContainer = $(".chat-input", conversationEl);
    var chatInput = $("input", chatInputContainer);
    var chatInputSendButton = $(".send-msg", chatInputContainer);

    function openChatWindow(){
      if( !chat.hasClass("chat-opened") ){
        chat.addClass("chat-opened");
        if (typeof ps_messages_scroll === "undefined" || !ps_messages_scroll || !ps_messages_scroll.nodeName) {
          ps_messages_scroll = initScroller(messagesScroll);
        }
      }
    }

    function closeChatWindow(){
      if( chat.hasClass("chat-opened") ){
        chat.removeClass("chat-opened");
      }
    }

    /*Open Conversation Window when click on chat user*/
    $(".user a", contactsEl).on('click',function( e ){
      openChatWindow();
      e.preventDefault();
    });

    /*Close chat conv window*/
    $(".title .return", conversationEl).on('click',function( e ){
      closeChatWindow();
      scrollerInit();
    });

    /*Send message*/
    function sendMsg(msg, self){
      var $message = $('<li class="' + ((self)?'self':'friend') + '"></li>');

      if( msg != '' ){
        $('<div class="msg">' + msg + '</div>').appendTo($message);
        $message.appendTo(messagesList);

        messagesScroll.stop().animate({
          'scrollTop': messagesScroll.prop("scrollHeight")
        }, 900, 'swing');

        updateScroller(ps_messages_scroll);
      }
    }

    /*Send msg when click on 'send' button or press 'Enter'*/
      chatInput.keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        var msg = $(this).val();

        if(keycode == '13'){
          sendMsg(msg, true);
          $(this).val("");
        }
        event.stopPropagation();
      });

      chatInputSendButton.on('click',function(){
        var msg = chatInput.val();
        sendMsg(msg, true);
        chatInput.val("");
      });
  }

  function scrollerInit(){
    if (typeof ps_be_scroller_notifications === "undefined" || !ps_be_scroller_notifications || !ps_be_scroller_notifications.nodeName) {
      ps_be_scroller_notifications = initScroller(notificationsDiv);
    }
  }

  function scrollTopButton(){
    var offset = 220;
    var duration = 500;
    var button = $('<div class="be-scroll-top"></div>');
    button.appendTo("body");

    $(window).on('scroll',function() {
      if ( $(this).scrollTop() > offset ) {
        button.fadeIn(duration);
      } else {
        button.fadeOut(duration);
      }
    });

    button.on('touchstart mouseup',function( e ) {
      $( 'html, body' ).animate({ scrollTop: 0 }, duration);
      e.preventDefault();
    });
  }

  //Add and remove active class on left sidebar
  function activeItemLeftSidebar(menu_item){
    var firstAnchor = menu_item;
    var li = $(firstAnchor).parent();
    var menu = $(li).parents('li');

    if( !li.hasClass('active') ){
      $('li.active', leftSidebar).removeClass('active');
      $(menu).addClass('active');
      $(li).addClass('active');
    }
  }

  function initAsidePS() {
    var pas = asideDiv;
    ps_aside_scroll = initScroller(asideDiv);
    $(window).resize(function () {
      if( $.isSm() && !wrapper.hasClass( config.offCanvasLeftSidebarClass ) ) {
        destroyScroller(ps_aside_scroll);
      } else {
        if( pas.hasClass('ps') ) {
          updateScroller(ps_aside_scroll);
        } else {
          if (typeof ps_aside_scroll === "undefined" || !ps_aside_scroll || !ps_aside_scroll.nodeName) {
            ps_aside_scroll = initScroller(asideDiv);
          }
        }
      }
    });
  }

  //Wait for final event on window resize
  var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
      if (!uniqueId) {
        uniqueId = "x1x2x3x4";
      }
      if (timers[uniqueId]) {
        clearTimeout (timers[uniqueId]);
      }
      timers[uniqueId] = setTimeout(callback, ms);
    };
  })();

  return {
    //General data
    conf: config,
    color: colors,
    scroller: scrollers,

    //Init function
    init: function (options) {

      //Get the main elements when document is ready
        body = $("body");
        wrapper = $(".be-wrapper");
        topNavbar = $('.be-top-header', wrapper);
        leftSidebar = $(".be-left-sidebar", wrapper);
        rightSidebar = $(".be-right-sidebar", wrapper);
        asideDiv = $(".be-scroller-aside", wrapper);
        toggleLeftSidebarButton = $('.be-toggle-left-sidebar', topNavbar);
        notificationsDiv = $(".be-scroller-notifications", topNavbar);
        openSidebar = false;

      //Extends basic config with options
        $.extend( config, options );

      /*FastClick on mobile*/
        FastClick.attach(document.body);

      /*Left Sidebar*/
        if ( config.enableLeftSidebar ){
          leftSidebarInit();
        } else {
          wrapper.addClass(config.disabledLeftSidebarClass);
        }

      /*Right Sidebar*/
        if ( rightSidebar.length ){
          rightSidebarInit();
          chatWidget();
        }

      /*Sidebars Swipe*/
        if( config.enableSwipe ){
          sidebarSwipe();
        }

      /*Scroll Top button*/
        if( config.scrollTop ){
          scrollTopButton();
        }

      /*Page Aside*/
        if( asideDiv.length ){
          initAsidePS();
        }

      /*Scroller plugin init*/
        if ( notificationsDiv.length ){
          scrollerInit();
        }

      /*Get colors*/
        colors.primary = getColor('clr-primary');
        colors.success = getColor('clr-success');
        colors.warning = getColor('clr-warning');
        colors.danger  = getColor('clr-danger');
        colors.grey    = getColor('clr-grey');

      /*Get scrollers*/
        scrollers.be_scroller_notifications = ps_be_scroller_notifications;
        scrollers.left_sidebar_scroll = ps_left_sidebar;
        scrollers.be_left_sidebar_scroll = ps_be_scroller_left_sidebar;
        scrollers.sub_menu_scroll = ps_sub_menu_scroller;
        scrollers.chat_scroll = ps_chat_scroll;
        scrollers.todo_scroll = ps_todo_scroll;
        scrollers.settings_scroll = ps_settings_scroll;
        scrollers.messages_scroll = ps_messages_scroll;
        scrollers.aside_scroll = ps_aside_scroll;
        scrollers.updateScroller = updateScroller;
        scrollers.destroyScroller = destroyScroller;
        scrollers.initScroller = initScroller;

      /*Bind plugins on hidden elements*/
      /*Dropdown shown event*/
      $('.be-icons-nav .dropdown').on('shown.bs.dropdown', function () {
        updateScroller(ps_be_scroller_notifications);
      });

      /*Tooltips*/
        $('[data-toggle="tooltip"]').tooltip();

      /*Popover*/
        $('[data-toggle="popover"]').popover();

      /*Bootstrap modal scroll top fix*/
        $('.modal').on('show.bs.modal', function(){
          $("html").addClass('be-modal-open');
        });

        $('.modal').on('hidden.bs.modal', function(){
          $("html").removeClass('be-modal-open');
        });

  		/*Fixes the Sweetalert gap in the top header on boxed layout*/
    		if (typeof Swal == 'function' && wrapper.hasClass('be-boxed-layout')) {
          var observer = new MutationObserver(function(mutationsList, observer) {
            mutationsList.forEach(function(mutation){
              if (mutation.type == 'attributes' && mutation.attributeName == 'style') {
                if(document.body.className.indexOf('swal2-shown') > 0){
                  topNavbar.css({marginLeft: 'calc(-' + document.body.style.paddingRight + ' / 2)'});
                } else {
                  topNavbar.css({marginLeft: '0'});
                }
              }
            });
          });

          observer.observe(document.body, { attributes: true });
    		}
    },

    //Methods
    activeItemLeftSidebar: activeItemLeftSidebar
  };

})();

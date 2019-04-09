(function($) {
    $.widget("app.tabs", $.ui.tabs, {  

        options: {
            scrollable: true, // Tab is scrollable
            mousewheel: false, // Active mouswheel event (needs jquery.mousewheel)
            closable: false, // Tabs are closable ?
            nonClosableIndexes: [], // Be carefull with shift when previous tab deleted 
            duration : 200, // Animation duration
            easing: 'swing', // Animation easing
            selectOnScroll: false, // Select the tab when scroll
            scrollDistance: 300, // Distance during scroll (in pixel)
            arrowsPosition: 'wrap' // Can be 'wrap', 'left' and 'right'
        },

        /**
         * Create
         */
        _create: function() {
            if (this._super) {
                this._super();
            } else {
                $.ui.tabs.prototype._create.apply( this, arguments );
            }
            // Compatibility
            this.activeTabClass = (this.version > '1.9.0') ? '.ui-tabs-active' : '.ui-tabs-selected';
            this.tablistName = this.tablist ? 'tablist' : 'list';
            this.tabsName = this.tabs ? 'tabs' : 'lis';
        },

        /**
         * Process after setOptions
         * @param options the options
         */
        _setOptions: function(options) {
            if (this._super) {
                this._super(options);
            } else {
                $.ui.tabs.prototype._setOptions.apply( this, arguments );
            }

            // Only way I found to process with the user specified options
            // Because always default options in _create (bug ?)
            // Is there another way ? :(
            this._process();
        },

        /**
         * Refresh
         * Called only by $ UI 1.9.0 and later
         */
        _refresh: function () {
            this._super();
            this._process();
        },	

        /**
         * Tabify
         * Called only by $ UI before 1.9.0
         */
        _tabify: function (init) {
            $.ui.tabs.prototype._tabify.apply( this, arguments );
            this._process();
        },

        /**
         * Process
         */
        _process: function() {
            // Closify
            this[(this.options.closable ? '_closify' : '_unclosify')]();

            // Scrollify
            this[(this.options.scrollable ? '_scrollify' : '_unscrollify')]();

            // Wheelify
            this[(this.options.mousewheel ? '_wheelify' : '_unwheelify')]();
        },

        /**
         * Wheelify
         */
        _wheelify: function() {
            // Clean
            this.tablist.unbind('mousewheel.tabs');
            this.tablist.bind('mousewheel.tabs', $.proxy(this._onMousewheeled, this));
            this.wheelified = true;
        }, 

        /**
         * Scrollify
         */
        _scrollify: function() {
            this._unscrollify();

            this.tablist.data('padding-left', this.tablist.css('padding-left'));
            // Change list width
            this.tablist.css({
                'width'       : '10000px',
                'padding-left': '0'
            });

            // Define position in function of options
            var containerMarginLeft = 0,
                containerMarginRight = 0,
                previousArrowPosition = {},
                nextArrowPosition = {};

            switch(this.options.arrowsPosition) {

                case 'wrap':
                default:
                    containerMarginLeft = 24;
                    containerMarginRight = 24;
                    previousArrowPosition = {'left': 3};
                    nextArrowPosition = {'left': 24};
                    previousArrowPosition = {'left': 3};
                    nextArrowPosition = {'right': 3};
                    break;
                case 'left':
                    containerMarginLeft = 48;
                    containerMarginRight = 0;
                    previousArrowPosition = {'left': 3};
                    nextArrowPosition = {'left': 24};
                    break;
                case 'right':
                    containerMarginLeft = 0;
                    containerMarginRight = 48;
                    previousArrowPosition = {'right': 24};
                    nextArrowPosition = {'right': 3};
                    break;
            }

            // Add container in order to control width
            this.tablist.wrap($('<div/>')
                    .addClass(this.widgetBaseClass + '-container')
                    .css({
                        'margin-left' : containerMarginLeft + 'px',
                        'margin-right': containerMarginRight + 'px',
                        'overflow'    : 'hidden',
                        'position'    : 'relative'
                    }));
            // Wrap doesn't allow to acceed to DOM element
            this.container = $('.' + this.widgetBaseClass + '-container', this.element);

            // Add arrows
            var arrowsTopMargin = (parseInt(parseInt(this.tablist.innerHeight()/2)-8)),
                arrowsCommonCss = {
                    'cursor'  :'pointer',
                    'z-index' :1000,
                    'position':'absolute',
                    'height'  :this.tablist.outerHeight()-($.browser.safari ? 2 : 1)
                };

            this.nav = 	$('<div/>')
                            .disableSelection()
                            .addClass(this.widgetBaseClass + '-navigation')
                            .css({
                                'position':'relative',
                                'z-index' :3000,
                                'display' :'none'
                                })
                            .append(
                                $('<span/>')
                                    .disableSelection()
                                    .attr('title', Global.tr ('tabs_previous'))
                                    .css(arrowsCommonCss)
                                    .addClass('ui-state-active ui-corner-tl ui-corner-bl')
                                    .addClass(this.widgetBaseClass + '-prev')
                                    .css(previousArrowPosition)
                                    .append($('<span/>')
                                            .disableSelection()
                                            .addClass('ui-icon ui-icon-carat-1-w')
                                            .html(Global.tr ('tabs_previous'))
                                            .css('margin-top',arrowsTopMargin))
                                    .click($.proxy(this._previousButtonClicked, this)),
                                $('<span/>')
                                    .disableSelection()
                                    .attr('title', Global.tr ('tabs_next'))
                                    .css(arrowsCommonCss)
                                    .addClass('ui-state-active ui-corner-tr ui-corner-br')
                                    .addClass(this.widgetBaseClass + '-next')
                                    .css(nextArrowPosition)
                                    .append($('<span/>')
                                            .addClass('ui-icon ui-icon-carat-1-e')
                                            .html(Global.tr ('tabs_next'))
                                            .css('margin-top',arrowsTopMargin))
                                    .click($.proxy(this._nextButtonClicked, this))

                            );
            this.element.prepend(this.nav);

            this._enableNavigationButtons();

            // Scroll when tab selected
            this.element.bind( this.widgetEventPrefix + "select.tabs", $.proxy(function(event, ui){
                this._scrollToTab(this.tabs.eq(ui.index));
            }, this));

            // Refresh navigation buttons when tab added
            this.element.bind( this.widgetEventPrefix + "add.tabs", $.proxy(function(){
                this._enableNavigationButtons();
            }, this));

            // Refresh navigation buttons when window resized 
            // (with timeout to be sure that resize is finished)
            $(window).bind('resize.tabs', $.proxy(function() {
                setTimeout($.proxy(function(){
                    this._enableNavigationButtons();}
                , this), this.options.duration);
            }, this));
            this.scrollified = true;
        },
        /**
         * Update navigation buttons state
         */
        _updateNavigationButtons: function(){
            setTimeout($.proxy(function(){
                var isLast = false,
                    isFirst = false;
                var $ntNav = this.nav.find('.' + this.widgetBaseClass + '-next');
                var $pvNav = this.nav.find('.' + this.widgetBaseClass + '-prev');

                if (this.options.selectOnScroll) {
                    //Check if last or first tab is selected than disable the navigation arrows
                    isLast = this.tablist.find('li' + this.activeTabClass).is(':last-child');
                    isFirst = this.tablist.find('li' + this.activeTabClass).is(':first-child');
                } else {
                    // Check if first or last tabs are viewable
                    var marginLeft = parseFloat(this.tablist.css('margin-left'));
                    isFirst = (marginLeft == 0);

                    var containerWidth = this.container.width();
                    var lastTab = this.tabs.last();
                    var lastTabRigthLimit = lastTab.position().left + lastTab.width();
                    isLast = (lastTabRigthLimit < containerWidth);
                }

                if(isLast)
                {
                    $pvNav.removeClass('ui-state-disabled');
                    $ntNav.addClass('ui-state-disabled');
                }
                else if(isFirst)
                {
                    $ntNav.removeClass('ui-state-disabled');
                    $pvNav.addClass('ui-state-disabled');
                }
                else
                {
                    $ntNav.removeClass('ui-state-disabled');
                    $pvNav.removeClass('ui-state-disabled');
                }
            }, this), this.options.duration);
        },

        /**
         * Check if navigation arrows are needed
         */
        _enableNavigationButtons: function() {
            if(this._needScroll())
            {
                this.nav.show();
            }
            // Else hide buttons and be sure that all tabs are shown
            // And deactive wheel
            else
            {
                this.nav.hide();
                this._showAllTabs();
            }
            this._updateNavigationButtons();
        },

        /**
         * Show all tabs when there is no navigation buttons
         */
        _showAllTabs: function() {
            // Check that first tab is shown
            this._scrollToTab(this.tablist.find('li:first'));

            // Check that last tab is shown
            this._scrollToTab(this.tablist.find('li:last'));
        },

        /**
         * Add close buttons
         */
        _closify: function() {
            this._unclosify();

            this.tabs.each($.proxy(function(index, li){
                // Check if index in non closable indexes list
                if ($.inArray(index, this.options.nonClosableIndexes) !== -1) {
                    // If existing close button, remove it
                    $('.ui-tabs-close', li).remove();
                    return;
                }

                // Check if tab already has close button
                if($(li).find('.ui-tabs-close').length > 0) {
                    return;
                }

                var closeTopMargin = parseInt(parseInt(this.tabs.find(':first').innerHeight()/2,10)-12);
                $(li).disableSelection().append(
                    $('<span>')
                        .css({
                            'float' :'left',
                            'cursor':'pointer',
                            'margin': '6px 4px 0 0px'
                        })
                        .addClass("ui-tabs-close ui-icon ui-icon-circle-close")
                        .attr('title', 'Fermer')
                        .click($.proxy(this._closeButtonClicked, this))
                );
            }, this));
            this.closified = true;
        },

        /**
         * When close boutton is clicked
         * @param event the event
         */
        _closeButtonClicked: function(event) {
            var li = $(event.currentTarget).parent();
            var index = this.tabs.index(li);
            this.remove(index);

            this._trigger( "close", null, {index: index, li: li} );

            // Update navigation buttons
            this._enableNavigationButtons();
            return false;
        },

        /**
         * When previous boutton is clicked
         * @param event the event
         */
        _previousButtonClicked: function (event) {
            if ($(event.currentTarget).hasClass('ui-state-disabled')) {
                return;
            }
            this._goToPreviousTab();
        },

        /**
         * When next boutton is clicked
         * @param event the event
         */
        _nextButtonClicked: function (event) {
            if ($(event.currentTarget).hasClass('ui-state-disabled')) {
                return;
            }
            this._goToNextTab();
        },

        /**
         * Returns the previous tab index 
         * @returns the previous tab index
         */
        _getPreviousTabIndex: function() {
            var index = this.tablist.find('li' + this.activeTabClass).prevAll().length - 1;
            if (index < 0) {
                index = 0;
            }
            return index;

        },

        /**
         * Returns the next tab index 
         * @returns the next tab index
         */
        _getNextTabIndex: function() {
            var index = this.tablist.find('li' + this.activeTabClass).prevAll().length + 1;
            if (index >= this.tabs.length) {
                index = this.tabs.length - 1;
            }
            return index;
        },

        /**
         * Go to the previous tab
         * Scroll to the right or select it
         */
        _goToPreviousTab: function() {
            var index = this._getPreviousTabIndex();
            this[(this.options.selectOnScroll ? '_selectTab' : '_scrollTo')]((this.options.selectOnScroll ? index : false));
        },

        /**
         * Go to the next tab.
         * Scroll to the left or select it
         */
        _goToNextTab: function() {
            var index = this._getNextTabIndex();
            this[(this.options.selectOnScroll ? '_selectTab' : '_scrollTo')]((this.options.selectOnScroll ? index : true));
        },

        /**
         * Select a tab. Trigger the click event on its anchor
         * @param index the index to select
         */
        _selectTab: function(index) {
            this.tabs.eq(index).find('a').trigger('click');
        },

        /**
         * Scroll to a direction
         * @param goToLeft true if we scroll to the left, else to the right
         */
        _scrollTo: function(goToLeft) {
            // Wait the previous animation to be finished
            if (! this.animated) {
                this.animated = true;
                var marginLeft = parseFloat(this.tablist.css('margin-left'));

                // Scroll to left
                if (goToLeft) {
                    delta = marginLeft - this.options.scrollDistance;

                    // Constraints
                    // Checks last tab position
                    var containerWidth = this.container.width();
                    var lastTab = this.tabs.last();
                    var lastTabFuturRigthLimit = lastTab.position().left + lastTab.width() - this.options.scrollDistance;
                    if (lastTabFuturRigthLimit < containerWidth) {
                        delta += containerWidth - lastTabFuturRigthLimit - 6;
                    }

                // Scroll to right
                } else {
                    delta = marginLeft + this.options.scrollDistance;
                    // Constraints
                    if (delta > 0) {
                        delta = 0;
                    }
                }
                this.tablist.animate({'margin-left': delta}, 
                                  this.options.duration, 
                                  this.options.easing, 
                                  $.proxy(function() {this.animated = false;}, this));
                this._updateNavigationButtons();
            }
        },

        /**
         * Scroll to a tab. If index not provided, go to selected tab
         * @param tabToScrollTo the tab to scroll to
         */
        _scrollToTab: function(tabToScrollTo) {
            // If tabToScrollTo not provided go to selected tab
            tabToScrollTo = (typeof tabToScrollTo !== 'undefined') ? tabToScrollTo : this.tabs.find(this.activeTabClass);

            var containerWidth = this.container.width();
            var tabLeftLimit =  tabToScrollTo.position() ? tabToScrollTo.position().left : 0;
            var tabRigthLimit = tabLeftLimit + tabToScrollTo.width();
            var marginLeft = parseFloat(this.tablist.css('margin-left'));

            // If offset on right
            if (tabRigthLimit > containerWidth) {
                var delta = -(tabRigthLimit - containerWidth) + marginLeft - 6;
                this.tablist.animate({'margin-left': delta}, this.options.duration, this.options.easing);
            }
            // If offset on left
            if (tabLeftLimit < 0) {
                var deltaOffset = -tabLeftLimit + marginLeft;
                this.tablist.animate ({'margin-left': deltaOffset}, this.options.duration, this.options.easing);
            }

            this._updateNavigationButtons();
        },

        /**
         * On mouse wheel
         * @param event the event
         * @param delta the wheel delta
         */
        _onMousewheeled: function(event, delta) {

            if (this._needScroll()) {
                this[((delta > 0) ? '_goToPreviousTab' : '_goToNextTab')](); 
            }
            return false;
        },
        /**
         * Return if we need to scroll
         * @returns {Boolean}
         */
        _needScroll: function() {
            // Compute all li tabs width
            var lisWidth = 0;
            this.tabs.each(function() {
                lisWidth += $(this).outerWidth();
            });

            var delta = 60;

            // If wider than we need scroll
            return (lisWidth > this.container.width() - delta);
        },

        /**
         * Clean up when destroy
         */
        _destroy: function() {

            this._unclosify();
            this._unscrollify();
            this._unwheelify();

            // Super
            if (this._super) {
                this._super();
            }
        },

        /**
         * Clean up scrollify
         */
        _unscrollify: function() {

            if (this.scrollified) {
                this.tablist.css({
                    'width': 'auto',
                    'padding-left': this.tablist.data('padding-left')
                });

                if (this.nav) {
                    this.nav.remove();
                }

                this.tablist.unwrap();

                this.element.unbind('.tabs');
                $(window).unbind('resize.tabs');
                this.scrollified = false;
            }
        },

        /**
         * Clean up wheelify
         */
        _unwheelify: function() {
            if (this.wheelified) {
                this.tablist.unbind('mousewheel.tabs');
                this.wheelified = false;
            }
        },

        /**
         * Clean up closify
         */
        _unclosify: function() {
            if (this.closified) {
                $('.ui-tabs-close', this.element).remove();
                this.closified = false;
            }
        }
    }); 

    // Fix $ UI before 1.9.0
    // Call _destroy on destroy
    if ($.ui.version < '1.9.0') {
        $.widget("app.tabs", $.app.tabs, { 

            /**
             * Call _destroy to clean up
             */
            destroy: function() {
                this._destroy();
                $.ui.tabs.prototype.destroy.apply( this, arguments );
            }
        });
    }



    //Fix $ UI after 1.9.0
    //Fix bug on cache
    if ($.ui.version >= '1.9.0') {
        /**
         * Fix bug cache :
         * Call super after beaforeload event bind,
         * else the first event trigger isn't binded
         */
        //ajaxOptions and cache options
        $.widget( "app.tabs", $.app.tabs, {
            _create: function() {
                var that = this;

                // Clean in order to limit bind cycles
                this.element.unbind("tabsbeforeload.tabs");

                this.element.bind( "tabsbeforeload.tabs", function( event, ui ) {
                    // tab is already cached
                    if ( $.data( ui.tab[ 0 ], "cache.tabs" ) ) {
                        event.preventDefault();
                        return;
                    }

                    $.extend( ui.ajaxSettings, that.options.ajaxOptions, {
                        error: function( xhr, s, e ) {
                            try {
                                // Passing index avoid a race condition when this method is
                                // called after the user has selected another tab.
                                // Pass the anchor that initiated this request allows
                                // loadError to manipulate the tab content panel via $(a.hash)
                                that.options.ajaxOptions.error( xhr, s, ui.tab.closest( "li" ).index(), ui.tab[ 0 ] );
                            }
                            catch ( e ) {}
                        }
                    });

                    ui.jqXHR.success(function() {
                        if ( that.options.cache ) {
                            $.data( ui.tab[ 0 ], "cache.tabs", true );
                        }
                    });
                });

                this._super();
            }
        });
    }
}) (jQuery);

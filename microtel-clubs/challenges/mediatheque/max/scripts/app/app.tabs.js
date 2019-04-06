(function(a) {
  a.widget("app.tabs", a.ui.tabs, {
    options: {
      scrollable: true,
      mousewheel: false,
      closable: false,
      nonClosableIndexes: [],
      duration: 200,
      easing: "swing",
      selectOnScroll: false,
      scrollDistance: 300,
      arrowsPosition: "wrap"
    },
    _create: function() {
      if (this._super) {
        this._super()
      } else {
        a.ui.tabs.prototype._create.apply(this, arguments)
      }
      this.activeTabClass = (this.version > "1.9.0") ? ".ui-tabs-active" : ".ui-tabs-selected";
      this.tablistName = this.tablist ? "tablist" : "list";
      this.tabsName = this.tabs ? "tabs" : "lis"
    },
    _setOptions: function(b) {
      if (this._super) {
        this._super(b)
      } else {
        a.ui.tabs.prototype._setOptions.apply(this, arguments)
      }
      this._process()
    },
    _refresh: function() {
      this._super();
      this._process()
    },
    _tabify: function(b) {
      a.ui.tabs.prototype._tabify.apply(this, arguments);
      this._process()
    },
    _process: function() {
      this[(this.options.closable ? "_closify" : "_unclosify")]();
      this[(this.options.scrollable ? "_scrollify" : "_unscrollify")]();
      this[(this.options.mousewheel ? "_wheelify" : "_unwheelify")]()
    },
    _wheelify: function() {
      this.tablist.unbind("mousewheel.tabs");
      this.tablist.bind("mousewheel.tabs", a.proxy(this._onMousewheeled, this));
      this.wheelified = true
    },
    _scrollify: function() {
      this._unscrollify();
      this.tablist.data("padding-left", this.tablist.css("padding-left"));
      this.tablist.css({
        width: "10000px",
        "padding-left": "0"
      });
      var f = 0,
        d = 0,
        e = {},
        g = {};
      switch (this.options.arrowsPosition) {
        case "wrap":
        default:
          f = 24;
          d = 24;
          e = {
            left: 3
          };
          g = {
            left: 24
          };
          e = {
            left: 3
          };
          g = {
            right: 3
          };
          break;
        case "left":
          f = 48;
          d = 0;
          e = {
            left: 3
          };
          g = {
            left: 24
          };
          break;
        case "right":
          f = 0;
          d = 48;
          e = {
            right: 24
          };
          g = {
            right: 3
          };
          break
      }
      this.tablist.wrap(a("<div/>").addClass(this.widgetBaseClass + "-container").css({
        "margin-left": f + "px",
        "margin-right": d + "px",
        overflow: "hidden",
        position: "relative"
      }));
      this.container = a("." + this.widgetBaseClass + "-container", this.element);
      var c = (parseInt(parseInt(this.tablist.innerHeight() / 2) - 8)),
        b = {
          cursor: "pointer",
          "z-index": 1000,
          position: "absolute",
          height: this.tablist.outerHeight() - (a.browser.safari ? 2 : 1)
        };
      this.nav = a("<div/>").disableSelection().addClass(this.widgetBaseClass + "-navigation").css({
        position: "relative",
        "z-index": 3000,
        display: "none"
      }).append(a("<span/>").disableSelection().attr("title", Global.tr("tabs_previous")).css(b).addClass("ui-state-active ui-corner-tl ui-corner-bl").addClass(this.widgetBaseClass + "-prev").css(e).append(a("<span/>").disableSelection().addClass("ui-icon ui-icon-carat-1-w").html(Global.tr("tabs_previous")).css("margin-top", c)).click(a.proxy(this._previousButtonClicked, this)), a("<span/>").disableSelection().attr("title", Global.tr("tabs_next")).css(b).addClass("ui-state-active ui-corner-tr ui-corner-br").addClass(this.widgetBaseClass + "-next").css(g).append(a("<span/>").addClass("ui-icon ui-icon-carat-1-e").html(Global.tr("tabs_next")).css("margin-top", c)).click(a.proxy(this._nextButtonClicked, this)));
      this.element.prepend(this.nav);
      this._enableNavigationButtons();
      this.element.bind(this.widgetEventPrefix + "select.tabs", a.proxy(function(h, i) {
        this._scrollToTab(this.tabs.eq(i.index))
      }, this));
      this.element.bind(this.widgetEventPrefix + "add.tabs", a.proxy(function() {
        this._enableNavigationButtons()
      }, this));
      a(window).bind("resize.tabs", a.proxy(function() {
        setTimeout(a.proxy(function() {
          this._enableNavigationButtons()
        }, this), this.options.duration)
      }, this));
      this.scrollified = true
    },
    _updateNavigationButtons: function() {
      setTimeout(a.proxy(function() {
        var h = false,
          b = false;
        var e = this.nav.find("." + this.widgetBaseClass + "-next");
        var c = this.nav.find("." + this.widgetBaseClass + "-prev");
        if (this.options.selectOnScroll) {
          h = this.tablist.find("li" + this.activeTabClass).is(":last-child");
          b = this.tablist.find("li" + this.activeTabClass).is(":first-child")
        } else {
          var g = parseFloat(this.tablist.css("margin-left"));
          b = (g == 0);
          var f = this.container.width();
          var d = this.tabs.last();
          var i = d.position().left + d.width();
          h = (i < f)
        }
        if (h) {
          c.removeClass("ui-state-disabled");
          e.addClass("ui-state-disabled")
        } else {
          if (b) {
            e.removeClass("ui-state-disabled");
            c.addClass("ui-state-disabled")
          } else {
            e.removeClass("ui-state-disabled");
            c.removeClass("ui-state-disabled")
          }
        }
      }, this), this.options.duration)
    },
    _enableNavigationButtons: function() {
      if (this._needScroll()) {
        this.nav.show()
      } else {
        this.nav.hide();
        this._showAllTabs()
      }
      this._updateNavigationButtons()
    },
    _showAllTabs: function() {
      this._scrollToTab(this.tablist.find("li:first"));
      this._scrollToTab(this.tablist.find("li:last"))
    },
    _closify: function() {
      this._unclosify();
      this.tabs.each(a.proxy(function(c, b) {
        if (a.inArray(c, this.options.nonClosableIndexes) !== -1) {
          a(".ui-tabs-close", b).remove();
          return
        }
        if (a(b).find(".ui-tabs-close").length > 0) {
          return
        }
        var d = parseInt(parseInt(this.tabs.find(":first").innerHeight() / 2, 10) - 12);
        a(b).disableSelection().append(a("<span>").css({
          "float": "left",
          cursor: "pointer",
          margin: "6px 4px 0 0px"
        }).addClass("ui-tabs-close ui-icon ui-icon-circle-close").attr("title", "Fermer").click(a.proxy(this._closeButtonClicked, this)))
      }, this));
      this.closified = true
    },
    _closeButtonClicked: function(d) {
      var b = a(d.currentTarget).parent();
      var c = this.tabs.index(b);
      this.remove(c);
      this._trigger("close", null, {
        index: c,
        li: b
      });
      this._enableNavigationButtons();
      return false
    },
    _previousButtonClicked: function(b) {
      if (a(b.currentTarget).hasClass("ui-state-disabled")) {
        return
      }
      this._goToPreviousTab()
    },
    _nextButtonClicked: function(b) {
      if (a(b.currentTarget).hasClass("ui-state-disabled")) {
        return
      }
      this._goToNextTab()
    },
    _getPreviousTabIndex: function() {
      var b = this.tablist.find("li" + this.activeTabClass).prevAll().length - 1;
      if (b < 0) {
        b = 0
      }
      return b
    },
    _getNextTabIndex: function() {
      var b = this.tablist.find("li" + this.activeTabClass).prevAll().length + 1;
      if (b >= this.tabs.length) {
        b = this.tabs.length - 1
      }
      return b
    },
    _goToPreviousTab: function() {
      var b = this._getPreviousTabIndex();
      this[(this.options.selectOnScroll ? "_selectTab" : "_scrollTo")]((this.options.selectOnScroll ? b : false))
    },
    _goToNextTab: function() {
      var b = this._getNextTabIndex();
      this[(this.options.selectOnScroll ? "_selectTab" : "_scrollTo")]((this.options.selectOnScroll ? b : true))
    },
    _selectTab: function(b) {
      this.tabs.eq(b).find("a").trigger("click")
    },
    _scrollTo: function(c) {
      if (!this.animated) {
        this.animated = true;
        var f = parseFloat(this.tablist.css("margin-left"));
        if (c) {
          delta = f - this.options.scrollDistance;
          var e = this.container.width();
          var b = this.tabs.last();
          var d = b.position().left + b.width() - this.options.scrollDistance;
          if (d < e) {
            delta += e - d - 6
          }
        } else {
          delta = f + this.options.scrollDistance;
          if (delta > 0) {
            delta = 0
          }
        }
        this.tablist.animate({
          "margin-left": delta
        }, this.options.duration, this.options.easing, a.proxy(function() {
          this.animated = false
        }, this));
        this._updateNavigationButtons()
      }
    },
    _scrollToTab: function(d) {
      d = (typeof d !== "undefined") ? d : this.tabs.find(this.activeTabClass);
      var g = this.container.width();
      var c = d.position() ? d.position().left : 0;
      var b = c + d.width();
      var f = parseFloat(this.tablist.css("margin-left"));
      if (b > g) {
        var h = -(b - g) + f - 6;
        this.tablist.animate({
          "margin-left": h
        }, this.options.duration, this.options.easing)
      }
      if (c < 0) {
        var e = -c + f;
        this.tablist.animate({
          "margin-left": e
        }, this.options.duration, this.options.easing)
      }
      this._updateNavigationButtons()
    },
    _onMousewheeled: function(b, c) {
      if (this._needScroll()) {
        this[((c > 0) ? "_goToPreviousTab" : "_goToNextTab")]()
      }
      return false
    },
    _needScroll: function() {
      var b = 0;
      this.tabs.each(function() {
        b += a(this).outerWidth()
      });
      var c = 60;
      return (b > this.container.width() - c)
    },
    _destroy: function() {
      this._unclosify();
      this._unscrollify();
      this._unwheelify();
      if (this._super) {
        this._super()
      }
    },
    _unscrollify: function() {
      if (this.scrollified) {
        this.tablist.css({
          width: "auto",
          "padding-left": this.tablist.data("padding-left")
        });
        if (this.nav) {
          this.nav.remove()
        }
        this.tablist.unwrap();
        this.element.unbind(".tabs");
        a(window).unbind("resize.tabs");
        this.scrollified = false
      }
    },
    _unwheelify: function() {
      if (this.wheelified) {
        this.tablist.unbind("mousewheel.tabs");
        this.wheelified = false
      }
    },
    _unclosify: function() {
      if (this.closified) {
        a(".ui-tabs-close", this.element).remove();
        this.closified = false
      }
    }
  });
  if (a.ui.version < "1.9.0") {
    a.widget("app.tabs", a.app.tabs, {
      destroy: function() {
        this._destroy();
        a.ui.tabs.prototype.destroy.apply(this, arguments)
      }
    })
  }
  if (a.ui.version >= "1.9.0") {
    a.widget("app.tabs", a.app.tabs, {
      _create: function() {
        var b = this;
        this.element.unbind("tabsbeforeload.tabs");
        this.element.bind("tabsbeforeload.tabs", function(c, d) {
          if (a.data(d.tab[0], "cache.tabs")) {
            c.preventDefault();
            return
          }
          a.extend(d.ajaxSettings, b.options.ajaxOptions, {
            error: function(h, f, g) {
              try {
                b.options.ajaxOptions.error(h, f, d.tab.closest("li").index(), d.tab[0])
              } catch (g) {}
            }
          });
          d.jqXHR.success(function() {
            if (b.options.cache) {
              a.data(d.tab[0], "cache.tabs", true)
            }
          })
        });
        this._super()
      }
    })
  }
})(jQuery);

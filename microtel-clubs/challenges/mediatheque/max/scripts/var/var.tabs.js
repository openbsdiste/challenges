var Tabs = {
  controleur: "index",
  tabs: undefined,
  tabsId: "#tabs",
  initTabs: function(a) {
    if (a != undefined) {
      Tabs.controleur = a
    }
    if (Tabs.tabs == undefined) {
      Tabs.tabs = jQuery(Tabs.tabsId);
      Tabs.tabs.tabs({
        beforeLoad: function(b, c) {
          if (c.tab.data("loaded")) {
            b.preventDefault();
            return
          }
          c.jqXHR.success(function() {
            c.tab.data("loaded", true)
          })
        }
      });
      jQuery(document).on("click", "span.ui-icon-close", function() {
        var b = jQuery(this).closest("li").remove().attr("aria-controls");
        jQuery("#" + b).remove();
        Tabs.tabs.tabs("refresh")
      })
    }
  },
  addTab: function(e, c, a, b) {
    var d = "";
    Tabs.initTabs();
    if (!jQuery(".ui-tabs-nav li[id='" + e + "']", Tabs.tabs).length) {
      if (b) {
        d = "<span class='ui-icon ui-icon-close'>Fermer</span>"
      }
      jQuery("<li id='" + e + "'><a href='" + a + "'>" + c + "</a>" + d + "</li>").appendTo("#tabs .ui-tabs-nav");
      Tabs.tabs.tabs("refresh")
    }
    jQuery(".ui-tabs-nav li", Tabs.tabs).each(function(f) {
      if (jQuery(this)[0]["id"] == e) {
        Tabs.tabs.tabs("option", "active", f)
      }
    })
  },
  getTabs: function() {
    if (Tabs.tabs == undefined) {
      Tabs.initTabs()
    }
    return Tabs.tabs
  },
  closeTab: function(c) {
    var b = Tabs.tabs.find(".ui-tabs-nav li[id=" + c + "]").remove();
    var a = b.attr("aria-controls");
    jQuery("#" + a).remove();
    Tabs.tabs.tabs("refresh")
  },
  setTabNotClosable: function(a) {
    Tabs.tabs.find(".ui-tabs-nav li[id='" + a + "']").find(".ui-icon-close").remove()
  },
  addQuestionsTab: function(b, a) {
    Tabs.initTabs();
    if (!jQuery(".ui-tabs-nav li[id='tabtitle-" + b + "']", Tabs.tabs).length) {
      jQuery("<li id='tabtitle-" + b + "'><a href='challenge/" + Tabs.controleur + "/question?id=" + b + "'>" + a + "</a><span class='ui-icon ui-icon-close'>Fermer</span></li>").appendTo("#tabs .ui-tabs-nav");
      Tabs.tabs.tabs("refresh")
    }
    jQuery(".ui-tabs-nav li", Tabs.tabs).each(function(c) {
      if (jQuery(this)[0]["id"] == "tabtitle-" + b) {
        Tabs.tabs.tabs("option", "active", c)
      }
    })
  },
  renameQuestionsTab: function(b, a) {
    Tabs.tabs.find(".ui-tabs-nav li[id=tabtitle-node_" + b + "] a").text(a)
  },
  closeQuestionsTab: function(a) {
    Tabs.closeTab("tabtitle-node_" + a)
  },
  closeAllQuestionsTab: function() {
    Tabs.tabs.find(".ui-tabs-nav li[id^='tabtitle-node_']").each(function(b, a) {
      Tabs.closeQuestionsTab(a.id.substr(14))
    })
  },
  closeOtherTabs: function(a) {
    Tabs.tabs.find(".ui-tabs-nav li[id!='" + a + "']").each(function(c, b) {
      Tabs.closeTab(b.id)
    });
    jQuery(".ui-tabs-nav li", Tabs.tabs).each(function(b) {
      if (jQuery(this)[0]["id"] == a) {
        Tabs.tabs.tabs("option", "active", b)
      }
    })
  }
};

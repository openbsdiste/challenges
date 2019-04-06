$(document).ready(function() {
  $("#contenuPrincipal").layout({
    defaults: {
      applyDefaultStyles: true,
      size: "auto",
      contentSelector: ".content"
    },
    west: {
      minSize: 60,
      spacing_open: 10,
      spacing_closed: 10,
      resizable: true,
      slidable: true,
      togglerTip_open: Global.tr("open_pane"),
      togglerTip_closed: Global.tr("close_pane"),
      resizerTip: Global.tr("resize_pane"),
      hideTogglerOnSlide: false,
      fxName: "slide",
      fxSpeed_open: 1000,
      fxSpeed_close: 1000,
      fxSettings_open: {
        easing: "easeInQuint"
      },
      fxSettings_close: {
        easing: "easeOutQuint"
      }
    },
    center: {
      spacing_open: 1,
      togglerLength_open: 0,
      togglerLength_closed: -1,
      resizable: false,
      slidable: false,
      fxName: "none"
    }
  });
  $("#questiontree").jstree({
    json_data: {
      ajax: {
        url: "restful/tree",
        datatype: "json",
        contentType: "application/json charset=utf-8",
        data: function(a) {
          return {
            operation: "getChildren",
            id: a.attr ? a.attr("id").replace("node_", "") : 1
          }
        },
        dataFilter: function(a) {
          var b = JSON.stringify(JSON.parse(a).data);
          return b
        },
        error: function(a) {
          Global.showError(a, "msg_sans_info", "msg_erreur_tree")
        }
      }
    },
    themes: {
      theme: "classic"
    },
    ui: {
      initially_select: ["node_1"]
    },
    core: {},
    plugins: ["themes", "json_data", "ui", "crrm"]
  }).bind("select_node.jstree", function(b, c) {
    var d = "";
    for (var a = 0; a < c.args[0].childNodes.length; a++) {
      d += c.args[0].childNodes[a]["textContent"]
    }
    Tabs.addQuestionsTab(c.rslt.obj[0]["id"], d.trim())
  });
  Tabs.initTabs("index");
  jQuery("#questiontree").prev().on("click", function() {
    Tabs.addTab("chalentier", "Le Challenge", "challenge/index/complet", true)
  })
});

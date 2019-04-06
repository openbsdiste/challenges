var StatutTree = {
  afficheListe: function() {
    jQuery("#questionstatut").empty().append("Statuts ");
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "getStatuts"
      },
      success: function(b) {
        var a = jQuery("<select id='statuts' name='statuts' />");
        jQuery("<option />", {
          value: "",
          text: ""
        }).appendTo(a);
        for (var e in b.data) {
          jQuery("<option />", {
            value: e,
            text: b.data[e]
          }).appendTo(a)
        }
        jQuery("#questionstatut").append(a);
        jQuery("#questionstatut").append("<br /><br />");
        var c = jQuery("<div />", {
          id: "statutslist",
          name: "statutslist"
        });
        jQuery("#questionstatut").append(c);
        jQuery("#statuts").on("change", function(d) {
          StatutTree.afficheStatut(jQuery(this).val())
        })
      },
      error: function(a) {
        Global.showError(a, "Impossible de récupérer la liste des statuts", "erreur")
      }
    })
  },
  afficheStatut: function(a) {
    var b = jQuery("#statutslist");
    b.empty();
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "getQuestionsByStatut",
        statut: a
      },
      success: function(c) {
        for (var d in c.data) {
          var e = jQuery("<div />", {
            question: d
          });
          e.addClass("listeCliquable");
          e.append(c.data[d]);
          e.on("click", function() {
            var f = jQuery(this);
            Tabs.addQuestionsTab("node_" + f.attr("question"), f.html())
          });
          b.append(e)
        }
      },
      error: function(c) {
        jQuery("questionstatut").empty();
        Global.showError(c, "Impossible de récupérer la liste des questions", "erreur")
      }
    })
  }
};

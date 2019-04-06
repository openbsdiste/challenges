var AuteurTree = {
  afficheListe: function() {
    jQuery("#questionauteur").empty().append("Auteurs ");
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "getAuthors"
      },
      success: function(b) {
        var a = jQuery("<select id='authors' name='authors' />");
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
        jQuery("#questionauteur").append(a);
        jQuery("#questionauteur").append("<br /><br />");
        var c = jQuery("<div />", {
          id: "authorslist",
          name: "authorslist"
        });
        jQuery("#questionauteur").append(c);
        jQuery("#authors").on("change", function(d) {
          AuteurTree.afficheAuteur(jQuery(this).val())
        })
      },
      error: function(a) {
        Global.showError(a, "Impossible de récupérer la liste des auteurs", "erreur")
      }
    })
  },
  afficheAuteur: function(b) {
    var a = jQuery("#authorslist");
    a.empty();
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "getQuestionsByAuthor",
        auteur: b
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
          a.append(e)
        }
      },
      error: function(c) {
        jQuery("questionauteur").empty();
        Global.showError(c, "Impossible de récupérer la liste des questions", "erreur")
      }
    })
  }
};

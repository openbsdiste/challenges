var OutilsAccueil = {
  initialize: function() {
    jQuery("#accordeonAccueil :button").bind("click", function() {
      Tabs.closeTab(this.id);
      Tabs.addTab(this.id, this.value, "challenge/notation/" + this.id, true)
    })
  }
};
var NotationManuelle = {
  initialize: function() {
    jQuery("#NoterALaMain").bind("click", function() {
      var a = jQuery("#idclub").val();
      if (a == "") {
        Global.showMessage("Vous devez sélectionner un club pour le noter !", "notation impossible");
        jQuery("zoneNotation").empty()
      } else {
        jQuery.ajax({
          type: "POST",
          async: false,
          datatype: "json",
          url: "restnotation/manuelle",
          data: {
            format: "json",
            club: a
          },
          success: function(b) {
            NotationManuelle.creeTableau(b.data)
          },
          error: function(b) {
            Global.showError(b, "ERREUR : Abandon", "Erreur")
          }
        })
      }
    })
  },
  creeLigne: function(g) {
    var f = jQuery("<tr />");
    var e = jQuery("<td />");
    var c = jQuery("<td />");
    var a = jQuery("<tr />");
    for (var b = 1; b < g.level; b++) {
      e.append("&nbsp;&nbsp;&nbsp;&nbsp;")
    }
    if (g.information == "1") {
      e.append("<b>" + g.titre + "&nbsp;</b>");
      c.append("&nbsp;");
      a.append("&nbsp;")
    } else {
      e.append(g.titre + "&nbsp;");
      c.append("&nbsp;(" + g.valeur + " points)");
      a.append('<input id="q' + g.id + '"  value="' + g.note + '" size="5"/>')
    }
    f.append(e);
    f.append(c);
    f.append(a);
    return f
  },
  creeTableau: function(b) {
    var a = jQuery("<table />");
    for (d in b) {
      a.append(NotationManuelle.creeLigne(b[d]))
    }
    jQuery("#zoneNotation").empty().append(a).append('<input id="validerNoter" class="ui-state-default ui-corner-all" type="button" value="Enregistrer" />');
    jQuery("#validerNoter").bind("click", function() {
      NotationManuelle.valideNotes()
    })
  },
  valideNotes: function() {
    var a = jQuery("#idclub").val();
    var b = new Array();
    jQuery(':input[id^="q"]', "#zoneNotation").each(function() {
      b.push(this.id + "_" + this.value)
    });
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restnotation/validenotes",
      data: {
        format: "json",
        club: a,
        notes: b
      },
      success: function(c) {
        Global.showMessage("Notes prises en compte.");
        jQuery("#zoneNotation").empty()
      },
      error: function(c) {
        Global.showError(c, "ERREUR : Abandon", "Erreur")
      }
    })
  }
};
var ImportChallenge = {
  initialize: function() {
    jQuery("#import").ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSend: ImportChallenge.beforeSendDocument,
      uploadProgress: ImportChallenge.uploadProgressDocument,
      complete: ImportChallenge.processDocument
    })
  },
  beforeSendDocument: function() {
    var b = jQuery("#importProgress");
    var a = jQuery(".progress-label", b);
    jQuery("#traitement").empty();
    b.progressbar({
      value: false,
      change: function() {
        a.text(b.progressbar("value") + " %")
      },
      complete: function() {
        a.text("terminé !")
      }
    })
  },
  uploadProgressDocument: function(f, a, e, c) {
    var b = jQuery("#importProgress");
    b.progressbar("value", c)
  },
  processDocument: function(b) {
    var a = jQuery.parseJSON(b.responseText).data;
    var c = jQuery("#traitement");
    jQuery("#importProgress").progressbar("value", 100);
    jQuery("#fichierExcel").val("");
    if (a.name !== undefined) {
      c.append("<b>Fichier incorrect. Abandon.</b>")
    } else {
      c.append("<b>Import des réponses pour " + a.infos.club + ".</b><br />");
      if (a.parse.problemes.problemes.length == 0) {
        c.append("<b>Pas de problèmes trouvés.</b><br />")
      } else {
        c.append("<b>" + a.parse.problemes.problemes.length + " problème(s) trouvé(s).</b><br />");
        if (a.parse.problemes.possibles.length == 0) {
          c.append("<b>Pas de solutions possibles</b><br />")
        } else {
          c.append("<b>" + a.parse.problemes.possibles.length + " solution(s) possible(s).</b><br />")
        }
      }
      c.append("<br />");
      ImportChallenge.creeFormulaire(a)
    }
  },
  creeFormulaire: function(g) {
    var e = jQuery('<form id="vaildImportForm" action="restnotation/valideimport" method="post" />');
    var b = '<option value=""></option>';
    var a = 0;
    a = g.parse.reponses.length;
    e.append('<input type="hidden" name="chalid" value="' + g.infos.chalid + '" />');
    e.append('<input type="hidden" name="club" value="' + g.infos.id + '" />');
    for (var c = 0; c < a; c++) {
      e.append('<input type="hidden" name="rn' + c + '" value="' + g.parse.reponses[c].noeud + '" />');
      e.append('<input type="hidden" name="rr' + c + '" value="' + g.parse.reponses[c].reponse + '" />')
    }
    a = g.parse.problemes.possibles.length;
    for (var c = 0; c < a; c++) {
      b += '<option value="' + g.parse.problemes.possibles[c].noeud + '">' + g.parse.problemes.possibles[c].indication + "</option>"
    }
    a = g.parse.problemes.problemes.length;
    for (var c = 0; c < a; c++) {
      e.append(g.parse.problemes.problemes[c].indication + "&nbsp;:&nbsp;");
      e.append('<select name="sn' + c + '">' + b + "</select>");
      e.append('<input type="hidden" name="sr' + c + '" value="' + g.parse.problemes.problemes[c].reponse + '" />');
      e.append("<br />")
    }
    e.append("<br />");
    e.append('<input value="Valider cet import" type="submit" class="ui-state-default ui-corner-all">');
    jQuery("#traitement").append(e);
    jQuery("#vaildImportForm").ajaxForm({
      dataType: "json",
      semantic: true,
      success: ImportChallenge.completeImport,
      error: ImportChallenge.errorImport
    })
  },
  completeImport: function() {
    jQuery("#traitement").empty().append("<b>Import confirmé.</b>")
  },
  errorImport: function() {
    jQuery("#traitement").empty().append("<b>Erreur durant le processus. Recommencez.</b>")
  }
};
var TermineChallenge = {
  lance: function() {
    Tabs.closeOtherTabs("modterchal");
    jQuery("#questiontree").hide();
    jQuery("#modterchalbt").unbind("click");
    TermineChallenge.termine()
  },
  termine: function() {
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restnotation/termine",
      data: {
        format: "json"
      },
      success: function(a) {
        setTimeout("window.location.href = '" + Global.baseUrl + "/authentification/deconnexion/index'", 100)
      },
      error: function(a) {
        Global.showError(a, "ERREUR : Abandon", "Erreur")
      }
    })
  }
};
var QuestionForms = {
  initialize: function(a) {
    Global.setAccordion("accordeonNotation" + a);
    Global.addEditorInstance("saisieReponseTexte" + a);
    jQuery("#choix" + a).on("change", function() {
      var b = this.value;
      var c = jQuery("#rq" + a);
      c.empty();
      jQuery("#frmnote" + a).hide();
      if (b != "vide") {
        jQuery.ajax({
          type: "POST",
          async: false,
          datatype: "json",
          url: "restnotation/reponse",
          data: {
            format: "json",
            qid: a,
            club: b
          },
          success: function(f) {
            jQuery("#frmnote" + a).show();
            var h = jQuery("<p />");
            h.append("Documents : ");
            for (var g in f.data.documents) {
              var e = jQuery("<a />");
              e.attr("href", "challenge/notation/fichier?q=" + a + "&club=" + b + "&fic=" + f.data.documents[g]);
              e.append(f.data.documents[g]);
              e.attr("target", "_blank");
              h.append(e);
              h.append("&nbsp;")
            }
            c.append(h);
            c.append("<br />");
            c.append(f.data.reponse);
            jQuery("#club" + a).val(b);
            jQuery("#note" + a).val(f.data.note);
            if (f.data.estReponse === "1") {
              document.getElementById("util" + a).checked = "checked";
            } else {
              document.getElementById("util" + a).checked = "";
            }
          },
          error: function(e) {
            c.append("<h2>ERREUR : Abandon.</h2>")
          }
        })
      }
    });
    jQuery("#reponsetexte" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSerialize: QuestionForms.beforeSerializeReponses,
      success: QuestionForms.processReponses,
      error: QuestionForms.cancelJson
    });
    jQuery("#frmnote" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSerialize: QuestionForms.beforeSerializeNote,
      success: QuestionForms.processNote,
      error: QuestionForms.cancelJson
    });
    jQuery("#frmnote" + a).hide()
  },
  beforeSerializeNote: function(a, b) {
    var e = a[0].id.substr(7);
    var c = jQuery("#note" + e).val();
    if ((parseFloat(c) != c) || (parseFloat(c) < 0)) {
      Global.showMessage("Note invalide.<br />La note doit-être supérieure ou égale à 0", "Erreur");
      return false
    }
    return true
  },
  processNote: function(a) {
    Global.showMessage("Note prise en compte")
  },
  cancelJson: function(a) {
    Global.showError(a, "Erreur de sauvegarde", "erreur")
  },
  beforeSerializeReponses: function(a, b) {
    var c = a[0].id.substr(12);
    jQuery("#texte" + c).val(jQuery("#saisieReponseTexte" + c).html());
    return true;
  },
  processReponses: function(a) {
    Global.showMessage("Réponse prise en compte");
  }
};
jQuery(document).ready(function() {
  jQuery("#contenuPrincipal").layout({
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
  jQuery("#layoutCenter").css("overflow", "hidden");
  jQuery("#questiontree").jstree({
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
    plugins: ["themes", "json_data", "ui"]
  }).bind("select_node.jstree", function(b, c) {
    var e = "";
    for (var a = 0; a < c.args[0].childNodes.length; a++) {
      e += c.args[0].childNodes[a]["textContent"]
    }
    Tabs.addQuestionsTab(c.rslt.obj[0]["id"], e.trim())
  });
  Tabs.initTabs("notation");
  var a = Global.getEditor();
  a.setPanel("nicCommonPanel");  
  jQuery("#preview").on("click", function() {
    Tabs.addTab("chalentier", "Le Challenge", "challenge/index/complet", true)
  });
  jQuery("#questionauteur").hide();
  jQuery("#spanarbre").on("click", function() {
    jQuery("#questiontree").jstree("refresh");
    jQuery("#questionauteur").hide();
    jQuery("#questionarbre").show()
  });
  jQuery("#spanauteur").on("click", function() {
    jQuery("#questionarbre").hide();
    jQuery("#questionauteur").show();
    AuteurTree.afficheListe()
  })
});

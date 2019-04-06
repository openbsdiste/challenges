var PingPong = {
  initialize: function() {
    var a = 300000;
    delay(function() {
      PingPong.ping()
    }, a)
  },
  ping: function() {
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restutilisateur/ping",
      data: {
        format: "json"
      },
      success: function(a) {
        PingPong.initialize()
      },
      error: function(a) {
        setTimeout("window.location.href = 'default/index/ping'", 100)
      }
    })
  }
};
var OutilsAccueil = {
  initialize: function() {
    jQuery("#accordeonAccueil :button").bind("click", function() {
      Tabs.closeTab(this.id);
      Tabs.addTab(this.id, this.value, "challenge/reponse/" + this.id, true)
    })
  }
};
var ValideChallenge = {
  lance: function() {
    Tabs.closeOtherTabs("modvalchal");
    jQuery("#confirmer").unbind("click");
    ValideChallenge.compte()
  },
  compte: function() {
    var a = jQuery("#textepublication");
    jQuery("<p />").append("Comptage en cours des questions et des documents... ").append(jQuery("<span />").attr("id", "cpte")).appendTo(a);
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restutilisateur/validation",
      data: {
        format: "json",
        action: "compte"
      },
      success: function(b) {
        jQuery("#cpte", a).empty().append("Ok.");
        var c = jQuery("<span />").attr("id", "qstions");
        jQuery("<p />").append("Validation des réponses... reste ").append(c).append(" réponse(s).").appendTo(a);
        jQuery("#publicationProgress").progressbar({
          max: b.data.questions.length + b.data.documents.length + 1,
          value: b.data.questions.length + b.data.documents.length + 1
        });
        ValideChallenge.question(b.data.questions, b.data.documents)
      },
      error: function(b) {
        jQuery("#cpte", a).empty().append("Ko.");
        jQuery("<br /><h2>ERREUR : Abandon.</h2>").appendTo(a)
      }
    })
  },
  question: function(d, c) {
    var a = jQuery("#textepublication");
    var b = true;
    while (d.length && b) {
      jQuery("#publicationProgress").progressbar({
        value: d.length + c.length + 1
      });
      jQuery("#qstions").empty().append(d.length);
      document.getElementById("qstions").innerHTML = d.length;
      var f = d.pop();
      jQuery.ajax({
        type: "POST",
        async: false,
        datatype: "json",
        url: "restutilisateur/validation",
        data: {
          format: "json",
          action: "question",
          id: f
        },
        success: function() {},
        error: function(g) {
          jQuery("<br /><h2>ERREUR sur " + f + " : Abandon.</h2>").appendTo(a);
          b = false
        }
      })
    }
    if (b) {
      jQuery("#qstions", a).empty().append("0");
      var e = jQuery("<span />").attr("id", "dcuments");
      jQuery("<p>").append("Validation des documents... reste ").append(e).append(" document(s).").appendTo(a);
      ValideChallenge.document(c)
    }
  },
  document: function(c) {
    var a = jQuery("#textepublication");
    var b = true;
    while (c.length && b) {
      jQuery("#publicationProgress").progressbar({
        value: c.length + 1
      });
      jQuery("#dcuments").empty().append(c.length);
      var f = c.pop();
      jQuery.ajax({
        type: "POST",
        async: false,
        datatype: "json",
        url: "restutilisateur/validation",
        data: {
          format: "json",
          action: "document",
          id: f.q,
          nom: f.nom
        },
        success: function() {},
        error: function(d) {
          jQuery("<br /><h2>ERREUR sur " + f.q + "-" + f.nom + " : Abandon.</h2>").appendTo(a);
          b = false
        }
      })
    }
    if (b) {
      jQuery("#dcuments", a).empty().append("0");
      var e = jQuery("<span />").attr("id", "ovrture");
      jQuery("<p />").append("Validation du challenge... ").append(e).appendTo(a);
      ValideChallenge.ouverture()
    }
  },
  ouverture: function() {
    var a = jQuery("#textepublication");
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restutilisateur/validation",
      data: {
        format: "json",
        action: "ouvre"
      },
      success: function(b) {
        jQuery("#publicationProgress").progressbar({
          value: 0
        });
        jQuery("#ovrture", a).empty().append("Ok.");
        jQuery("<br /><h2>Terminé !</h2>").appendTo(a);
        jQuery("<p>Redirection vers la page d'accueil...").appendTo(a);
        setTimeout("window.location.href = '" + Global.baseUrl + "'", 2000)
      },
      error: function(b) {
        jQuery("#ovrture", a).empty().append("Ko.");
        jQuery("<br /><h2>ERREUR : Abandon.</h2>").appendTo(a)
      }
    })
  }
};
var DeleteDocumentsReponse = {
  initialize: function() {
    jQuery(".deletefilereponse").unbind("click").on("click", function(a) {
      var b = a.target.attributes.qid.nodeValue;
      var c = a.target.attributes.filename.nodeValue;
      jQuery.ajax({
        type: "DELETE",
        datatype: "json",
        url: "restutilisateur/documents",
        data: {
          format: "json",
          qid: b,
          fic: c
        },
        success: function(d) {
          Preview.showDocumentsReponseClub(d.data.id, d.data.documents, d.data.statut);
          DeleteDocumentsReponse.initialize()
        },
        error: function(d) {
          Global.showError(d, "msg_sans_info", "msg_erreur_tree")
        }
      })
    })
  }
};
var QuestionForms = {
  initialize: function(a) {
    Global.setAccordion("accordeonReponse" + a);
    if (jQuery("#saisie" + a).length) {
      Global.addEditorInstance("saisie" + a);
      Global.addEditorInstance("saisieinterne" + a);
      jQuery("#textes" + a).ajaxForm({
        dataType: "json",
        semantic: true,
        beforeSerialize: QuestionForms.beforeSerializeTextes,
        success: QuestionForms.processTextes,
        error: QuestionForms.cancelJson
      });
      jQuery("#notesinternes" + a).ajaxForm({
        dataType: "json",
        semantic: true,
        beforeSerialize: QuestionForms.beforeSerializeNotes,
        success: QuestionForms.processNotes,
        error: QuestionForms.cancelJson
      });
      jQuery("#documents" + a).ajaxForm({
        dataType: "json",
        semantic: true,
        beforeSend: QuestionForms.beforeSendDocuments,
        uploadProgress: QuestionForms.uploadProgressDocuments,
        complete: QuestionForms.processDocuments
      })
    }
  },
  beforeSerializeTextes: function(a, b) {
    var c = a[0].id.substr(6);
    jQuery("#texte" + c).val(jQuery("#saisie" + c).html());
    return true
  },
  processTextes: function(a) {
    Global.showMessage("Texte pris en compte")
  },
  beforeSerializeNotes: function(a, b) {
    var c = a[0].id.substr(13);
    jQuery("#noteinterne" + c).val(jQuery("#saisieinterne" + c).html());
    return true
  },
  processNotes: function(a) {
    Global.showMessage("Notes prises en compte")
  },
  beforeSendDocuments: function() {
    var c = this.url.substr(29);
    var b = jQuery("#docprogress" + c);
    var a = jQuery(".progress-label", b);
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
  uploadProgressDocuments: function(e, a, d, c) {
    var f = this.url.substr(29);
    var b = jQuery("#docprogress" + f);
    b.progressbar("value", c)
  },
  processDocuments: function(b) {
    var a = jQuery.parseJSON(b.responseText).data;
    jQuery("#docprogress" + a.id).progressbar("value", 100);
    jQuery("#document" + a.id).val("");
    Preview.showDocumentsReponseClub(a.id, a.documents, a.statut);
    DeleteDocumentsReponse.initialize()
  },
  cancelJson: function(a) {
    Global.showError(a, "Erreur de sauvegarde", "erreur")
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
        data: function(b) {
          return {
            operation: "getChildren",
            id: b.attr ? b.attr("id").replace("node_", "") : 1
          }
        },
        dataFilter: function(b) {
          var c = JSON.stringify(JSON.parse(b).data);
          return c
        },
        error: function(b) {
          Global.showError(b, "msg_sans_info", "msg_erreur_tree")
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
  }).bind("select_node.jstree", function(c, d) {
    var e = "";
    for (var b = 0; b < d.args[0].childNodes.length; b++) {
      e += d.args[0].childNodes[b]["textContent"]
    }
    Tabs.addQuestionsTab(d.rslt.obj[0]["id"], e.trim())
  });
  Tabs.initTabs("reponse");
  var a = Global.getEditor();
  a.setPanel("nicCommonPanel");
  jQuery("#preview").on("click", function() {
    Tabs.addTab("chalentier", "Le Challenge", "challenge/index/complet", true)
  });
  PingPong.initialize()
});

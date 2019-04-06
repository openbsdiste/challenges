var OutilsAccueil = {
  initialize: function() {
    jQuery("#accordeonAccueil :button").bind("click", function() {
      Tabs.closeTab(this.id);
      Tabs.addTab(this.id, this.value, "challenge/elaboration/" + this.id, true)
    })
  }
};
var PublieChallenge = {
  lance: function() {
    Tabs.closeOtherTabs("modpubchal");
    PublieChallenge.compte();
    jQuery("#confirmer").unbind("click")
  },
  compte: function() {
    var a = jQuery("#textepublication");
    jQuery("<p />").append("Comptage en cours des questions et des documents... ").append(jQuery("<span />").attr("id", "cpte")).appendTo(a);
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restmoderateur/publication",
      data: {
        format: "json",
        action: "compte"
      },
      success: function(b) {
        jQuery("#cpte", a).empty().append("Ok.");
        var c = jQuery("<span />").attr("id", "qstions");
        jQuery("<p />").append("Publication des questions... reste ").append(c).append(" question(s).").appendTo(a);
        jQuery("#publicationProgress").progressbar({
          max: b.data.questions.length + b.data.documents.length + 1,
          value: b.data.questions.length + b.data.documents.length + 1
        });
        PublieChallenge.question(b.data.questions, b.data.documents)
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
        url: "restmoderateur/publication",
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
      jQuery("<p>").append("Publication des documents... reste ").append(e).append(" document(s).").appendTo(a);
      PublieChallenge.document(c)
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
        url: "restmoderateur/publication",
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
      jQuery("<p />").append("Ouverture du challenge... ").append(e).appendTo(a);
      PublieChallenge.ouverture()
    }
  },
  ouverture: function() {
    var a = jQuery("#textepublication");
    jQuery.ajax({
      type: "POST",
      async: false,
      datatype: "json",
      url: "restmoderateur/publication",
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
var DeleteDocuments = {
  initialize: function() {
    jQuery(".deletefile").unbind("click");
    jQuery(".deletefile").on("click", function(a) {
      var b = a.target.attributes.qid.nodeValue;
      var c = a.target.attributes.filename.nodeValue;
      jQuery.ajax({
        type: "DELETE",
        datatype: "json",
        url: "restmoderateur/documents",
        data: {
          format: "json",
          qid: b,
          fic: c
        },
        success: function(d) {
          Preview.showPreview(d.data.id, d.data.question, d.data.documents, d.data.statut);
          Preview.showDocumentsQuestion(d.data.id, d.data.documents, d.data.statut);
          DeleteDocuments.initialize()
        },
        error: function(d) {
          Global.showError(d, "msg_sans_info", "msg_erreur_tree")
        }
      })
    })
  }
};
var DeleteDocumentsReponse = {
  initialize: function() {
    jQuery(".deletefilereponse").unbind("click");
    jQuery(".deletefilereponse").on("click", function(a) {
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
          Preview.showDocumentsReponse(d.data.id, d.data.documents, d.data.statut, "Reponse");
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
    Global.setAccordion("accordeonQuestion" + a);
    Global.addEditorInstance("saisie" + a);
    Global.addEditorInstance("saisieReponse" + a);
    jQuery("#informations" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSerialize: QuestionForms.beforeSerializeInformations,
      success: QuestionForms.processInformations
    });
    jQuery("#textes" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSerialize: QuestionForms.beforeSerializeTextes,
      success: QuestionForms.processTextes,
      error: QuestionForms.cancelJson
    });
    jQuery("#documents" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSend: QuestionForms.beforeSendDocuments,
      uploadProgress: QuestionForms.uploadProgressDocuments,
      complete: QuestionForms.processDocuments,
      error: QuestionForms.cancelJson
    });
    jQuery("#reponses" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSerialize: QuestionForms.beforeSerializeReponses,
      success: QuestionForms.processReponses,
      error: QuestionForms.cancelJson
    });
    jQuery("#documentsReponse" + a).ajaxForm({
      dataType: "json",
      semantic: true,
      beforeSend: QuestionForms.beforeSendDocumentsReponse,
      uploadProgress: QuestionForms.uploadProgressDocumentsReponse,
      complete: QuestionForms.processDocumentsReponse,
      error: QuestionForms.cancelJson
    })
  },
  beforeSerializeInformations: function(b, a) {
    return true
  },
  processInformations: function(b) {
    var a = jQuery("#image" + b.data.id);
    a.attr("src", a.attr("srcbase") + "&t=" + new Date().getTime());
    jQuery("#imgsrc" + b.data.id).val("");
    Preview.showPreview(b.data.id, b.data.question, b.data.documents, b.data.statut);
    Global.showMessage("Informations prises en compte")
  },
  beforeSerializeTextes: function(a, b) {
    var c = a[0].id.substr(6);
    jQuery("#texte" + c).val(jQuery("#saisie" + c).html());
    return true
  },
  processTextes: function(a) {
    jQuery("#preview" + a.data.id).empty().append(a.data.preview);
    Global.showMessage("Texte pris en compte")
  },
  beforeSerializeReponses: function(a, b) {
    var c = a[0].id.substr(8);
    jQuery("#reponse" + c).val(jQuery("#saisieReponse" + c).html());
    return true
  },
  processReponses: function(a) {
    Global.showMessage("Réponse prise en compte")
  },
  beforeSendDocuments: function() {
    var c = this.url.substr(28);
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
    var f = this.url.substr(28);
    var b = jQuery("#docprogress" + f);
    b.progressbar("value", c)
  },
  processDocuments: function(b) {
    var a = jQuery.parseJSON(b.responseText).data;
    jQuery("#docprogress" + a.id).progressbar("value", 100);
    jQuery("#document" + a.id).val("");
    Preview.showPreview(a.id, a.question, a.documents, a.statut);
    Preview.showDocumentsQuestion(a.id, a.documents, a.statut);
    DeleteDocuments.initialize()
  },
  beforeSendDocumentsReponse: function() {
    var c = this.url.substr(29);
    var b = jQuery("#docreponseprogress" + c);
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
  uploadProgressDocumentsReponse: function(e, a, d, c) {
    var f = this.url.substr(29);
    var b = jQuery("#docreponseprogress" + f);
    b.progressbar("value", c)
  },
  processDocumentsReponse: function(b) {
    var a = jQuery.parseJSON(b.responseText).data;
    jQuery("#docreponseprogress" + a.id).progressbar("value", 100);
    jQuery("#documentReponse" + a.id).val("");
    Preview.showDocumentsReponse(a.id, a.documents, a.statut, "Reponse");
    DeleteDocumentsReponse.initialize()
  },
  cancelJson: function(a) {
    Global.showError(a, "Erreur de sauvegarde", "erreur")
  }
};
var StatutsGrid = {
  genereGrille: function() {
    var a;
    jQuery("#statutsgrid").jqGrid({
      url: Global.baseUrl + "/restmoderateur/grid",
      datatype: "json",
      mtype: "POST",
      colNames: ["Id", "Statut", "Bloquant"],
      colModel: [{
        name: "id",
        sortable: false,
        index: "id",
        hidden: true,
        fixed: true,
        editable: false,
        width: 50,
        align: "right"
      }, {
        name: "statut",
        sortable: true,
        index: "statut",
        editable: true
      }, {
        name: "bloquant",
        index: "bloquant",
        fixed: true,
        width: 60,
        align: "center",
        stype: "select",
        searchoptions: {
          value: "tous:Tous;on:Oui;off:Non"
        },
        editable: true,
        edittype: "checkbox",
        formatter: "checkbox"
      }],
      rowNum: 10,
      rowList: [10],
      pager: "#statutspager",
      viewrecords: true,
      hidegrid: false,
      autowidth: true,
      height: "100%",
      sortname: "statut",
      sortorder: "asc",
      onSelectRow: function(b) {
        if (b && b !== a) {
          jQuery("#statutsgrid").jqGrid("restoreRow", a);
          jQuery("#statutsgrid").jqGrid("editRow", b, true);
          a = b
        }
      },
      editurl: Global.baseUrl + "/restmoderateur/saisie",
      caption: "Gestion des statuts"
    });
    jQuery("#statutsgrid").jqGrid("navGrid", "#statutspager", {
      edit: false,
      add: true,
      del: false,
      search: false
    });
    jQuery("#statutsgrid").jqGrid("filterToolbar", {
      stringResult: true,
      searchOnEnter: false
    })
  }
};

function reloadParentTreeNode(c) {
  var b = jQuery.jstree._reference("#questiontree");
  var a = b._get_parent("node_" + c);
  b.refresh(a)
}
jQuery(document).ready(function() {
  Tabs.controleur = "elaboration";
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
    contextmenu: {
      items: {
        ccp: false,
        create: {
          label: Global.tr("tree_ctx_creer")
        },
        rename: {
          label: Global.tr("tree_ctx_renommer")
        },
        remove: {
          label: Global.tr("tree_ctx_supprimer")
        }
      }
    },
    dnd: {
      drag_check: function(b) {
        return {
          before: false,
          after: false,
          inside: true
        }
      },
      drag_finish: function(b) {
        jQuery.ajax({
          async: false,
          type: "PUT",
          datatype: "json",
          url: "restful/tree",
          data: {
            operation: "dragQuestion",
            id: b.o.id,
            qid: b.o.attributes.getNamedItem("qid").nodeValue,
            tid: b.r[0].id
          },
          success: function(c) {
            var d = Tabs.tabs.tabs("option", "active");
            Tabs.tabs.tabs("option", "active", 0);
            Tabs.tabs.tabs("option", "active", d)
          },
          error: function(c) {
            jQuery.jstree.rollback(b.rlbk);
            Global.showError(c, "msg_sans_info", "msg_erreur_tree")
          }
        })
      }
    },
    plugins: ["themes", "json_data", "ui", "crrm", "dnd", "contextmenu"]
  }).bind("select_node.jstree", function(c, d) {
    var e = "";
    for (var b = 0; b < d.args[0].childNodes.length; b++) {
      e += d.args[0].childNodes[b]["textContent"]
    }
    Tabs.addQuestionsTab(d.rslt.obj[0]["id"], e.trim())
  }).bind("delete_node.jstree", function(b, c) {
    Global.confirmActionDialog("Confirmer la suppression ?", "Suppression", function() {
      c.rslt.obj.each(function() {
        var d = jQuery(this).attr("id").replace("node_", "");
        jQuery.ajax({
          async: false,
          type: "DELETE",
          datatype: "json",
          url: "restful/tree",
          data: {
            format: "json",
            operation: "deleteNode",
            id: d
          },
          success: function(j, e, g) {
            var h = jQuery.parseJSON(g.responseText).data;
            for (var f in h.id) {
              Tabs.closeQuestionsTab(h.id[f])
            }
            reloadParentTreeNode(d)
          },
          error: function(e) {
            jQuery.jstree.rollback(c.rlbk);
            Global.showError(e, "msg_sans_info", "msg_erreur_tree")
          }
        })
      })
    }, function() {
      jQuery.jstree.rollback(c.rlbk)
    })
  }).bind("create.jstree", function(b, c) {
    jQuery.ajax({
      async: false,
      type: "PUT",
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "addNode",
        title: c.rslt.name,
        position: c.rslt.position,
        parent: c.args[0][0].getAttribute("id").replace("node_", "")
      },
      success: function(d) {
        jQuery(c.rslt.obj).attr("id", "node_" + d.data.id)
      },
      error: function(d) {
        jQuery.jstree.rollback(c.rlbk);
        Global.showError(d, "msg_sans_info", "msg_erreur_tree")
      }
    })
  }).bind("rename.jstree", function(b, c) {
    jQuery.ajax({
      async: false,
      type: "PUT",
      datatype: "json",
      url: "restful/tree",
      data: {
        format: "json",
        operation: "renameNode",
        id: c.args[0][0].id.replace("node_", ""),
        title: c.rslt.new_name
      },
      success: function(d) {
        if (!d.data.status || !d.headTitle) {
          jQuery.jstree.rollback(c.rlbk);
          Global.showMessage(d.data.message || "msg_sans_info", "msg_erreur_tree")
        } else {
          Tabs.renameQuestionsTab(d.data.id, d.data.title);
          reloadParentTreeNode(d.data.id)
        }
      },
      error: function(d) {
        jQuery.jstree.rollback(c.rlbk);
        Global.showError(d, "msg_sans_info", "msg_erreur_tree")
      }
    })
  }).bind("move_node.jstree", function(b, c) {
    c.rslt.o.each(function(d) {
      jQuery.ajax({
        async: false,
        type: "POST",
        datatype: "json",
        url: "restful/tree",
        data: {
          format: "json",
          operation: "moveNode",
          id: jQuery(this).attr("id").replace("node_", ""),
          ref: c.rslt.cr === -1 ? 1 : c.rslt.np.attr("id").replace("node_", ""),
          position: c.rslt.cp + d,
          title: c.rslt.name,
          copy: c.rslt.cy ? 1 : 0
        },
        success: function(e) {
          if (!e.data || !e.data.status) {
            jQuery.jstree.rollback(c.rlbk);
            Global.showMessage("msg_sans_info", "msg_erreur_tree")
          } else {
            jQuery(c.rslt.oc).attr("id", "node_" + e.data.id);
            if (c.rslt.cy && jQuery(c.rslt.oc).children("UL").length) {
              c.inst.refresh(c.inst._get_parent(c.rslt.oc))
            }
          }
        },
        error: function(e) {
          jQuery.jstree.rollback(c.rlbk);
          Global.showError(e, "msg_sans_info", "msg_erreur_tree")
        }
      })
    })
  });
  Tabs.initTabs("elaboration");
  var a = Global.getEditor();
  a.setPanel("nicCommonPanel");
  jQuery("#preview").on("click", function() {
    Tabs.addTab("chalentier", "Le Challenge", "challenge/elaboration/complet", true);
  });
  jQuery("#questionauteur").hide();
  jQuery("#questionstatut").hide();
  jQuery("#spanarbre").on("click", function() {
    jQuery("#questiontree").jstree("refresh");
    jQuery("#questionauteur").hide();
    jQuery("#questionstatut").hide();
    jQuery("#questionarbre").show()
  });
  jQuery("#spanauteur").on("click", function() {
    jQuery("#questionarbre").hide();
    jQuery("#questionstatut").hide();
    jQuery("#questionauteur").show();
    AuteurTree.afficheListe()
  });
  jQuery("#spanstatut").on("click", function() {
    jQuery("#questionarbre").hide();
    jQuery("#questionauteur").hide();
    jQuery("#questionstatut").show();
    StatutTree.afficheListe()
  })
});

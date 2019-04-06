$(document).ready(function() {
  var a;
  jQuery("#usersgrid").jqGrid({
    url: Global.baseUrl + "restadministrateur/grid",
    datatype: "json",
    mtype: "POST",
    colNames: ["Id", "Identifiant", "Mot de passe", "Mémoire", "Rôle", "Courriel", "Club", "Actif"],
    colModel: [{
      name: "id",
      sortable: false,
      index: "id",
      hidden: true,
      fixed: true,
      width: 50,
      align: "right"
    }, {
      name: "login",
      index: "login",
      fixed: true,
      width: 120,
      editable: true
    }, {
      name: "password",
      index: "password",
      fixed: true,
      width: 200,
      editable: true,
      search: false
    }, {
      name: "memoire",
      index: "memoire",
      fixed: true,
      width: 300,
      editable: true,
      search: false
    }, {
      name: "role",
      index: "role",
      fixed: true,
      width: 100,
      stype: "select",
      searchoptions: {
        value: "tous:Tous;utilisateur:Utilisateur;moderateur:Moderateur;administrateur:Administrateur"
      },
      editable: true,
      edittype: "select",
      formatter: "select",
      editoptions: {
        value: "utilisateur:Utilisateur;moderateur:Moderateur;administrateur:Administrateur"
      }
    }, {
      name: "email",
      index: "email",
      fixed: true,
      width: 200,
      editable: true
    }, {
      name: "club",
      index: "club",
      editable: true
    }, {
      name: "actif",
      index: "actif",
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
    pager: "#userspager",
    viewrecords: true,
    hidegrid: false,
    autowidth: true,
    height: "100%",
    sortname: "login",
    sortorder: "asc",
    onSelectRow: function(b) {
      if (b && b !== a) {
        jQuery("#usersgrid").jqGrid("restoreRow", a);
        jQuery("#usersgrid").jqGrid("editRow", b, true);
        a = b
      }
    },
    editurl: Global.baseUrl + "restadministrateur/saisie",
    caption: "Gestion des utilisateurs"
  });
  jQuery("#usersgrid").jqGrid("navGrid", "#userspager", {
    edit: false,
    add: true,
    del: false,
    search: false
  });
  jQuery("#usersgrid").jqGrid("filterToolbar", {
    stringResult: true,
    searchOnEnter: false
  })
});

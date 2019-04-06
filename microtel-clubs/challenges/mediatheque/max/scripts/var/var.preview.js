var Preview = {
  showPreview: function(b, e, k, f) {
    var a = jQuery("#preview" + b);
    var j = jQuery("<p />");
    var c = jQuery("<p />");
    var l = jQuery("<span />");
    var g = (f == 0) ? "elaboration" : "index";
    var d = Preview.creeImage(g, e);
    var m = Preview.creePoints(e);
    var h = Preview.creeDocuments(g, e.id, k);
    a.empty();
    j.append(d);
    l.append(m);
    l.append("<br />");
    l.append(h);
    j.append(l);
    a.append(j);
    c.css("clear", "both");
    c.append("<br />");
    c.append(e.texte);
    a.append(c)
  },
  showDocumentsQuestion: function(g, d, b) {
    var f = jQuery("#listeDocuments" + g);
    var e = (b == 0) ? "challenge/elaboration/fichier" : "challenge/index/fichier";
    f.empty();
    if (d.length) {
      var c = jQuery("<ul />");
      for (i in d) {
        var a = Preview.creeDocumentLi(e, g, d[i], "");
        c.append(a)
      }
      f.append(c)
    }
  },
  showDocumentsReponse: function(h, d, b, f) {
    if (f == undefined) {
      f = ""
    }
    var g = jQuery("#listeDocuments" + f + h);
    var e = "";
    if (f == "") {
      e = (b == 1) ? "challenge/elaboration/fichier" : "index/fichier"
    } else {
      e = "challenge/elaboration/fichierreponse"
    }
    g.empty();
    if (d.length) {
      var c = jQuery("<ul />");
      for (i in d) {
        var a = Preview.creeDocumentLi(e, h, d[i], "reponse");
        c.append(a)
      }
      g.append(c)
    }
  },
  showDocumentsReponseClub: function(h, d, b, f) {
    if (f == undefined) {
      f = ""
    }
    var g = jQuery("#listeDocuments" + f + h);
    var e = "";
    if (f == "") {
      e = (b == 1) ? "challenge/reponse/fichierreponse" : "index/fichier"
    } else {
      e = "challenge/reponse/fichierreponse"
    }
    g.empty();
    if (d.length) {
      var c = jQuery("<ul />");
      for (i in d) {
        var a = Preview.creeDocumentLi(e, h, d[i], "reponse");
        c.append(a)
      }
      g.append(c)
    }
  },
  creeImage: function(c, b) {
    var a = "";
    if (b.image != "") {
      a = jQuery("<img />");
      a.attr("id", "imgpreview" + b.id);
      a.attr("src", Global.baseUrl + "/challenge/" + c + "/image?id=" + b.id + "&t=" + new Date().getTime());
      a.attr("width", 300);
      a.css("float", "left")
    }
    return a
  },
  creePoints: function(c) {
    var d = "";
    if (c.information == "0") {
      d = jQuery("<span />");
      var e = jQuery("<p />");
      var a = jQuery("<b />");
      a.append("&nbsp;Valeur de la question");
      e.append(a);
      e.append(" : " + c.valeur);
      d.append(e)
    }
    return d
  },
  creeDocuments: function(f, h, e) {
    var g = "";
    if (e.length) {
      g = jQuery("<p >");
      g.append("&nbsp;");
      var c = jQuery("<b />");
      c.append("Document(s) : ");
      g.append(c);
      for (i in e) {
        var d = jQuery("<a />");
        d.attr("target", "_blank");
        d.attr("href", Global.baseUrl + "/challenge/" + f + "/fichier?id=" + h + "&nom=" + e[i] + "&t=" + new Date().getTime());
        d.append(e[i]);
        g.append(d);
        g.append("&nbsp;")
      }
    }
    return g
  },
  creeImageDelete: function(d, a, c) {
    var b = jQuery("<img />");
    b.attr("filename", a);
    b.attr("qid", d);
    b.attr("src", Global.baseUrl + "/mediatheque/images/close.png");
    b.attr("width", 16);
    b.attr("height", 16);
    b.addClass("deletefile" + c);
    return b
  },
  creeDocumentLi: function(e, g, c, f) {
    var b = jQuery("<li />");
    var d = jQuery("<a />");
    b.append(Preview.creeImageDelete(g, c, f));
    b.append("&nbsp;");
    d.attr("href", Global.baseUrl + "/" + e + "?" + jQuery.param({
      id: g,
      nom: c
    }));
    d.attr("target", "_blank");
    d.append(c);
    b.append(d);
    return b
  }
};

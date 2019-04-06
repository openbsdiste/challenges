var Translations = Translations || {};
var Global = {
  baseUrl: "",
  _editor: undefined,
  translate: function(a) {
    if (Translations[a]) {
      return Translations[a]
    } else {
      return a
    }
  },
  tr: function(a) {
    return this.translate(a)
  },
  showMessage: function(b, c, a) {
    if (a == undefined) {
      a = {
        Ok: function() {
          jQuery(this).remove()
        }
      }
    }
    var d = jQuery("<div>").append(this.translate(b));
    d.attr("title", this.translate(c || ""));
    d.dialog({
      modal: true,
      zIndex: 6000,
      width: "500",
      buttons: a,
      close: function() {
        jQuery(this).remove()
      }
    })
  },
  confirmActionDialog: function(b, d, a, c) {
    var e = jQuery("<div>").append(this.translate(b));
    e.attr("title", this.translate(d || ""));
    e.dialog({
      modal: true,
      zIndex: 6000,
      width: "500",
      buttons: [{
        text: "Oui",
        click: function() {
          if (typeof(a) == "function") {
            setTimeout(a, 50)
          }
          jQuery(this).remove()
        }
      }, {
        text: "Non",
        click: function() {
          if (typeof(c) == "function") {
            setTimeout(c, 50)
          }
          jQuery(this).remove()
        }
      }]
    })
  },
  showError: function(c, a, d) {
    var b = a || "erreur";
    if ((c != undefined) && (c.responseXML != undefined)) {
      b = jQuery.xml2json(c.responseXML).message || b
    } else {
      if ((c != undefined) && (c.responseText != undefined)) {
        b = c.responseText.message || b
      }
    }
    Global.showMessage(b, d)
  },
  getEditor: function() {
    if (this._editor == undefined) {
      this._editor = new nicEditor({
        iconsPath: "mediatheque/images/nicEditorIcons.gif",
        buttonList: ["save", "bold", "italic", "underline", "left", "center", "right", "justify", "fontFamily", "fontFormat", "link", "unlink", "forecolor", "bgcolor"]
      })
    }
    return this._editor
  },
  addEditorInstance: function(b) {
    var a = jQuery("#" + b);
    if (a) {
      a.width(a.parent().width());
      a.height(a.parent().height() - 28);
      a.css("overflow", "auto");
      a.css("background-color", "#ffffff");
      Global.getEditor().addInstance(b)
    }
  },
  setAccordion: function(b, a) {
    if (a == undefined) {
      a = 0
    }
    jQuery("#" + b).liteAccordion({
      theme: "light",
      rounded: true,
      firstSlide: a + 1,
      containerWidth: jQuery("#" + b).parent().parent().width() - 70,
      containerHeight: jQuery("#" + b).parent().parent().height() - 115,
      headerWidth: 48,
      enumerateSlides: true,
      easing: "easeInOutQuart"
    })
  },
  getTop: function(a) {
    var c = 0;
    var b = document.getElementById(a);
    while ((b.offsetParent != undefined) && (b.offsetParent != null)) {
      c += b.offsetTop;
      if (b.clientTop != null) {
        c += b.clientTop
      }
      b = b.offsetParent
    }
    return c
  },
  getLeft: function(a) {
    var c = 0;
    var b = document.getElementById(a);
    while ((b.offsetParent != undefined) && (b.offsetParent != null)) {
      c += b.offsetLeft;
      if (b.clientLeft != null) {
        c += b.clientLeft
      }
      b = b.offsetParent
    }
    return c
  },
  getYDistance: function(b, a) {
    var c = Global.getTop(b) - Global.getTop(a);
    if (c < 0) {
      c = -1 * c
    }
    return c
  },
  getXDistance: function(b, a) {
    var c = Global.getLeft(b) - Global.getLeft(a);
    if (c < 0) {
      c = -1 * c
    }
    return c
  },
  showSpinner: function() {
    jQuery("#spinner").show()
  },
  hideSpinner: function() {
    jQuery("#spinner").hide()
  },
  validPasteHtml: function(a, b) {
    var c = "";
    if (b && b.clipboardData && b.clipboardData.getData) {
      if (/text\/html/.test(b.clipboardData.types)) {
        c = HTMLtoXML(b.clipboardData.getData("text/html"))
      } else {
        if (/text\/plain/.test(b.clipboardData.types)) {
          c = b.clipboardData.getData("text/plain")
        } else {
          c = ""
        }
      }
    }
    document.execCommand("insertText", false, c);
    b.preventDefault()
  }
};
var delay = (function() {
  var a = 0;
  return function(c, b) {
    clearTimeout(a);
    a = setTimeout(c, b)
  }
})();
jQuery(document).ready(function() {
  jQuery("#spinner").bind("ajaxSend", function() {
    jQuery(this).show()
  }).bind("ajaxStop", function() {
    jQuery(this).hide()
  }).bind("ajaxError", function() {
    jQuery(this).hide()
  })
});

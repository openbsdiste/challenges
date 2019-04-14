var bkExtend = function() {
    var a = arguments;
    if (a.length == 1) {
        a = [this, a[0]]
    }
    for (var b in a[1]) {
        a[0][b] = a[1][b]
    }
    return a[0]
};

function bkClass() {}
bkClass.prototype.construct = function() {};
bkClass.extend = function(c) {
    var a = function() {
        if (arguments[0] !== bkClass) {
            return this.construct.apply(this, arguments)
        }
    };
    var b = new this(bkClass);
    bkExtend(b, c);
    a.prototype = b;
    a.extend = this.extend;
    return a
};
var bkElement = bkClass.extend({
    construct: function(b, a) {
        if (typeof(b) == "string") {
            b = (a || document).createElement(b)
        }
        b = $BK(b);
        return b
    },
    appendTo: function(a) {
        a.appendChild(this);
        return this
    },
    appendBefore: function(a) {
        a.parentNode.insertBefore(this, a);
        return this
    },
    addEvent: function(b, a) {
        bkLib.addEvent(this, b, a);
        return this
    },
    setContent: function(a) {
        this.innerHTML = a;
        return this
    },
    pos: function() {
        var d = curtop = 0;
        var c = obj = this;
        if (obj.offsetParent) {
            do {
                d += obj.offsetLeft;
                curtop += obj.offsetTop
            } while (obj = obj.offsetParent)
        }
        var a = (!window.opera) ? parseInt(this.getStyle("border-width") || this.style.border) || 0 : 0;
        return [d + a, curtop + a + this.offsetHeight]
    },
    noSelect: function() {
        bkLib.noSelect(this);
        return this
    },
    parentTag: function(a) {
        var b = this;
        do {
            if (b && b.nodeName && b.nodeName.toUpperCase() == a) {
                return b
            }
            b = b.parentNode
        } while (b);
        return false
    },
    hasClass: function(a) {
        return this.className.match(new RegExp("(\\s|^)nicEdit-" + a + "(\\s|$)"))
    },
    addClass: function(a) {
        if (!this.hasClass(a)) {
            this.className += " nicEdit-" + a
        }
        return this
    },
    removeClass: function(a) {
        if (this.hasClass(a)) {
            this.className = this.className.replace(new RegExp("(\\s|^)nicEdit-" + a + "(\\s|$)"), " ")
        }
        return this
    },
    setStyle: function(a) {
        var b = this.style;
        for (var c in a) {
            switch (c) {
                case "float":
                    b.cssFloat = b.styleFloat = a[c];
                    break;
                case "opacity":
                    b.opacity = a[c];
                    b.filter = "alpha(opacity=" + Math.round(a[c] * 100) + ")";
                    break;
                case "className":
                    this.className = a[c];
                    break;
                default:
                    b[c] = a[c]
            }
        }
        return this
    },
    getStyle: function(a, c) {
        var b = (!c) ? document.defaultView : c;
        if (this.nodeType == 1) {
            return (b && b.getComputedStyle) ? b.getComputedStyle(this, null).getPropertyValue(a) : this.currentStyle[bkLib.camelize(a)]
        }
    },
    remove: function() {
        this.parentNode.removeChild(this);
        return this
    },
    setAttributes: function(a) {
        for (var b in a) {
            this[b] = a[b]
        }
        return this
    }
});
var bkLib = {
    isMSIE: (navigator.appVersion.indexOf("MSIE") != -1),
    addEvent: function(c, b, a) {
        (c.addEventListener) ? c.addEventListener(b, a, false): c.attachEvent("on" + b, a)
    },
    toArray: function(c) {
        var b = c.length,
            a = new Array(b);
        while (b--) {
            a[b] = c[b]
        }
        return a
    },
    noSelect: function(b) {
        if (b.setAttribute && b.nodeName.toLowerCase() != "input" && b.nodeName.toLowerCase() != "textarea") {
            b.setAttribute("unselectable", "on")
        }
        for (var a = 0; a < b.childNodes.length; a++) {
            bkLib.noSelect(b.childNodes[a])
        }
    },
    camelize: function(a) {
        return a.replace(/\-(.)/g, function(b, c) {
            return c.toUpperCase()
        })
    },
    inArray: function(a, b) {
        return (bkLib.search(a, b) != null)
    },
    search: function(a, c) {
        for (var b = 0; b < a.length; b++) {
            if (a[b] == c) {
                return b
            }
        }
        return null
    },
    cancelEvent: function(a) {
        a = a || window.event;
        if (a.preventDefault && a.stopPropagation) {
            a.preventDefault();
            a.stopPropagation()
        }
        return false
    },
    domLoad: [],
    domLoaded: function() {
        if (arguments.callee.done) {
            return
        }
        arguments.callee.done = true;
        for (i = 0; i < bkLib.domLoad.length; i++) {
            bkLib.domLoad[i]()
        }
    },
    onDomLoaded: function(a) {
        this.domLoad.push(a);
        if (document.addEventListener) {
            document.addEventListener("DOMContentLoaded", bkLib.domLoaded, null)
        } else {
            if (bkLib.isMSIE) {
                document.write("<style>.nicEdit-main p { margin: 0; }</style><script id=__ie_onload defer " + ((location.protocol == "https:") ? "src='javascript:void(0)'" : "src=//0") + "><\/script>");
                $BK("__ie_onload").onreadystatechange = function() {
                    if (this.readyState == "complete") {
                        bkLib.domLoaded()
                    }
                }
            }
        }
        window.onload = bkLib.domLoaded
    }
};

function $BK(a) {
    if (typeof(a) == "string") {
        a = document.getElementById(a)
    }
    return (a && !a.appendTo) ? bkExtend(a, bkElement.prototype) : a
}
var bkEvent = {
    addEvent: function(a, b) {
        if (b) {
            this.eventList = this.eventList || {};
            this.eventList[a] = this.eventList[a] || [];
            this.eventList[a].push(b)
        }
        return this
    },
    fireEvent: function() {
        var a = bkLib.toArray(arguments),
            c = a.shift();
        if (this.eventList && this.eventList[c]) {
            for (var b = 0; b < this.eventList[c].length; b++) {
                this.eventList[c][b].apply(this, a)
            }
        }
    }
};

function __(a) {
    return Global.tr(a)
}
Function.prototype.closure = function() {
    var a = this,
        b = bkLib.toArray(arguments),
        c = b.shift();
    return function() {
        if (typeof(bkLib) != "undefined") {
            return a.apply(c, b.concat(bkLib.toArray(arguments)))
        }
    }
};
Function.prototype.closureListener = function() {
    var a = this,
        c = bkLib.toArray(arguments),
        b = c.shift();
    return function(f) {
        f = f || window.event;
        if (f.target) {
            var d = f.target
        } else {
            var d = f.srcElement
        }
        return a.apply(b, [f, d].concat(c))
    }
};
var nicEditorConfig = bkClass.extend({
    buttons: {
        bold: {
            name: __("icone_gras"),
            command: "Bold",
            tags: ["B", "STRONG"],
            css: {
                "font-weight": "bold"
            },
            key: "b"
        },
        italic: {
            name: __("icone_italique"),
            command: "Italic",
            tags: ["EM", "I"],
            css: {
                "font-style": "italic"
            },
            key: "i"
        },
        underline: {
            name: __("icone_souligne"),
            command: "Underline",
            tags: ["U"],
            css: {
                "text-decoration": "underline"
            },
            key: "u"
        },
        left: {
            name: __("icone_gauche"),
            command: "justifyleft",
            noActive: true
        },
        center: {
            name: __("icone_centre"),
            command: "justifycenter",
            noActive: true
        },
        right: {
            name: __("icone_droit"),
            command: "justifyright",
            noActive: true
        },
        justify: {
            name: __("icone_justifie"),
            command: "justifyfull",
            noActive: true
        },
        ol: {
            name: __("icone_ol"),
            command: "insertorderedlist",
            tags: ["OL"]
        },
        ul: {
            name: __("icone_ul"),
            command: "insertunorderedlist",
            tags: ["UL"]
        },
        subscript: {
            name: __("icone_sub"),
            command: "subscript",
            tags: ["SUB"]
        },
        superscript: {
            name: __("icone_sup"),
            command: "superscript",
            tags: ["SUP"]
        },
        strikethrough: {
            name: __("icone_barre"),
            command: "strikeThrough",
            css: {
                "text-decoration": "line-through"
            }
        },
        removeformat: {
            name: __("icone_aucun"),
            command: "removeformat",
            noActive: true
        },
        indent: {
            name: __("icone_indente"),
            command: "indent",
            noActive: true
        },
        outdent: {
            name: __("icone_desindente"),
            command: "outdent",
            noActive: true
        },
        hr: {
            name: __("icone_regle"),
            command: "insertHorizontalRule",
            noActive: true
        }
    },
    iconsPath: "../nicEditorIcons.gif",
    buttonList: ["save", "bold", "italic", "underline", "left", "center", "right", "justify", "ol", "ul", "fontSize", "fontFamily", "fontFormat", "indent", "outdent", "image", "upload", "link", "unlink", "forecolor", "bgcolor"],
    iconList: {
        bgcolor: 1,
        forecolor: 2,
        bold: 3,
        center: 4,
        hr: 5,
        indent: 6,
        italic: 7,
        justify: 8,
        left: 9,
        ol: 10,
        outdent: 11,
        removeformat: 12,
        right: 13,
        save: 24,
        strikethrough: 15,
        subscript: 16,
        superscript: 17,
        ul: 18,
        underline: 19,
        image: 20,
        link: 21,
        unlink: 22,
        close: 23,
        arrow: 25,
        upload: 26
    }
});
var nicEditors = {
    nicPlugins: [],
    editors: [],
    registerPlugin: function(b, a) {
        this.nicPlugins.push({
            p: b,
            o: a
        })
    },
    allTextAreas: function(c) {
        var a = document.getElementsByTagName("textarea");
        for (var b = 0; b < a.length; b++) {
            nicEditors.editors.push(new nicEditor(c).panelInstance(a[b]))
        }
        return nicEditors.editors
    },
    findEditor: function(c) {
        var b = nicEditors.editors;
        for (var a = 0; a < b.length; a++) {
            if (b[a].instanceById(c)) {
                return b[a].instanceById(c)
            }
        }
    }
};
var nicEditor = bkClass.extend({
    construct: function(c) {
        this.options = new nicEditorConfig();
        bkExtend(this.options, c);
        this.nicInstances = new Array();
        this.loadedPlugins = new Array();
        var a = nicEditors.nicPlugins;
        for (var b = 0; b < a.length; b++) {
            this.loadedPlugins.push(new a[b].p(this, a[b].o))
        }
        nicEditors.editors.push(this);
        bkLib.addEvent(document.body, "mousedown", this.selectCheck.closureListener(this))
    },
    panelInstance: function(b, c) {
        b = this.checkReplace($BK(b));
        var a = new bkElement("DIV").setStyle({
            width: (parseInt(b.getStyle("width")) || b.clientWidth) + "px"
        }).appendBefore(b);
        this.setPanel(a);
        return this.addInstance(b, c)
    },
    checkReplace: function(b) {
        var a = nicEditors.findEditor(b);
        if (a) {
            a.removeInstance(b);
            a.removePanel()
        }
        return b
    },
    addInstance: function(b, c) {
        b = this.checkReplace($BK(b));
        if (b.contentEditable || !!window.opera) {
            var a = new nicEditorInstance(b, c, this)
        } else {
            var a = new nicEditorIFrameInstance(b, c, this)
        }
        this.nicInstances.push(a);
        return this
    },
    removeInstance: function(c) {
        c = $BK(c);
        var b = this.nicInstances;
        for (var a = 0; a < b.length; a++) {
            if (b[a].e == c) {
                b[a].remove();
                this.nicInstances.splice(a, 1)
            }
        }
    },
    removePanel: function(a) {
        if (this.nicPanel) {
            this.nicPanel.remove();
            this.nicPanel = null
        }
    },
    instanceById: function(c) {
        c = $BK(c);
        var b = this.nicInstances;
        for (var a = 0; a < b.length; a++) {
            if (b[a].e == c) {
                return b[a]
            }
        }
    },
    setPanel: function(a) {
        this.nicPanel = new nicEditorPanel($BK(a), this.options, this);
        this.fireEvent("panel", this.nicPanel);
        return this
    },
    nicCommand: function(b, a) {
        if (this.selectedInstance) {
            this.selectedInstance.nicCommand(b, a)
        }
    },
    getIcon: function(d, a) {
        var c = this.options.iconList[d];
        var b = (a.iconFiles) ? a.iconFiles[d] : "";
        return {
            backgroundImage: "url('" + ((c) ? this.options.iconsPath : b) + "')",
            backgroundPosition: ((c) ? ((c - 1) * -18) : 0) + "px 0px"
        }
    },
    selectCheck: function(c, a) {
        var b = false;
        do {
            if (a.className && a.className.indexOf("nicEdit") != -1) {
                return false
            }
        } while (a = a.parentNode);
        this.fireEvent("blur", this.selectedInstance, a);
        this.lastSelectedInstance = this.selectedInstance;
        this.selectedInstance = null;
        return false
    }
});
nicEditor = nicEditor.extend(bkEvent);
var nicEditorInstance = bkClass.extend({
    isSelected: false,
    construct: function(j, d, c) {
        this.ne = c;
        this.elm = this.e = j;
        this.options = d || {};
        newX = parseInt(j.getStyle("width")) || j.clientWidth;
        newY = parseInt(j.getStyle("height")) || j.clientHeight;
        this.initialHeight = newY - 8;
        var k = (j.nodeName.toLowerCase() == "textarea");
        if (k || this.options.hasPanel) {
            var b = (bkLib.isMSIE && !((typeof document.body.style.maxHeight != "undefined") && document.compatMode == "CSS1Compat"));
            var g = {
                width: newX + "px",
                border: "1px solid #ccc",
                borderTop: 0,
                overflowY: "auto",
                overflowX: "hidden"
            };
            g[(b) ? "height" : "maxHeight"] = (this.ne.options.maxHeight) ? this.ne.options.maxHeight + "px" : null;
            this.editorContain = new bkElement("DIV").setStyle(g).appendBefore(j);
            var a = new bkElement("DIV").setStyle({
                width: (newX - 8) + "px",
                margin: "4px",
                minHeight: newY + "px"
            }).addClass("main").appendTo(this.editorContain);
            j.setStyle({
                display: "none"
            });
            a.innerHTML = j.innerHTML;
            if (k) {
                a.setContent(j.value);
                this.copyElm = j;
                var h = j.parentTag("FORM");
                if (h) {
                    bkLib.addEvent(h, "submit", this.saveContent.closure(this))
                }
            }
            a.setStyle((b) ? {
                height: newY + "px"
            } : {
                overflow: "hidden"
            });
            this.elm = a
        }
        this.ne.addEvent("blur", this.blur.closure(this));
        this.init();
        this.blur()
    },
    init: function() {
        this.elm.setAttribute("contentEditable", "true");
        if (this.getContent() == "") {
            this.setContent("<br />")
        }
        this.instanceDoc = document.defaultView;
        this.elm.addEvent("mousedown", this.selected.closureListener(this)).addEvent("keypress", this.keyDown.closureListener(this)).addEvent("focus", this.selected.closure(this)).addEvent("blur", this.blur.closure(this)).addEvent("keyup", this.selected.closure(this));
        this.ne.fireEvent("add", this);
        this.ne.addEvent("paste", this.initPasteClean.closureListener(this))
    },
    initPasteClean: function() {
        this.pasteCache = this.getElm().innerHTML;
        setTimeout(this.pasteClean.closure(this), 100)
    },
    pasteClean: function() {
        var d = "";
        var b = "";
        var g = this.getElm().innerHTML;
        this.ne.fireEvent("get", this);
        var f = 0;
        var j = 0;
        var a = "";
        var c = document.createElement("div");
        for (f = 0; g.charAt(f) == this.pasteCache.charAt(f); f++) {
            d += this.pasteCache.charAt(f)
        }
        for (var e = f; e >= 0; e--) {
            if (this.pasteCache.charAt(e) == "<") {
                f = e;
                d = this.pasteCache.substring(0, f);
                break
            } else {
                if (this.pasteCache.charAt(e) == ">") {
                    break
                }
            }
        }
        g = this.reverse(g);
        this.pasteCache = this.reverse(this.pasteCache);
        for (j = 0; g.charAt(j) == this.pasteCache.charAt(j); j++) {
            b += this.pasteCache.charAt(j)
        }
        for (var e = j; e >= 0; e--) {
            if (this.pasteCache.charAt(e) == ">") {
                j = e;
                b = this.pasteCache.substring(0, j);
                break
            } else {
                if (this.pasteCache.charAt(e) == "<") {
                    break
                }
            }
        }
        b = this.reverse(b);
        if (f == g.length - j) {
            return false
        }
        g = this.reverse(g);
        a = g.substring(f, g.length - j);
        a = this.validTags(a);
        a = a.replace(/<b(\s+|>)/g, "<strong$1");
        a = a.replace(/<\/b(\s+|>)/g, "</strong$1");
        a = a.replace(/<i(\s+|>)/g, "<em$1");
        a = a.replace(/<\/i(\s+|>)/g, "</em$1");
        a = a.replace(/<!(?:--[\s\S]*?--\s*)?>\s*/g, "");
        a = a.replace(/&nbsp;/gi, " ");
        a = a.replace(/ <\//gi, "</");
        while (a.indexOf("  ") != -1) {
            var h = a.split("  ");
            a = h.join(" ")
        }
        a = a.replace(/^\s*|\s*$/g, "");
        a = a.replace(/<[^>]*>/g, function(k) {
            k = k.replace(/ ([^=]+)="[^"]*"/g, function(m, l) {
                if (l == "alt" || l == "href" || l == "src" || l == "title") {
                    return m
                }
                return ""
            });
            return k
        });
        a = a.replace(/<\?xml[^>]*>/g, "");
        a = a.replace(/<[^ >]+:[^>]*>/g, "");
        a = a.replace(/<\/[^ >]+:[^>]*>/g, "");
        a = a.replace(/<(div|span|style|meta|link){1}.*?>/gi, "");
        this.content = d + a + b;
        this.ne.fireEvent("set", this);
        this.elm.innerHTML = this.content
    },
    reverse: function(c) {
        var a = "";
        for (var b = c.length - 1; b >= 0; b--) {
            a += c.charAt(b)
        }
        return a
    },
    validTags: function(b) {
        var a = b;
        a = a.replace(/<[^> ]*/g, function(c) {
            return c.toLowerCase()
        });
        a = a.replace(/<[^>]*>/g, function(c) {
            c = c.replace(/ [^=]+=/g, function(d) {
                return d.toLowerCase()
            });
            return c
        });
        a = a.replace(/<[^>]*>/g, function(c) {
            c = c.replace(/( [^=]+=)([^"][^ >]*)/g, '$1"$2"');
            return c
        });
        return a
    },
    remove: function() {
        this.saveContent();
        if (this.copyElm || this.options.hasPanel) {
            this.editorContain.remove();
            this.e.setStyle({
                display: "block"
            });
            this.ne.removePanel()
        }
        this.disable();
        this.ne.fireEvent("remove", this)
    },
    disable: function() {
        this.elm.setAttribute("contentEditable", "false")
    },
    getSel: function() {
        return (window.getSelection) ? window.getSelection() : document.selection
    },
    getRng: function() {
        var a = this.getSel();
        if (!a || a.rangeCount === 0) {
            return
        }
        return (a.rangeCount > 0) ? a.getRangeAt(0) : a.createRange()
    },
    selRng: function(a, b) {
        if (window.getSelection) {
            b.removeAllRanges();
            b.addRange(a)
        } else {
            a.select()
        }
    },
    selElm: function() {
        var c = this.getRng();
        if (!c) {
            return
        }
        if (c.startContainer) {
            var d = c.startContainer;
            if (c.cloneContents().childNodes.length == 1) {
                for (var b = 0; b < d.childNodes.length; b++) {
                    var a = d.childNodes[b].ownerDocument.createRange();
                    a.selectNode(d.childNodes[b]);
                    if (c.compareBoundaryPoints(Range.START_TO_START, a) != 1 && c.compareBoundaryPoints(Range.END_TO_END, a) != -1) {
                        return $BK(d.childNodes[b])
                    }
                }
            }
            return $BK(d)
        } else {
            return $BK((this.getSel().type == "Control") ? c.item(0) : c.parentElement())
        }
    },
    saveRng: function() {
        this.savedRange = this.getRng();
        this.savedSel = this.getSel()
    },
    restoreRng: function() {
        if (this.savedRange) {
            this.selRng(this.savedRange, this.savedSel)
        }
    },
    keyDown: function(b, a) {
        if (b.ctrlKey) {
            this.ne.fireEvent("key", this, b)
        }
    },
    selected: function(c, a) {
        if (!a && !(a = this.selElm)) {
            a = this.selElm()
        }
        if (!c.ctrlKey) {
            var b = this.ne.selectedInstance;
            if (b != this) {
                if (b) {
                    this.ne.fireEvent("blur", b, a)
                }
                this.ne.selectedInstance = this;
                this.ne.fireEvent("focus", b, a)
            }
            this.ne.fireEvent("selected", b, a);
            this.isFocused = true;
            this.elm.addClass("selected")
        }
        return false
    },
    blur: function() {
        this.isFocused = false;
        this.elm.removeClass("selected")
    },
    saveContent: function() {
        if (this.copyElm || this.options.hasPanel) {
            this.ne.fireEvent("save", this);
            (this.copyElm) ? this.copyElm.value = this.getContent(): this.e.innerHTML = this.getContent()
        }
    },
    getElm: function() {
        return this.elm
    },
    getContent: function() {
        this.content = this.getElm().innerHTML;
        this.ne.fireEvent("get", this);
        return this.content
    },
    setContent: function(a) {
        this.content = a;
        this.ne.fireEvent("set", this);
        this.elm.innerHTML = this.content
    },
    nicCommand: function(b, a) {
        document.execCommand(b, false, a)
    }
});
var nicEditorIFrameInstance = nicEditorInstance.extend({
    savedStyles: [],
    init: function() {
        var b = this.elm.innerHTML.replace(/^\s+|\s+$/g, "");
        this.elm.innerHTML = "";
        (!b) ? b = "<br />": b;
        this.initialContent = b;
        this.elmFrame = new bkElement("iframe").setAttributes({
            src: "javascript:;",
            frameBorder: 0,
            allowTransparency: "true",
            scrolling: "no"
        }).setStyle({
            height: "100px",
            width: "100%"
        }).addClass("frame").appendTo(this.elm);
        if (this.copyElm) {
            this.elmFrame.setStyle({
                width: (this.elm.offsetWidth - 4) + "px"
            })
        }
        var a = ["font-size", "font-family", "font-weight", "color"];
        for (itm in a) {
            this.savedStyles[bkLib.camelize(itm)] = this.elm.getStyle(itm)
        }
        setTimeout(this.initFrame.closure(this), 50)
    },
    disable: function() {
        this.elm.innerHTML = this.getContent()
    },
    initFrame: function() {
        var b = $BK(this.elmFrame.contentWindow.document);
        b.designMode = "on";
        b.open();
        var a = this.ne.options.externalCSS;
        b.write("<html><head>" + ((a) ? '<link href="' + a + '" rel="stylesheet" type="text/css" />' : "") + '</head><body id="nicEditContent" style="margin: 0 !important; background-color: transparent !important;">' + this.initialContent + "</body></html>");
        b.close();
        this.frameDoc = b;
        this.frameWin = $BK(this.elmFrame.contentWindow);
        this.frameContent = $BK(this.frameWin.document.body).setStyle(this.savedStyles);
        this.instanceDoc = this.frameWin.document.defaultView;
        this.heightUpdate();
        this.frameDoc.addEvent("mousedown", this.selected.closureListener(this)).addEvent("keyup", this.heightUpdate.closureListener(this)).addEvent("keydown", this.keyDown.closureListener(this)).addEvent("keyup", this.selected.closure(this));
        this.ne.fireEvent("add", this)
    },
    getElm: function() {
        return this.frameContent
    },
    setContent: function(a) {
        this.content = a;
        this.ne.fireEvent("set", this);
        this.frameContent.innerHTML = this.content;
        this.heightUpdate()
    },
    getSel: function() {
        return (this.frameWin) ? this.frameWin.getSelection() : this.frameDoc.selection
    },
    heightUpdate: function() {
        this.elmFrame.style.height = Math.max(this.frameContent.offsetHeight, this.initialHeight) + "px"
    },
    nicCommand: function(b, a) {
        this.frameDoc.execCommand(b, false, a);
        setTimeout(this.heightUpdate.closure(this), 100)
    }
});
var nicEditorPanel = bkClass.extend({
    construct: function(f, b, a) {
        this.elm = f;
        this.options = b;
        this.ne = a;
        this.panelButtons = new Array();
        this.buttonList = bkExtend([], this.ne.options.buttonList);
        this.panelContain = new bkElement("DIV").setStyle({
            overflow: "hidden"
        }).addClass("panelContain");
        this.panelElm = new bkElement("DIV").setStyle({
            margin: "2px",
            marginTop: "0px",
            zoom: 1,
            overflow: "hidden"
        }).addClass("panel").appendTo(this.panelContain);
        this.panelContain.appendTo(f);
        var c = this.ne.options;
        var d = c.buttons;
        for (button in d) {
            this.addButton(button, c, true)
        }
        this.reorder();
        f.noSelect()
    },
    addButton: function(buttonName, options, noOrder) {
        var button = options.buttons[buttonName];
        var type = (button.type) ? eval("(typeof(" + button.type + ') == "undefined") ? null : ' + button.type + ";") : nicEditorButton;
        var hasButton = bkLib.inArray(this.buttonList, buttonName);
        if (type && (hasButton || this.ne.options.fullPanel)) {
            this.panelButtons.push(new type(this.panelElm, buttonName, options, this.ne));
            if (!hasButton) {
                this.buttonList.push(buttonName)
            }
        }
    },
    findButton: function(b) {
        for (var a = 0; a < this.panelButtons.length; a++) {
            if (this.panelButtons[a].name == b) {
                return this.panelButtons[a]
            }
        }
    },
    reorder: function() {
        var c = this.buttonList;
        for (var b = 0; b < c.length; b++) {
            var a = this.findButton(c[b]);
            if (a) {
                this.panelElm.appendChild(a.margin)
            }
        }
    },
    remove: function() {
        this.elm.remove()
    }
});
var nicEditorButton = bkClass.extend({
    construct: function(d, a, c, b) {
        this.options = c.buttons[a];
        this.name = a;
        this.ne = b;
        this.elm = d;
        this.margin = new bkElement("DIV").setStyle({
            "float": "left",
            marginTop: "2px"
        }).appendTo(d);
        this.contain = new bkElement("DIV").setStyle({
            width: "20px",
            height: "20px"
        }).addClass("buttonContain").appendTo(this.margin);
        this.border = new bkElement("DIV").setStyle({
            backgroundColor: "#efefef",
            border: "1px solid #efefef"
        }).appendTo(this.contain);
        this.button = new bkElement("DIV").setStyle({
            width: "18px",
            height: "18px",
            overflow: "hidden",
            zoom: 1,
            cursor: "pointer"
        }).addClass("button").setStyle(this.ne.getIcon(a, c)).appendTo(this.border);
        this.button.addEvent("mouseover", this.hoverOn.closure(this)).addEvent("mouseout", this.hoverOff.closure(this)).addEvent("mousedown", this.mouseClick.closure(this)).noSelect();
        if (!window.opera) {
            this.button.onmousedown = this.button.onclick = bkLib.cancelEvent
        }
        b.addEvent("selected", this.enable.closure(this)).addEvent("blur", this.disable.closure(this)).addEvent("key", this.key.closure(this));
        this.disable();
        this.init()
    },
    init: function() {},
    hide: function() {
        this.contain.setStyle({
            display: "none"
        })
    },
    updateState: function() {
        if (this.isDisabled) {
            this.setBg()
        } else {
            if (this.isHover) {
                this.setBg("hover")
            } else {
                if (this.isActive) {
                    this.setBg("active")
                } else {
                    this.setBg()
                }
            }
        }
    },
    setBg: function(a) {
        switch (a) {
            case "hover":
                var b = {
                    border: "1px solid #666",
                    backgroundColor: "#ddd"
                };
                break;
            case "active":
                var b = {
                    border: "1px solid #666",
                    backgroundColor: "#ccc"
                };
                break;
            default:
                var b = {
                    border: "1px solid #efefef",
                    backgroundColor: "#efefef"
                }
        }
        this.border.setStyle(b).addClass("button-" + a)
    },
    checkNodes: function(a) {
        var b = a;
        do {
            if (this.options.tags && bkLib.inArray(this.options.tags, b.nodeName)) {
                this.activate();
                return true
            }
        } while (b = b.parentNode && b.className != "nicEdit");
        b = $BK(a);
        while (b.nodeType == 3) {
            b = $BK(b.parentNode)
        }
        if (this.options.css) {
            for (itm in this.options.css) {
                if (b.getStyle(itm, this.ne.selectedInstance.instanceDoc) == this.options.css[itm]) {
                    this.activate();
                    return true
                }
            }
        }
        this.deactivate();
        return false
    },
    activate: function() {
        if (!this.isDisabled) {
            this.isActive = true;
            this.updateState();
            this.ne.fireEvent("buttonActivate", this)
        }
    },
    deactivate: function() {
        this.isActive = false;
        this.updateState();
        if (!this.isDisabled) {
            this.ne.fireEvent("buttonDeactivate", this)
        }
    },
    enable: function(a, b) {
        this.isDisabled = false;
        this.contain.setStyle({
            opacity: 1
        }).addClass("buttonEnabled");
        this.updateState();
        this.checkNodes(b)
    },
    disable: function(a, b) {
        this.isDisabled = true;
        this.contain.setStyle({
            opacity: 0.6
        }).removeClass("buttonEnabled");
        this.updateState()
    },
    toggleActive: function() {
        (this.isActive) ? this.deactivate(): this.activate()
    },
    hoverOn: function() {
        if (!this.isDisabled) {
            this.isHover = true;
            this.updateState();
            this.ne.fireEvent("buttonOver", this)
        }
    },
    hoverOff: function() {
        this.isHover = false;
        this.updateState();
        this.ne.fireEvent("buttonOut", this)
    },
    mouseClick: function() {
        if (this.options.command) {
            this.ne.nicCommand(this.options.command, this.options.commandArgs);
            if (!this.options.noActive) {
                this.toggleActive()
            }
        }
        this.ne.fireEvent("buttonClick", this)
    },
    key: function(a, b) {
        if (this.options.key && b.ctrlKey && String.fromCharCode(b.keyCode || b.charCode).toLowerCase() == this.options.key) {
            this.mouseClick();
            if (b.preventDefault) {
                b.preventDefault()
            }
        }
    }
});
var nicPlugin = bkClass.extend({
    construct: function(b, a) {
        this.options = a;
        this.ne = b;
        this.ne.addEvent("panel", this.loadPanel.closure(this));
        this.init()
    },
    loadPanel: function(c) {
        var b = this.options.buttons;
        for (var a in b) {
            c.addButton(a, this.options)
        }
        c.reorder()
    },
    init: function() {}
});
var nicPaneOptions = {};
var nicEditorPane = bkClass.extend({
    construct: function(d, c, b, a) {
        this.ne = c;
        this.elm = d;
        this.pos = d.pos();
        this.contain = new bkElement("div").setStyle({
            zIndex: "99999",
            overflow: "hidden",
            position: "absolute",
            left: this.pos[0] + "px",
            top: this.pos[1] + "px"
        });
        this.pane = new bkElement("div").setStyle({
            fontSize: "12px",
            border: "1px solid #ccc",
            overflow: "hidden",
            padding: "4px",
            textAlign: "left",
            backgroundColor: "#ffffc9"
        }).addClass("pane").setStyle(b).appendTo(this.contain);
        if (a && !a.options.noClose) {
            this.close = new bkElement("div").setStyle({
                "float": "right",
                height: "16px",
                width: "16px",
                cursor: "pointer"
            }).setStyle(this.ne.getIcon("close", nicPaneOptions)).addEvent("mousedown", a.removePane.closure(this)).appendTo(this.pane)
        }
        this.contain.noSelect().appendTo(document.body);
        this.position();
        this.init()
    },
    init: function() {},
    position: function() {
        if (this.ne.nicPanel) {
            var b = this.ne.nicPanel.elm;
            var a = b.pos();
            var c = a[0] + parseInt(b.getStyle("width")) - (parseInt(this.pane.getStyle("width")) + 8);
            if (c < this.pos[0]) {
                this.contain.setStyle({
                    left: c + "px"
                })
            }
        }
    },
    toggle: function() {
        this.isVisible = !this.isVisible;
        this.contain.setStyle({
            display: ((this.isVisible) ? "block" : "none")
        })
    },
    remove: function() {
        if (this.contain) {
            this.contain.remove();
            this.contain = null
        }
    },
    append: function(a) {
        a.appendTo(this.pane)
    },
    setContent: function(a) {
        this.pane.setContent(a)
    }
});
var nicEditorAdvancedButton = nicEditorButton.extend({
    init: function() {
        this.ne.addEvent("selected", this.removePane.closure(this)).addEvent("blur", this.removePane.closure(this))
    },
    mouseClick: function() {
        if (!this.isDisabled) {
            if (this.pane && this.pane.pane) {
                this.removePane()
            } else {
                this.pane = new nicEditorPane(this.contain, this.ne, {
                    width: (this.width || "270px"),
                    backgroundColor: "#fff"
                }, this);
                this.addPane();
                this.ne.selectedInstance.saveRng()
            }
        }
    },
    addForm: function(c, h) {
        this.form = new bkElement("form").addEvent("submit", this.submit.closureListener(this));
        this.pane.append(this.form);
        this.inputs = {};
        for (itm in c) {
            var d = c[itm];
            var g = "";
            if (h) {
                g = h.getAttribute(itm)
            }
            if (!g) {
                g = d.value || ""
            }
            var a = c[itm].type;
            if (a == "title") {
                new bkElement("div").setContent(d.txt).setStyle({
                    fontSize: "14px",
                    fontWeight: "bold",
                    padding: "0px",
                    margin: "2px 0"
                }).appendTo(this.form)
            } else {
                var b = new bkElement("div").setStyle({
                    overflow: "hidden",
                    clear: "both"
                }).appendTo(this.form);
                if (d.txt) {
                    new bkElement("label").setAttributes({
                        "for": itm
                    }).setContent(d.txt).setStyle({
                        margin: "2px 4px",
                        fontSize: "13px",
                        width: "50px",
                        lineHeight: "20px",
                        textAlign: "right",
                        "float": "left"
                    }).appendTo(b)
                }
                switch (a) {
                    case "text":
                        this.inputs[itm] = new bkElement("input").setAttributes({
                            id: itm,
                            value: g,
                            type: "text"
                        }).setStyle({
                            margin: "2px 0",
                            fontSize: "13px",
                            "float": "left",
                            height: "20px",
                            border: "1px solid #ccc",
                            overflow: "hidden"
                        }).setStyle(d.style).appendTo(b);
                        break;
                    case "select":
                        this.inputs[itm] = new bkElement("select").setAttributes({
                            id: itm
                        }).setStyle({
                            border: "1px solid #ccc",
                            "float": "left",
                            margin: "2px 0"
                        }).appendTo(b);
                        for (opt in d.options) {
                            var e = new bkElement("option").setAttributes({
                                value: opt,
                                selected: (opt == g) ? "selected" : ""
                            }).setContent(d.options[opt]).appendTo(this.inputs[itm])
                        }
                        break;
                    case "content":
                        this.inputs[itm] = new bkElement("textarea").setAttributes({
                            id: itm
                        }).setStyle({
                            border: "1px solid #ccc",
                            "float": "left"
                        }).setStyle(d.style).appendTo(b);
                        this.inputs[itm].value = g
                }
            }
        }
        new bkElement("input").setAttributes({
            type: "submit"
        }).setStyle({
            backgroundColor: "#efefef",
            border: "1px solid #ccc",
            margin: "3px 0",
            "float": "left",
            clear: "both"
        }).appendTo(this.form);
        this.form.onsubmit = bkLib.cancelEvent
    },
    submit: function() {},
    findElm: function(b, a, e) {
        var d = this.ne.selectedInstance.getElm().getElementsByTagName(b);
        for (var c = 0; c < d.length; c++) {
            if (d[c].getAttribute(a) == e) {
                return $BK(d[c])
            }
        }
    },
    removePane: function() {
        if (this.pane) {
            this.pane.remove();
            this.pane = null;
            this.ne.selectedInstance.restoreRng()
        }
    }
});
var nicButtonTips = bkClass.extend({
    construct: function(a) {
        this.ne = a;
        a.addEvent("buttonOver", this.show.closure(this)).addEvent("buttonOut", this.hide.closure(this))
    },
    show: function(a) {
        this.timer = setTimeout(this.create.closure(this, a), 400)
    },
    create: function(a) {
        this.timer = null;
        if (!this.pane) {
            this.pane = new nicEditorPane(a.button, this.ne, {
                fontSize: "12px",
                marginTop: "5px"
            });
            this.pane.setContent(a.options.name)
        }
    },
    hide: function(a) {
        if (this.timer) {
            clearTimeout(this.timer)
        }
        if (this.pane) {
            this.pane = this.pane.remove()
        }
    }
});
nicEditors.registerPlugin(nicButtonTips);
var nicSelectOptions = {
    buttons: {
        fontSize: {
            name: __("taille_police"),
            type: "nicEditorFontSizeSelect",
            command: "fontsize"
        },
        fontFamily: {
            name: __("font_family"),
            type: "nicEditorFontFamilySelect",
            command: "fontname"
        },
        fontFormat: {
            name: __("format_police"),
            type: "nicEditorFontFormatSelect",
            command: "formatBlock"
        }
    }
};
var nicEditorSelect = bkClass.extend({
    construct: function(d, a, c, b) {
        this.options = c.buttons[a];
        this.elm = d;
        this.ne = b;
        this.name = a;
        this.selOptions = new Array();
        this.margin = new bkElement("div").setStyle({
            "float": "left",
            margin: "2px 1px 0 1px"
        }).appendTo(this.elm);
        this.contain = new bkElement("div").setStyle({
            width: "90px",
            height: "20px",
            cursor: "pointer",
            overflow: "hidden"
        }).addClass("selectContain").addEvent("click", this.toggle.closure(this)).appendTo(this.margin);
        this.items = new bkElement("div").setStyle({
            overflow: "hidden",
            zoom: 1,
            border: "1px solid #ccc",
            paddingLeft: "3px",
            backgroundColor: "#fff"
        }).appendTo(this.contain);
        this.control = new bkElement("div").setStyle({
            overflow: "hidden",
            "float": "right",
            height: "18px",
            width: "16px"
        }).addClass("selectControl").setStyle(this.ne.getIcon("arrow", c)).appendTo(this.items);
        this.txt = new bkElement("div").setStyle({
            overflow: "hidden",
            "float": "left",
            width: "66px",
            height: "14px",
            marginTop: "1px",
            fontFamily: "sans-serif",
            textAlign: "center",
            fontSize: "12px"
        }).addClass("selectTxt").appendTo(this.items);
        if (!window.opera) {
            this.contain.onmousedown = this.control.onmousedown = this.txt.onmousedown = bkLib.cancelEvent
        }
        this.margin.noSelect();
        this.ne.addEvent("selected", this.enable.closure(this)).addEvent("blur", this.disable.closure(this));
        this.disable();
        this.init()
    },
    disable: function() {
        this.isDisabled = true;
        this.close();
        this.contain.setStyle({
            opacity: 0.6
        })
    },
    enable: function(a) {
        this.isDisabled = false;
        this.close();
        this.contain.setStyle({
            opacity: 1
        })
    },
    setDisplay: function(a) {
        this.txt.setContent(a)
    },
    toggle: function() {
        if (!this.isDisabled) {
            (this.pane) ? this.close(): this.open()
        }
    },
    open: function() {
        this.pane = new nicEditorPane(this.items, this.ne, {
            width: "88px",
            padding: "0px",
            borderTop: 0,
            borderLeft: "1px solid #ccc",
            borderRight: "1px solid #ccc",
            borderBottom: "0px",
            backgroundColor: "#fff"
        });
        for (var c = 0; c < this.selOptions.length; c++) {
            var b = this.selOptions[c];
            var a = new bkElement("div").setStyle({
                overflow: "hidden",
                borderBottom: "1px solid #ccc",
                width: "88px",
                textAlign: "left",
                overflow: "hidden",
                cursor: "pointer"
            });
            var d = new bkElement("div").setStyle({
                padding: "0px 4px"
            }).setContent(b[1]).appendTo(a).noSelect();
            d.addEvent("click", this.update.closure(this, b[0])).addEvent("mouseover", this.over.closure(this, d)).addEvent("mouseout", this.out.closure(this, d)).setAttributes("id", b[0]);
            this.pane.append(a);
            if (!window.opera) {
                d.onmousedown = bkLib.cancelEvent
            }
        }
    },
    close: function() {
        if (this.pane) {
            this.pane = this.pane.remove()
        }
    },
    over: function(a) {
        a.setStyle({
            backgroundColor: "#ccc"
        })
    },
    out: function(a) {
        a.setStyle({
            backgroundColor: "#fff"
        })
    },
    add: function(b, a) {
        this.selOptions.push(new Array(b, a))
    },
    update: function(a) {
        this.ne.nicCommand(this.options.command, a);
        this.close()
    }
});
var nicEditorFontSizeSelect = nicEditorSelect.extend({
    sel: {
        1: "1&nbsp;(8pt)",
        2: "2&nbsp;(10pt)",
        3: "3&nbsp;(12pt)",
        4: "4&nbsp;(14pt)",
        5: "5&nbsp;(18pt)",
        6: "6&nbsp;(24pt)"
    },
    init: function() {
        this.setDisplay(__("taille_police"));
        for (itm in this.sel) {
            this.add(itm, '<font size="' + itm + '">' + this.sel[itm] + "</font>")
        }
    }
});
var nicEditorFontFamilySelect = nicEditorSelect.extend({
    sel: {
        arial: "Arial",
        "comic sans ms": "Comic Sans",
        "courier new": "Courier New",
        georgia: "Georgia",
        helvetica: "Helvetica",
        impact: "Impact",
        "times new roman": "Times",
        "trebuchet ms": "Trebuchet",
        verdana: "Verdana"
    },
    init: function() {
        this.setDisplay(__("font_family"));
        for (itm in this.sel) {
            this.add(itm, '<font face="' + itm + '">' + this.sel[itm] + "</font>")
        }
    }
});
var nicEditorFontFormatSelect = nicEditorSelect.extend({
    sel: {
        p: __("icone_paragraphe"),
        pre: "Pre",
        h6: __("icone_titre") + "&nbsp;5",
        h5: __("icone_titre") + "&nbsp;4",
        h4: __("icone_titre") + "&nbsp;3",
        h3: __("icone_titre") + "&nbsp;2",
        h2: __("icone_titre") + "&nbsp;1"
    },
    init: function() {
        this.setDisplay(__("format_police"));
        for (itm in this.sel) {
            var a = itm.toUpperCase();
            this.add("<" + a + ">", "<" + itm + ' style="padding: 0px; margin: 0px;">' + this.sel[itm] + "</" + a + ">")
        }
    }
});
nicEditors.registerPlugin(nicPlugin, nicSelectOptions);
var nicLinkOptions = {
    buttons: {
        link: {
            name: __("icone_addlink"),
            type: "nicLinkButton",
            tags: ["A"]
        },
        unlink: {
            name: __("icone_removelink"),
            command: "unlink",
            noActive: true
        }
    }
};
var nicLinkButton = nicEditorAdvancedButton.extend({
    addPane: function() {
        this.ln = this.ne.selectedInstance.selElm().parentTag("A");
        this.addForm({
            "": {
                type: "title",
                txt: __("addedit_link")
            },
            href: {
                type: "text",
                txt: "URL",
                value: "http://",
                style: {
                    width: "150px"
                }
            },
            title: {
                type: "text",
                txt: __("addedit_title")
            },
            target: {
                type: "select",
                txt: __("addedit_ouvrir"),
                options: {
                    "": __("addedit_courante"),
                    _blank: __("addedit_nouvelle")
                },
                style: {
                    width: "100px"
                }
            }
        }, this.ln)
    },
    submit: function(c) {
        var a = this.inputs.href.value;
        if (a == "http://" || a == "") {
            alert(__("err_url"));
            return false
        }
        this.removePane();
        if (!this.ln) {
            var b = "javascript:nicTemp();";
            this.ne.nicCommand("createlink", b);
            this.ln = this.findElm("A", "href", b)
        }
        if (this.ln) {
            this.ln.setAttributes({
                href: this.inputs.href.value,
                title: this.inputs.title.value,
                target: this.inputs.target.options[this.inputs.target.selectedIndex].value
            })
        }
    }
});
nicEditors.registerPlugin(nicPlugin, nicLinkOptions);
var nicColorOptions = {
    buttons: {
        forecolor: {
            name: __("icone_coultexte"),
            type: "nicEditorColorButton",
            noClose: true
        },
        bgcolor: {
            name: __("icone_coulfond"),
            type: "nicEditorBgColorButton",
            noClose: true
        }
    }
};
var nicEditorColorButton = nicEditorAdvancedButton.extend({
    addPane: function() {
        var e = {
            0: "00",
            1: "33",
            2: "66",
            3: "99",
            4: "CC",
            5: "FF"
        };
        var k = new bkElement("DIV").setStyle({
            width: "270px"
        });
        for (var a in e) {
            for (var h in e) {
                for (var f in e) {
                    var l = "#" + e[a] + e[f] + e[h];
                    var d = new bkElement("DIV").setStyle({
                        cursor: "pointer",
                        height: "15px",
                        "float": "left"
                    }).appendTo(k);
                    var j = new bkElement("DIV").setStyle({
                        border: "2px solid " + l
                    }).appendTo(d);
                    var c = new bkElement("DIV").setStyle({
                        backgroundColor: l,
                        overflow: "hidden",
                        width: "11px",
                        height: "11px"
                    }).addEvent("click", this.colorSelect.closure(this, l)).addEvent("mouseover", this.on.closure(this, j)).addEvent("mouseout", this.off.closure(this, j, l)).appendTo(j);
                    if (!window.opera) {
                        d.onmousedown = c.onmousedown = bkLib.cancelEvent
                    }
                }
            }
        }
        this.pane.append(k.noSelect())
    },
    colorSelect: function(a) {
        this.ne.nicCommand("foreColor", a);
        this.removePane()
    },
    on: function(a) {
        a.setStyle({
            border: "2px solid #000"
        })
    },
    off: function(a, b) {
        a.setStyle({
            border: "2px solid " + b
        })
    }
});
var nicEditorBgColorButton = nicEditorColorButton.extend({
    colorSelect: function(a) {
        this.ne.nicCommand("hiliteColor", a);
        this.removePane()
    }
});
nicEditors.registerPlugin(nicPlugin, nicColorOptions);
var nicImageOptions = {
    buttons: {
        image: {
            name: __("icone_addimage"),
            type: "nicImageButton",
            tags: ["IMG"]
        }
    }
};
var nicImageButton = nicEditorAdvancedButton.extend({
    addPane: function() {
        this.im = this.ne.selectedInstance.selElm().parentTag("IMG");
        this.addForm({
            "": {
                type: "title",
                txt: __("addedit_image")
            },
            src: {
                type: "text",
                txt: "URL",
                value: "http://",
                style: {
                    width: "150px"
                }
            },
            alt: {
                type: "text",
                txt: __("addedit_alt"),
                style: {
                    width: "100px"
                }
            },
            align: {
                type: "select",
                txt: __("addedit_aligne"),
                options: {
                    none: __("defaut"),
                    left: __("gauche"),
                    right: __("droite")
                }
            }
        }, this.im)
    },
    submit: function(b) {
        var c = this.inputs.src.value;
        if (c == "" || c == "http://") {
            alert(__("err_image"));
            return false
        }
        this.removePane();
        if (!this.im) {
            var a = "javascript:nicImTemp();";
            this.ne.nicCommand("insertImage", a);
            this.im = this.findElm("IMG", "src", a)
        }
        if (this.im) {
            this.im.setAttributes({
                src: this.inputs.src.value,
                alt: this.inputs.alt.value,
                align: this.inputs.align.value
            })
        }
    }
});
nicEditors.registerPlugin(nicPlugin, nicImageOptions);
var nicSaveOptions = {
    buttons: {
        save: {
            name: __("sauve"),
            type: "nicEditorSaveButton"
        }
    }
};
var nicEditorSaveButton = nicEditorButton.extend({
    init: function() {
        if (!this.ne.options.onSave) {
            this.margin.setStyle({
                display: "none"
            })
        }
    },
    mouseClick: function() {
        var b = this.ne.options.onSave;
        var a = this.ne.selectedInstance;
        b(a.getContent(), a.elm.id, a)
    }
});
nicEditors.registerPlugin(nicPlugin, nicSaveOptions);
var nicUploadOptions = {
    buttons: {
        upload: {
            name: __("upload_image"),
            type: "nicUploadButton"
        }
    }
};
var nicUploadButton = nicEditorAdvancedButton.extend({
    nicURI: "http://api.imgur.com/2/upload.json",
    errorText: "Failed to upload image",
    addPane: function() {
        if (typeof window.FormData === "undefined") {
            return this.onError(__("image_noupload"))
        }
        this.im = this.ne.selectedInstance.selElm().parentTag("IMG");
        var a = new bkElement("div").setStyle({
            padding: "10px"
        }).appendTo(this.pane.pane);
        new bkElement("div").setStyle({
            fontSize: "14px",
            fontWeight: "bold",
            paddingBottom: "5px"
        }).setContent(__("insere_image")).appendTo(a);
        this.fileInput = new bkElement("input").setAttributes({
            type: "file"
        }).appendTo(a);
        this.progress = new bkElement("progress").setStyle({
            width: "100%",
            display: "none"
        }).setAttributes("max", 100).appendTo(a);
        this.fileInput.onchange = this.uploadFile.closure(this)
    },
    onError: function(a) {
        this.removePane();
        alert(a || __("err_uploadimage"))
    },
    uploadFile: function() {
        var b = this.fileInput.files[0];
        if (!b || !b.type.match(/image.*/)) {
            this.onError(__("err_imageseulement"));
            return
        }
        this.fileInput.setStyle({
            display: "none"
        });
        this.setProgress(0);
        var a = new FormData();
        a.append("image", b);
        a.append("key", "b7ea18a4ecbda8e92203fa4968d10660");
        var c = new XMLHttpRequest();
        c.open("POST", this.ne.options.uploadURI || this.nicURI);
        c.onload = function() {
            try {
                var d = JSON.parse(c.responseText)
            } catch (f) {
                return this.onError()
            }
            this.onUploaded(d.upload)
        }.closure(this);
        c.onerror = this.onError.closure(this);
        c.upload.onprogress = function(d) {
            this.setProgress(d.loaded / d.total)
        }.closure(this);
        c.send(a)
    },
    setProgress: function(a) {
        this.progress.setStyle({
            display: "block"
        });
        if (a < 0.98) {
            this.progress.value = a
        } else {
            this.progress.removeAttribute("value")
        }
    },
    onUploaded: function(b) {
        this.removePane();
        var d = b.links.original;
        if (!this.im) {
            this.ne.selectedInstance.restoreRng();
            var c = "javascript:nicImTemp();";
            this.ne.nicCommand("insertImage", d);
            this.im = this.findElm("IMG", "src", d)
        }
        var a = parseInt(this.ne.selectedInstance.elm.getStyle("width"));
        if (this.im) {
            this.im.setAttributes({
                src: d,
                width: (a && b.image.width) ? Math.min(a, b.image.width) : ""
            })
        }
    }
});
nicEditors.registerPlugin(nicPlugin, nicUploadOptions);
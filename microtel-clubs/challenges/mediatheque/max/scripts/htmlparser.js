(function() {
  var k = /^<([-A-Za-z0-9_]+)((?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,
    c = /^<\/([-A-Za-z0-9_]+)[^>]*>/,
    g = /([-A-Za-z0-9_]+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g;
  var f = b("area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed");
  var a = b("address,applet,blockquote,button,center,dd,del,dir,div,dl,dt,fieldset,form,frameset,hr,iframe,ins,isindex,li,map,menu,noframes,noscript,object,ol,p,pre,script,table,tbody,td,tfoot,th,thead,tr,ul");
  var i = b("a,abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var");
  var d = b("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr");
  var j = b("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected");
  var h = b("script,style");
  var e = this.HTMLParser = function(m, u) {
    var p, q, n, r = [],
      s = m;
    r.last = function() {
      return this[this.length - 1]
    };
    while (m) {
      q = true;
      if (!r.last() || !h[r.last()]) {
        if (m.indexOf("<!--") == 0) {
          p = m.indexOf("-->");
          if (p >= 0) {
            if (u.comment) {
              u.comment(m.substring(4, p))
            }
            m = m.substring(p + 3);
            q = false
          }
        } else {
          if (m.indexOf("</") == 0) {
            n = m.match(c);
            if (n) {
              m = m.substring(n[0].length);
              n[0].replace(c, o);
              q = false
            }
          } else {
            if (m.indexOf("<") == 0) {
              n = m.match(k);
              if (n) {
                m = m.substring(n[0].length);
                n[0].replace(k, l);
                q = false
              }
            }
          }
        }
        if (q) {
          p = m.indexOf("<");
          var t = p < 0 ? m : m.substring(0, p);
          m = p < 0 ? "" : m.substring(p);
          if (u.chars) {
            u.chars(t)
          }
        }
      } else {
        m = m.replace(new RegExp("(.*)</" + r.last() + "[^>]*>"), function(v, w) {
          w = w.replace(/<!--(.*?)-->/g, "$1").replace(/<!\[CDATA\[(.*?)]]>/g, "$1");
          if (u.chars) {
            u.chars(w)
          }
          return ""
        });
        o("", r.last())
      }
      if (m == s) {
        throw "Parse Error: " + m
      }
      s = m
    }
    o();

    function l(v, y, z, w) {
      y = y.toLowerCase();
      if (a[y]) {
        while (r.last() && i[r.last()]) {
          o("", r.last())
        }
      }
      if (d[y] && r.last() == y) {
        o("", y)
      }
      w = f[y] || !!w;
      if (!w) {
        r.push(y)
      }
      if (u.start) {
        var x = [];
        z.replace(g, function(B, A) {
          var C = arguments[2] ? arguments[2] : arguments[3] ? arguments[3] : arguments[4] ? arguments[4] : j[A] ? A : "";
          x.push({
            name: A,
            value: C,
            escaped: C.replace(/(^|[^\\])"/g, '$1\\"')
          })
        });
        if (u.start) {
          u.start(y, x, w)
        }
      }
    }

    function o(v, x) {
      if (!x) {
        var y = 0
      } else {
        for (var y = r.length - 1; y >= 0; y--) {
          if (r[y] == x) {
            break
          }
        }
      }
      if (y >= 0) {
        for (var w = r.length - 1; w >= y; w--) {
          if (u.end) {
            u.end(r[w])
          }
        }
        r.length = y
      }
    }
  };
  this.HTMLtoXML = function(m) {
    var l = "";
    e(m, {
      start: function(n, p, o) {
        l += "<" + n;
        for (var q = 0; q < p.length; q++) {
          l += " " + p[q].name + '="' + p[q].escaped + '"'
        }
        l += (o ? "/" : "") + ">"
      },
      end: function(n) {
        l += "</" + n + ">"
      },
      chars: function(n) {
        l += n
      },
      comment: function(n) {
        l += "<!--" + n + "-->"
      }
    });
    return l
  };
  this.HTMLtoDOM = function(p, s) {
    var o = b("html,head,body,title");
    var l = {
      link: "head",
      base: "head"
    };
    if (!s) {
      if (typeof DOMDocument != "undefined") {
        s = new DOMDocument()
      } else {
        if (typeof document != "undefined" && document.implementation && document.implementation.createDocument) {
          s = document.implementation.createDocument("", "", null)
        } else {
          if (typeof ActiveX != "undefined") {
            s = new ActiveXObject("Msxml.DOMDocument")
          }
        }
      }
    } else {
      s = s.ownerDocument || s.getOwnerDocument && s.getOwnerDocument() || s
    }
    var m = [],
      r = s.documentElement || s.getDocumentElement && s.getDocumentElement();
    if (!r && s.createElement) {
      (function() {
        var u = s.createElement("html");
        var t = s.createElement("head");
        t.appendChild(s.createElement("title"));
        u.appendChild(t);
        u.appendChild(s.createElement("body"));
        s.appendChild(u)
      })()
    }
    if (s.getElementsByTagName) {
      for (var n in o) {
        o[n] = s.getElementsByTagName(n)[0]
      }
    }
    var q = o.body;
    e(p, {
      start: function(w, v, u) {
        if (o[w]) {
          q = o[w];
          if (!u) {
            m.push(q)
          }
          return
        }
        var x = s.createElement(w);
        for (var t in v) {
          x.setAttribute(v[t].name, v[t].value)
        }
        if (l[w] && typeof o[l[w]] != "boolean") {
          o[l[w]].appendChild(x)
        } else {
          if (q && q.appendChild) {
            q.appendChild(x)
          }
        }
        if (!u) {
          m.push(x);
          q = x
        }
      },
      end: function(t) {
        m.length -= 1;
        q = m[m.length - 1]
      },
      chars: function(t) {
        q.appendChild(s.createTextNode(t))
      },
      comment: function(t) {}
    });
    return s
  };

  function b(o) {
    var n = {},
      l = o.split(",");
    for (var m = 0; m < l.length; m++) {
      n[l[m]] = true
    }
    return n
  }
})();

var bkExtend=function(){var d=arguments;if(d.length==1){d=[this,d[0]]}for(var c in d[1]){d[0][c]=d[1][c]}return d[0]};function bkClass(){}bkClass.prototype.construct=function(){};bkClass.extend=function(f){var e=function(){if(arguments[0]!==bkClass){return this.construct.apply(this,arguments)}};var d=new this(bkClass);bkExtend(d,f);e.prototype=d;e.extend=this.extend;return e};var bkElement=bkClass.extend({construct:function(c,d){if(typeof(c)=="string"){c=(d||document).createElement(c)}c=$BK(c);return c},appendTo:function(b){b.appendChild(this);return this},appendBefore:function(b){b.parentNode.insertBefore(this,b);return this},addEvent:function(c,d){bkLib.addEvent(this,c,d);return this},setContent:function(b){this.innerHTML=b;return this},pos:function(){var e=curtop=0;var f=obj=this;if(obj.offsetParent){do{e+=obj.offsetLeft;curtop+=obj.offsetTop}while(obj=obj.offsetParent)}var b=(!window.opera)?parseInt(this.getStyle("border-width")||this.style.border)||0:0;return[e+b,curtop+b+this.offsetHeight]},noSelect:function(){bkLib.noSelect(this);return this},parentTag:function(d){var c=this;do{if(c&&c.nodeName&&c.nodeName.toUpperCase()==d){return c}c=c.parentNode}while(c);return false},hasClass:function(b){return this.className.match(new RegExp("(\\s|^)nicEdit-"+b+"(\\s|$)"))},addClass:function(b){if(!this.hasClass(b)){this.className+=" nicEdit-"+b}return this},removeClass:function(b){if(this.hasClass(b)){this.className=this.className.replace(new RegExp("(\\s|^)nicEdit-"+b+"(\\s|$)")," ")}return this},setStyle:function(e){var d=this.style;for(var f in e){switch(f){case"float":d.cssFloat=d.styleFloat=e[f];break;case"opacity":d.opacity=e[f];d.filter="alpha(opacity="+Math.round(e[f]*100)+")";break;case"className":this.className=e[f];break;default:d[f]=e[f]}}return this},getStyle:function(e,f){var d=(!f)?document.defaultView:f;if(this.nodeType==1){return(d&&d.getComputedStyle)?d.getComputedStyle(this,null).getPropertyValue(e):this.currentStyle[bkLib.camelize(e)]}},remove:function(){this.parentNode.removeChild(this);return this},setAttributes:function(d){for(var c in d){this[c]=d[c]}return this}});var bkLib={isMSIE:(navigator.appVersion.indexOf("MSIE")!=-1),addEvent:function(f,d,e){(f.addEventListener)?f.addEventListener(d,e,false):f.attachEvent("on"+d,e)},toArray:function(f){var d=f.length,e=new Array(d);while(d--){e[d]=f[d]}return e},noSelect:function(c){if(c.setAttribute&&c.nodeName.toLowerCase()!="input"&&c.nodeName.toLowerCase()!="textarea"){c.setAttribute("unselectable","on")}for(var d=0;d<c.childNodes.length;d++){bkLib.noSelect(c.childNodes[d])}},camelize:function(b){return b.replace(/\-(.)/g,function(a,d){return d.toUpperCase()})},inArray:function(d,c){return(bkLib.search(d,c)!=null)},search:function(e,f){for(var d=0;d<e.length;d++){if(e[d]==f){return d}}return null},cancelEvent:function(b){b=b||window.event;if(b.preventDefault&&b.stopPropagation){b.preventDefault();b.stopPropagation()}return false},domLoad:[],domLoaded:function(){if(arguments.callee.done){return}arguments.callee.done=true;for(i=0;i<bkLib.domLoad.length;i++){bkLib.domLoad[i]()}},onDomLoaded:function(b){this.domLoad.push(b);if(document.addEventListener){document.addEventListener("DOMContentLoaded",bkLib.domLoaded,null)}else{if(bkLib.isMSIE){document.write("<style>.nicEdit-main p { margin: 0; }</style><script id=__ie_onload defer "+((location.protocol=="https:")?"src='javascript:void(0)'":"src=//0")+"><\/script>");$BK("__ie_onload").onreadystatechange=function(){if(this.readyState=="complete"){bkLib.domLoaded()}}}}window.onload=bkLib.domLoaded}};function $BK(b){if(typeof(b)=="string"){b=document.getElementById(b)}return(b&&!b.appendTo)?bkExtend(b,bkElement.prototype):b}var bkEvent={addEvent:function(d,c){if(c){this.eventList=this.eventList||{};this.eventList[d]=this.eventList[d]||[];this.eventList[d].push(c)}return this},fireEvent:function(){var e=bkLib.toArray(arguments),f=e.shift();if(this.eventList&&this.eventList[f]){for(var d=0;d<this.eventList[f].length;d++){this.eventList[f][d].apply(this,e)}}}};function __(b){return Global.tr(b)}Function.prototype.closure=function(){var e=this,d=bkLib.toArray(arguments),f=d.shift();return function(){if(typeof(bkLib)!="undefined"){return e.apply(f,d.concat(bkLib.toArray(arguments)))}}};Function.prototype.closureListener=function(){var e=this,f=bkLib.toArray(arguments),d=f.shift();return function(a){a=a||window.event;if(a.target){var b=a.target}else{var b=a.srcElement}return e.apply(d,[a,b].concat(f))}};var nicEditorConfig=bkClass.extend({buttons:{bold:{name:__("icone_gras"),command:"Bold",tags:["B","STRONG"],css:{"font-weight":"bold"},key:"b"},italic:{name:__("icone_italique"),command:"Italic",tags:["EM","I"],css:{"font-style":"italic"},key:"i"},underline:{name:__("icone_souligne"),command:"Underline",tags:["U"],css:{"text-decoration":"underline"},key:"u"},left:{name:__("icone_gauche"),command:"justifyleft",noActive:true},center:{name:__("icone_centre"),command:"justifycenter",noActive:true},right:{name:__("icone_droit"),command:"justifyright",noActive:true},justify:{name:__("icone_justifie"),command:"justifyfull",noActive:true},ol:{name:__("icone_ol"),command:"insertorderedlist",tags:["OL"]},ul:{name:__("icone_ul"),command:"insertunorderedlist",tags:["UL"]},subscript:{name:__("icone_sub"),command:"subscript",tags:["SUB"]},superscript:{name:__("icone_sup"),command:"superscript",tags:["SUP"]},strikethrough:{name:__("icone_barre"),command:"strikeThrough",css:{"text-decoration":"line-through"}},removeformat:{name:__("icone_aucun"),command:"removeformat",noActive:true},indent:{name:__("icone_indente"),command:"indent",noActive:true},outdent:{name:__("icone_desindente"),command:"outdent",noActive:true},hr:{name:__("icone_regle"),command:"insertHorizontalRule",noActive:true}},iconsPath:"../nicEditorIcons.gif",buttonList:["save","bold","italic","underline","left","center","right","justify","ol","ul","fontSize","fontFamily","fontFormat","indent","outdent","image","upload","link","unlink","forecolor","bgcolor"],iconList:{bgcolor:1,forecolor:2,bold:3,center:4,hr:5,indent:6,italic:7,justify:8,left:9,ol:10,outdent:11,removeformat:12,right:13,save:24,strikethrough:15,subscript:16,superscript:17,ul:18,underline:19,image:20,link:21,unlink:22,close:23,arrow:25,upload:26}});var nicEditors={nicPlugins:[],editors:[],registerPlugin:function(c,d){this.nicPlugins.push({p:c,o:d})},allTextAreas:function(f){var e=document.getElementsByTagName("textarea");for(var d=0;d<e.length;d++){nicEditors.editors.push(new nicEditor(f).panelInstance(e[d]))}return nicEditors.editors},findEditor:function(f){var d=nicEditors.editors;for(var e=0;e<d.length;e++){if(d[e].instanceById(f)){return d[e].instanceById(f)}}}};var nicEditor=bkClass.extend({construct:function(f){this.options=new nicEditorConfig();bkExtend(this.options,f);this.nicInstances=new Array();this.loadedPlugins=new Array();var e=nicEditors.nicPlugins;for(var d=0;d<e.length;d++){this.loadedPlugins.push(new e[d].p(this,e[d].o))}nicEditors.editors.push(this);bkLib.addEvent(document.body,"mousedown",this.selectCheck.closureListener(this))},panelInstance:function(d,f){d=this.checkReplace($BK(d));var e=new bkElement("DIV").setStyle({width:(parseInt(d.getStyle("width"))||d.clientWidth)+"px"}).appendBefore(d);this.setPanel(e);return this.addInstance(d,f)},checkReplace:function(c){var d=nicEditors.findEditor(c);if(d){d.removeInstance(c);d.removePanel()}return c},addInstance:function(d,f){d=this.checkReplace($BK(d));if(d.contentEditable||!!window.opera){var e=new nicEditorInstance(d,f,this)}else{var e=new nicEditorIFrameInstance(d,f,this)}this.nicInstances.push(e);return this},removeInstance:function(f){f=$BK(f);var d=this.nicInstances;for(var e=0;e<d.length;e++){if(d[e].e==f){d[e].remove();this.nicInstances.splice(e,1)}}},removePanel:function(b){if(this.nicPanel){this.nicPanel.remove();this.nicPanel=null}},instanceById:function(f){f=$BK(f);var d=this.nicInstances;for(var e=0;e<d.length;e++){if(d[e].e==f){return d[e]}}},setPanel:function(b){this.nicPanel=new nicEditorPanel($BK(b),this.options,this);this.fireEvent("panel",this.nicPanel);return this},nicCommand:function(c,d){if(this.selectedInstance){this.selectedInstance.nicCommand(c,d)}},getIcon:function(g,f){var h=this.options.iconList[g];var e=(f.iconFiles)?f.iconFiles[g]:"";return{backgroundImage:"url('"+((h)?this.options.iconsPath:e)+"')",backgroundPosition:((h)?((h-1)*-18):0)+"px 0px"}},selectCheck:function(f,e){var d=false;do{if(e.className&&e.className.indexOf("nicEdit")!=-1){return false}}while(e=e.parentNode);this.fireEvent("blur",this.selectedInstance,e);this.lastSelectedInstance=this.selectedInstance;this.selectedInstance=null;return false}});nicEditor=nicEditor.extend(bkEvent);var nicEditorInstance=bkClass.extend({isSelected:false,construct:function(m,p,q){this.ne=q;this.elm=this.e=m;this.options=p||{};newX=parseInt(m.getStyle("width"))||m.clientWidth;newY=parseInt(m.getStyle("height"))||m.clientHeight;this.initialHeight=newY-8;var l=(m.nodeName.toLowerCase()=="textarea");if(l||this.options.hasPanel){var e=(bkLib.isMSIE&&!((typeof document.body.style.maxHeight!="undefined")&&document.compatMode=="CSS1Compat"));var o={width:newX+"px",border:"1px solid #ccc",borderTop:0,overflowY:"auto",overflowX:"hidden"};o[(e)?"height":"maxHeight"]=(this.ne.options.maxHeight)?this.ne.options.maxHeight+"px":null;this.editorContain=new bkElement("DIV").setStyle(o).appendBefore(m);var f=new bkElement("DIV").setStyle({width:(newX-8)+"px",margin:"4px",minHeight:newY+"px"}).addClass("main").appendTo(this.editorContain);m.setStyle({display:"none"});f.innerHTML=m.innerHTML;if(l){f.setContent(m.value);this.copyElm=m;var n=m.parentTag("FORM");if(n){bkLib.addEvent(n,"submit",this.saveContent.closure(this))}}f.setStyle((e)?{height:newY+"px"}:{overflow:"hidden"});this.elm=f}this.ne.addEvent("blur",this.blur.closure(this));this.init();this.blur()},init:function(){this.elm.setAttribute("contentEditable","true");if(this.getContent()==""){this.setContent("<br />")}this.instanceDoc=document.defaultView;this.elm.addEvent("mousedown",this.selected.closureListener(this)).addEvent("keypress",this.keyDown.closureListener(this)).addEvent("focus",this.selected.closure(this)).addEvent("blur",this.blur.closure(this)).addEvent("keyup",this.selected.closure(this));this.ne.fireEvent("add",this);this.ne.addEvent("paste",this.initPasteClean.closureListener(this))},initPasteClean:function(){this.pasteCache=this.getElm().innerHTML;setTimeout(this.pasteClean.closure(this),100)},pasteClean:function(){var p="";var r="";var m=this.getElm().innerHTML;this.ne.fireEvent("get",this);var n=0;var k=0;var s="";var q=document.createElement("div");for(n=0;m.charAt(n)==this.pasteCache.charAt(n);n++){p+=this.pasteCache.charAt(n)}for(var o=n;o>=0;o--){if(this.pasteCache.charAt(o)=="<"){n=o;p=this.pasteCache.substring(0,n);break}else{if(this.pasteCache.charAt(o)==">"){break}}}m=this.reverse(m);this.pasteCache=this.reverse(this.pasteCache);for(k=0;m.charAt(k)==this.pasteCache.charAt(k);k++){r+=this.pasteCache.charAt(k)}for(var o=k;o>=0;o--){if(this.pasteCache.charAt(o)==">"){k=o;r=this.pasteCache.substring(0,k);break}else{if(this.pasteCache.charAt(o)=="<"){break}}}r=this.reverse(r);if(n==m.length-k){return false}m=this.reverse(m);s=m.substring(n,m.length-k);s=this.validTags(s);s=s.replace(/<b(\s+|>)/g,"<strong$1");s=s.replace(/<\/b(\s+|>)/g,"</strong$1");s=s.replace(/<i(\s+|>)/g,"<em$1");s=s.replace(/<\/i(\s+|>)/g,"</em$1");s=s.replace(/<!(?:--[\s\S]*?--\s*)?>\s*/g,"");s=s.replace(/&nbsp;/gi," ");s=s.replace(/ <\//gi,"</");while(s.indexOf("  ")!=-1){var l=s.split("  ");s=l.join(" ")}s=s.replace(/^\s*|\s*$/g,"");s=s.replace(/<[^>]*>/g,function(a){a=a.replace(/ ([^=]+)="[^"]*"/g,function(b,c){if(c=="alt"||c=="href"||c=="src"||c=="title"){return b}return""});return a});s=s.replace(/<\?xml[^>]*>/g,"");s=s.replace(/<[^ >]+:[^>]*>/g,"");s=s.replace(/<\/[^ >]+:[^>]*>/g,"");s=s.replace(/<(div|span|style|meta|link){1}.*?>/gi,"");this.content=p+s+r;this.ne.fireEvent("set",this);this.elm.innerHTML=this.content},reverse:function(f){var e="";for(var d=f.length-1;d>=0;d--){e+=f.charAt(d)}return e},validTags:function(c){var d=c;d=d.replace(/<[^> ]*/g,function(a){return a.toLowerCase()});d=d.replace(/<[^>]*>/g,function(a){a=a.replace(/ [^=]+=/g,function(b){return b.toLowerCase()});return a});d=d.replace(/<[^>]*>/g,function(a){a=a.replace(/( [^=]+=)([^"][^ >]*)/g,'$1"$2"');return a});return d},remove:function(){this.saveContent();if(this.copyElm||this.options.hasPanel){this.editorContain.remove();this.e.setStyle({display:"block"});this.ne.removePanel()}this.disable();this.ne.fireEvent("remove",this)},disable:function(){this.elm.setAttribute("contentEditable","false")},getSel:function(){return(window.getSelection)?window.getSelection():document.selection},getRng:function(){var b=this.getSel();if(!b||b.rangeCount===0){return}return(b.rangeCount>0)?b.getRangeAt(0):b.createRange()},selRng:function(d,c){if(window.getSelection){c.removeAllRanges();c.addRange(d)}else{d.select()}},selElm:function(){var h=this.getRng();if(!h){return}if(h.startContainer){var g=h.startContainer;if(h.cloneContents().childNodes.length==1){for(var e=0;e<g.childNodes.length;e++){var f=g.childNodes[e].ownerDocument.createRange();f.selectNode(g.childNodes[e]);if(h.compareBoundaryPoints(Range.START_TO_START,f)!=1&&h.compareBoundaryPoints(Range.END_TO_END,f)!=-1){return $BK(g.childNodes[e])}}}return $BK(g)}else{return $BK((this.getSel().type=="Control")?h.item(0):h.parentElement())}},saveRng:function(){this.savedRange=this.getRng();this.savedSel=this.getSel()},restoreRng:function(){if(this.savedRange){this.selRng(this.savedRange,this.savedSel)}},keyDown:function(c,d){if(c.ctrlKey){this.ne.fireEvent("key",this,c)}},selected:function(f,e){if(!e&&!(e=this.selElm)){e=this.selElm()}if(!f.ctrlKey){var d=this.ne.selectedInstance;if(d!=this){if(d){this.ne.fireEvent("blur",d,e)}this.ne.selectedInstance=this;this.ne.fireEvent("focus",d,e)}this.ne.fireEvent("selected",d,e);this.isFocused=true;this.elm.addClass("selected")}return false},blur:function(){this.isFocused=false;this.elm.removeClass("selected")},saveContent:function(){if(this.copyElm||this.options.hasPanel){this.ne.fireEvent("save",this);(this.copyElm)?this.copyElm.value=this.getContent():this.e.innerHTML=this.getContent()}},getElm:function(){return this.elm},getContent:function(){this.content=this.getElm().innerHTML;this.ne.fireEvent("get",this);return this.content},setContent:function(b){this.content=b;this.ne.fireEvent("set",this);this.elm.innerHTML=this.content},nicCommand:function(c,d){document.execCommand(c,false,d)}});var nicEditorIFrameInstance=nicEditorInstance.extend({savedStyles:[],init:function(){var c=this.elm.innerHTML.replace(/^\s+|\s+$/g,"");this.elm.innerHTML="";(!c)?c="<br />":c;this.initialContent=c;this.elmFrame=new bkElement("iframe").setAttributes({src:"javascript:;",frameBorder:0,allowTransparency:"true",scrolling:"no"}).setStyle({height:"100px",width:"100%"}).addClass("frame").appendTo(this.elm);if(this.copyElm){this.elmFrame.setStyle({width:(this.elm.offsetWidth-4)+"px"})}var d=["font-size","font-family","font-weight","color"];for(itm in d){this.savedStyles[bkLib.camelize(itm)]=this.elm.getStyle(itm)}setTimeout(this.initFrame.closure(this),50)},disable:function(){this.elm.innerHTML=this.getContent()},initFrame:function(){var c=$BK(this.elmFrame.contentWindow.document);c.designMode="on";c.open();var d=this.ne.options.externalCSS;c.write("<html><head>"+((d)?'<link href="'+d+'" rel="stylesheet" type="text/css" />':"")+'</head><body id="nicEditContent" style="margin: 0 !important; background-color: transparent !important;">'+this.initialContent+"</body></html>");c.close();this.frameDoc=c;this.frameWin=$BK(this.elmFrame.contentWindow);this.frameContent=$BK(this.frameWin.document.body).setStyle(this.savedStyles);this.instanceDoc=this.frameWin.document.defaultView;this.heightUpdate();this.frameDoc.addEvent("mousedown",this.selected.closureListener(this)).addEvent("keyup",this.heightUpdate.closureListener(this)).addEvent("keydown",this.keyDown.closureListener(this)).addEvent("keyup",this.selected.closure(this));this.ne.fireEvent("add",this)},getElm:function(){return this.frameContent},setContent:function(b){this.content=b;this.ne.fireEvent("set",this);this.frameContent.innerHTML=this.content;this.heightUpdate()},getSel:function(){return(this.frameWin)?this.frameWin.getSelection():this.frameDoc.selection},heightUpdate:function(){this.elmFrame.style.height=Math.max(this.frameContent.offsetHeight,this.initialHeight)+"px"},nicCommand:function(c,d){this.frameDoc.execCommand(c,false,d);setTimeout(this.heightUpdate.closure(this),100)}});var nicEditorPanel=bkClass.extend({construct:function(h,e,g){this.elm=h;this.options=e;this.ne=g;this.panelButtons=new Array();this.buttonList=bkExtend([],this.ne.options.buttonList);this.panelContain=new bkElement("DIV").setStyle({overflow:"hidden"}).addClass("panelContain");this.panelElm=new bkElement("DIV").setStyle({margin:"2px",marginTop:"0px",zoom:1,overflow:"hidden"}).addClass("panel").appendTo(this.panelContain);this.panelContain.appendTo(h);var k=this.ne.options;var j=k.buttons;for(button in j){this.addButton(button,k,true)}this.reorder();h.noSelect()},addButton:function(buttonName,options,noOrder){var button=options.buttons[buttonName];var type=(button.type)?eval("(typeof("+button.type+') == "undefined") ? null : '+button.type+";"):nicEditorButton;var hasButton=bkLib.inArray(this.buttonList,buttonName);if(type&&(hasButton||this.ne.options.fullPanel)){this.panelButtons.push(new type(this.panelElm,buttonName,options,this.ne));if(!hasButton){this.buttonList.push(buttonName)}}},findButton:function(c){for(var d=0;d<this.panelButtons.length;d++){if(this.panelButtons[d].name==c){return this.panelButtons[d]}}},reorder:function(){var f=this.buttonList;for(var d=0;d<f.length;d++){var e=this.findButton(f[d]);if(e){this.panelElm.appendChild(e.margin)}}},remove:function(){this.elm.remove()}});var nicEditorButton=bkClass.extend({construct:function(g,f,h,e){this.options=h.buttons[f];this.name=f;this.ne=e;this.elm=g;this.margin=new bkElement("DIV").setStyle({"float":"left",marginTop:"2px"}).appendTo(g);this.contain=new bkElement("DIV").setStyle({width:"20px",height:"20px"}).addClass("buttonContain").appendTo(this.margin);this.border=new bkElement("DIV").setStyle({backgroundColor:"#efefef",border:"1px solid #efefef"}).appendTo(this.contain);this.button=new bkElement("DIV").setStyle({width:"18px",height:"18px",overflow:"hidden",zoom:1,cursor:"pointer"}).addClass("button").setStyle(this.ne.getIcon(f,h)).appendTo(this.border);this.button.addEvent("mouseover",this.hoverOn.closure(this)).addEvent("mouseout",this.hoverOff.closure(this)).addEvent("mousedown",this.mouseClick.closure(this)).noSelect();if(!window.opera){this.button.onmousedown=this.button.onclick=bkLib.cancelEvent}e.addEvent("selected",this.enable.closure(this)).addEvent("blur",this.disable.closure(this)).addEvent("key",this.key.closure(this));this.disable();this.init()},init:function(){},hide:function(){this.contain.setStyle({display:"none"})},updateState:function(){if(this.isDisabled){this.setBg()}else{if(this.isHover){this.setBg("hover")}else{if(this.isActive){this.setBg("active")}else{this.setBg()}}}},setBg:function(d){switch(d){case"hover":var c={border:"1px solid #666",backgroundColor:"#ddd"};break;case"active":var c={border:"1px solid #666",backgroundColor:"#ccc"};break;default:var c={border:"1px solid #efefef",backgroundColor:"#efefef"}}this.border.setStyle(c).addClass("button-"+d)},checkNodes:function(d){var c=d;do{if(this.options.tags&&bkLib.inArray(this.options.tags,c.nodeName)){this.activate();return true}}while(c=c.parentNode&&c.className!="nicEdit");c=$BK(d);while(c.nodeType==3){c=$BK(c.parentNode)}if(this.options.css){for(itm in this.options.css){if(c.getStyle(itm,this.ne.selectedInstance.instanceDoc)==this.options.css[itm]){this.activate();return true}}}this.deactivate();return false},activate:function(){if(!this.isDisabled){this.isActive=true;this.updateState();this.ne.fireEvent("buttonActivate",this)}},deactivate:function(){this.isActive=false;this.updateState();if(!this.isDisabled){this.ne.fireEvent("buttonDeactivate",this)}},enable:function(d,c){this.isDisabled=false;this.contain.setStyle({opacity:1}).addClass("buttonEnabled");this.updateState();this.checkNodes(c)},disable:function(d,c){this.isDisabled=true;this.contain.setStyle({opacity:0.6}).removeClass("buttonEnabled");this.updateState()},toggleActive:function(){(this.isActive)?this.deactivate():this.activate()},hoverOn:function(){if(!this.isDisabled){this.isHover=true;this.updateState();this.ne.fireEvent("buttonOver",this)}},hoverOff:function(){this.isHover=false;this.updateState();this.ne.fireEvent("buttonOut",this)},mouseClick:function(){if(this.options.command){this.ne.nicCommand(this.options.command,this.options.commandArgs);if(!this.options.noActive){this.toggleActive()}}this.ne.fireEvent("buttonClick",this)},key:function(d,c){if(this.options.key&&c.ctrlKey&&String.fromCharCode(c.keyCode||c.charCode).toLowerCase()==this.options.key){this.mouseClick();if(c.preventDefault){c.preventDefault()}}}});var nicPlugin=bkClass.extend({construct:function(c,d){this.options=d;this.ne=c;this.ne.addEvent("panel",this.loadPanel.closure(this));this.init()},loadPanel:function(f){var d=this.options.buttons;for(var e in d){f.addButton(e,this.options)}f.reorder()},init:function(){}});var nicPaneOptions={};var nicEditorPane=bkClass.extend({construct:function(g,h,e,f){this.ne=h;this.elm=g;this.pos=g.pos();this.contain=new bkElement("div").setStyle({zIndex:"99999",overflow:"hidden",position:"absolute",left:this.pos[0]+"px",top:this.pos[1]+"px"});this.pane=new bkElement("div").setStyle({fontSize:"12px",border:"1px solid #ccc",overflow:"hidden",padding:"4px",textAlign:"left",backgroundColor:"#ffffc9"}).addClass("pane").setStyle(e).appendTo(this.contain);if(f&&!f.options.noClose){this.close=new bkElement("div").setStyle({"float":"right",height:"16px",width:"16px",cursor:"pointer"}).setStyle(this.ne.getIcon("close",nicPaneOptions)).addEvent("mousedown",f.removePane.closure(this)).appendTo(this.pane)}this.contain.noSelect().appendTo(document.body);this.position();this.init()},init:function(){},position:function(){if(this.ne.nicPanel){var d=this.ne.nicPanel.elm;var e=d.pos();var f=e[0]+parseInt(d.getStyle("width"))-(parseInt(this.pane.getStyle("width"))+8);if(f<this.pos[0]){this.contain.setStyle({left:f+"px"})}}},toggle:function(){this.isVisible=!this.isVisible;this.contain.setStyle({display:((this.isVisible)?"block":"none")})},remove:function(){if(this.contain){this.contain.remove();this.contain=null}},append:function(b){b.appendTo(this.pane)},setContent:function(b){this.pane.setContent(b)}});var nicEditorAdvancedButton=nicEditorButton.extend({init:function(){this.ne.addEvent("selected",this.removePane.closure(this)).addEvent("blur",this.removePane.closure(this))},mouseClick:function(){if(!this.isDisabled){if(this.pane&&this.pane.pane){this.removePane()}else{this.pane=new nicEditorPane(this.contain,this.ne,{width:(this.width||"270px"),backgroundColor:"#fff"},this);this.addPane();this.ne.selectedInstance.saveRng()}}},addForm:function(o,k){this.form=new bkElement("form").addEvent("submit",this.submit.closureListener(this));this.pane.append(this.form);this.inputs={};for(itm in o){var n=o[itm];var l="";if(k){l=k.getAttribute(itm)}if(!l){l=n.value||""}var j=o[itm].type;if(j=="title"){new bkElement("div").setContent(n.txt).setStyle({fontSize:"14px",fontWeight:"bold",padding:"0px",margin:"2px 0"}).appendTo(this.form)}else{var f=new bkElement("div").setStyle({overflow:"hidden",clear:"both"}).appendTo(this.form);if(n.txt){new bkElement("label").setAttributes({"for":itm}).setContent(n.txt).setStyle({margin:"2px 4px",fontSize:"13px",width:"50px",lineHeight:"20px",textAlign:"right","float":"left"}).appendTo(f)}switch(j){case"text":this.inputs[itm]=new bkElement("input").setAttributes({id:itm,value:l,type:"text"}).setStyle({margin:"2px 0",fontSize:"13px","float":"left",height:"20px",border:"1px solid #ccc",overflow:"hidden"}).setStyle(n.style).appendTo(f);break;case"select":this.inputs[itm]=new bkElement("select").setAttributes({id:itm}).setStyle({border:"1px solid #ccc","float":"left",margin:"2px 0"}).appendTo(f);for(opt in n.options){var m=new bkElement("option").setAttributes({value:opt,selected:(opt==l)?"selected":""}).setContent(n.options[opt]).appendTo(this.inputs[itm])}break;case"content":this.inputs[itm]=new bkElement("textarea").setAttributes({id:itm}).setStyle({border:"1px solid #ccc","float":"left"}).setStyle(n.style).appendTo(f);this.inputs[itm].value=l}}}new bkElement("input").setAttributes({type:"submit"}).setStyle({backgroundColor:"#efefef",border:"1px solid #ccc",margin:"3px 0","float":"left",clear:"both"}).appendTo(this.form);this.form.onsubmit=bkLib.cancelEvent},submit:function(){},findElm:function(f,g,h){var j=this.ne.selectedInstance.getElm().getElementsByTagName(f);for(var k=0;k<j.length;k++){if(j[k].getAttribute(g)==h){return $BK(j[k])}}},removePane:function(){if(this.pane){this.pane.remove();this.pane=null;this.ne.selectedInstance.restoreRng()}}});var nicButtonTips=bkClass.extend({construct:function(b){this.ne=b;b.addEvent("buttonOver",this.show.closure(this)).addEvent("buttonOut",this.hide.closure(this))},show:function(b){this.timer=setTimeout(this.create.closure(this,b),400)},create:function(b){this.timer=null;if(!this.pane){this.pane=new nicEditorPane(b.button,this.ne,{fontSize:"12px",marginTop:"5px"});this.pane.setContent(b.options.name)}},hide:function(b){if(this.timer){clearTimeout(this.timer)}if(this.pane){this.pane=this.pane.remove()}}});nicEditors.registerPlugin(nicButtonTips);var nicSelectOptions={buttons:{fontSize:{name:__("taille_police"),type:"nicEditorFontSizeSelect",command:"fontsize"},fontFamily:{name:__("font_family"),type:"nicEditorFontFamilySelect",command:"fontname"},fontFormat:{name:__("format_police"),type:"nicEditorFontFormatSelect",command:"formatBlock"}}};var nicEditorSelect=bkClass.extend({construct:function(g,f,h,e){this.options=h.buttons[f];this.elm=g;this.ne=e;this.name=f;this.selOptions=new Array();this.margin=new bkElement("div").setStyle({"float":"left",margin:"2px 1px 0 1px"}).appendTo(this.elm);this.contain=new bkElement("div").setStyle({width:"90px",height:"20px",cursor:"pointer",overflow:"hidden"}).addClass("selectContain").addEvent("click",this.toggle.closure(this)).appendTo(this.margin);this.items=new bkElement("div").setStyle({overflow:"hidden",zoom:1,border:"1px solid #ccc",paddingLeft:"3px",backgroundColor:"#fff"}).appendTo(this.contain);this.control=new bkElement("div").setStyle({overflow:"hidden","float":"right",height:"18px",width:"16px"}).addClass("selectControl").setStyle(this.ne.getIcon("arrow",h)).appendTo(this.items);this.txt=new bkElement("div").setStyle({overflow:"hidden","float":"left",width:"66px",height:"14px",marginTop:"1px",fontFamily:"sans-serif",textAlign:"center",fontSize:"12px"}).addClass("selectTxt").appendTo(this.items);if(!window.opera){this.contain.onmousedown=this.control.onmousedown=this.txt.onmousedown=bkLib.cancelEvent}this.margin.noSelect();this.ne.addEvent("selected",this.enable.closure(this)).addEvent("blur",this.disable.closure(this));this.disable();this.init()},disable:function(){this.isDisabled=true;this.close();this.contain.setStyle({opacity:0.6})},enable:function(b){this.isDisabled=false;this.close();this.contain.setStyle({opacity:1})},setDisplay:function(b){this.txt.setContent(b)},toggle:function(){if(!this.isDisabled){(this.pane)?this.close():this.open()}},open:function(){this.pane=new nicEditorPane(this.items,this.ne,{width:"88px",padding:"0px",borderTop:0,borderLeft:"1px solid #ccc",borderRight:"1px solid #ccc",borderBottom:"0px",backgroundColor:"#fff"});for(var h=0;h<this.selOptions.length;h++){var e=this.selOptions[h];var f=new bkElement("div").setStyle({overflow:"hidden",borderBottom:"1px solid #ccc",width:"88px",textAlign:"left",overflow:"hidden",cursor:"pointer"});var g=new bkElement("div").setStyle({padding:"0px 4px"}).setContent(e[1]).appendTo(f).noSelect();g.addEvent("click",this.update.closure(this,e[0])).addEvent("mouseover",this.over.closure(this,g)).addEvent("mouseout",this.out.closure(this,g)).setAttributes("id",e[0]);this.pane.append(f);if(!window.opera){g.onmousedown=bkLib.cancelEvent}}},close:function(){if(this.pane){this.pane=this.pane.remove()}},over:function(b){b.setStyle({backgroundColor:"#ccc"})},out:function(b){b.setStyle({backgroundColor:"#fff"})},add:function(c,d){this.selOptions.push(new Array(c,d))},update:function(b){this.ne.nicCommand(this.options.command,b);this.close()}});var nicEditorFontSizeSelect=nicEditorSelect.extend({sel:{1:"1&nbsp;(8pt)",2:"2&nbsp;(10pt)",3:"3&nbsp;(12pt)",4:"4&nbsp;(14pt)",5:"5&nbsp;(18pt)",6:"6&nbsp;(24pt)"},init:function(){this.setDisplay(__("taille_police"));for(itm in this.sel){this.add(itm,'<font size="'+itm+'">'+this.sel[itm]+"</font>")}}});var nicEditorFontFamilySelect=nicEditorSelect.extend({sel:{arial:"Arial","comic sans ms":"Comic Sans","courier new":"Courier New",georgia:"Georgia",helvetica:"Helvetica",impact:"Impact","times new roman":"Times","trebuchet ms":"Trebuchet",verdana:"Verdana"},init:function(){this.setDisplay(__("font_family"));for(itm in this.sel){this.add(itm,'<font face="'+itm+'">'+this.sel[itm]+"</font>")}}});var nicEditorFontFormatSelect=nicEditorSelect.extend({sel:{p:__("icone_paragraphe"),pre:"Pre",h6:__("icone_titre")+"&nbsp;5",h5:__("icone_titre")+"&nbsp;4",h4:__("icone_titre")+"&nbsp;3",h3:__("icone_titre")+"&nbsp;2",h2:__("icone_titre")+"&nbsp;1"},init:function(){this.setDisplay(__("format_police"));for(itm in this.sel){var b=itm.toUpperCase();this.add("<"+b+">","<"+itm+' style="padding: 0px; margin: 0px;">'+this.sel[itm]+"</"+b+">")}}});nicEditors.registerPlugin(nicPlugin,nicSelectOptions);var nicLinkOptions={buttons:{link:{name:__("icone_addlink"),type:"nicLinkButton",tags:["A"]},unlink:{name:__("icone_removelink"),command:"unlink",noActive:true}}};var nicLinkButton=nicEditorAdvancedButton.extend({addPane:function(){this.ln=this.ne.selectedInstance.selElm().parentTag("A");this.addForm({"":{type:"title",txt:__("addedit_link")},href:{type:"text",txt:"URL",value:"http://",style:{width:"150px"}},title:{type:"text",txt:__("addedit_title")},target:{type:"select",txt:__("addedit_ouvrir"),options:{"":__("addedit_courante"),_blank:__("addedit_nouvelle")},style:{width:"100px"}}},this.ln)},submit:function(f){var e=this.inputs.href.value;if(e=="http://"||e==""){alert(__("err_url"));return false}this.removePane();if(!this.ln){var d="javascript:nicTemp();";this.ne.nicCommand("createlink",d);this.ln=this.findElm("A","href",d)}if(this.ln){this.ln.setAttributes({href:this.inputs.href.value,title:this.inputs.title.value,target:this.inputs.target.options[this.inputs.target.selectedIndex].value})}}});nicEditors.registerPlugin(nicPlugin,nicLinkOptions);var nicColorOptions={buttons:{forecolor:{name:__("icone_coultexte"),type:"nicEditorColorButton",noClose:true},bgcolor:{name:__("icone_coulfond"),type:"nicEditorBgColorButton",noClose:true}}};var nicEditorColorButton=nicEditorAdvancedButton.extend({addPane:function(){var p={0:"00",1:"33",2:"66",3:"99",4:"CC",5:"FF"};var g=new bkElement("DIV").setStyle({width:"270px"});for(var s in p){for(var n in p){for(var o in p){var b="#"+p[s]+p[o]+p[n];var q=new bkElement("DIV").setStyle({cursor:"pointer",height:"15px","float":"left"}).appendTo(g);var m=new bkElement("DIV").setStyle({border:"2px solid "+b}).appendTo(q);var r=new bkElement("DIV").setStyle({backgroundColor:b,overflow:"hidden",width:"11px",height:"11px"}).addEvent("click",this.colorSelect.closure(this,b)).addEvent("mouseover",this.on.closure(this,m)).addEvent("mouseout",this.off.closure(this,m,b)).appendTo(m);if(!window.opera){q.onmousedown=r.onmousedown=bkLib.cancelEvent}}}}this.pane.append(g.noSelect())},colorSelect:function(b){this.ne.nicCommand("foreColor",b);this.removePane()},on:function(b){b.setStyle({border:"2px solid #000"})},off:function(d,c){d.setStyle({border:"2px solid "+c})}});var nicEditorBgColorButton=nicEditorColorButton.extend({colorSelect:function(b){this.ne.nicCommand("hiliteColor",b);this.removePane()}});nicEditors.registerPlugin(nicPlugin,nicColorOptions);var nicImageOptions={buttons:{image:{name:__("icone_addimage"),type:"nicImageButton",tags:["IMG"]}}};var nicImageButton=nicEditorAdvancedButton.extend({addPane:function(){this.im=this.ne.selectedInstance.selElm().parentTag("IMG");this.addForm({"":{type:"title",txt:__("addedit_image")},src:{type:"text",txt:"URL",value:"http://",style:{width:"150px"}},alt:{type:"text",txt:__("addedit_alt"),style:{width:"100px"}},align:{type:"select",txt:__("addedit_aligne"),options:{none:__("defaut"),left:__("gauche"),right:__("droite")}}},this.im)},submit:function(d){var f=this.inputs.src.value;if(f==""||f=="http://"){alert(__("err_image"));return false}this.removePane();if(!this.im){var e="javascript:nicImTemp();";this.ne.nicCommand("insertImage",e);this.im=this.findElm("IMG","src",e)}if(this.im){this.im.setAttributes({src:this.inputs.src.value,alt:this.inputs.alt.value,align:this.inputs.align.value})}}});nicEditors.registerPlugin(nicPlugin,nicImageOptions);var nicSaveOptions={buttons:{save:{name:__("sauve"),type:"nicEditorSaveButton"}}};var nicEditorSaveButton=nicEditorButton.extend({init:function(){if(!this.ne.options.onSave){this.margin.setStyle({display:"none"})}},mouseClick:function(){var c=this.ne.options.onSave;var d=this.ne.selectedInstance;c(d.getContent(),d.elm.id,d)}});nicEditors.registerPlugin(nicPlugin,nicSaveOptions);var nicUploadOptions={buttons:{upload:{name:__("upload_image"),type:"nicUploadButton"}}};var nicUploadButton=nicEditorAdvancedButton.extend({nicURI:"http://api.imgur.com/2/upload.json",errorText:"Failed to upload image",addPane:function(){if(typeof window.FormData==="undefined"){return this.onError(__("image_noupload"))}this.im=this.ne.selectedInstance.selElm().parentTag("IMG");var b=new bkElement("div").setStyle({padding:"10px"}).appendTo(this.pane.pane);new bkElement("div").setStyle({fontSize:"14px",fontWeight:"bold",paddingBottom:"5px"}).setContent(__("insere_image")).appendTo(b);this.fileInput=new bkElement("input").setAttributes({type:"file"}).appendTo(b);this.progress=new bkElement("progress").setStyle({width:"100%",display:"none"}).setAttributes("max",100).appendTo(b);this.fileInput.onchange=this.uploadFile.closure(this)},onError:function(b){this.removePane();alert(b||__("err_uploadimage"))},uploadFile:function(){var d=this.fileInput.files[0];if(!d||!d.type.match(/image.*/)){this.onError(__("err_imageseulement"));return}this.fileInput.setStyle({display:"none"});this.setProgress(0);var e=new FormData();e.append("image",d);e.append("key","b7ea18a4ecbda8e92203fa4968d10660");var f=new XMLHttpRequest();f.open("POST",this.ne.options.uploadURI||this.nicURI);f.onload=function(){try{var b=JSON.parse(f.responseText)}catch(a){return this.onError()}this.onUploaded(b.upload)}.closure(this);f.onerror=this.onError.closure(this);f.upload.onprogress=function(a){this.setProgress(a.loaded/a.total)}.closure(this);f.send(e)},setProgress:function(b){this.progress.setStyle({display:"block"});if(b<0.98){this.progress.value=b}else{this.progress.removeAttribute("value")}},onUploaded:function(e){this.removePane();var g=e.links.original;if(!this.im){this.ne.selectedInstance.restoreRng();var h="javascript:nicImTemp();";this.ne.nicCommand("insertImage",g);this.im=this.findElm("IMG","src",g)}var f=parseInt(this.ne.selectedInstance.elm.getStyle("width"));if(this.im){this.im.setAttributes({src:g,width:(f&&e.image.width)?Math.min(f,e.image.width):""})}}});nicEditors.registerPlugin(nicPlugin,nicUploadOptions);
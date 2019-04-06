var OutilsAccueil={initialize:function(){jQuery("#accordeonAccueil :button").bind("click",function(){Tabs.closeTab(this.id);Tabs.addTab(this.id,this.value,"challenge/notation/"+this.id,true)})}};var NotationManuelle={initialize:function(){jQuery("#NoterALaMain").bind("click",function(){var b=jQuery("#idclub").val();if(b==""){Global.showMessage("Vous devez sélectionner un club pour le noter !","notation impossible");jQuery("zoneNotation").empty()}else{jQuery.ajax({type:"POST",async:false,datatype:"json",url:"restnotation/manuelle",data:{format:"json",club:b},success:function(a){NotationManuelle.creeTableau(a.data)},error:function(a){Global.showError(a,"ERREUR : Abandon","Erreur")}})}})},creeLigne:function(j){var k=jQuery("<tr />");var l=jQuery("<td />");var m=jQuery("<td />");var i=jQuery("<tr />");for(var h=1;h<j.level;h++){l.append("&nbsp;&nbsp;&nbsp;&nbsp;")}if(j.information=="1"){l.append("<b>"+j.titre+"&nbsp;</b>");m.append("&nbsp;");i.append("&nbsp;")}else{l.append(j.titre+"&nbsp;");m.append("&nbsp;("+j.valeur+" points)");i.append('<input id="q'+j.id+'"  value="'+j.note+'" size="5"/>')}k.append(l);k.append(m);k.append(i);return k},creeTableau:function(c){var e=jQuery("<table />");for(d in c){e.append(NotationManuelle.creeLigne(c[d]))}jQuery("#zoneNotation").empty().append(e).append('<input id="validerNoter" class="ui-state-default ui-corner-all" type="button" value="Enregistrer" />');jQuery("#validerNoter").bind("click",function(){NotationManuelle.valideNotes()})},valideNotes:function(){var e=jQuery("#idclub").val();var c=new Array();jQuery(':input[id^="q"]',"#zoneNotation").each(function(){c.push(this.id+"_"+this.value)});jQuery.ajax({type:"POST",async:false,datatype:"json",url:"restnotation/validenotes",data:{format:"json",club:e,notes:c},success:function(a){Global.showMessage("Notes prises en compte.");jQuery("#zoneNotation").empty()},error:function(a){Global.showError(a,"ERREUR : Abandon","Erreur")}})}};var ImportChallenge={initialize:function(){jQuery("#import").ajaxForm({dataType:"json",semantic:true,beforeSend:ImportChallenge.beforeSendDocument,uploadProgress:ImportChallenge.uploadProgressDocument,complete:ImportChallenge.processDocument})},beforeSendDocument:function(){var c=jQuery("#importProgress");var e=jQuery(".progress-label",c);jQuery("#traitement").empty();c.progressbar({value:false,change:function(){e.text(c.progressbar("value")+" %")},complete:function(){e.text("terminé !")}})},uploadProgressDocument:function(i,h,j,k){var g=jQuery("#importProgress");g.progressbar("value",k)},processDocument:function(e){var f=jQuery.parseJSON(e.responseText).data;var g=jQuery("#traitement");jQuery("#importProgress").progressbar("value",100);jQuery("#fichierExcel").val("");if(f.name!==undefined){g.append("<b>Fichier incorrect. Abandon.</b>")}else{g.append("<b>Import des réponses pour "+f.infos.club+".</b><br />");if(f.parse.problemes.problemes.length==0){g.append("<b>Pas de problèmes trouvés.</b><br />")}else{g.append("<b>"+f.parse.problemes.problemes.length+" problème(s) trouvé(s).</b><br />");if(f.parse.problemes.possibles.length==0){g.append("<b>Pas de solutions possibles</b><br />")}else{g.append("<b>"+f.parse.problemes.possibles.length+" solution(s) possible(s).</b><br />")}}g.append("<br />");ImportChallenge.creeFormulaire(f)}},creeFormulaire:function(i){var j=jQuery('<form id="vaildImportForm" action="restnotation/valideimport" method="post" />');var f='<option value=""></option>';var h=0;h=i.parse.reponses.length;j.append('<input type="hidden" name="chalid" value="'+i.infos.chalid+'" />');j.append('<input type="hidden" name="club" value="'+i.infos.id+'" />');for(var k=0;k<h;k++){j.append('<input type="hidden" name="rn'+k+'" value="'+i.parse.reponses[k].noeud+'" />');j.append('<input type="hidden" name="rr'+k+'" value="'+i.parse.reponses[k].reponse+'" />')}h=i.parse.problemes.possibles.length;for(var k=0;k<h;k++){f+='<option value="'+i.parse.problemes.possibles[k].noeud+'">'+i.parse.problemes.possibles[k].indication+"</option>"}h=i.parse.problemes.problemes.length;for(var k=0;k<h;k++){j.append(i.parse.problemes.problemes[k].indication+"&nbsp;:&nbsp;");j.append('<select name="sn'+k+'">'+f+"</select>");j.append('<input type="hidden" name="sr'+k+'" value="'+i.parse.problemes.problemes[k].reponse+'" />');j.append("<br />")}j.append("<br />");j.append('<input value="Valider cet import" type="submit" class="ui-state-default ui-corner-all">');jQuery("#traitement").append(j);jQuery("#vaildImportForm").ajaxForm({dataType:"json",semantic:true,success:ImportChallenge.completeImport,error:ImportChallenge.errorImport})},completeImport:function(){jQuery("#traitement").empty().append("<b>Import confirmé.</b>")},errorImport:function(){jQuery("#traitement").empty().append("<b>Erreur durant le processus. Recommencez.</b>")}};var TermineChallenge={lance:function(){Tabs.closeOtherTabs("modterchal");jQuery("#questiontree").hide();jQuery("#modterchalbt").unbind("click");TermineChallenge.termine()},termine:function(){jQuery.ajax({type:"POST",async:false,datatype:"json",url:"restnotation/termine",data:{format:"json"},success:function(b){setTimeout("window.location.href = '"+Global.baseUrl+"/authentification/deconnexion/index'",100)},error:function(b){Global.showError(b,"ERREUR : Abandon","Erreur")}})}};var QuestionForms={initialize:function(b){Global.setAccordion("accordeonNotation"+b);Global.addEditorInstance("saisieReponseTexte"+b);jQuery("#choix"+b).on("change",function(){var a=this.value;var e=jQuery("#rq"+b);e.empty();jQuery("#frmnote"+b).hide();if(a!="vide"){jQuery.ajax({type:"POST",async:false,datatype:"json",url:"restnotation/reponse",data:{format:"json",qid:b,club:a},success:function(j){jQuery("#frmnote"+b).show();var c=jQuery("<p />");c.append("Documents : ");for(var i in j.data.documents){var k=jQuery("<a />");k.attr("href","challenge/notation/fichier?q="+b+"&club="+a+"&fic="+j.data.documents[i]);k.append(j.data.documents[i]);k.attr("target","_blank");c.append(k);c.append("&nbsp;")}e.append(c);e.append("<br />");e.append(j.data.reponse);jQuery("#club"+b).val(a);jQuery("#note"+b).val(j.data.note);if(j.data.estReponse==="1"){document.getElementById("util"+b).checked="checked"}else{document.getElementById("util"+b).checked=""}},error:function(c){e.append("<h2>ERREUR : Abandon.</h2>")}})}});jQuery("#reponsetexte"+b).ajaxForm({dataType:"json",semantic:true,beforeSerialize:QuestionForms.beforeSerializeReponses,success:QuestionForms.processReponses,error:QuestionForms.cancelJson});jQuery("#frmnote"+b).ajaxForm({dataType:"json",semantic:true,beforeSerialize:QuestionForms.beforeSerializeNote,success:QuestionForms.processNote,error:QuestionForms.cancelJson});jQuery("#frmnote"+b).hide()},beforeSerializeNote:function(g,f){var h=g[0].id.substr(7);var i=jQuery("#note"+h).val();if((parseFloat(i)!=i)||(parseFloat(i)<0)){Global.showMessage("Note invalide.<br />La note doit-être supérieure ou égale à 0","Erreur");return false}return true},processNote:function(b){Global.showMessage("Note prise en compte")},cancelJson:function(b){Global.showError(b,"Erreur de sauvegarde","erreur")},beforeSerializeReponses:function(f,e){var g=f[0].id.substr(12);jQuery("#texte"+g).val(jQuery("#saisieReponseTexte"+g).html());return true},processReponses:function(b){Global.showMessage("Réponse prise en compte")}};jQuery(document).ready(function(){jQuery("#contenuPrincipal").layout({defaults:{applyDefaultStyles:true,size:"auto",contentSelector:".content"},west:{minSize:60,spacing_open:10,spacing_closed:10,resizable:true,slidable:true,togglerTip_open:Global.tr("open_pane"),togglerTip_closed:Global.tr("close_pane"),resizerTip:Global.tr("resize_pane"),hideTogglerOnSlide:false,fxName:"slide",fxSpeed_open:1000,fxSpeed_close:1000,fxSettings_open:{easing:"easeInQuint"},fxSettings_close:{easing:"easeOutQuint"}},center:{spacing_open:1,togglerLength_open:0,togglerLength_closed:-1,resizable:false,slidable:false,fxName:"none"}});jQuery("#layoutCenter").css("overflow","hidden");jQuery("#questiontree").jstree({json_data:{ajax:{url:"restful/tree",datatype:"json",contentType:"application/json charset=utf-8",data:function(c){return{operation:"getChildren",id:c.attr?c.attr("id").replace("node_",""):1}},dataFilter:function(e){var c=JSON.stringify(JSON.parse(e).data);return c},error:function(c){Global.showError(c,"msg_sans_info","msg_erreur_tree")}}},themes:{theme:"classic"},ui:{initially_select:["node_1"]},core:{},plugins:["themes","json_data","ui"]}).bind("select_node.jstree",function(f,i){var h="";for(var g=0;g<i.args[0].childNodes.length;g++){h+=i.args[0].childNodes[g]["textContent"]}Tabs.addQuestionsTab(i.rslt.obj[0]["id"],h.trim())});Tabs.initTabs("notation");var b=Global.getEditor();b.setPanel("nicCommonPanel");jQuery("#preview").on("click",function(){Tabs.addTab("chalentier","Le Challenge","challenge/index/complet",true)});jQuery("#questionauteur").hide();jQuery("#spanarbre").on("click",function(){jQuery("#questiontree").jstree("refresh");jQuery("#questionauteur").hide();jQuery("#questionarbre").show()});jQuery("#spanauteur").on("click",function(){jQuery("#questionarbre").hide();jQuery("#questionauteur").show();AuteurTree.afficheListe()})});
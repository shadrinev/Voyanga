/************ Plugin JQuery ************* */
/* Réalisé par Creamama - Matthieu Léorat */
/* ************ juillet 2010 ************ */ 
/* ******** http://www.creamama.fr ****** */

/********** NECESSITE ************/
/* ******************************** 
jquery-1.4.2.min.js
ui.core-1.7.2.js
ui.draggable-1.7.2.js
jquery.mousewheel.min.js
******************************** */

(function($) {
	$.fn.scrollbar = function(params) {
		// Fusionner les paramètres par défaut et ceux de l'utilisateur
		params = $.extend( {
			taille_englobe: 'auto',			//Taille de l'espace visible - /!\ Doit être un nombre ou 'auto'
			taille_scrollbar: "auto",		//Taille de la scrollbar - /!\ Doit être un nombre ou 'auto'
			taille_bouton: 50,				//Taille du bouton - /!\ Doit être un nombre
			pas:75,							//Pas du scroll molette - /!\ Doit être un nombre ou 'auto'
			molette: true,					//Détection du scroll molette - /!\ true ou false
			drag: true,						//Bouton de la scrollbar déplacable à la souris - /!\ true ou false
			debug: true,					//Afficher la console de debug - /!\ true ou false
			style: 'basic',					//Choix des styles - /!\ Par défault, il n'y a que le style 'basic'
			position:'droite',				//Position de la scrollBar - /!\ 'gauche' ou 'droite'
			alignement_scrollbar:'haut',	//Alignement de la scrollBar. Utilisé uniquement si elle à une taille inférieur à celle de taille_englobe
			orientation: 'vertical',		//Orientation du contenu, 'vertical' ou 'horizontal'
			marge_scroll_contenu: 15,		//Marge entre la scrollBar et le contenu - /!\ Doit être un nombre
			largeur_scrollbar:5				//Largeur de la scrollbar
		}, params);
		
		return this.each(function() {
			var $$ = $(this);
			var taille_englobe_init = params.taille_englobe;
			var taille_scrollbar_init = params.taille_scrollbar;
			
			//Fonction de calcul de position top maximum du contenu
			function calcul_contenu_top_max(){
				return  params.taille_englobe - taille_contenu ;
			}
			
			//Fonction de calcul de position top maximum du bouton
			function calcul_bouton_top_max(){
				return params.taille_scrollbar - params.taille_bouton
			}
			
			//Fonction de calcul du déplacement du bouton
			function deplacement_bouton(info_top_contenu){
				//On calcul la nouvelle position du bouton
				var depl_bouton = (info_top_contenu/calcul_contenu_top_max())*(calcul_bouton_top_max());
				//On vérifie que ca déborde pas en haut
				if(depl_bouton < 0){depl_bouton = 0;}
				//On vérifie que ca déborde pas en bas
				if(depl_bouton > calcul_bouton_top_max()){depl_bouton = calcul_bouton_top_max();}
				$('#bouton').css({'top':depl_bouton+"px"});
			}
			
			function deplacement_contenu(info_top_bouton){
				//On calcul la nouvelle position du contenu
				var depl_contenu = (info_top_bouton/calcul_bouton_top_max())*(calcul_contenu_top_max());
				//On vérifie que ca déborde pas en haut
				if (depl_contenu > 0){depl_contenu = 0}
				//On vérifie que ca déborde pas en bas
				if (depl_contenu < calcul_contenu_top_max()){depl_contenu = calcul_contenu_top_max()}
				$('#englobe').css({'top':depl_contenu+"px"});
			}
			
			function styler_scrollbar(position,orientation){
				var type_marge_position;
				var marge_position;
				var marge_orientation;
				switch (position){
					case 'droite':
						$('#englobe').after('<div id="scrollbar"><div id="bouton">&nbsp;</div></div>');
						$('#scrollbar').css({'margin-left':params.marge_scroll_contenu+'px'});
					break;
					case 'gauche':
						$('#englobe').before('<div id="scrollbar"><div id="bouton">&nbsp;</div></div>');
						$('#scrollbar').css({'margin-right':params.marge_scroll_contenu+'px'});
					break;
				}
				switch (orientation){
					case 'haut':
						$('#scrollbar').css({'margin-top':'0px'});
					break;
					case 'centre':
						var marge = (params.taille_englobe -params.taille_scrollbar)/2;
						$('#scrollbar').css({'margin-top':marge+'px'});
					break;
					case 'bas':
						var marge = params.taille_englobe -params.taille_scrollbar;
						$('#scrollbar').css({'margin-top':marge+'px'});
					break;
				}
			}
			var padTop=0;
			var padBot=0;
			//Hauteur du contenu
            var taille_contenu = $$.height()+40;
			
			function calcul_hauteur_auto(){
				if(params.taille_englobe == "auto"){
					padTop = $('#contenu').css('padding-top');
					padTop = padTop.substring(0,padTop.length-2);
					
					padBot = $('#contenu').css('padding-bottom');
					padBot = padBot.substring(0,padBot.length-2);
					
					params.taille_englobe = $(window).height()-40-padBot-padTop;
					
				}else{return false}
			}
			calcul_hauteur_auto();
			//La hauteur de l'espace visible est la hauteur de la fenetre du navigateur si taille_englobe="auto"
			
			
			function controle_donnee(){
				calcul_hauteur_auto();
				//La taille du contenu doit être supérieur à celle de l'espace visible (taille_englobe)
				if(taille_contenu > params.taille_englobe){
					//La hauteur de la scroll bar est égale à la hauteur de "englobe" si hauteur_srollbar="auto"
					if(params.taille_scrollbar == "auto"){params.taille_scrollbar = params.taille_englobe;}
					//La taille de la scrollbar doit être inférieur ou égale à la taille de taille_englobe
					if(params.taille_scrollbar > params.taille_englobe){						
						params.taille_scrollbar = params.taille_englobe;	
					}
					return true;
				}else{return false;}		
			}
			
			//Si la hauteur du contenu est supérieur à la hauteur de l'espace visible
			if(controle_donnee()){
				//Au redimensionnement de la fenetre
				//N'est concerné par cette fonction que les éléments en 'auto'
				window.onresize = function() {
					if(taille_englobe_init == "auto"){
						params.taille_englobe = $(window).height()-40-padBot-padTop;
						$$.css({'height':params.taille_englobe+'px'});
						if(taille_scrollbar_init == "auto"){
							params.taille_scrollbar = params.taille_englobe;
							$('#scrollbar').css({'height':params.taille_scrollbar+'px'});
							deplacement_bouton($("#englobe").css('top').substring(0,$("#englobe").css('top').length-2));
							if(params.debug){affiche_position();}
						}
					}	
				};
				
				//calcul des largeurs
				var temp = $$.width();
				$$.css({'width':params.marge_scroll_contenu+params.largeur_scrollbar+temp+'px'});
				//On construit une div autour du contenu, mais à l'intérieur de la div
				$$.wrapInner('<div id="englobe"></div>');
				$$.css({'height':params.taille_englobe+'px','overflow':'hidden','position':'relative'});
				$("#englobe").css({'top':0+'px','float':'left','position':'relative','width':temp+'px'});	
				
				//On construit la scrollBar
				styler_scrollbar(params.position,params.alignement_scrollbar);
				
				$$.append('<div class="clear"></div>');
				$(".clear").css({'clear':'both'});
				
				
				switch (params.style) {
					case 'basic':
						//Style de la scrollBar
						$('#scrollbar').css({'width':params.largeur_scrollbar+'px',
											'float':'left',
											'height':params.taille_scrollbar+'px',
											'background':'#ebd9b3'		
						});
				
						//Style du bouton de la scrollBar
						$("#bouton").css({'width':params.largeur_scrollbar+'px',
										'height':params.taille_bouton+'px',
										'background':'#cda042',
										'top':0+'px',
										'cursor':'pointer'
										});
					break;
				}

				
				
				//Si le drag du bouton est activé(true)
				if(params.drag){
					$("#bouton").draggable({ 
						containment: 'parent',
						axis: 'y',
						start:function(){},
						drag: function(event, ui) {
							//ui.position.top est la valeur renvoyé par le plugin JQuery UI
							deplacement_contenu(ui.position.top);
							if(params.debug){affiche_position();}
						},
						stop: function(){}
					});
				}
				
				if(params.molette){
					$$.mousewheel(function(event, delta) {
						//On récupère la position du contenu
						var top_contenu = $('#englobe').css('top');
						
						//On enlève le 'px' et on le convertit en entier pour pouvoir le manipuler
						top_contenu = parseInt( top_contenu.substring(0,(top_contenu.length-2)) );
						
						//On récupère la position du bouton
						var top_bouton = $('#bouton').css('top');
						//On enlève le 'px' et on le convertit en entier pour pouvoir le manipuler
						top_bouton = parseInt( top_bouton.substring(0,(top_bouton.length-2)) );
						
						//Si le delta est positif, c'est à dire que l'on "pousse" la molette
						if (delta > 0) {							
							top_contenu = top_contenu + params.pas;
							//On vérifie que l'on n'a pas atteint le haut du contenu
							if(top_contenu > 0){top_contenu = 0}
							$('#englobe').css({'top':top_contenu+"px"});
						//Si le delta est négatif, c'est à dire que l'on "ramène" la molette
						}else if (delta < 0){							
							top_contenu = top_contenu - params.pas;
							//On vérifie que l'on n'a pas atteint le bas du contenu
							if(top_contenu < calcul_contenu_top_max()){top_contenu = calcul_contenu_top_max()}
							$('#englobe').css({'top':top_contenu+"px"});							
						}
						
						//calcul de déplacement du bouton					
						deplacement_bouton(top_contenu);
						
						if(params.debug){affiche_position();}
					});
				}
				
				//Affiche la console de debug
				if(params.debug){
					$$.after('<div id="debug"></div>');
					$('#debug').css({'position':'fixed','top':'200px','right':'50px', 'border':'2px solid #EF9700','background':'#FFDC9F','padding':'0px'})				
					.append('<p id="hauteurcontenu">Hauteur du contenu:'+taille_contenu+'</p>')
					.append('<p id="hauteurenglobe">Hauteur de l\'espace visible:'+params.taille_englobe+'</p>')
					.append('<p id="hauteurscrollbar">Hauteur du srcoll:'+params.taille_scrollbar+'</p>')
					.append('<p id="hauteurbouton">Hauteur du bouton:'+params.taille_bouton+'</p>')
					.append('<p id="topcontenu">Top du contenu:'+$$.css('top')+'</p>')
					.append('<p id="topbouton">Top du bouton:'+$('#bouton').css('top')+'</p>')
					.append('<p id="topmaxbouton">Top max du bouton:'+calcul_contenu_top_max()+'</p>')
					.append('<p id="topmaxcontenu">Top max du contenu:'+calcul_bouton_top_max()+'</p>')
					.children().css({'margin':'0','padding':'5px'});
					
					//Fonction de mise à jour des infos de la console de debug
					function affiche_position(){
						$("#hauteurcontenu").html('Hauteur du contenu:'+taille_contenu);
						$("#hauteurenglobe").html('Hauteur de l\'espace visible:'+params.taille_englobe);
						$("#hauteurscrollbar").html('Hauteur du srcoll:'+params.taille_scrollbar);
						$("#hauteurbouton").html('Hauteur du bouton:'+params.taille_bouton);
						$("#topcontenu").html('Top du contenu:'+$('#englobe').css('top'));
						$("#topbouton").html('Top du bouton:'+$('#bouton').css('top'));
						$("#topmaxbouton").html('Top max du bouton:'+calcul_contenu_top_max());
						$("#topmaxcontenu").html('Top max du contenu:'+calcul_bouton_top_max());
					}
				}	
			}else{
				alert('Pas de scrollbar');
			}
        });
	}
})(jQuery);
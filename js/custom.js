//~ Init
jQuery(document).ready(function(){
	//~ Languages
	$('[data-lng]').css({'opacity': 0, 'visibility': 'hidden'});
	var _lang = [];
	var cur_lang = ($('html').attr('lang') && $('html').attr('lang') !== "" ? $('html').attr('lang') : "fr");
	$.getJSON('lang/'+cur_lang+'.json', function(jsnlab){
		_lang[cur_lang] = jsnlab;

		//~ Load default text
		$('[data-lng]').each(function(index){
			var $this = $(this);
			if($this.attr('data-lng') === ''){
				$this.text(label($this.text()));
			}else{
				$this.attr($this.attr('data-lng'), label($this.attr($this.attr('data-lng'))));
			}
		});
		$('[data-lng]').css({'opacity': 1, 'visibility': 'visible'});
	});

	//~ Form & AJAX request
	$('#submit').click(function(){	
		var $this = $(this),
			$rsslk = $('#rss-lk'),
			$n = $('#n');
		var isn = ($n.length ? troue : false);

		if( $('#flux').val() !== ''){
			$('#ajax-message').hide().empty();
			$this.attr('disabled', 'disabled');
			$('html, body').scrollTo($rsslk, 'fast');
			$rsslk.html("<span class='ajax-loader'></span><span>"+label('Génération en cours')+"</span>");

			setTimeout(function(){
				$.ajax({
					type: 'POST',
					url: 'generate.php',
					data: $('#xml_form').serialize()+'&isAjax=true&t='+$('meta[name="csrf-token"]').attr('content'),
					headers : {
				        'CsrfToken': $('meta[name="csrf-token"]').attr('content')
				    },
					success: function(data) {
						$this.removeAttr('disabled');
						if(!data || !data.sucess){
							$rsslk.empty();
							show_msg(label(data.message));
						}else{
							$rsslk.html("<br /><br /><a class='btn btn-primary' id='n' href='"+data['data'].file+"' target='_blank'>"+label(data.message)+"</a>");
							if(isn){
								$n = $('#n');
								if($n.hasClass('btn-primary')){
									$n.removeClass('btn-primary').addClass('btn-info');
								}else{
									$n.removeClass('btn-info').addClass('btn-primary');
								}
							}
						}

						
					},
					statusCode: {
					    404: function() {
					     	show_error(label("La page cible est introuvable."));
					    },
					    500: function(){
					    	show_error(label("Erreur serveur sur la page cible."));
					    },
					    204: function(){
					    	show_error(label("Aucun retour serveur"));
					    }
				  	},
					error: function(){
						show_msg(label("Impossible de réaliser le traitement distant."));
					}
				});
			}, 1000);		
			
		} 
		else {
			show_msg(label("Merci de renseigner au moins une URL de flux RSS"));
		}
	});

function label(k){
if(k && k !== "" && _lang && typeof(_lang) !== 'undefined'){if(_lang[cur_lang] && typeof(_lang[cur_lang]) == 'object' && _lang[cur_lang][k] && _lang[cur_lang][k] !== ""){return _lang[cur_lang][k];}}return false;}

	function show_msg(msg) {
		$('html, body').scrollTo( $('#ajax-message'), 'fast' );
		if(msg === ""){
			msg = label("Une erreur est survenue");
		}
		$('#ajax-message').css('display','block').html(msg);	
	}

	function show_error(msg){
		if(typeof(debugShow) !== 'undefined' && debugShow){
				$('.modal-background').remove();
				$('body').append('<div class="modal-background"><div class="modal-body"><h2 style="font-weight:bold;text-decoration:underline;font-size:120%;margin-bottom:12px;">'+label("Une erreur est survenue")+' :</h2><pre>'+msg+'</pre></div></div>');
	    		$('.modal-background')
	    			.css({
						'position': 'fixed',
						'left': 0,
						'top': 0,
						'right': 0,
						'bottom': 0,
						'background': 'rgba(0, 0, 0, 0.75)',
						'display': 'none',
						'cursor':'pointer'
					})
					.fadeIn()
					.click(function(){
						$(this).fadeOut().find('.modal-body').slideUp();
					})
				.find('.modal-body')
					.css({
						'position': 'fixed',
					    'width': '350px',
					    'height': '200px',
					    'left': '50%',
					    'top': '50%',
					    'margin-left': '-195px',
					    'margin-top': '-170px',
					    'background': '#fff',
					    'display': 'none',
					    'padding': '15px',
					    'cursor': 'default'
					})
					.click(function(event){
						event.stopPropagation();
					})
					.slideDown();
		}
	}
});
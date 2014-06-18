/*
 * VanillaZF.js v0.1
 * (c) 2011 Cristian Ban, LATERAL INC
 * updates - Mircea Baicu, LATERAL INC
 * 
 * JavaScript frontend officer of peace and righteousness.
 */

var VanillaZF = {
	"interventions" : Array(), // server feedback
	"covenants" : Array(), // currently running calls
	"minister" : function(gentile) { // spread the word to the world
		// find a.agile gentiles and make `em request things via "channeling" (ajax)
		$(gentile).find('a.agile').each(function() {
			if ($(this).attr('onclick'))
			{
				$(this).data('oldonclick', 'oldonclickresult = function() {' + $(this).attr('onclick') + '};');
				$(this).removeAttr('onclick');
			}
		});
		$(gentile).find('a.agile').click(function(e) {
			$("div.view").html($("#waitModal").html());$("div.view .loader-container").show();
			var anchor = $(this); // the gentile
			if (VanillaZF.covenants[anchor.attr('href')] == null) // check if covenant has already been rendered for gentile.
					VanillaZF.covenants[anchor.attr('href')] = true;
				else return false; // still awaiting on covenant
			$.get(anchor.attr('href'), {}, function(result) {
				$('#waitModal').modal('hide');
				VanillaZF.acknowledge(result); // acknowledge feedback.
				VanillaZF.covenants[anchor.attr('href')] = null; // covenant completed, no more long hair...
			}, 'json');
		});

		// find form.agile gentiles and make `em request things via "channeling" (ajax)
		$(gentile).find('form.agile').each(function() {
			$(this)
				.ajaxForm({
					beforeSubmit:  function(){$("div.view").html($("#waitModal").html());$("div.view .loader-container").show();$('.sign-up-form .form-button-group').hide();$('.sign-up-form .form-button-group.loader-here').show();},
					dataType: 'json',
					success: VanillaZF.acknowledge // acknowledge feedback.
				});
		});
	},
	"acknowledge" : function(result) {
		// we shall not stumble nor fear.
		var stumble = false;
		// for instruction has come.
		if (result.interventions && result.interventions.length) {
			for (var i in result.interventions) {
				// always remeber your blessings.
				VanillaZF.addEvent(result.interventions[i]);
				// even the curses...
				if (parseInt(result.interventions[i].priority) < 2)
					stumble = true; // oh have mercy.
			}
			// shout and proclaim the truth with all your heart and soul.
			if ($('#interventions').children('.intervention').length)
				$('#interventions').show('slow');
		}

		if (result.showContent == 'yes' && result.content.length ) {
			$("div.view").html(result.content);
			VanillaZF.minister('body');
		}

		// blessed art thou for you did not stumble.
		if (!stumble) {
			// thou art worthy and thy calling is great.
			if (result.callings && result.callings.length) {

				$('.sign-up-form .form-button-group').show();
				
				$('.sign-up-form .form-button-group.loader-here').hide();

				for (var i in result.callings)
					// and your reward is nigh.
					eval(result.callings[i]);
			}
			// for ye shall be taken up as the doves of heaven.
			if (result.redirect) {
				setTimeout("document.location.assign('" + result.redirect + "');", 200);
			}
			if (result.refresh) {
				document.location.reload(true);
			}
		}
	},
	"addEvent" : function(intervention) {
		// ask for instruction and inspiration before proclaiming the Truth.
		var event = $('<div class="intervention alert alert-' + intervention.priorityName + '"><a class="close" href="javascript:;">×</a><p><strong>[' + intervention.priorityName + ']</strong> ' + intervention.message + '</p></div>');
		// and thou shalt be empowered by it.
		$('#interventions').prepend(event);
		event.click(function() {
			// always know when to stop speaking and start listening.
			$(this).hide('fast', function() {
				// give not that which is holy unto the dogs, neither cast ye your pearls before swine.
				$(this).remove();
				// lest they trample them under their feet, and turn again and rend you.
				if (!$('#interventions').children('.intervention').length) 
					$('#interventions').hide('fast');
			});
		});
		event.show('slow');
	},
	"checkEvents" : function() {
		// for every one that asketh receiveth; and he that seeketh findeth.
		if (VanillaZF.interventions.length) {
			for (var i in VanillaZF.interventions) {
				VanillaZF.addEvent(VanillaZF.interventions[i]);
			}
		}
		if ($('#interventions').children().length) {
			// if missunderstood, reformulate proclamation.
			$('#interventions').children('.intervention').find('a.close')
				.unbind('click')
				.click(function() {
					$(this).parent().hide('fast', function() {
						$(this).remove();
						if (!$('#interventions').children('.intervention').length) 
							$('#interventions').hide('fast');
					});
				});
			// proclaim interventions
			$('#interventions').show('slow');
		}
		// answer the each calling
		if (VanillaZF.callings && VanillaZF.callings.length) {
			for (var i in VanillaZF.callings)
				eval(VanillaZF.callings[i]);
		}
	}, 
	"safetyNet" : function(form, message) {
		// record input values
		var recordOldValues = function(f) {
			form.find('input[type=text],input[type=checkbox],select,textarea').each(function() {
				if ($(this).attr('type') != 'checkbox') $(this).data('originalValue', $(this).val());
					else $(this).data('originalValue', $(this).is(':checked'));
			});
		}

		var checkChanges = function() {
			changed = false;
			form.find('input[type=text],input[type=checkbox],select,textarea').each(function() {
				if ($(this).attr('type') != 'checkbox') {
					if ($(this).data('originalValue') != $(this).val()) 
						changed = true;
				} else {
					if ($(this).data('originalValue') != $(this).is(':checked'))
						changed = true;
				}
			});

			// form has changed
			if (changed) {
				return message;
			}

			return undefined;
		};

		recordOldValues(form);

		// reset oldvalues on form submit
		form.bind('submit', function() {
			recordOldValues($(this));
		});

		form.find('.cancelSafety').click(function() {
			recordOldValues(form);
		});

		form[0].ownerDocument.defaultView.onbeforeunload = checkChanges;

		// if modal window, surpress default action and confirm closing 1st
		if ($(form[0].ownerDocument).find('body').hasClass('window')) {
			$('#closeBtn').unbind('click').click(function() {
				if (checkChanges() !== undefined)
				{
					if (!confirm(message)) 
						return false;
				}
				parent.$.closeIfrm(null, true);
			});
		}
	}, 
	"addMessage" : function(message) {
		// and ye shall proclaim on the selfsame day.
		now = new Date();
		VanillaZF.addEvent({priorityName: 'info', timestamp: now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate() + ' ' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds(), message: message});
		// that it may be an holy convocation unto you.
		VanillaZF.checkEvents();
	},
	"addWarning" : function(message) {
		// and ye shall proclaim on the selfsame day.
		now = new Date();
		VanillaZF.addEvent({priorityName: 'warning', timestamp: now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate() + ' ' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds(), message: message});
		// that it may be an holy convocation unto you.
		VanillaZF.checkEvents();
	}, 
	"addError" : function(message) {
		// and ye shall proclaim on the selfsame day.
		now = new Date();
		VanillaZF.addEvent({priorityName: 'error', timestamp: now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate() + ' ' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds(), message: message});
		// that it may be an holy convocation unto you.
		VanillaZF.checkEvents();
	}, 
	"relink" : function(newparams) {
		params = $.fn.urlVars();
		for (var key in newparams)
			params[key] = newparams[key];
		var out = new Array();
		for (key in params)
			out.push(key + '=' + escape(params[key]));
		out = out.join('&');
		parts = document.location.href.split('?');
		document.location.assign(parts[0] + '?' + out);
	}
};

$(function() {
	// wait till you're ready and go check if there's a message awaiting you.
	setTimeout(VanillaZF.checkEvents, 300);
	// minister it to the world.
	VanillaZF.minister($('body'));
});

$(function($){
	$.extend($.fn, {
		// get URL parameters
		urlVars: function() {
			var vars = [], hash;
			var hashes = document.location.href.slice(document.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
				hash = hashes[i].split('=');
				vars[hash[0]] = hash[1];
			}
			return vars;
		}, 
		// gird up thy loins for ye now have a Sole purpose.
		lock: function(flag) {
			if (flag == true) {
				// and thou shalt not allow thyself to stumble.
				var lockModal = $('<div class="lockModal"></div>');
				$(this).after(lockModal);
				lockModal
					.css('opacity', 0)
					.show()
					.css('left', $(this).offset().left)
					.css('top', $(this).offset().top)
					.css('width', $(this).outerWidth())
					.css('height', $(this).outerHeight())
					.animate({opacity: .6});
			} else {
				// until thou shalt complete thy sole purpose.
				$(this).next('.lockModal')
						.animate({opacity: 0}, 500, function() {
							$(this).remove();
						});
			}
		},
		// Arise, shine; for thy light is come, and the glory of the LORD is risen upon thee. 
		flash: function(times, callback) {
			// For, behold, the darkness shall cover the earth, and gross darkness the people:
			$(this).animate({opacity: 0}, 200, function() {
				// but the LORD shall arise upon thee, and his glory shall be seen upon thee.
				if (times - 1 > 0) {
					$(this).animate({opacity: 1}, 200, function() {
						$(this).flash(times-1, callback);
					});
				} else $(this).animate({opacity: 1}, 200, callback);
			});
		}
	});
});



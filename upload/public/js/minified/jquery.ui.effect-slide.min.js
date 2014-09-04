/*! jQuery UI - v1.9.2 - 2014-04-02
* http://jqueryui.com
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(e){e.effects.effect.slide=function(t,i){var a,s=e(this),n=["position","top","bottom","left","right","width","height"],r=e.effects.setMode(s,t.mode||"show"),o="show"===r,h=t.direction||"left",l="up"===h||"down"===h?"top":"left",u="up"===h||"left"===h,d={};e.effects.save(s,n),s.show(),a=t.distance||s["top"===l?"outerHeight":"outerWidth"](!0),e.effects.createWrapper(s).css({overflow:"hidden"}),o&&s.css(l,u?isNaN(a)?"-"+a:-a:a),d[l]=(o?u?"+=":"-=":u?"-=":"+=")+a,s.animate(d,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){"hide"===r&&s.hide(),e.effects.restore(s,n),e.effects.removeWrapper(s),i()}})}})(jQuery);
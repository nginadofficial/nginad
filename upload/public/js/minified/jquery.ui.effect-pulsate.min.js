/*! jQuery UI - v1.9.2 - 2014-04-02
* http://jqueryui.com
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(e){e.effects.effect.pulsate=function(t,i){var a,s=e(this),n=e.effects.setMode(s,t.mode||"show"),r="show"===n,o="hide"===n,l=r||"hide"===n,h=2*(t.times||5)+(l?1:0),u=t.duration/h,d=0,c=s.queue(),p=c.length;for((r||!s.is(":visible"))&&(s.css("opacity",0).show(),d=1),a=1;h>a;a++)s.animate({opacity:d},u,t.easing),d=1-d;s.animate({opacity:d},u,t.easing),s.queue(function(){o&&s.hide(),i()}),p>1&&c.splice.apply(c,[1,0].concat(c.splice(p,h+1))),s.dequeue()}})(jQuery);
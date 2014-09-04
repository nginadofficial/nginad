/*! jQuery UI - v1.9.2 - 2014-04-02
* http://jqueryui.com
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(e){e.effects.effect.highlight=function(t,i){var a=e(this),s=["backgroundImage","backgroundColor","opacity"],n=e.effects.setMode(a,t.mode||"show"),r={backgroundColor:a.css("backgroundColor")};"hide"===n&&(r.opacity=0),e.effects.save(a,s),a.show().css({backgroundImage:"none",backgroundColor:t.color||"#ffff99"}).animate(r,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){"hide"===n&&a.hide(),e.effects.restore(a,s),i()}})}})(jQuery);
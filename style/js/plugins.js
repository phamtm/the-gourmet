/**
 * jQuery.Preload
 * Copyright (c) 2008 Ariel Flesler - aflesler(at)gmail(dot)com
 * Dual licensed under MIT and GPL.
 * Date: 3/25/2009
 *
 * @projectDescription Multifunctional preloader
 * @author Ariel Flesler
 * @version 1.0.8
 *
 * @id jQuery.preload
 * @param {String, jQuery, Array< String, <a>, <link>, <img> >} original Collection of sources to preload
 * @param {Object} settings Hash of settings.
 *
 * @id jQuery.fn.preload
 * @param {Object} settings Hash of settings.
 * @return {jQuery} Returns the same jQuery object, for chaining.
 *
 * @example Link Mode:
 *	$.preload( '#images a' );
 *
 * @example Rollover Mode:
 *	$.preload( '#images img', {
 *		find:/\.(gif|jpg)/,
 *		replace:'_over.$1'
 *	});
 *
 * @example Src Mode:
 *	$.preload( [ 'red', 'blue', 'yellow' ], {
 *		base:'images/colors/',
 *		ext:'.jpg'
 *	});
 *
 * @example Placeholder Mode:
 *	$.preload( '#images img', {
 *		placeholder:'placeholder.jpg',
 *		notFound:'notfound.jpg'
 *	});
 *
 * @example Placeholder+Rollover Mode(High res):
 *	$.preload( '#images img', {
 *		placeholder:true,
 *		find:/\.(gif|jpg)/,
 *		replace:'_high.$1'
 *	});
 */
;(function( $ ){

	var $preload = $.preload = function( original, settings ){
		if( original.split ) // selector
			original = $(original);

		settings = $.extend( {}, $preload.defaults, settings );
		var sources = $.map( original, function( source ){
			if( !source ) 
				return; // skip
			if( source.split ) // URL Mode
				return settings.base + source + settings.ext;
			var url = source.src || source.href; // save the original source
			if( typeof settings.placeholder == 'string' && source.src ) // Placeholder Mode, if it's an image, set it.
				source.src = settings.placeholder;
			if( url && settings.find ) // Rollover mode
				url = url.replace( settings.find, settings.replace );
			return url || null; // skip if empty string
		});

		var data = {
			loaded:0, // how many were loaded successfully
			failed:0, // how many urls failed
			next:0, // which one's the next image to load (index)
			done:0, // how many urls were tried
			/*
			index:0, // index of the related image			
			found:false, // whether the last one was successful
			*/
			total:sources.length // how many images are being preloaded overall
		};
		
		if( !data.total ) // nothing to preload
			return finish();
		
		var imgs = $(Array(settings.threshold+1).join('<img/>'))
			.load(handler).error(handler).bind('abort',handler).each(fetch);
		
		function handler( e ){
			data.element = this;
			data.found = e.type == 'load';
			data.image = this.src;
			data.index = this.index;
			var orig = data.original = original[this.index];
			data[data.found?'loaded':'failed']++;
			data.done++;

			// This will ensure that the images aren't "un-cached" after a while
			if( settings.enforceCache )
				$preload.cache.push( 
					$('<img/>').attr('src',data.image)[0]
				);

			if( settings.placeholder && orig.src ) // special case when on placeholder mode
				orig.src = data.found ? data.image : settings.notFound || orig.src;
			if( settings.onComplete )
				settings.onComplete( data );
			if( data.done < data.total ) // let's continue
				fetch( 0, this );
			else{ // we are finished
				if( imgs && imgs.unbind )
					imgs.unbind('load').unbind('error').unbind('abort'); // cleanup
				imgs = null;
				finish();
			}
		};
		function fetch( i, img, retry ){
			// IE problem, can't preload more than 15
			if( img.attachEvent /* msie */ && data.next && data.next % $preload.gap == 0 && !retry ){
				setTimeout(function(){ fetch( i, img, true ); }, 0);
				return false;
			}
			if( data.next == data.total ) return false; // no more to fetch
			img.index = data.next; // save it, we'll need it.
			img.src = sources[data.next++];
			if( settings.onRequest ){
				data.index = img.index;
				data.element = img;
				data.image = img.src;
				data.original = original[data.next-1];
				settings.onRequest( data );
			}
		};
		function finish(){
			if( settings.onFinish )
				settings.onFinish( data );
		};
	};

	 // each time we load this amount and it's IE, we must rest for a while, make it lower if you get stack overflow.
	$preload.gap = 14; 
	$preload.cache = [];
	
	$preload.defaults = {
		threshold:2, // how many images to load simultaneously
		base:'', // URL mode: a base url can be specified, it is prepended to all string urls
		ext:'', // URL mode:same as base, but it's appended after the original url.
		replace:'' // Rollover mode: replacement (can be left empty)
		/*
		enforceCache: false, // If true, the plugin will save a copy of the images in $.preload.cache
		find:null, // Rollover mode: a string or regex for the replacement
		notFound:'' // Placeholder Mode: Optional url of an image to use when the original wasn't found
		placeholder:'', // Placeholder Mode: url of an image to set while loading
		onRequest:function( data ){ ... }, // callback called every time a new url is requested
		onComplete:function( data ){ ... }, // callback called every time a response is received(successful or not)
		onFinish:function( data ){ ... } // callback called after all the images were loaded(or failed)
		*/
	};

	$.fn.preload = function( settings ){
		$preload( this, settings );
		return this;
	};

})( jQuery );


/*
 * Clear Default Text: functions for clearing and replacing default text in
 * <input> elements.
 *
 * by Ross Shannon, http://www.yourhtmlsource.com/
 */

addEvent(window, 'load', init, false);

function init() {
    var formInputs = document.getElementsByTagName('input');
    for (var i = 0; i < formInputs.length; i++) {
        var theInput = formInputs[i];
        
        if (theInput.type == 'text' && theInput.className.match(/\bcleardefault\b/)) {  
            /* Add event handlers */          
            addEvent(theInput, 'focus', clearDefaultText, false);
            addEvent(theInput, 'blur', replaceDefaultText, false);
            
            /* Save the current value */
            if (theInput.value != '') {
                theInput.defaultText = theInput.value;
            }
        }
    }
}

function clearDefaultText(e) {
    var target = window.event ? window.event.srcElement : e ? e.target : null;
    if (!target) return;
    
    if (target.value == target.defaultText) {
        target.value = '';
    }
}

function replaceDefaultText(e) {
    var target = window.event ? window.event.srcElement : e ? e.target : null;
    if (!target) return;
    
    if (target.value == '' && target.defaultText) {
        target.value = target.defaultText;
    }
}





/*
 * jQuery Cycle Plugin (with Transition Definitions)
 * Examples and documentation at: http://jquery.malsup.com/cycle/
 * Copyright (c) 2007-2010 M. Alsup
 * Version: 2.88 (08-JUN-2010)
 * Dual licensed under the MIT and GPL licenses.
 * http://jquery.malsup.com/license.html
 * Requires: jQuery v1.2.6 or later
 */
(function($){var ver="2.88";if($.support==undefined){$.support={opacity:!($.browser.msie)};}function debug(s){if($.fn.cycle.debug){log(s);}}function log(){if(window.console&&window.console.log){window.console.log("[cycle] "+Array.prototype.join.call(arguments," "));}}$.fn.cycle=function(options,arg2){var o={s:this.selector,c:this.context};if(this.length===0&&options!="stop"){if(!$.isReady&&o.s){log("DOM not ready, queuing slideshow");$(function(){$(o.s,o.c).cycle(options,arg2);});return this;}log("terminating; zero elements found by selector"+($.isReady?"":" (DOM not ready)"));return this;}return this.each(function(){var opts=handleArguments(this,options,arg2);if(opts===false){return;}opts.updateActivePagerLink=opts.updateActivePagerLink||$.fn.cycle.updateActivePagerLink;if(this.cycleTimeout){clearTimeout(this.cycleTimeout);}this.cycleTimeout=this.cyclePause=0;var $cont=$(this);var $slides=opts.slideExpr?$(opts.slideExpr,this):$cont.children();var els=$slides.get();if(els.length<2){log("terminating; too few slides: "+els.length);return;}var opts2=buildOptions($cont,$slides,els,opts,o);if(opts2===false){return;}var startTime=opts2.continuous?10:getTimeout(els[opts2.currSlide],els[opts2.nextSlide],opts2,!opts2.rev);if(startTime){startTime+=(opts2.delay||0);if(startTime<10){startTime=10;}debug("first timeout: "+startTime);this.cycleTimeout=setTimeout(function(){go(els,opts2,0,(!opts2.rev&&!opts.backwards));},startTime);}});};function handleArguments(cont,options,arg2){if(cont.cycleStop==undefined){cont.cycleStop=0;}if(options===undefined||options===null){options={};}if(options.constructor==String){switch(options){case"destroy":case"stop":var opts=$(cont).data("cycle.opts");if(!opts){return false;}cont.cycleStop++;if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);}cont.cycleTimeout=0;$(cont).removeData("cycle.opts");if(options=="destroy"){destroy(opts);}return false;case"toggle":cont.cyclePause=(cont.cyclePause===1)?0:1;checkInstantResume(cont.cyclePause,arg2,cont);return false;case"pause":cont.cyclePause=1;return false;case"resume":cont.cyclePause=0;checkInstantResume(false,arg2,cont);return false;case"prev":case"next":var opts=$(cont).data("cycle.opts");if(!opts){log('options not found, "prev/next" ignored');return false;}$.fn.cycle[options](opts);return false;default:options={fx:options};}return options;}else{if(options.constructor==Number){var num=options;options=$(cont).data("cycle.opts");if(!options){log("options not found, can not advance slide");return false;}if(num<0||num>=options.elements.length){log("invalid slide index: "+num);return false;}options.nextSlide=num;if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}if(typeof arg2=="string"){options.oneTimeFx=arg2;}go(options.elements,options,1,num>=options.currSlide);return false;}}return options;function checkInstantResume(isPaused,arg2,cont){if(!isPaused&&arg2===true){var options=$(cont).data("cycle.opts");if(!options){log("options not found, can not resume");return false;}if(cont.cycleTimeout){clearTimeout(cont.cycleTimeout);cont.cycleTimeout=0;}go(options.elements,options,1,(!opts.rev&&!opts.backwards));}}}function removeFilter(el,opts){if(!$.support.opacity&&opts.cleartype&&el.style.filter){try{el.style.removeAttribute("filter");}catch(smother){}}}function destroy(opts){if(opts.next){$(opts.next).unbind(opts.prevNextEvent);}if(opts.prev){$(opts.prev).unbind(opts.prevNextEvent);}if(opts.pager||opts.pagerAnchorBuilder){$.each(opts.pagerAnchors||[],function(){this.unbind().remove();});}opts.pagerAnchors=null;if(opts.destroy){opts.destroy(opts);}}function buildOptions($cont,$slides,els,options,o){var opts=$.extend({},$.fn.cycle.defaults,options||{},$.metadata?$cont.metadata():$.meta?$cont.data():{});if(opts.autostop){opts.countdown=opts.autostopCount||els.length;}var cont=$cont[0];$cont.data("cycle.opts",opts);opts.$cont=$cont;opts.stopCount=cont.cycleStop;opts.elements=els;opts.before=opts.before?[opts.before]:[];opts.after=opts.after?[opts.after]:[];opts.after.unshift(function(){opts.busy=0;});if(!$.support.opacity&&opts.cleartype){opts.after.push(function(){removeFilter(this,opts);});}if(opts.continuous){opts.after.push(function(){go(els,opts,0,(!opts.rev&&!opts.backwards));});}saveOriginalOpts(opts);if(!$.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg){clearTypeFix($slides);}if($cont.css("position")=="static"){$cont.css("position","relative");}if(opts.width){$cont.width(opts.width);}if(opts.height&&opts.height!="auto"){$cont.height(opts.height);}if(opts.startingSlide){opts.startingSlide=parseInt(opts.startingSlide);}else{if(opts.backwards){opts.startingSlide=els.length-1;}}if(opts.random){opts.randomMap=[];for(var i=0;i<els.length;i++){opts.randomMap.push(i);}opts.randomMap.sort(function(a,b){return Math.random()-0.5;});opts.randomIndex=1;opts.startingSlide=opts.randomMap[1];}else{if(opts.startingSlide>=els.length){opts.startingSlide=0;}}opts.currSlide=opts.startingSlide||0;var first=opts.startingSlide;$slides.css({position:"absolute",top:0,left:0}).hide().each(function(i){var z;if(opts.backwards){z=first?i<=first?els.length+(i-first):first-i:els.length-i;}else{z=first?i>=first?els.length-(i-first):first-i:els.length-i;}$(this).css("z-index",z);});$(els[first]).css("opacity",1).show();removeFilter(els[first],opts);if(opts.fit&&opts.width){$slides.width(opts.width);}if(opts.fit&&opts.height&&opts.height!="auto"){$slides.height(opts.height);}var reshape=opts.containerResize&&!$cont.innerHeight();if(reshape){var maxw=0,maxh=0;for(var j=0;j<els.length;j++){var $e=$(els[j]),e=$e[0],w=$e.outerWidth(),h=$e.outerHeight();if(!w){w=e.offsetWidth||e.width||$e.attr("width");}if(!h){h=e.offsetHeight||e.height||$e.attr("height");}maxw=w>maxw?w:maxw;maxh=h>maxh?h:maxh;}if(maxw>0&&maxh>0){$cont.css({width:maxw+"px",height:maxh+"px"});}}if(opts.pause){$cont.hover(function(){this.cyclePause++;},function(){this.cyclePause--;});}if(supportMultiTransitions(opts)===false){return false;}var requeue=false;options.requeueAttempts=options.requeueAttempts||0;$slides.each(function(){var $el=$(this);this.cycleH=(opts.fit&&opts.height)?opts.height:($el.height()||this.offsetHeight||this.height||$el.attr("height")||0);this.cycleW=(opts.fit&&opts.width)?opts.width:($el.width()||this.offsetWidth||this.width||$el.attr("width")||0);if($el.is("img")){var loadingIE=($.browser.msie&&this.cycleW==28&&this.cycleH==30&&!this.complete);var loadingFF=($.browser.mozilla&&this.cycleW==34&&this.cycleH==19&&!this.complete);var loadingOp=($.browser.opera&&((this.cycleW==42&&this.cycleH==19)||(this.cycleW==37&&this.cycleH==17))&&!this.complete);var loadingOther=(this.cycleH==0&&this.cycleW==0&&!this.complete);if(loadingIE||loadingFF||loadingOp||loadingOther){if(o.s&&opts.requeueOnImageNotLoaded&&++options.requeueAttempts<100){log(options.requeueAttempts," - img slide not loaded, requeuing slideshow: ",this.src,this.cycleW,this.cycleH);setTimeout(function(){$(o.s,o.c).cycle(options);},opts.requeueTimeout);requeue=true;return false;}else{log("could not determine size of image: "+this.src,this.cycleW,this.cycleH);}}}return true;});if(requeue){return false;}opts.cssBefore=opts.cssBefore||{};opts.animIn=opts.animIn||{};opts.animOut=opts.animOut||{};$slides.not(":eq("+first+")").css(opts.cssBefore);if(opts.cssFirst){$($slides[first]).css(opts.cssFirst);}if(opts.timeout){opts.timeout=parseInt(opts.timeout);if(opts.speed.constructor==String){opts.speed=$.fx.speeds[opts.speed]||parseInt(opts.speed);}if(!opts.sync){opts.speed=opts.speed/2;}var buffer=opts.fx=="shuffle"?500:250;while((opts.timeout-opts.speed)<buffer){opts.timeout+=opts.speed;}}if(opts.easing){opts.easeIn=opts.easeOut=opts.easing;}if(!opts.speedIn){opts.speedIn=opts.speed;}if(!opts.speedOut){opts.speedOut=opts.speed;}opts.slideCount=els.length;opts.currSlide=opts.lastSlide=first;if(opts.random){if(++opts.randomIndex==els.length){opts.randomIndex=0;}opts.nextSlide=opts.randomMap[opts.randomIndex];}else{if(opts.backwards){opts.nextSlide=opts.startingSlide==0?(els.length-1):opts.startingSlide-1;}else{opts.nextSlide=opts.startingSlide>=(els.length-1)?0:opts.startingSlide+1;}}if(!opts.multiFx){var init=$.fn.cycle.transitions[opts.fx];if($.isFunction(init)){init($cont,$slides,opts);}else{if(opts.fx!="custom"&&!opts.multiFx){log("unknown transition: "+opts.fx,"; slideshow terminating");return false;}}}var e0=$slides[first];if(opts.before.length){opts.before[0].apply(e0,[e0,e0,opts,true]);}if(opts.after.length>1){opts.after[1].apply(e0,[e0,e0,opts,true]);}if(opts.next){$(opts.next).bind(opts.prevNextEvent,function(){return advance(opts,opts.rev?-1:1);});}if(opts.prev){$(opts.prev).bind(opts.prevNextEvent,function(){return advance(opts,opts.rev?1:-1);});}if(opts.pager||opts.pagerAnchorBuilder){buildPager(els,opts);}exposeAddSlide(opts,els);return opts;}function saveOriginalOpts(opts){opts.original={before:[],after:[]};opts.original.cssBefore=$.extend({},opts.cssBefore);opts.original.cssAfter=$.extend({},opts.cssAfter);opts.original.animIn=$.extend({},opts.animIn);opts.original.animOut=$.extend({},opts.animOut);$.each(opts.before,function(){opts.original.before.push(this);});$.each(opts.after,function(){opts.original.after.push(this);});}function supportMultiTransitions(opts){var i,tx,txs=$.fn.cycle.transitions;if(opts.fx.indexOf(",")>0){opts.multiFx=true;opts.fxs=opts.fx.replace(/\s*/g,"").split(",");for(i=0;i<opts.fxs.length;i++){var fx=opts.fxs[i];tx=txs[fx];if(!tx||!txs.hasOwnProperty(fx)||!$.isFunction(tx)){log("discarding unknown transition: ",fx);opts.fxs.splice(i,1);i--;}}if(!opts.fxs.length){log("No valid transitions named; slideshow terminating.");return false;}}else{if(opts.fx=="all"){opts.multiFx=true;opts.fxs=[];for(p in txs){tx=txs[p];if(txs.hasOwnProperty(p)&&$.isFunction(tx)){opts.fxs.push(p);}}}}if(opts.multiFx&&opts.randomizeEffects){var r1=Math.floor(Math.random()*20)+30;for(i=0;i<r1;i++){var r2=Math.floor(Math.random()*opts.fxs.length);opts.fxs.push(opts.fxs.splice(r2,1)[0]);}debug("randomized fx sequence: ",opts.fxs);}return true;}function exposeAddSlide(opts,els){opts.addSlide=function(newSlide,prepend){var $s=$(newSlide),s=$s[0];if(!opts.autostopCount){opts.countdown++;}els[prepend?"unshift":"push"](s);if(opts.els){opts.els[prepend?"unshift":"push"](s);}opts.slideCount=els.length;$s.css("position","absolute");$s[prepend?"prependTo":"appendTo"](opts.$cont);if(prepend){opts.currSlide++;opts.nextSlide++;}if(!$.support.opacity&&opts.cleartype&&!opts.cleartypeNoBg){clearTypeFix($s);}if(opts.fit&&opts.width){$s.width(opts.width);}if(opts.fit&&opts.height&&opts.height!="auto"){$slides.height(opts.height);}s.cycleH=(opts.fit&&opts.height)?opts.height:$s.height();s.cycleW=(opts.fit&&opts.width)?opts.width:$s.width();$s.css(opts.cssBefore);if(opts.pager||opts.pagerAnchorBuilder){$.fn.cycle.createPagerAnchor(els.length-1,s,$(opts.pager),els,opts);}if($.isFunction(opts.onAddSlide)){opts.onAddSlide($s);}else{$s.hide();}};}$.fn.cycle.resetState=function(opts,fx){fx=fx||opts.fx;opts.before=[];opts.after=[];opts.cssBefore=$.extend({},opts.original.cssBefore);opts.cssAfter=$.extend({},opts.original.cssAfter);opts.animIn=$.extend({},opts.original.animIn);opts.animOut=$.extend({},opts.original.animOut);opts.fxFn=null;$.each(opts.original.before,function(){opts.before.push(this);});$.each(opts.original.after,function(){opts.after.push(this);});var init=$.fn.cycle.transitions[fx];if($.isFunction(init)){init(opts.$cont,$(opts.elements),opts);}};function go(els,opts,manual,fwd){if(manual&&opts.busy&&opts.manualTrump){debug("manualTrump in go(), stopping active transition");$(els).stop(true,true);opts.busy=false;}if(opts.busy){debug("transition active, ignoring new tx request");return;}var p=opts.$cont[0],curr=els[opts.currSlide],next=els[opts.nextSlide];if(p.cycleStop!=opts.stopCount||p.cycleTimeout===0&&!manual){return;}if(!manual&&!p.cyclePause&&!opts.bounce&&((opts.autostop&&(--opts.countdown<=0))||(opts.nowrap&&!opts.random&&opts.nextSlide<opts.currSlide))){if(opts.end){opts.end(opts);}return;}var changed=false;if((manual||!p.cyclePause)&&(opts.nextSlide!=opts.currSlide)){changed=true;var fx=opts.fx;curr.cycleH=curr.cycleH||$(curr).height();curr.cycleW=curr.cycleW||$(curr).width();next.cycleH=next.cycleH||$(next).height();next.cycleW=next.cycleW||$(next).width();if(opts.multiFx){if(opts.lastFx==undefined||++opts.lastFx>=opts.fxs.length){opts.lastFx=0;}fx=opts.fxs[opts.lastFx];opts.currFx=fx;}if(opts.oneTimeFx){fx=opts.oneTimeFx;opts.oneTimeFx=null;}$.fn.cycle.resetState(opts,fx);if(opts.before.length){$.each(opts.before,function(i,o){if(p.cycleStop!=opts.stopCount){return;}o.apply(next,[curr,next,opts,fwd]);});}var after=function(){$.each(opts.after,function(i,o){if(p.cycleStop!=opts.stopCount){return;}o.apply(next,[curr,next,opts,fwd]);});};debug("tx firing; currSlide: "+opts.currSlide+"; nextSlide: "+opts.nextSlide);opts.busy=1;if(opts.fxFn){opts.fxFn(curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}else{if($.isFunction($.fn.cycle[opts.fx])){$.fn.cycle[opts.fx](curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}else{$.fn.cycle.custom(curr,next,opts,after,fwd,manual&&opts.fastOnEvent);}}}if(changed||opts.nextSlide==opts.currSlide){opts.lastSlide=opts.currSlide;if(opts.random){opts.currSlide=opts.nextSlide;if(++opts.randomIndex==els.length){opts.randomIndex=0;}opts.nextSlide=opts.randomMap[opts.randomIndex];if(opts.nextSlide==opts.currSlide){opts.nextSlide=(opts.currSlide==opts.slideCount-1)?0:opts.currSlide+1;}}else{if(opts.backwards){var roll=(opts.nextSlide-1)<0;if(roll&&opts.bounce){opts.backwards=!opts.backwards;opts.nextSlide=1;opts.currSlide=0;}else{opts.nextSlide=roll?(els.length-1):opts.nextSlide-1;opts.currSlide=roll?0:opts.nextSlide+1;}}else{var roll=(opts.nextSlide+1)==els.length;if(roll&&opts.bounce){opts.backwards=!opts.backwards;opts.nextSlide=els.length-2;opts.currSlide=els.length-1;}else{opts.nextSlide=roll?0:opts.nextSlide+1;opts.currSlide=roll?els.length-1:opts.nextSlide-1;}}}}if(changed&&opts.pager){opts.updateActivePagerLink(opts.pager,opts.currSlide,opts.activePagerClass);}var ms=0;if(opts.timeout&&!opts.continuous){ms=getTimeout(els[opts.currSlide],els[opts.nextSlide],opts,fwd);}else{if(opts.continuous&&p.cyclePause){ms=10;}}if(ms>0){p.cycleTimeout=setTimeout(function(){go(els,opts,0,(!opts.rev&&!opts.backwards));},ms);}}$.fn.cycle.updateActivePagerLink=function(pager,currSlide,clsName){$(pager).each(function(){$(this).children().removeClass(clsName).eq(currSlide).addClass(clsName);});};function getTimeout(curr,next,opts,fwd){if(opts.timeoutFn){var t=opts.timeoutFn.call(curr,curr,next,opts,fwd);while((t-opts.speed)<250){t+=opts.speed;}debug("calculated timeout: "+t+"; speed: "+opts.speed);if(t!==false){return t;}}return opts.timeout;}$.fn.cycle.next=function(opts){advance(opts,opts.rev?-1:1);};$.fn.cycle.prev=function(opts){advance(opts,opts.rev?1:-1);};function advance(opts,val){var els=opts.elements;var p=opts.$cont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}if(opts.random&&val<0){opts.randomIndex--;if(--opts.randomIndex==-2){opts.randomIndex=els.length-2;}else{if(opts.randomIndex==-1){opts.randomIndex=els.length-1;}}opts.nextSlide=opts.randomMap[opts.randomIndex];}else{if(opts.random){opts.nextSlide=opts.randomMap[opts.randomIndex];}else{opts.nextSlide=opts.currSlide+val;if(opts.nextSlide<0){if(opts.nowrap){return false;}opts.nextSlide=els.length-1;}else{if(opts.nextSlide>=els.length){if(opts.nowrap){return false;}opts.nextSlide=0;}}}}var cb=opts.onPrevNextEvent||opts.prevNextClick;if($.isFunction(cb)){cb(val>0,opts.nextSlide,els[opts.nextSlide]);}go(els,opts,1,val>=0);return false;}function buildPager(els,opts){var $p=$(opts.pager);$.each(els,function(i,o){$.fn.cycle.createPagerAnchor(i,o,$p,els,opts);});opts.updateActivePagerLink(opts.pager,opts.startingSlide,opts.activePagerClass);}$.fn.cycle.createPagerAnchor=function(i,el,$p,els,opts){var a;if($.isFunction(opts.pagerAnchorBuilder)){a=opts.pagerAnchorBuilder(i,el);debug("pagerAnchorBuilder("+i+", el) returned: "+a);}else{a='<a href="#">'+(i+1)+"</a>";}if(!a){return;}var $a=$(a);if($a.parents("body").length===0){var arr=[];if($p.length>1){$p.each(function(){var $clone=$a.clone(true);$(this).append($clone);arr.push($clone[0]);});$a=$(arr);}else{$a.appendTo($p);}}opts.pagerAnchors=opts.pagerAnchors||[];opts.pagerAnchors.push($a);$a.bind(opts.pagerEvent,function(e){e.preventDefault();opts.nextSlide=i;var p=opts.$cont[0],timeout=p.cycleTimeout;if(timeout){clearTimeout(timeout);p.cycleTimeout=0;}var cb=opts.onPagerEvent||opts.pagerClick;if($.isFunction(cb)){cb(opts.nextSlide,els[opts.nextSlide]);}go(els,opts,1,opts.currSlide<i);});if(!/^click/.test(opts.pagerEvent)&&!opts.allowPagerClickBubble){$a.bind("click.cycle",function(){return false;});}if(opts.pauseOnPagerHover){$a.hover(function(){opts.$cont[0].cyclePause++;},function(){opts.$cont[0].cyclePause--;});}};$.fn.cycle.hopsFromLast=function(opts,fwd){var hops,l=opts.lastSlide,c=opts.currSlide;if(fwd){hops=c>l?c-l:opts.slideCount-l;}else{hops=c<l?l-c:l+opts.slideCount-c;}return hops;};function clearTypeFix($slides){debug("applying clearType background-color hack");function hex(s){s=parseInt(s).toString(16);return s.length<2?"0"+s:s;}function getBg(e){for(;e&&e.nodeName.toLowerCase()!="html";e=e.parentNode){var v=$.css(e,"background-color");if(v.indexOf("rgb")>=0){var rgb=v.match(/\d+/g);return"#"+hex(rgb[0])+hex(rgb[1])+hex(rgb[2]);}if(v&&v!="transparent"){return v;}}return"#ffffff";}$slides.each(function(){$(this).css("background-color",getBg(this));});}$.fn.cycle.commonReset=function(curr,next,opts,w,h,rev){$(opts.elements).not(curr).hide();opts.cssBefore.opacity=1;opts.cssBefore.display="block";if(w!==false&&next.cycleW>0){opts.cssBefore.width=next.cycleW;}if(h!==false&&next.cycleH>0){opts.cssBefore.height=next.cycleH;}opts.cssAfter=opts.cssAfter||{};opts.cssAfter.display="none";$(curr).css("zIndex",opts.slideCount+(rev===true?1:0));$(next).css("zIndex",opts.slideCount+(rev===true?0:1));};$.fn.cycle.custom=function(curr,next,opts,cb,fwd,speedOverride){var $l=$(curr),$n=$(next);var speedIn=opts.speedIn,speedOut=opts.speedOut,easeIn=opts.easeIn,easeOut=opts.easeOut;$n.css(opts.cssBefore);if(speedOverride){if(typeof speedOverride=="number"){speedIn=speedOut=speedOverride;}else{speedIn=speedOut=1;}easeIn=easeOut=null;}var fn=function(){$n.animate(opts.animIn,speedIn,easeIn,cb);};$l.animate(opts.animOut,speedOut,easeOut,function(){if(opts.cssAfter){$l.css(opts.cssAfter);}if(!opts.sync){fn();}});if(opts.sync){fn();}};$.fn.cycle.transitions={fade:function($cont,$slides,opts){$slides.not(":eq("+opts.currSlide+")").css("opacity",0);opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.opacity=0;});opts.animIn={opacity:1};opts.animOut={opacity:0};opts.cssBefore={top:0,left:0};}};$.fn.cycle.ver=function(){return ver;};$.fn.cycle.defaults={fx:"fade",timeout:4000,timeoutFn:null,continuous:0,speed:1000,speedIn:null,speedOut:null,next:null,prev:null,onPrevNextEvent:null,prevNextEvent:"click.cycle",pager:null,onPagerEvent:null,pagerEvent:"click.cycle",allowPagerClickBubble:false,pagerAnchorBuilder:null,before:null,after:null,end:null,easing:null,easeIn:null,easeOut:null,shuffle:null,animIn:null,animOut:null,cssBefore:null,cssAfter:null,fxFn:null,height:"auto",startingSlide:0,sync:1,random:0,fit:0,containerResize:1,pause:0,pauseOnPagerHover:0,autostop:0,autostopCount:0,delay:0,slideExpr:null,cleartype:!$.support.opacity,cleartypeNoBg:false,nowrap:0,fastOnEvent:0,randomizeEffects:1,rev:0,manualTrump:true,requeueOnImageNotLoaded:true,requeueTimeout:250,activePagerClass:"activeSlide",updateActivePagerLink:null,backwards:false};})(jQuery);
/*
 * jQuery Cycle Plugin Transition Definitions
 * This script is a plugin for the jQuery Cycle Plugin
 * Examples and documentation at: http://malsup.com/jquery/cycle/
 * Copyright (c) 2007-2010 M. Alsup
 * Version:	 2.72
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function($){$.fn.cycle.transitions.none=function($cont,$slides,opts){opts.fxFn=function(curr,next,opts,after){$(next).show();$(curr).hide();after();};};$.fn.cycle.transitions.scrollUp=function($cont,$slides,opts){$cont.css("overflow","hidden");opts.before.push($.fn.cycle.commonReset);var h=$cont.height();opts.cssBefore={top:h,left:0};opts.cssFirst={top:0};opts.animIn={top:0};opts.animOut={top:-h};};$.fn.cycle.transitions.scrollDown=function($cont,$slides,opts){$cont.css("overflow","hidden");opts.before.push($.fn.cycle.commonReset);var h=$cont.height();opts.cssFirst={top:0};opts.cssBefore={top:-h,left:0};opts.animIn={top:0};opts.animOut={top:h};};$.fn.cycle.transitions.scrollLeft=function($cont,$slides,opts){$cont.css("overflow","hidden");opts.before.push($.fn.cycle.commonReset);var w=$cont.width();opts.cssFirst={left:0};opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:0-w};};$.fn.cycle.transitions.scrollRight=function($cont,$slides,opts){$cont.css("overflow","hidden");opts.before.push($.fn.cycle.commonReset);var w=$cont.width();opts.cssFirst={left:0};opts.cssBefore={left:-w,top:0};opts.animIn={left:0};opts.animOut={left:w};};$.fn.cycle.transitions.scrollHorz=function($cont,$slides,opts){$cont.css("overflow","hidden").width();opts.before.push(function(curr,next,opts,fwd){$.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.left=fwd?(next.cycleW-1):(1-next.cycleW);opts.animOut.left=fwd?-curr.cycleW:curr.cycleW;});opts.cssFirst={left:0};opts.cssBefore={top:0};opts.animIn={left:0};opts.animOut={top:0};};$.fn.cycle.transitions.scrollVert=function($cont,$slides,opts){$cont.css("overflow","hidden");opts.before.push(function(curr,next,opts,fwd){$.fn.cycle.commonReset(curr,next,opts);opts.cssBefore.top=fwd?(1-next.cycleH):(next.cycleH-1);opts.animOut.top=fwd?curr.cycleH:-curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0};opts.animIn={top:0};opts.animOut={left:0};};$.fn.cycle.transitions.slideX=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$(opts.elements).not(curr).hide();$.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;});opts.cssBefore={left:0,top:0,width:0};opts.animIn={width:"show"};opts.animOut={width:0};};$.fn.cycle.transitions.slideY=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$(opts.elements).not(curr).hide();$.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;});opts.cssBefore={left:0,top:0,height:0};opts.animIn={height:"show"};opts.animOut={height:0};};$.fn.cycle.transitions.shuffle=function($cont,$slides,opts){var i,w=$cont.css("overflow","visible").width();$slides.css({left:0,top:0});opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,true,true);});if(!opts.speedAdjusted){opts.speed=opts.speed/2;opts.speedAdjusted=true;}opts.random=0;opts.shuffle=opts.shuffle||{left:-w,top:15};opts.els=[];for(i=0;i<$slides.length;i++){opts.els.push($slides[i]);}for(i=0;i<opts.currSlide;i++){opts.els.push(opts.els.shift());}opts.fxFn=function(curr,next,opts,cb,fwd){var $el=fwd?$(curr):$(next);$(next).css(opts.cssBefore);var count=opts.slideCount;$el.animate(opts.shuffle,opts.speedIn,opts.easeIn,function(){var hops=$.fn.cycle.hopsFromLast(opts,fwd);for(var k=0;k<hops;k++){fwd?opts.els.push(opts.els.shift()):opts.els.unshift(opts.els.pop());}if(fwd){for(var i=0,len=opts.els.length;i<len;i++){$(opts.els[i]).css("z-index",len-i+count);}}else{var z=$(curr).css("z-index");$el.css("z-index",parseInt(z)+1+count);}$el.animate({left:0,top:0},opts.speedOut,opts.easeOut,function(){$(fwd?this:curr).hide();if(cb){cb();}});});};opts.cssBefore={display:"block",opacity:1,top:0,left:0};};$.fn.cycle.transitions.turnUp=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=next.cycleH;opts.animIn.height=next.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,height:0};opts.animIn={top:0};opts.animOut={height:0};};$.fn.cycle.transitions.turnDown=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,false);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssFirst={top:0};opts.cssBefore={left:0,top:0,height:0};opts.animOut={height:0};};$.fn.cycle.transitions.turnLeft=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=next.cycleW;opts.animIn.width=next.cycleW;});opts.cssBefore={top:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};$.fn.cycle.transitions.turnRight=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,true);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={top:0,left:0,width:0};opts.animIn={left:0};opts.animOut={width:0};};$.fn.cycle.transitions.zoom=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,false,true);opts.cssBefore.top=next.cycleH/2;opts.cssBefore.left=next.cycleW/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};opts.animOut={width:0,height:0,top:curr.cycleH/2,left:curr.cycleW/2};});opts.cssFirst={top:0,left:0};opts.cssBefore={width:0,height:0};};$.fn.cycle.transitions.fadeZoom=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,false);opts.cssBefore.left=next.cycleW/2;opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,left:0,width:next.cycleW,height:next.cycleH};});opts.cssBefore={width:0,height:0};opts.animOut={opacity:0};};$.fn.cycle.transitions.blindX=function($cont,$slides,opts){var w=$cont.css("overflow","hidden").width();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts);opts.animIn.width=next.cycleW;opts.animOut.left=curr.cycleW;});opts.cssBefore={left:w,top:0};opts.animIn={left:0};opts.animOut={left:w};};$.fn.cycle.transitions.blindY=function($cont,$slides,opts){var h=$cont.css("overflow","hidden").height();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:0};opts.animIn={top:0};opts.animOut={top:h};};$.fn.cycle.transitions.blindZ=function($cont,$slides,opts){var h=$cont.css("overflow","hidden").height();var w=$cont.width();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts);opts.animIn.height=next.cycleH;opts.animOut.top=curr.cycleH;});opts.cssBefore={top:h,left:w};opts.animIn={top:0,left:0};opts.animOut={top:h,left:w};};$.fn.cycle.transitions.growX=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,true);opts.cssBefore.left=this.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:0};});opts.cssBefore={width:0,top:0};};$.fn.cycle.transitions.growY=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,false);opts.cssBefore.top=this.cycleH/2;opts.animIn={top:0,height:this.cycleH};opts.animOut={top:0};});opts.cssBefore={height:0,left:0};};$.fn.cycle.transitions.curtainX=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,false,true,true);opts.cssBefore.left=next.cycleW/2;opts.animIn={left:0,width:this.cycleW};opts.animOut={left:curr.cycleW/2,width:0};});opts.cssBefore={top:0,width:0};};$.fn.cycle.transitions.curtainY=function($cont,$slides,opts){opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,false,true);opts.cssBefore.top=next.cycleH/2;opts.animIn={top:0,height:next.cycleH};opts.animOut={top:curr.cycleH/2,height:0};});opts.cssBefore={left:0,height:0};};$.fn.cycle.transitions.cover=function($cont,$slides,opts){var d=opts.direction||"left";var w=$cont.css("overflow","hidden").width();var h=$cont.height();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts);if(d=="right"){opts.cssBefore.left=-w;}else{if(d=="up"){opts.cssBefore.top=h;}else{if(d=="down"){opts.cssBefore.top=-h;}else{opts.cssBefore.left=w;}}}});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};$.fn.cycle.transitions.uncover=function($cont,$slides,opts){var d=opts.direction||"left";var w=$cont.css("overflow","hidden").width();var h=$cont.height();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,true,true);if(d=="right"){opts.animOut.left=w;}else{if(d=="up"){opts.animOut.top=-h;}else{if(d=="down"){opts.animOut.top=h;}else{opts.animOut.left=-w;}}}});opts.animIn={left:0,top:0};opts.animOut={opacity:1};opts.cssBefore={top:0,left:0};};$.fn.cycle.transitions.toss=function($cont,$slides,opts){var w=$cont.css("overflow","visible").width();var h=$cont.height();opts.before.push(function(curr,next,opts){$.fn.cycle.commonReset(curr,next,opts,true,true,true);if(!opts.animOut.left&&!opts.animOut.top){opts.animOut={left:w*2,top:-h/2,opacity:0};}else{opts.animOut.opacity=0;}});opts.cssBefore={left:0,top:0};opts.animIn={left:0};};$.fn.cycle.transitions.wipe=function($cont,$slides,opts){var w=$cont.css("overflow","hidden").width();var h=$cont.height();opts.cssBefore=opts.cssBefore||{};var clip;if(opts.clip){if(/l2r/.test(opts.clip)){clip="rect(0px 0px "+h+"px 0px)";}else{if(/r2l/.test(opts.clip)){clip="rect(0px "+w+"px "+h+"px "+w+"px)";}else{if(/t2b/.test(opts.clip)){clip="rect(0px "+w+"px 0px 0px)";}else{if(/b2t/.test(opts.clip)){clip="rect("+h+"px "+w+"px "+h+"px 0px)";}else{if(/zoom/.test(opts.clip)){var top=parseInt(h/2);var left=parseInt(w/2);clip="rect("+top+"px "+left+"px "+top+"px "+left+"px)";}}}}}}opts.cssBefore.clip=opts.cssBefore.clip||clip||"rect(0px 0px 0px 0px)";var d=opts.cssBefore.clip.match(/(\d+)/g);var t=parseInt(d[0]),r=parseInt(d[1]),b=parseInt(d[2]),l=parseInt(d[3]);opts.before.push(function(curr,next,opts){if(curr==next){return;}var $curr=$(curr),$next=$(next);$.fn.cycle.commonReset(curr,next,opts,true,true,false);opts.cssAfter.display="block";var step=1,count=parseInt((opts.speedIn/13))-1;(function f(){var tt=t?t-parseInt(step*(t/count)):0;var ll=l?l-parseInt(step*(l/count)):0;var bb=b<h?b+parseInt(step*((h-b)/count||1)):h;var rr=r<w?r+parseInt(step*((w-r)/count||1)):w;$next.css({clip:"rect("+tt+"px "+rr+"px "+bb+"px "+ll+"px)"});(step++<=count)?setTimeout(f,13):$curr.css("display","none");})();});opts.cssBefore={display:"block",opacity:1,top:0,left:0};opts.animIn={left:0};opts.animOut={left:0};};})(jQuery);





/*
 * FancyBox - jQuery Plugin
 * Simple and fancy lightbox alternative
 *
 * Examples and documentation at: http://fancybox.net
 * 
 * Copyright (c) 2008 - 2010 Janis Skarnelis
 * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
 * 
 * Version: 1.3.2 (20/10/2010)
 * Requires: jQuery v1.3+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

;(function(a){var m,t,u,f,D,h,E,n,z,A,q=0,e={},o=[],p=0,c={},l=[],I=null,v=new Image,J=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,W=/[^\.]\.(swf)\s*$/i,K,L=1,y=0,s="",r,j,i=false,B=a.extend(a("<div/>")[0],{prop:0}),M=a.browser.msie&&a.browser.version<7&&!window.XMLHttpRequest,N=function(){t.hide();v.onerror=v.onload=null;I&&I.abort();m.empty()},O=function(){if(false===e.onError(o,q,e)){t.hide();i=false}else{e.titleShow=false;e.width="auto";e.height="auto";m.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>');
F()}},H=function(){var b=o[q],d,g,k,C,P,w;N();e=a.extend({},a.fn.fancybox.defaults,typeof a(b).data("fancybox")=="undefined"?e:a(b).data("fancybox"));w=e.onStart(o,q,e);if(w===false)i=false;else{if(typeof w=="object")e=a.extend(e,w);k=e.title||(b.nodeName?a(b).attr("title"):b.title)||"";if(b.nodeName&&!e.orig)e.orig=a(b).children("img:first").length?a(b).children("img:first"):a(b);if(k===""&&e.orig&&e.titleFromAlt)k=e.orig.attr("alt");d=e.href||(b.nodeName?a(b).attr("href"):b.href)||null;if(/^(?:javascript)/i.test(d)||
d=="#")d=null;if(e.type){g=e.type;if(!d)d=e.content}else if(e.content)g="html";else if(d)g=d.match(J)?"image":d.match(W)?"swf":a(b).hasClass("iframe")?"iframe":d.indexOf("#")===0?"inline":"ajax";if(g){if(g=="inline"){b=d.substr(d.indexOf("#"));g=a(b).length>0?"inline":"ajax"}e.type=g;e.href=d;e.title=k;if(e.autoDimensions&&e.type!=="iframe"&&e.type!=="swf"){e.width="auto";e.height="auto"}if(e.modal){e.overlayShow=true;e.hideOnOverlayClick=false;e.hideOnContentClick=false;e.enableEscapeButton=false;
e.showCloseButton=false}e.padding=parseInt(e.padding,10);e.margin=parseInt(e.margin,10);m.css("padding",e.padding+e.margin);a(".fancybox-inline-tmp").unbind("fancybox-cancel").bind("fancybox-change",function(){a(this).replaceWith(h.children())});switch(g){case "html":m.html(e.content);F();break;case "inline":if(a(b).parent().is("#fancybox-content")===true){i=false;break}a('<div class="fancybox-inline-tmp" />').hide().insertBefore(a(b)).bind("fancybox-cleanup",function(){a(this).replaceWith(h.children())}).bind("fancybox-cancel",
function(){a(this).replaceWith(m.children())});a(b).appendTo(m);F();break;case "image":i=false;a.fancybox.showActivity();v=new Image;v.onerror=function(){O()};v.onload=function(){i=true;v.onerror=v.onload=null;e.width=v.width;e.height=v.height;a("<img />").attr({id:"fancybox-img",src:v.src,alt:e.title}).appendTo(m);Q()};v.src=d;break;case "swf":C='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+e.width+'" height="'+e.height+'"><param name="movie" value="'+d+'"></param>';P="";
a.each(e.swf,function(x,G){C+='<param name="'+x+'" value="'+G+'"></param>';P+=" "+x+'="'+G+'"'});C+='<embed src="'+d+'" type="application/x-shockwave-flash" width="'+e.width+'" height="'+e.height+'"'+P+"></embed></object>";m.html(C);F();break;case "ajax":i=false;a.fancybox.showActivity();e.ajax.win=e.ajax.success;I=a.ajax(a.extend({},e.ajax,{url:d,data:e.ajax.data||{},error:function(x){x.status>0&&O()},success:function(x,G,U){if(U.status==200){if(typeof e.ajax.win=="function"){w=e.ajax.win(d,x,G,
U);if(w===false){t.hide();return}else if(typeof w=="string"||typeof w=="object")x=w}m.html(x);F()}}}));break;case "iframe":Q()}}else O()}},F=function(){m.width(e.width);m.height(e.height);if(e.width=="auto")e.width=m.width();if(e.height=="auto")e.height=m.height();Q()},Q=function(){var b,d;t.hide();if(f.is(":visible")&&false===c.onCleanup(l,p,c)){a.event.trigger("fancybox-cancel");i=false}else{i=true;a(h.add(u)).unbind();a(window).unbind("resize.fb scroll.fb");a(document).unbind("keydown.fb");f.is(":visible")&&
c.titlePosition!=="outside"&&f.css("height",f.height());l=o;p=q;c=e;if(c.overlayShow){u.css({"background-color":c.overlayColor,opacity:c.overlayOpacity,cursor:c.hideOnOverlayClick?"pointer":"auto",height:a(document).height()});if(!u.is(":visible")){M&&a("select:not(#fancybox-tmp select)").filter(function(){return this.style.visibility!=="hidden"}).css({visibility:"hidden"}).one("fancybox-cleanup",function(){this.style.visibility="inherit"});u.show()}}else u.hide();h.get(0).scrollTop=0;h.get(0).scrollLeft=
0;j=X();s=c.title||"";y=0;n.empty().removeAttr("style").removeClass();if(c.titleShow!==false){if(a.isFunction(c.titleFormat))b=c.titleFormat(s,l,p,c);else b=s&&s.length?c.titlePosition=="float"?'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+s+'</td><td id="fancybox-title-float-right"></td></tr></table>':'<div id="fancybox-title-'+c.titlePosition+'">'+s+"</div>":false;s=b;if(!(!s||s==="")){n.addClass("fancybox-title-"+
c.titlePosition).html(s).appendTo("body").show();switch(c.titlePosition){case "inside":n.css({width:j.width-c.padding*2,marginLeft:c.padding,marginRight:c.padding});y=n.outerHeight(true);n.appendTo(D);j.height+=y;break;case "over":n.css({marginLeft:c.padding,width:j.width-c.padding*2,bottom:c.padding}).appendTo(D);break;case "float":n.css("left",parseInt((n.width()-j.width-40)/2,10)*-1).appendTo(f);break;default:n.css({width:j.width-c.padding*2,paddingLeft:c.padding,paddingRight:c.padding}).appendTo(f)}}}n.hide();
if(f.is(":visible")){a(E.add(z).add(A)).hide();b=f.position();r={top:b.top,left:b.left,width:f.width(),height:f.height()};d=r.width==j.width&&r.height==j.height;h.fadeTo(c.changeFade,0.3,function(){var g=function(){h.html(m.contents()).fadeTo(c.changeFade,1,R)};a.event.trigger("fancybox-change");h.empty().removeAttr("filter").css({"border-width":c.padding,width:j.width-c.padding*2,height:c.type=="image"||c.type=="swf"||c.type=="iframe"?j.height-y-c.padding*2:"auto"});if(d)g();else{B.prop=0;a(B).animate({prop:1},
{duration:c.changeSpeed,easing:c.easingChange,step:S,complete:g})}})}else{f.removeAttr("style");h.css("border-width",c.padding);if(c.transitionIn=="elastic"){r=V();h.html(m.contents());f.show();if(c.opacity)j.opacity=0;B.prop=0;a(B).animate({prop:1},{duration:c.speedIn,easing:c.easingIn,step:S,complete:R})}else{c.titlePosition=="inside"&&y>0&&n.show();h.css({width:j.width-c.padding*2,height:c.type=="image"||c.type=="swf"||c.type=="iframe"?j.height-y-c.padding*2:"auto"}).html(m.contents());f.css(j).fadeIn(c.transitionIn==
"none"?0:c.fadeIn,R)}}}},Y=function(){if(c.enableEscapeButton||c.enableKeyboardNav)a(document).bind("keydown.fb",function(b){if(b.keyCode==27&&c.enableEscapeButton){b.preventDefault();a.fancybox.close()}else if((b.keyCode==37||b.keyCode==39)&&c.enableKeyboardNav&&b.target.tagName!=="INPUT"&&b.target.tagName!=="TEXTAREA"&&b.target.tagName!=="SELECT"){b.preventDefault();a.fancybox[b.keyCode==37?"prev":"next"]()}});if(c.showNavArrows){if(c.cyclic&&l.length>1||p!==0)z.show();if(c.cyclic&&l.length>1||
p!=l.length-1)A.show()}else{z.hide();A.hide()}},R=function(){if(!a.support.opacity){h.get(0).style.removeAttribute("filter");f.get(0).style.removeAttribute("filter")}f.css("height","auto");c.type!=="image"&&c.type!=="swf"&&c.type!=="iframe"&&h.css("height","auto");s&&s.length&&n.show();c.showCloseButton&&E.show();Y();c.hideOnContentClick&&h.bind("click",a.fancybox.close);c.hideOnOverlayClick&&u.bind("click",a.fancybox.close);a(window).bind("resize.fb",a.fancybox.resize);c.centerOnScroll&&a(window).bind("scroll.fb",
a.fancybox.center);if(c.type=="iframe")a('<iframe id="fancybox-frame" name="fancybox-frame'+(new Date).getTime()+'" frameborder="0" hspace="0" '+(a.browser.msie?'allowtransparency="true""':"")+' scrolling="'+e.scrolling+'" src="'+c.href+'"></iframe>').appendTo(h);f.show();i=false;a.fancybox.center();c.onComplete(l,p,c);var b,d;if(l.length-1>p){b=l[p+1].href;if(typeof b!=="undefined"&&b.match(J)){d=new Image;d.src=b}}if(p>0){b=l[p-1].href;if(typeof b!=="undefined"&&b.match(J)){d=new Image;d.src=b}}},
S=function(b){var d={width:parseInt(r.width+(j.width-r.width)*b,10),height:parseInt(r.height+(j.height-r.height)*b,10),top:parseInt(r.top+(j.top-r.top)*b,10),left:parseInt(r.left+(j.left-r.left)*b,10)};if(typeof j.opacity!=="undefined")d.opacity=b<0.5?0.5:b;f.css(d);h.css({width:d.width-c.padding*2,height:d.height-y*b-c.padding*2})},T=function(){return[a(window).width()-c.margin*2,a(window).height()-c.margin*2,a(document).scrollLeft()+c.margin,a(document).scrollTop()+c.margin]},X=function(){var b=
T(),d={},g=c.autoScale,k=c.padding*2;d.width=c.width.toString().indexOf("%")>-1?parseInt(b[0]*parseFloat(c.width)/100,10):c.width+k;d.height=c.height.toString().indexOf("%")>-1?parseInt(b[1]*parseFloat(c.height)/100,10):c.height+k;if(g&&(d.width>b[0]||d.height>b[1]))if(e.type=="image"||e.type=="swf"){g=c.width/c.height;if(d.width>b[0]){d.width=b[0];d.height=parseInt((d.width-k)/g+k,10)}if(d.height>b[1]){d.height=b[1];d.width=parseInt((d.height-k)*g+k,10)}}else{d.width=Math.min(d.width,b[0]);d.height=
Math.min(d.height,b[1])}d.top=parseInt(Math.max(b[3]-20,b[3]+(b[1]-d.height-40)*0.5),10);d.left=parseInt(Math.max(b[2]-20,b[2]+(b[0]-d.width-40)*0.5),10);return d},V=function(){var b=e.orig?a(e.orig):false,d={};if(b&&b.length){d=b.offset();d.top+=parseInt(b.css("paddingTop"),10)||0;d.left+=parseInt(b.css("paddingLeft"),10)||0;d.top+=parseInt(b.css("border-top-width"),10)||0;d.left+=parseInt(b.css("border-left-width"),10)||0;d.width=b.width();d.height=b.height();d={width:d.width+c.padding*2,height:d.height+
c.padding*2,top:d.top-c.padding-20,left:d.left-c.padding-20}}else{b=T();d={width:c.padding*2,height:c.padding*2,top:parseInt(b[3]+b[1]*0.5,10),left:parseInt(b[2]+b[0]*0.5,10)}}return d},Z=function(){if(t.is(":visible")){a("div",t).css("top",L*-40+"px");L=(L+1)%12}else clearInterval(K)};a.fn.fancybox=function(b){if(!a(this).length)return this;a(this).data("fancybox",a.extend({},b,a.metadata?a(this).metadata():{})).unbind("click.fb").bind("click.fb",function(d){d.preventDefault();if(!i){i=true;a(this).blur();
o=[];q=0;d=a(this).attr("rel")||"";if(!d||d==""||d==="nofollow")o.push(this);else{o=a("a[rel="+d+"], area[rel="+d+"]");q=o.index(this)}H()}});return this};a.fancybox=function(b,d){var g;if(!i){i=true;g=typeof d!=="undefined"?d:{};o=[];q=parseInt(g.index,10)||0;if(a.isArray(b)){for(var k=0,C=b.length;k<C;k++)if(typeof b[k]=="object")a(b[k]).data("fancybox",a.extend({},g,b[k]));else b[k]=a({}).data("fancybox",a.extend({content:b[k]},g));o=jQuery.merge(o,b)}else{if(typeof b=="object")a(b).data("fancybox",
a.extend({},g,b));else b=a({}).data("fancybox",a.extend({content:b},g));o.push(b)}if(q>o.length||q<0)q=0;H()}};a.fancybox.showActivity=function(){clearInterval(K);t.show();K=setInterval(Z,66)};a.fancybox.hideActivity=function(){t.hide()};a.fancybox.next=function(){return a.fancybox.pos(p+1)};a.fancybox.prev=function(){return a.fancybox.pos(p-1)};a.fancybox.pos=function(b){if(!i){b=parseInt(b);o=l;if(b>-1&&b<l.length){q=b;H()}else if(c.cyclic&&l.length>1){q=b>=l.length?0:l.length-1;H()}}};a.fancybox.cancel=
function(){if(!i){i=true;a.event.trigger("fancybox-cancel");N();e.onCancel(o,q,e);i=false}};a.fancybox.close=function(){function b(){u.fadeOut("fast");n.empty().hide();f.hide();a.event.trigger("fancybox-cleanup");h.empty();c.onClosed(l,p,c);l=e=[];p=q=0;c=e={};i=false}if(!(i||f.is(":hidden"))){i=true;if(c&&false===c.onCleanup(l,p,c))i=false;else{N();a(E.add(z).add(A)).hide();a(h.add(u)).unbind();a(window).unbind("resize.fb scroll.fb");a(document).unbind("keydown.fb");h.find("iframe").attr("src",M&&
/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank");c.titlePosition!=="inside"&&n.empty();f.stop();if(c.transitionOut=="elastic"){r=V();var d=f.position();j={top:d.top,left:d.left,width:f.width(),height:f.height()};if(c.opacity)j.opacity=1;n.empty().hide();B.prop=1;a(B).animate({prop:0},{duration:c.speedOut,easing:c.easingOut,step:S,complete:b})}else f.fadeOut(c.transitionOut=="none"?0:c.speedOut,b)}}};a.fancybox.resize=function(){u.is(":visible")&&u.css("height",a(document).height());
a.fancybox.center(true)};a.fancybox.center=function(b){var d,g;if(!i){g=b===true?1:0;d=T();!g&&(f.width()>d[0]||f.height()>d[1])||f.stop().animate({top:parseInt(Math.max(d[3]-20,d[3]+(d[1]-h.height()-40)*0.5-c.padding)),left:parseInt(Math.max(d[2]-20,d[2]+(d[0]-h.width()-40)*0.5-c.padding))},typeof b=="number"?b:200)}};a.fancybox.init=function(){if(!a("#fancybox-wrap").length){a("body").append(m=a('<div id="fancybox-tmp"></div>'),t=a('<div id="fancybox-loading"><div></div></div>'),u=a('<div id="fancybox-overlay"></div>'),
f=a('<div id="fancybox-wrap"></div>'));D=a('<div id="fancybox-outer"></div>').append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>').appendTo(f);
D.append(h=a('<div id="fancybox-content"></div>'),E=a('<a id="fancybox-close"></a>'),n=a('<div id="fancybox-title"></div>'),z=a('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),A=a('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>'));E.click(a.fancybox.close);t.click(a.fancybox.cancel);z.click(function(b){b.preventDefault();a.fancybox.prev()});A.click(function(b){b.preventDefault();a.fancybox.next()});
a.fn.mousewheel&&f.bind("mousewheel.fb",function(b,d){b.preventDefault();a.fancybox[d>0?"prev":"next"]()});a.support.opacity||f.addClass("fancybox-ie");if(M){t.addClass("fancybox-ie6");f.addClass("fancybox-ie6");a('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank")+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(D)}}};a.fn.fancybox.defaults={padding:10,margin:40,opacity:false,modal:false,cyclic:false,
scrolling:"auto",width:560,height:340,autoScale:true,autoDimensions:true,centerOnScroll:false,ajax:{},swf:{wmode:"transparent"},hideOnOverlayClick:true,hideOnContentClick:false,overlayShow:true,overlayOpacity:0.7,overlayColor:"#777",titleShow:true,titlePosition:"float",titleFormat:null,titleFromAlt:false,transitionIn:"fade",transitionOut:"fade",speedIn:300,speedOut:300,changeSpeed:300,changeFade:"fast",easingIn:"swing",easingOut:"swing",showCloseButton:true,showNavArrows:true,enableEscapeButton:true,
enableKeyboardNav:true,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}};a(document).ready(function(){a.fancybox.init()})})(jQuery);







/* Scrollpane */

(function(A){A.jScrollPane={active:[]};A.fn.jScrollPane=function(C){C=A.extend({},A.fn.jScrollPane.defaults,C);var B=function(){return false};return this.each(function(){var O=A(this);O.css("overflow","hidden");var X=this;if(A(this).parent().is(".jScrollPaneContainer")){var Ac=C.maintainPosition?O.position().top:0;var L=A(this).parent();var d=L.innerWidth();var Ad=L.outerHeight();var M=Ad;A(">.jScrollPaneTrack, >.jScrollArrowUp, >.jScrollArrowDown",L).remove();O.css({top:0})}else{var Ac=0;this.originalPadding=O.css("paddingTop")+" "+O.css("paddingRight")+" "+O.css("paddingBottom")+" "+O.css("paddingLeft");this.originalSidePaddingTotal=(parseInt(O.css("paddingLeft"))||0)+(parseInt(O.css("paddingRight"))||0);var d=O.innerWidth();var Ad=O.innerHeight();var M=Ad;O.wrap(A("<div></div>").attr({className:"jScrollPaneContainer"}).css({height:Ad+"px",width:d+"px"}));A(document).bind("emchange",function(Ae,Af,p){O.jScrollPane(C)})}if(C.reinitialiseOnImageLoad){var N=A.data(X,"jScrollPaneImagesToLoad")||A("img",O);var G=[];if(N.length){N.each(function(p,Ae){A(this).bind("load",function(){if(A.inArray(p,G)==-1){G.push(Ae);N=A.grep(N,function(Ag,Af){return Ag!=Ae});A.data(X,"jScrollPaneImagesToLoad",N);C.reinitialiseOnImageLoad=false;O.jScrollPane(C)}}).each(function(Af,Ag){if(this.complete||this.complete===undefined){this.src=this.src}})})}}var o=this.originalSidePaddingTotal;var l={height:"auto",width:d-C.scrollbarWidth-C.scrollbarMargin-o+"px"};if(C.scrollbarOnLeft){l.paddingLeft=C.scrollbarMargin+C.scrollbarWidth+"px"}else{l.paddingRight=C.scrollbarMargin+"px"}O.css(l);var m=O.outerHeight();var i=Ad/m;if(i<0.99){var H=O.parent();H.append(A("<div></div>").attr({className:"jScrollPaneTrack"}).css({width:C.scrollbarWidth+"px"}).append(A("<div></div>").attr({className:"jScrollPaneDrag"}).css({width:C.scrollbarWidth+"px"}).append(A("<div></div>").attr({className:"jScrollPaneDragTop"}).css({width:C.scrollbarWidth+"px"}),A("<div></div>").attr({className:"jScrollPaneDragBottom"}).css({width:C.scrollbarWidth+"px"}))));var z=A(">.jScrollPaneTrack",H);var P=A(">.jScrollPaneTrack .jScrollPaneDrag",H);if(C.showArrows){var g;var Ab;var S;var r;var j=function(){if(r>4||r%4==0){y(u+Ab*b)}r++};var K=function(p){A("html").unbind("mouseup",K);g.removeClass("jScrollActiveArrowButton");clearInterval(S)};var Z=function(){A("html").bind("mouseup",K);g.addClass("jScrollActiveArrowButton");r=0;j();S=setInterval(j,100)};H.append(A("<a></a>").attr({href:"javascript:;",className:"jScrollArrowUp"}).css({width:C.scrollbarWidth+"px"}).html("Scroll up").bind("mousedown",function(){g=A(this);Ab=-1;Z();this.blur();return false}).bind("click",B),A("<a></a>").attr({href:"javascript:;",className:"jScrollArrowDown"}).css({width:C.scrollbarWidth+"px"}).html("Scroll down").bind("mousedown",function(){g=A(this);Ab=1;Z();this.blur();return false}).bind("click",B));var Q=A(">.jScrollArrowUp",H);var J=A(">.jScrollArrowDown",H);if(C.arrowSize){M=Ad-C.arrowSize-C.arrowSize;z.css({height:M+"px",top:C.arrowSize+"px"})}else{var s=Q.height();C.arrowSize=s;M=Ad-s-J.height();z.css({height:M+"px",top:s+"px"})}}var w=A(this).css({position:"absolute",overflow:"visible"});var D;var Y;var b;var u=0;var V=i*Ad/2;var a=function(Ae,Ag){var Af=Ag=="X"?"Left":"Top";return Ae["page"+Ag]||(Ae["client"+Ag]+(document.documentElement["scroll"+Af]||document.body["scroll"+Af]))||0};var f=function(){return false};var v=function(){n();D=P.offset(false);D.top-=u;Y=M-P[0].offsetHeight;b=2*C.wheelSpeed*Y/m};var E=function(p){v();V=a(p,"Y")-u-D.top;A("html").bind("mouseup",T).bind("mousemove",h);if(A.browser.msie){A("html").bind("dragstart",f).bind("selectstart",f)}return false};var T=function(){A("html").unbind("mouseup",T).unbind("mousemove",h);V=i*Ad/2;if(A.browser.msie){A("html").unbind("dragstart",f).unbind("selectstart",f)}};var y=function(Ae){Ae=Ae<0?0:(Ae>Y?Y:Ae);u=Ae;P.css({top:Ae+"px"});var Af=Ae/Y;w.css({top:((Ad-m)*Af)+"px"});O.trigger("scroll");if(C.showArrows){Q[Ae==0?"addClass":"removeClass"]("disabled");J[Ae==Y?"addClass":"removeClass"]("disabled")}};var h=function(p){y(a(p,"Y")-D.top-V)};var q=Math.max(Math.min(i*(Ad-C.arrowSize*2),C.dragMaxHeight),C.dragMinHeight);P.css({height:"109px"}).bind("mousedown",E);var k;var R;var I;var t=function(){if(R>8||R%4==0){y((u-((u-I)/2)))}R++};var Aa=function(){clearInterval(k);A("html").unbind("mouseup",Aa).unbind("mousemove",e)};var e=function(p){I=a(p,"Y")-D.top-V};var U=function(p){v();e(p);R=0;A("html").bind("mouseup",Aa).bind("mousemove",e);k=setInterval(t,100);t()};z.bind("mousedown",U);H.bind("mousewheel",function(Ae,Ag){v();n();var Af=u;y(u-Ag*b);var p=Af!=u;return !p});var F;var W;function c(){var p=(F-u)/C.animateStep;if(p>1||p<-1){y(u+p)}else{y(F);n()}}var n=function(){if(W){clearInterval(W);delete F}};var x=function(Af,p){if(typeof Af=="string"){$e=A(Af,O);if(!$e.length){return}Af=$e.offset().top-O.offset().top}H.scrollTop(0);n();var Ae=-Af/(Ad-m)*Y;if(p||!C.animateTo){y(Ae)}else{F=Ae;W=setInterval(c,C.animateInterval)}};O[0].scrollTo=x;O[0].scrollBy=function(Ae){var p=-parseInt(w.css("top"))||0;x(p+Ae)};v();x(-Ac,true);A("*",this).bind("focus",function(Ah){var Ag=A(this);var Aj=0;while(Ag[0]!=O[0]){Aj+=Ag.position().top;Ag=Ag.offsetParent()}var p=-parseInt(w.css("top"))||0;var Ai=p+Ad;var Af=Aj>p&&Aj<Ai;if(!Af){var Ae=Aj-C.scrollbarMargin;if(Aj>p){Ae+=A(this).height()+15+C.scrollbarMargin-Ad}x(Ae)}});if(location.hash){x(location.hash)}A(document).bind("click",function(Ae){$target=A(Ae.target);if($target.is("a")){var p=$target.attr("href");if(p.substr(0,1)=="#"){x(p)}}});A.jScrollPane.active.push(O[0])}else{O.css({height:Ad+"px",width:d-this.originalSidePaddingTotal+"px",padding:this.originalPadding});O.parent().unbind("mousewheel")}})};A.fn.jScrollPane.defaults={scrollbarWidth:25,scrollbarMargin:5,wheelSpeed:18,showArrows:false,arrowSize:0,animateTo:false,dragMinHeight:1,dragMaxHeight:99999,animateInterval:100,animateStep:3,maintainPosition:true,scrollbarOnLeft:false,reinitialiseOnImageLoad:false};A(window).bind("unload",function(){var C=A.jScrollPane.active;for(var B=0;B<C.length;B++){C[B].scrollTo=C[B].scrollBy=null}})})(jQuery);





/* 
 * Cross-browser event handling, by Scott Andrew
 */
function addEvent(element, eventType, lamdaFunction, useCapture) {
    if (element.addEventListener) {
        element.addEventListener(eventType, lamdaFunction, useCapture);
        return true;
    } else if (element.attachEvent) {
        var r = element.attachEvent('on' + eventType, lamdaFunction);
        return r;
    } else {
        return false;
    }
}

/* 
 * Kills an event's propagation and default action
 */
function knackerEvent(eventObject) {
    if (eventObject && eventObject.stopPropagation) {
        eventObject.stopPropagation();
    }
    if (window.event && window.event.cancelBubble ) {
        window.event.cancelBubble = true;
    }
    
    if (eventObject && eventObject.preventDefault) {
        eventObject.preventDefault();
    }
    if (window.event) {
        window.event.returnValue = false;
    }
}

/* 
 * Safari doesn't support canceling events in the standard way, so we must
 * hard-code a return of false for it to work.
 */
function cancelEventSafari() {
    return false;        
}

/* 
 * Cross-browser style extraction, from the JavaScript & DHTML Cookbook
 * <http://www.oreillynet.com/pub/a/javascript/excerpt/JSDHTMLCkbk_chap5/index5.html>
 */
function getElementStyle(elementID, CssStyleProperty) {
    var element = document.getElementById(elementID);
    if (element.currentStyle) {
        return element.currentStyle[toCamelCase(CssStyleProperty)];
    } else if (window.getComputedStyle) {
        var compStyle = window.getComputedStyle(element, '');
        return compStyle.getPropertyValue(CssStyleProperty);
    } else {
        return '';
    }
}

/* 
 * CamelCases CSS property names. Useful in conjunction with 'getElementStyle()'
 * From <http://dhtmlkitchen.com/learn/js/setstyle/index4.jsp>
 */
function toCamelCase(CssProperty) {
    var stringArray = CssProperty.toLowerCase().split('-');
    if (stringArray.length == 1) {
        return stringArray[0];
    }
    var ret = (CssProperty.indexOf("-") == 0)
              ? stringArray[0].charAt(0).toUpperCase() + stringArray[0].substring(1)
              : stringArray[0];
    for (var i = 1; i < stringArray.length; i++) {
        var s = stringArray[i];
        ret += s.charAt(0).toUpperCase() + s.substring(1);
    }
    return ret;
}

/*
 * Disables all 'test' links, that point to the href '#', by Ross Shannon
 */
function disableTestLinks() {
  var pageLinks = document.getElementsByTagName('a');
  for (var i=0; i<pageLinks.length; i++) {
    if (pageLinks[i].href.match(/[^#]#$/)) {
      addEvent(pageLinks[i], 'click', knackerEvent, false);
    }
  }
}

/* 
 * Cookie functions
 */
function createCookie(name, value, days) {
    var expires = '';
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        var expires = '; expires=' + date.toGMTString();
    }
    document.cookie = name + '=' + value + expires + '; path=/';
}

function readCookie(name) {
    var cookieCrumbs = document.cookie.split(';');
    var nameToFind = name + '=';
    for (var i = 0; i < cookieCrumbs.length; i++) {
        var crumb = cookieCrumbs[i];
        while (crumb.charAt(0) == ' ') {
            crumb = crumb.substring(1, crumb.length); /* delete spaces */
        }
        if (crumb.indexOf(nameToFind) == 0) {
            return crumb.substring(nameToFind.length, crumb.length);
        }
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, '', -1);
}
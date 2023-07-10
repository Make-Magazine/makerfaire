(function() {
	"use strict";
	
	if (document.querySelector('.essb-postbar')) {
		
		var postbarStart = document.querySelector('.essb_postbar_start'),
			postbarEnd = document.querySelector('.essb_postbar_end');
		
		if (postbarStart && postbarEnd) {
			
			function getOffset(element) {
			    if (!element.getClientRects().length) {
			      return { top: 0, left: 0 };
			    }
	
			    let rect = element.getBoundingClientRect();
			    let win = element.ownerDocument.defaultView;
			    return ({
			      top: rect.top + win.pageYOffset,
			      left: rect.left + win.pageXOffset
			    });   
			}
			
			var docOffset = getOffset(postbarStart).top,
	    		docEndOffset = getOffset(postbarEnd).top,
	    		elmHeight = docEndOffset - docOffset,
	    		progressBar = document.querySelector('.essb-postbar-progress-bar'),
	    		winHeight = Math.max(
	        		  document.body.scrollHeight, document.documentElement.scrollHeight,
	        		  document.body.offsetHeight, document.documentElement.offsetHeight,
	        		  document.body.clientHeight, document.documentElement.clientHeight
	        		),
	        	docScroll,
	        	viewedPortion;
			
	        window.onload = function() {
	            docOffset = getOffset(postbarStart).top,
	            docEndOffset = getOffset(postbarEnd).top,
	            elmHeight = docEndOffset - docOffset;
	        };
	        
	        window.onscroll = function() {
	
				docScroll = window.pageYOffset,
	            viewedPortion = docScroll;
					
				if(viewedPortion < 0) {
					viewedPortion = 0;
				}
	            if(viewedPortion > elmHeight) {
	            	viewedPortion = elmHeight;
	            }
	            var viewedPercentage = (viewedPortion / elmHeight) * 100;
	            progressBar.style.width = viewedPercentage + '%';
	
			};
	
			window.onresize = function() {
				docOffset = getOffset(postbarStart).top;
				docEndOffset = getOffset(postbarEnd).top;
				elmHeight = docEndOffset - docOffset;
				winHeight = Math.max(
		        		  document.body.scrollHeight, document.documentElement.scrollHeight,
		        		  document.body.offsetHeight, document.documentElement.offsetHeight,
		        		  document.body.clientHeight, document.documentElement.clientHeight
		        		);
				window.onscroll();
			}
	
			window.onscroll();
			
		}
	}

})();
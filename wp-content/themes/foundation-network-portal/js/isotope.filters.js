jQuery(document).ready(function(){
		$(function(){
		  
		  var $container = $('#main');
		
		  $container.isotope({
		    itemSelector : '.network-post'
		  });
		  
		  
		  var $optionSets = $('.f-dropdown'),
		      $optionLinks = $optionSets.find('a');
		
		  $optionLinks.click(function(){
		    var $this = $(this);
		    // don't proceed if already selected
		    if ( $this.hasClass('selected') ) {
		      return false;
		    }
		    
		    var $optionSet = $this.parents('.f-dropdown');
		    $optionSet.find('.selected').removeClass('selected');
		    $this.addClass('selected');

		    var $linkTitle = $this.text();
		    var $currentTitle = $this.parents('h4');
		    $currentTitle.find('.filter-title').text($linkTitle);
		    
		
		    // make option object dynamically, i.e. { filter: '.my-filter-class' }
		    var options = {},
		        key = $optionSet.attr('data-option-key'),
		        value = $this.attr('data-option-value');
		        
		    // parse 'false' as false boolean
		    value = value === 'false' ? false : value;
		    options[ key ] = value;
		    
		    if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
		      // changes in layout modes need extra logic
		      changeLayoutMode( $this, options )
		    } else {
		      // otherwise, apply new options
		      $container.isotope( options );
		    }

		    if ( key === 'layoutMode' && value === 'straightDown' ) {
		      $container.removeClass('masonry');
		      $container.isotope('reLayout');
		    }
		    if ( key === 'layoutMode' && value === 'masonry' ) {
		      $container.addClass('masonry');
		      $container.isotope('reLayout');
		    }
		    
		    return false;
		  });
		
		  
		});
	});
// JavaScript Document
jQuery.noConflict();
jQuery(document).ready( function ($)
			{
				$( '#pagetop' ).scrollFollow();
			}
		);
jQuery( document ).ready( function ($) {
	$( '#pagetop' ).scrollFollow( {
		speed: 1000,
		easing: 'easeOutCubic',
		offset: 150
		} );

$(function () {
  $('#pagetop').click(function () {
    $('html,body').animate({ scrollTop: 0 }, 'slow', 'easeOutCirc');
  });
  
});

} );





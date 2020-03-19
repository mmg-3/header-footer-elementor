(function($){

	EHF_Footer = {

        /**
		 * Binds events for the Elementor Header Footer.
		 *
		 * @since x.x.x
		 * @access private
		 * @method _bind
		 */
		init: function() {
			elementor.on( "document:loaded", function() {
                setTimeout( function() {
                    jQuery.each(elementorFrontend.documentsManager.documents, function (index, document) {
                        var $documentElement = document.$element;
                        var ids_array = JSON.parse( hfe_admin.ids_array );
                        ids_array.forEach( function(item, index){
                        	var elementor_id = $documentElement.data('elementor-id');
                        	if( elementor_id == ids_array[index].ID ){
                        		$documentElement.find( '.elementor-document-handle__title' ).text( elementor.translate('edit_element', [ids_array[index].VALUE] ) );
                        	}
                        } );
                        // Update this selector - elementor-document-handle__title text.
                    });
                }, 1000 );
            });
		}
	};

	/**
	 * Initialize EHF_Footer
	 */
	$(function(){
		EHF_Footer.init();
	});

})(jQuery);

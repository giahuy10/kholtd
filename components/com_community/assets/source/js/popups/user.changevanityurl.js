(function( root, $, factory ) {

    joms.popup || (joms.popup = {});
    joms.popup.user || (joms.popup.user = {});
    joms.popup.user.changeVanityURL = factory( root, $ );

    define([ 'utils/popup' ], function() {
        return joms.popup.user.changeVanityURL;
    });

})( window, joms.jQuery, function() {

var popup, elem, id;

function render( _popup, _id ) {
    if ( elem ) elem.off();
    popup = _popup;
    id = _id;

    joms.ajax({
        func: 'profile,ajaxUpdateURL',
        data: [ id ],
        callback: function( json ) {
            popup.items[0] = {
                type: 'inline',
                src: buildHtml( json )
            };

            popup.updateItemHTML();

            elem = popup.contentContainer;
            elem.on( 'click', '[data-ui-object=popup-button-cancel]', cancel );
            elem.on( 'click', '[data-ui-object=popup-button-save]', save );
        }
    });
}

function cancel() {
    elem.off();
    popup.close();
}

function save() {
    elem.find('form').submit();
}

function buildHtml( json ) {
    var action = '';

    json || (json = {});

    if ( json.btnUpdate && json.btnCancel ) {
        action = [
            '<div class="joms-popup__action">',
            '<a href="javascript:" class="joms-button--neutral joms-button--small joms-left" data-ui-object="popup-button-cancel">', json.btnCancel, '</a> &nbsp;',
            '<button class="joms-button--primary joms-button--small" data-ui-object="popup-button-save">', json.btnUpdate, '</button>',
            '</div>'
        ].join('');
    }

    return [
        '<div class="joms-popup joms-popup--whiteblock">',
        '<div class="joms-popup__title"><button class="mfp-close" type="button" title="Close (Esc)">×</button>', json.title, '</div>',
        '<div class="joms-popup__content">', ( json.html || json.message ), '</div>',
        action,
        '</div>'
    ].join('');
}

// Exports.
return function( id ) {
    joms.util.popup.prepare(function( mfp ) {
        render( mfp, id );
    });
};

});

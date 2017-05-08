function confirmDelContenuPopup(idContenu) {
    $('#mg-newsl-confirmDelContenu').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
    
    $('#mg-newsl-confirmDelContenu').find("#idContenu").val(idContenu);
}

function editContenuPopup(idContenu) {
    // Chargement du contenu
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadContenu/"+idContenu,
        dataType: "text",
        success: function(data) {
            var obj = $.parseJSON(data);
            var value = decodeURIComponent(obj.Titre);
            var titre = unescape(value.replace(/\+/g,  " "));
            var value = decodeURIComponent(obj.Corps);
            var corps = unescape(value.replace(/\+/g,  " "));
            $('#mg-newsl-editContenu').find('#Contenu_ID').val(obj.ID);
            $('#mg-newsl-editContenu').find('#Contenu_Titre').val(titre);
            $('#mg-newsl-editContenu').find('#Contenu_Corps_0').val(corps);
            $('#mg-newsl-editContenu').find('#Contenu_IDLangue').val(obj.IDLangue);
        }
    });
    
    $('#mg-newsl-editContenu').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
    
    $('#mg-newsl-editContenu').find('.form_advanced_table td.caption').css('width', '0');
}

jQuery(document).ready(function() {
    $('.form_advanced_table td.caption').css('width', '0');
    
    $('#mg-newsl-confirmDelContenu').find("#ok").click(function() {
        idContenu = $('#mg-newsl-confirmDelContenu').find("#idContenu").val();
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/deleteContenu/"+idContenu,
            success: function() {
                $('#mg-newsl-confirmDelContenu').dolPopupHide();
                window.location.reload();
            }
        });
    });
    
    $('#mg-newsl-confirmDelContenu').find("#cancel").click(function() {
        $('#mg-newsl-confirmDelContenu').dolPopupHide();
    });
});

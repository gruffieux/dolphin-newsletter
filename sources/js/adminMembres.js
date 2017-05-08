function confirmDelMembrePopup(idMembre) {
    $('#mg-newsl-confirmDelMembre').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
    
    $('#mg-newsl-confirmDelMembre').find("#idMembre").val(idMembre);
}

function editMembrePopup(idMembre) {
    // Chargement du membre
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadMembre/"+idMembre,
        dataType: "text",
        success: function(data) {
            var obj = $.parseJSON(data);
            $('#mg-newsl-editMembre').find('#Membre_ID').val(obj.ID);
            $('#mg-newsl-editMembre').find('#Membre_Email').val(obj.Email);
            $('#mg-newsl-editMembre').find('#Membre_Nom').val(obj.Nom);
            $('#mg-newsl-editMembre').find('#Membre_Prenom').val(obj.Prenom);
            $('#mg-newsl-editMembre').find('#Membre_DateNaissance_0').val(obj.DateNaissance);
            $('#mg-newsl-editMembre').find('#Membre_Pays').val(obj.Pays);
            $('#mg-newsl-editMembre').find('#Membre_Ville').val(obj.Ville);
            $('#mg-newsl-editMembre').find('#Membre_Zip').val(obj.Zip);
            $('#mg-newsl-editMembre').find('#Membre_IDLangue').val(obj.IDLangue);
            $('#mg-newsl-editMembre').find('#Membre_IDSource').val(obj.IDSource);
            $('#mg-newsl-editMembre').find('#Membre_Sexe').val(obj.Sexe);
            $('#mg-newsl-editMembre').find('#Membre_Adresse').val(obj.Adresse);
            $('#mg-newsl-editMembre').find('#Membre_ComplAdr').val(obj.ComplAdr);
            $('#mg-newsl-editMembre').find('#Membre_Telephone').val(obj.Telephone);
        }
    });
    
    $('#mg-newsl-editMembre').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
}

function viewMembreCampagnePopup(idMembre) {
    // Chargement du membre
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadMembreCampagnes/"+idMembre,
        dataType: "text",
        success: function(data) {
            $('#mg-newsl-membreCampagnes-table').html(data);
        }
    });
    
    $('#mg-newsl-membreCampagnes').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
}

function trieMembre(type) {
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadSortedMembres/"+type,
        dataType: "text",
        success: function(data) {
            $('#mg_newsl_membres').html(data);
        }
    });
}

jQuery(document).ready(function() {
    $('#selectAllMembres').live('click', function() {
        var checked = $(this).attr("checked");
        $('.SelectMembre').each(function(index) {
            if (checked) {
                $(this).attr("checked", true);
            }
            else {
                $(this).removeAttr("checked");
            }
        });
    });
    
    $('.ActionMembresBtn').live('click', function() {
        var actionMembres = $(this).attr("id");
        $('#actionMembres').val(actionMembres);
        $('.actionContent').hide();
        $('#'+actionMembres).show();
        $('#mg-newsl-confirmAction').dolPopup({
            fog: {
                color: '#fff', 
                opacity: .7
            },
            closeOnOuterClick: false
        });
    });
    
    $('#mg-newsl-confirmAction').find("#ok").click(function() {
        var membreStr = "";
        $('.SelectMembre').each(function(index) {
            if ($(this).attr("checked")) {
                if (membreStr) {
                    membreStr += ",";
                }
                membreStr += $(this).val();
            }
        });
        $('#membreStr').val(membreStr);
        var blackMail = $('#mg-newsl-confirmAction').find("#blackMail").attr('checked') ? 1 : 0;
        $('#blackList').val(blackMail);
        $('#formMembres').submit();
    });
    
    $('#mg-newsl-confirmAction').find("#cancel").click(function() {
        $('#mg-newsl-confirmAction').dolPopupHide();
    });
    
    $('#botsNewBtn').live('click', function() {
        $('#mg_newsl_membres').html('<div align="center"><img src="../templates/base/images/loading.gif" /></div>');
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/botsNew",
            success: function(data) {
                $('#mg_newsl_membres').html(data);
            }
        });
    });
    
    $('#mg-newsl-confirmDelMembre').find("#ok").click(function() {
        var idMembre = $('#mg-newsl-confirmDelMembre').find("#idMembre").val();
        var blackMail = $('#mg-newsl-confirmDelMembre').find("#blackMail").attr('checked') ? 1 : 0;
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/deleteMembre/"+idMembre+"/"+blackMail,
            success: function() {
                $('#mg-newsl-confirmDelMembre').dolPopupHide();
                window.location.reload();
            }
        });
    });
    
    $('#mg-newsl-confirmDelMembre').find("#cancel").click(function() {
        $('#mg-newsl-confirmDelMembre').dolPopupHide();
    });
});

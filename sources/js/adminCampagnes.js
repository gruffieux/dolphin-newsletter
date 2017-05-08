function addCampagneContenuPopup(idCampagne) {
    var ajaxRequest = 0;
    $('#contenuChooser').html('');
    $('#contenuUnchooser').html('');
    $('#contenuChooser').attr('disabled', 'true');
    $('#contenuUnchooser').attr('disabled', 'true');
    $('#contenuActions').hide();
    $('#contenuLoader').show();
    $('#idCampagne').val(idCampagne);
    
    // Chargement de la liste des membres
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadUnchoosedContenus/"+idCampagne,
        success: function(data) {
            $('#contenuChooser').html(data);
            ajaxRequest++;
            if (ajaxRequest >= 2) {
                $('#contenuLoader').hide();
                $('#contenuActions').show();
                $('#contenuChooser').removeAttr('disabled');
                $('#contenuUnchooser').removeAttr('disabled');
            }
        }
    });
    
    // Chargement de la liste des membres dans la campagne
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadChoosedContenus/"+idCampagne,
        success: function(data) {
            $('#contenuUnchooser').html(data);
            ajaxRequest++;
            if (ajaxRequest >= 2) {
                $('#contenuLoader').hide();
                $('#contenuActions').show();
                $('#contenuChooser').removeAttr('disabled');
                $('#contenuUnchooser').removeAttr('disabled');
            }
        }
    });
    
    $('#mg-newsl-addCampagneContenu').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
}

function addCampagneMembrePopup(idCampagne) {
    var ajaxRequest = 0;
    $('#membreChooser').html('');
    $('#membreUnchooser').html('');
    $('#membreChooser').attr('disabled', 'true');
    $('#membreUnchooser').attr('disabled', 'true');
    $('#membreActions').hide();
    $('#membreLoader').show();
    $('#idCampagne').val(idCampagne);
    
    // Chargement de la liste des membres
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadUnchoosedMembres/"+idCampagne,
        success: function(data) {
            $('#membreChooser').html(data);
            ajaxRequest++;
            if (ajaxRequest >= 2) {
                $('#membreLoader').hide();
                $('#membreActions').show();
                $('#membreChooser').removeAttr('disabled');
                $('#membreUnchooser').removeAttr('disabled');
            }
        }
    });
    
    // Chargement de la liste des membres dans la campagne
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadChoosedMembres/"+idCampagne,
        success: function(data) {
            $('#membreUnchooser').html(data);
            ajaxRequest++;
            if (ajaxRequest >= 2) {
                $('#membreLoader').hide();
                $('#membreActions').show();
                $('#membreChooser').removeAttr('disabled');
                $('#membreUnchooser').removeAttr('disabled');
            }
        }
    });
    
    $('#mg-newsl-addCampagneMembre').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
}

function confirmDelCampagnePopup(idCampagne) {
    $('#mg-newsl-confirmDelCampagne').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
    
    $('#mg-newsl-confirmDelCampagne').find("#idCampagne").val(idCampagne);
}

function editCampagnePopup(idCampagne) {
    // Chargement de la campagne
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/loadCampagne/"+idCampagne,
        dataType: "text",
        success: function(data) {
            var obj = $.parseJSON(data);
            var value = decodeURIComponent(obj.Descriptif);
            var desc = unescape(value.replace(/\+/g,  " "));
            $('#mg-newsl-editCampagne').find('#Campagne_ID').val(obj.ID);
            $('#mg-newsl-editCampagne').find('#Campagne_Nom').val(obj.Nom);
            $('#mg-newsl-editCampagne').find('#Campagne_Descriptif').val(desc);
            $('#mg-newsl-editCampagne').find('#Campagne_DateEcheance_0').val(obj.DateEcheance);
        }
    });
    
    $('#mg-newsl-editCampagne').dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        },
        closeOnOuterClick: false
    });
}

function moveContenus(startId, endId, action) {
    var contenuStr = "";
    
    $('#'+startId+' option:selected').each(function(index) {
        if (contenuStr) {
            contenuStr += ",";
        }
        contenuStr += $(this).val();
    });
    
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/"+action+"/"+$('#idCampagne').val()+"/"+contenuStr,
        dataType: "text",
        success: function(result) {
            $('#'+startId+' option:selected').remove().appendTo('#'+endId);
        },
        error: function(xml, text, error) {
            alert(text);
        }
    });
}

function moveMembres(startId, endId, action) {
    var membreGroup = new Array();
    var membreStr = "";
    
    $('#'+startId+' option:selected').each(function(index) {
        if (membreStr.length >= 200) {
            membreGroup.push(membreStr);
            membreStr = "";
        }
        if (membreStr) {
            membreStr += ",";
        }
        membreStr += $(this).val();
    });
    
    if (membreStr) {
        membreGroup.push(membreStr);
    }
    
    var reqCounter = 0;
    
    for (var i = 0; i < membreGroup.length; i++) {
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/"+action+"/"+$('#idCampagne').val()+"/"+membreGroup[i],
            dataType: "text",
            success: function(result) {
                reqCounter++;
                if (reqCounter == membreGroup.length) {
                    $('#'+startId+' option:selected').remove().appendTo('#'+endId);
                }
            },
            error: function(xml, text, error) {
                alert(text);
            }
        });
    }
    
    return true;
}

function send() {
    $.ajax({
        type: "GET",
        url: "",
        data: "r=newsletter/beforeSend",
        dataType: "text",
        success: function(data) {
            if (data == 'on') {
                $.ajax({
                    type: "GET",
                    url: "",
                    data: "r=newsletter/send",
                    success: function() {
                        window.location.reload();
                    }
                });
            }
            else {
                $('#mg-newsl-confirmSend').dolPopup({
                    fog: {
                        color: '#fff', 
                        opacity: .7
                    },
                    closeOnOuterClick: false
                });
            }
        }
    });
}

jQuery(document).ready(function() {
    $('#contenuChooser').dblclick(function() {
        moveContenus("contenuChooser", "contenuUnchooser", "addCampagneContenus");
    });
    
    $('#contenuUnchooser').dblclick(function() {
        moveContenus("contenuUnchooser", "contenuChooser", "removeCampagneContenus");
    });
    
    $('#addContenu').click(function() {
        moveContenus("contenuChooser", "contenuUnchooser", "addCampagneContenus");
    });
    
    $('#removeContenu').click(function() {
        moveContenus("contenuUnchooser", "contenuChooser", "removeCampagneContenus");
    });
    
    $('#membreChooser').dblclick(function() {
        moveMembres("membreChooser", "membreUnchooser", "addCampagneMembres");
    });
    
    $('#membreUnchooser').dblclick(function() {
        moveMembres("membreUnchooser", "membreChooser", "removeCampagneMembres");
    });
    
    $('#addMembre').click(function() {
        moveMembres("membreChooser", "membreUnchooser", "addCampagneMembres");
    });
    
    $('#removeMembre').click(function() {
        moveMembres("membreUnchooser", "membreChooser", "removeCampagneMembres");
    });
    
    $('#mg-newsl-confirmDelCampagne').find("#ok").click(function() {
        idCampagne = $('#mg-newsl-confirmDelCampagne').find("#idCampagne").val();
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/deleteCampagne/"+idCampagne,
            success: function() {
                $('#mg-newsl-confirmDelCampagne').dolPopupHide();
                window.location.reload();
            }
        });
    });
    
    $('#mg-newsl-confirmDelCampagne').find("#cancel").click(function() {
        $('#mg-newsl-confirmDelCampagne').dolPopupHide();
    });
    
    $('#mg-newsl-confirmSend').find("#ok").click(function() {
        $.ajax({
            type: "GET",
            url: "",
            data: "r=newsletter/send",
            success: function() {
                $('#mg-newsl-confirmSend').dolPopupHide();
                window.location.reload();
            }
        });
    });
    
    $('#mg-newsl-confirmSend').find("#cancel").click(function() {
        $('#mg-newsl-confirmSend').dolPopupHide();
    });
});

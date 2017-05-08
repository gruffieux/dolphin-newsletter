function deleteMe(hash) {
    $("#dialog-confirm").dialog({
        resizable: false,
        modal: true,
        buttons: {
            Ok: function() {
                $.ajax({
                    type: "GET",
                    url: "modules",
                    data: "r=newsletter/deleteMe/"+hash,
                    success: function() {
                        $('#message').html($('#text-deleted').val());
                    }
                });
                $(this).dialog("close");
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
}

jQuery(document).ready(function() {
    $('.MembreCampagne_Form').submit(function() {
        var campagneStr = "";
        
        $(".CampagneChecker").each(function(index) {
            if ($(this).prop("checked")) {
                if (campagneStr) {
                    campagneStr += ",";
                }
                campagneStr = campagneStr + $(this).val();
            }
        });
        
        $("#MembreCampagne_Str").val(campagneStr);
    });
    
    $('.MembreNotif_Form').submit(function() {
        var notifStr = "";
        
        $(".NotifChecker").each(function(index) {
            if ($(this).prop("checked")) {
                if (notifStr) {
                    notifStr += ",";
                }
                notifStr = notifStr + $(this).val();
            }
        });
        
        $("#MembreNotif_Str").val(notifStr);
    });
});

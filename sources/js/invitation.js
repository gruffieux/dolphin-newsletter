jQuery(document).ready(function() {
    var counter = $("#inviteCounter").val();
    
    $(".InviteSup").parent().parent().parent().hide();
    
    // Ajout d'un invité
    $('#ajouteInvite').click(function() {
        $(".InviteSup").eq(counter).parent().parent().parent().show();
        counter++;
        $("#inviteCounter").val(counter);
    });
    
    $('#invitationForm').submit(function() {
        var campagneStr = "";
        
        $(".CampagneChecker").each(function(index) {
            if ($(this).attr("checked")) {
                if (campagneStr) {
                    campagneStr += ",";
                }
                campagneStr = campagneStr + $(this).val();
            }
        });
        
        $("#CampagneStr").val(campagneStr);
    });
});

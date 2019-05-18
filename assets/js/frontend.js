jQuery( document ).ready( function ( e ) {
    jQuery('.btn-select-wrestler').click(function (e) {
        var $this = jQuery(e.target);
        var leagueId = $this.closest('.league-container').data('league-id');
        jQuery('.btn-wrestler-selected').data('league-id', leagueId);
        jQuery.post(ajax_object.ajax_url, {
            action:'wrestlers_in_league',
            league_id: leagueId
        }, function (result) {
            console.log(result);
            if (result.length > 0) {
                var selected = false;
                jQuery.map(jQuery('#select-wrestler-id').find('option'), function (elem) {
                    if (!elem) {
                        return;
                    }
                    elem = jQuery(elem);
                    var include = result.filter(wrestler => wrestler.wrestler_id == elem.val())
                    if (include.length > 0) {
                        elem.prop("disabled", true);
                        elem.prop("selected", false);
                    }
                    else {
                        elem.prop("disabled", false);
                        if(!selected){
                            elem.prop("selected", true);
                            selected = true;
                        }
                    }
                });
            }
            jQuery('#select-wrestler').modal();
        });

        // jQuery.get(ajax_object.ajax_url + "?action=get_wrestlers", function (data) {
        //     console.log(data);
        // })
    })
});

function selectWrestler(event) {
    jQuery.post(ajax_object.ajax_url, {
        action:'select_wrestler',
        wrestler_id: jQuery('#select-wrestler-id').val(),
        league_id: jQuery(event.target).data('league-id')
    }, function (result) {
        console.log(result);
        location.reload();
    });
}
jQuery(document).ready(function (e) {
    jQuery('.btn-select-wrestler').click(function (e) {
        var $this = jQuery(e.target);
        var leagueId = $this.closest('.league-container').data('league-id');
        jQuery('.btn-wrestler-selected').data('league-id', leagueId);
        jQuery.post(ajax_object.ajax_url, {
            action: 'wrestlers_in_league',
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
                    var include = result.filter(function (wrestler) {
                        return wrestler.wrestler_id == elem.val();
                    });

                    if (include.length > 0) {
                        elem.prop("disabled", true);
                        elem.prop("selected", false);
                    }
                    else {
                        elem.prop("disabled", false);
                        if (!selected) {
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
    });

    jQuery('#form-field-friends-selector').editableSelect();

});

function selectWrestler(event) {
    jQuery.post(ajax_object.ajax_url, {
        action: 'select_wrestler',
        wrestler_id: jQuery('#select-wrestler-id').val(),
        league_id: jQuery(event.target).data('league-id')
    }, function (result) {
        console.log(result);
        location.reload();
    });
}

function findLeagueByEmail(event) {
    event.preventDefault();
    if(jQuery('#friend_email').val() === ""){
        alert("Please input correct email!");
    }
    jQuery.post(ajax_object.ajax_url, {
        action: 'find_league_email',
        friend_email: jQuery('#friend_email').val(),
    }, function (result) {
        console.log(result);
        var select = jQuery('#form-field-league');
        select.find('option')
            .remove()
            .end();
        if(result === "0" || result.length === 0){
            alert("Please input correct email!");
        }
        for (var i = 0; i < result.length; i++) {
            var league = result[i];
            select.append('<option value=' + league['league_id'] + '>' + league['league_name'] + '</option>');
        }

    });
}

function rejectJoinRequest(requestID){
    jQuery('<form>', {
        "id": "rejectJoinRequest",
        "method": "POST",
        "html": '<input type="text" id="request_id" name="request_id" value="' + requestID + '" />'
            + '<input type="hidden" name="action" value="reject_join_request">',

    }).appendTo(document.body).submit();
}

function acceptJoinRequest(requestID) {
    jQuery('<form>', {
        "id": "acceptJoinRequest",
        "method": "POST",
        "html": '<input type="text" id="request_id" name="request_id" value="' + requestID + '" />'
        + '<input type="hidden" name="action" value="accept_join_request">',

    }).appendTo(document.body).submit();
}

function addFriend(event){
    event.preventDefault();
    var selected = jQuery('#form-field-friends-selector').siblings('.es-list').find('li.es-visible');
    var value, text;
    if(selected.length > 0){
        value = selected.val();
        text = selected.text();
    }
    else{
        value = jQuery('#form-field-friends-selector').val();
        text = value;
        if(!validateEmail(value)){
            alert('Please input valid email!');
            return;
        }
    }
    if(jQuery("#form-field-friends option[value='" + value + "']").length > 0){
        alert("This is a already added friend!");
        return;
    }
    var option = jQuery('<option/>', {
        value: value,
        text : text,
        selected: true
    });
    jQuery('#form-field-friends').append(option);

}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

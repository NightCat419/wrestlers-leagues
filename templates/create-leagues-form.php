<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/9/2019
 * Time: 11:14 PM
 */

?>
<form class="elementor-form" method="post" id="create_league_form" name="Create League">
    <input type="hidden" name="action" value="create_league"/>
    <div class="elementor-form-fields-wrapper elementor-labels-above">
        <div  style="margin-top: 10px;" class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-lg_name" class="elementor-field-label">League Name</label>
            <input size="1" type="text" name="lg_name" id="lg_name"
                class="elementor-field elementor-size-sm  elementor-field-textual"
                placeholder="Please input league name"
                required="required"
                aria-required="true">
        </div>
        <div style="margin-top: 10px;" class="elementor-field-type-number elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-num_teams" class="elementor-field-label">Limit of Team</label>
            <input type="number" name="num_teams" id="num_teams"
                class="elementor-field elementor-size-sm  elementor-field-textual"
                placeholder="input limit of team can join" value="2" required="required"
                aria-required="true" min="2" max="10">
        </div>
        <div style="margin-top: 10px;" class="elementor-field-type-number elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-num_wrestlers" class="elementor-field-label">Number Of Wrestlers Per Team</label>
            <input type="number" name="num_wrestlers" id="form-field-num_wrestlers"
                class="elementor-field elementor-size-sm  elementor-field-textual"
                placeholder="input number of wrestlers per team"
                value="4" required="required" aria-required="true"
                min="4" max="10">
        </div>
        <div style="margin-top: 10px;" class="elementor-field-type-select elementor-field-group elementor-column elementor-col-100 elementor-field-type-select-multiple ">
            <label for="form-field-friends" class="elementor-field-label">Invite Friends</label>
            <div class="elementor-field elementor-select-wrapper ">
                <select name="friends[]" id="form-field-friends"
                    class="elementor-field-textual elementor-size-sm"
                    multiple="" size="6">
                    <?php
                    $args1 = array(
                        'role' => 'sp_player',
                        'orderby' => 'user_nicename',
                        'order' => 'ASC'
                    );
                    $players = get_users($args1);
                    $currentid = get_current_user_id();
                    foreach ($players as $pl):
                        if($pl->ID !== $currentid){
                            echo '<option value="'.$pl->ID.'">'.$pl->display_name.'</option>';
                        }
                    endforeach;
                    ?>

                </select>
            </div>
        </div>
        <div style="margin-top: 10px;" class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
            <button type="submit" class="elementor-button elementor-size-sm" style="margin: auto;">
                <span>
                    <span class="elementor-align-icon-left elementor-button-icon">
                        <i class="fa fa-steam" aria-hidden="true"></i>
                    </span>
                    <span class="elementor-button-text">Create League</span>
                </span>
            </button>
        </div>
    </div>
</form>


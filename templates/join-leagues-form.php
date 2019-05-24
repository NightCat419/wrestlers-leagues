<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/9/2019
 * Time: 11:14 PM
 */

$user = wp_get_current_user();
if(!empty($_GET['league_id'])){
    $league_id = $_GET['league_id'];
}
?>
<form class="elementor-form" method="post" id="create_league_form" name="Create League">
    <input type="hidden" name="action" value="join_league"/>
    <div class="elementor-form-fields-wrapper elementor-labels-above">
        <div  style="margin-top: 10px;" class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-lg_name" class="elementor-field-label">Team Name</label>
            <input size="1" type="text" name="team_name" id="lg_name"
                class="elementor-field elementor-size-sm  elementor-field-textual"
                placeholder="Please input team name"
                required="required"
                aria-required="true"
                value="<?=$user->display_name;?>">
        </div>
        <div class="elementor-column elementor-col-100" style="width: 100%;">
            <div  style="margin-top: 10px;" class="elementor-field-type-text elementor-field-group elementor-column elementor-col-80 elementor-field-required elementor-mark-required">
                <label for="friend_email" class="elementor-field-label">Email of Friend</label>
                <input size="1" type="email" name="friend_email" id="friend_email"
                       class="elementor-field elementor-size-sm  elementor-field-textual"
                       placeholder="Please input email of friend"
                       required="required"
                       aria-required="true"
                       value="">
            </div>
            <div  style="margin-top: 10px;" class="elementor-field-type-text elementor-field-group elementor-column elementor-col-20 elementor-field-required elementor-mark-required">
                <button type="reset" class="elementor-button elementor-size-sm" style="margin: auto;margin-bottom: 0;" onclick="findLeagueByEmail(event);">
                <span>
                    <span class="elementor-align-icon-left elementor-button-icon">
                        <i class="fa fa-find" aria-hidden="true"></i>
                    </span>
                    <span class="elementor-button-text">Find</span>
                </span>
                </button>
            </div>
        </div>
        <div style="margin-top: 10px;" class="elementor-field-type-select elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-league" class="elementor-field-label">Select League</label>
            <div class="elementor-field elementor-select-wrapper ">
                <select name="league" id="form-field-league"
                        class="elementor-field-textual elementor-size-sm"
                        required="required"
                        aria-required="true">

                </select>
            </div>
        </div>
        <div  style="margin-top: 10px;" class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="client_email" class="elementor-field-label">Your Email</label>
            <input size="1" type="email" name="client_email" id="client_email"
                   class="elementor-field elementor-size-sm  elementor-field-textual"
                   placeholder="Please input your email to receive notification."
                   required="required"
                   aria-required="true"
                   value="">
        </div>
        <div style="margin-top: 10px;" class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
            <button type="submit" class="elementor-button elementor-size-sm" style="margin: auto;">
                <span>
                    <span class="elementor-align-icon-left elementor-button-icon">
                        <i class="fa fa-steam" aria-hidden="true"></i>
                    </span>
                    <span class="elementor-button-text">Request To Join League</span>
                </span>
            </button>
        </div>
    </div>
</form>


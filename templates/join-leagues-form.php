<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/9/2019
 * Time: 11:14 PM
 */

$user = wp_get_current_user();
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
        <div style="margin-top: 10px;" class="elementor-field-type-select elementor-field-group elementor-column elementor-col-100 elementor-field-required elementor-mark-required">
            <label for="form-field-league" class="elementor-field-label">Select League</label>
            <div class="elementor-field elementor-select-wrapper ">
                <select name="league" id="form-field-league"
                        class="elementor-field-textual elementor-size-sm"
                        required="required"
                        aria-required="true">
                    <?php
                    $args = array(
                        'post_type'=> Wrestlers_Leagues::$post_type_league,
                        'order'    => 'ASC'
                    );
                    global $post;
                    $the_query = new WP_Query( $args );
                    if($the_query->have_posts() ) {
                        while ( $the_query->have_posts() ) {
                            $the_query->the_post();
                            echo "<option value='{$post->ID}'>{$post->post_title}</option>";
                        }
                    }

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
                    <span class="elementor-button-text">Join League</span>
                </span>
            </button>
        </div>
    </div>
</form>


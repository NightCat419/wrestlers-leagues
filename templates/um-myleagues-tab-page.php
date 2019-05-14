<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/11/2019
 * Time: 11:11 AM
 */
$user_id = get_current_user_id();
global $wpdb;
$query = "SELECT u.*, lg.post_title AS name FROM {$wpdb->prefix}kwl_league_user AS u "
    ."JOIN {$wpdb->prefix}posts AS lg ON u.league_id=lg.ID "
    ." WHERE u.user_id={$user_id}";
$leagues_joined = $wpdb->get_results($query);

?>

<div class="kwl-elements">
    <div class="leagues-list">
        <?php
        foreach ($leagues_joined as $league):
            $custom = get_post_custom($league->league_id);
        ?>
            <div class="league-container">
                <div class="league-title">
                    <a href="<?=get_permalink($league->league_id)?>"><?=$league->name?></a>
                </div>
                <div class="league-wrestlers-description">
                    <label>Your Wrestlers in <?= $league->name ?> & their stats</label>
                </div>
                <div class="container league-wrestlers-list">
                    <?php
                    $limit_wrestlers = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
                    for($i = 0; $i < $limit_wrestlers; ):
                    ?>
                        <div class="row wrestlers-row">
                            <div class="col-sm">
                                <div class="leagues-wrestler" <?php if($i >= $limit_wrestlers) echo "style='display:none;'"?>>
                                    <label>Pending selection <?= $i++?></label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="leagues-wrestler" <?php if($i >= $limit_wrestlers) echo "style='display:none;'"?>>
                                    <label>Pending selection <?= $i++?></label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="leagues-wrestler" <?php if($i >= $limit_wrestlers) echo "style='display:none;'"?>>
                                    <label>Pending selection <?= $i++?></label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="leagues-wrestler" <?php if($i >= $limit_wrestlers) echo "style='display:none;'";?>>
                                    <label>Pending selection <?= $i++?></label>
                                </div>
                            </div>
                        </div>

                    <?php
                    endfor;
                    ?>
                </div>
            </div>
        <?php
        endforeach;
        ?>

    </div>

</div>

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
    . "JOIN {$wpdb->prefix}posts AS lg ON u.league_id=lg.ID "
    . " WHERE u.user_id={$user_id}";
$leagues_joined = $wpdb->get_results($query);

?>

<div class="kwl-elements">
    <div class="leagues-list">
        <?php
        foreach ($leagues_joined as $league):
            $custom = get_post_custom($league->league_id);
            $wrestlers = $wpdb->get_results("SELECT lw.wrestler_id, p.post_title wrestler_name FROM {$wpdb->prefix}kwl_league_wrestler lw "
                . "JOIN {$wpdb->prefix}posts p ON lw.wrestler_id = p.ID "
                . "WHERE lw.league_id = {$league->league_id} AND lw.user_id = {$user_id} "
                ."ORDER BY lw.ID ASC");
            $wrestlers_count = count($wrestlers);
            ?>
            <div class="league-container" data-league-id="<?= $league->league_id ?>">
                <div class="league-title">
                    <a href="<?= get_permalink($league->league_id) ?>"><?= $league->name ?></a>
                </div>
                <div class="league-wrestlers-description">
                    <label>Your Wrestlers in <?= $league->name ?> & their stats</label>
                </div>
                <div class="container league-wrestlers-list">
                    <?php
                    $limit_wrestlers = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
                    for ($i = 0; $i < $limit_wrestlers;):
                        ?>
                        <div class="row wrestlers-row">
                            <div class="col-sm">
                                <?php
                                if ($wrestlers_count > $i):
                                    $wrestler = $wrestlers[$i];
                                    $image = get_the_post_thumbnail_url($wrestler->wrestler_id);
                                    $i++;
                                    ?>
                                    <div class="leagues-wrestler"
                                         style="background:url(<?= $image ?>)no-repeat;background-size:cover;background-position: center;">
                                        <button class="btn btn-trade-wrestler"
                                                value="Select"><?= $wrestler->wrestler_name ?></button>
                                    </div>
                                    <?php
                                else:
                                    ?>
                                    <div class="leagues-wrestler" <?php if ($i >= $limit_wrestlers) echo "style='display:none;'" ?>>
                                        <label>Pending selection <?= $i++ ?></label>
                                        <button class="btn btn-select-wrestler" value="Select">Select</button>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="col-sm">
                                <?php
                                if ($wrestlers_count > $i):
                                    $wrestler = $wrestlers[$i];
                                    $image = get_the_post_thumbnail_url($wrestler->wrestler_id);
                                    $i++;
                                    ?>
                                    <div class="leagues-wrestler"
                                         style="background:url(<?= $image ?>)no-repeat;background-size:cover;background-position: center;">
                                        <button class="btn btn-trade-wrestler"
                                                value="Select"><?= $wrestler->wrestler_name ?></button>
                                    </div>
                                    <?php
                                else:
                                    ?>
                                    <div class="leagues-wrestler" <?php if ($i >= $limit_wrestlers) echo "style='display:none;'" ?>>
                                        <label>Pending selection <?= $i++ ?></label>
                                        <button class="btn btn-select-wrestler" value="Select">Select</button>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="col-sm">
                                <?php
                                if ($wrestlers_count > $i):
                                    $wrestler = $wrestlers[$i];
                                    $image = get_the_post_thumbnail_url($wrestler->wrestler_id);
                                    $i++;
                                    ?>
                                    <div class="leagues-wrestler"
                                         style="background:url(<?= $image ?>)no-repeat;background-size:cover;background-position: center;">
                                        <button class="btn btn-trade-wrestler"
                                                value="Select"><?= $wrestler->wrestler_name ?></button>
                                    </div>
                                    <?php
                                else:
                                    ?>
                                    <div class="leagues-wrestler" <?php if ($i >= $limit_wrestlers) echo "style='display:none;'" ?>>
                                        <label>Pending selection <?= $i++ ?></label>
                                        <button class="btn btn-select-wrestler" value="Select">Select</button>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="col-sm">
                                <?php
                                if ($wrestlers_count > $i):
                                    $wrestler = $wrestlers[$i];
                                    $image = get_the_post_thumbnail_url($wrestler->wrestler_id);
                                    $i++;
                                    ?>
                                    <div class="leagues-wrestler"
                                         style="background:url(<?= $image ?>)no-repeat;background-size:cover;background-position: center;">
                                        <button class="btn btn-trade-wrestler"
                                                value="Select"><?= $wrestler->wrestler_name ?></button>
                                    </div>
                                    <?php
                                else:
                                    ?>
                                    <div class="leagues-wrestler" <?php if ($i >= $limit_wrestlers) echo "style='display:none;'" ?>>
                                        <label>Pending selection <?= $i++ ?></label>
                                        <button class="btn btn-select-wrestler" value="Select">Select</button>
                                    </div>
                                    <?php
                                endif;
                                ?>
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

    <div id="select-wrestler" class="modal" style="overflow: unset;">
        <div>
            <label for="select-wrestler">Select Wrestler</label>
            <select name="wrestler" id="select-wrestler-id" class="wrestlers-select headline">
                <?php
                $wrestlers = get_posts([
                    'post_type' => Wrestlers_Leagues::$post_type_wrestler,
                    'posts_per_page' => -1,
                    'numberposts' => -1
                ]);
                foreach ($wrestlers as $wrestler) {
                    ?>
                    <option value="<?= $wrestler->ID ?>"><?= $wrestler->post_title ?></option>
                    <?php
                }
                ?>
            </select>

        </div>
        <div style="text-align: center; margin: 20px auto;">
            <button class="btn-wrestler-selected" onclick="selectWrestler(event)">Okay</button>
        </div>
    </div>
</div>

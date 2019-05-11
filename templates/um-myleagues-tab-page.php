<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/11/2019
 * Time: 11:11 AM
 */
$user_id = get_current_user_id();
global $wpdb;
?>

<div class="kwl-elements">
    <table id="my-leagues">
        <thead>
        <tr>
            <th>League Name</th>
            <th>Team Name</th>
            <th>Date Joined</th>
            <th>Is Commissioner</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT u.*, lg.post_title AS name FROM wp_kwl_league_user AS u "
            ."JOIN wp_posts AS lg ON u.league_id=lg.ID "
            ." WHERE u.user_id={$user_id}";
            $leagues_joined = $wpdb->get_results($query);
            $row_num = "even";
            foreach ($leagues_joined as $league):
            ?>
                <tr class="<?=$row_num?>">
                    <td><a href="<?=get_permalink($league->league_id)?>"><?=$league->name?></a></td>
                    <td><?=$league->team_name?></td>
                    <td><?=date("Y-m-d H:i:s", $league->date_join)?></td>
                    <td><?=$league->is_commissioner ? "Yes" : "No"?></td>
                    <td><?=$league->status == 1 ? "Joined" : ($league->status == -1 ? "Rejected" : "Waiting")?></td>
                </tr>
            <?php
                $row_num = $row_num == "even" ? "odd" : "even";
            endforeach;
            ?>
        </tbody>
    </table>
    <div class="sm-col-6">

    </div>
</div>

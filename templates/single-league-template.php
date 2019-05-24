<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/20/2019
 * Time: 11:16 AM
 */
get_header(); ?>

<?php do_action('ocean_before_content_wrap');
global $post, $wpdb;
$custom = get_post_custom($post->ID);
$limit_teams_league_meta = $custom[Wrestlers_Leagues::$limit_teams_league_meta][0];
$limit_wrestlers_team_meta = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
$limit_wrestlers_team_meta = intval($limit_wrestlers_team_meta);

$user_id = get_current_user_id();
$is_commissioner = false;

$query_teams = "SELECT l.team_name, l.user_id, l.commissioner_id, u.display_name AS user_name, d.draft_time, d.minutes_selection FROM {$wpdb->prefix}kwl_league_user AS l "
    . "JOIN {$wpdb->prefix}users AS u ON l.user_id=u.ID "
    . "JOIN {$wpdb->prefix}kwl_drafts AS d ON l.league_id=d.league_id "
    . "WHERE l.league_id={$post->ID} ORDER BY l.commissioner_id DESC";
$teams = $wpdb->get_results($query_teams);
if (count($teams) > 0 && $teams[0]->commissioner_id == $user_id) {
    $is_commissioner = true;
}
?>

    <div data-elementor-type="post" data-elementor-id="3191" class="elementor elementor-3191 single-league-area"
         data-elementor-settings="[]">
        <div class="elementor-inner">
            <div class="elementor-section-wrap">
                <section
                        class="elementor-element elementor-element-58d4ed4 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section inner_header"
                        data-id="58d4ed4" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default title-background">
                        <div class="elementor-row">
                            <div class="elementor-element elementor-element-2355bfa elementor-column elementor-col-100 elementor-top-column"
                                 data-id="2355bfa" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <section
                                                class="elementor-element elementor-element-c35b109 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section"
                                                data-id="c35b109" data-element_type="section">
                                            <div class="elementor-container elementor-column-gap-default">
                                                <div class="elementor-row">
                                                    <div class="elementor-element elementor-element-5176ef8 elementor-column elementor-col-20 elementor-inner-column"
                                                         data-id="5176ef8" data-element_type="column">
                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                            <div class="elementor-widget-wrap background_title">
                                                                <div class="elementor-element elementor-element-7688601 elementor-widget elementor-widget-heading"
                                                                     data-id="7688601" data-element_type="widget"
                                                                     data-widget_type="heading.default">
                                                                    <div class="elementor-widget-container master_heading">
                                                                        <h2 class="elementor-heading-title elementor-size-xl ">
                                                                            League Settings:</h2></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="elementor-element elementor-element-9df0f9c elementor-column elementor-col-20 elementor-inner-column"
                                                         data-id="9df0f9c" data-element_type="column">
                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                            <div class="elementor-widget-wrap background_title">
                                                                <div class="elementor-element elementor-element-10a2eb6 elementor-widget elementor-widget-heading"
                                                                     data-id="10a2eb6" data-element_type="widget"
                                                                     data-widget_type="heading.default">
                                                                    <div class="elementor-widget-container master_heading">
                                                                        <h1 class="elementor-heading-title elementor-size-default">
                                                                            Draft
                                                                            time: <?= date('m-d-Y H:i:s', $teams[0]->draft_time) ?></h1>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="elementor-element elementor-element-9df0f9c elementor-column elementor-col-20 elementor-inner-column"
                                                         data-id="9df0f9c" data-element_type="column">
                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                            <div class="elementor-widget-wrap background_title">
                                                                <div class="elementor-element elementor-element-10a2eb6 elementor-widget elementor-widget-heading"
                                                                     data-id="10a2eb6" data-element_type="widget"
                                                                     data-widget_type="heading.default">
                                                                    <div class="elementor-widget-container master_heading">
                                                                        <h1 class="elementor-heading-title elementor-size-default">
                                                                            Team
                                                                            Limit: <?= $limit_teams_league_meta ?></h1>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="elementor-element elementor-element-e6b8b91 elementor-column elementor-col-20 elementor-inner-column"
                                                         data-id="e6b8b91" data-element_type="column">
                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                            <div class="elementor-widget-wrap">
                                                                <div class="elementor-element elementor-element-31e497c elementor-widget elementor-widget-heading"
                                                                     data-id="31e497c" data-element_type="widget"
                                                                     data-widget_type="heading.default">
                                                                    <div class="elementor-widget-container">
                                                                        <h1 class="elementor-heading-title elementor-size-default">
                                                                            Wrestlers: <?= $limit_wrestlers_team_meta ?></h1>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if ($is_commissioner): ?>
                                                        <div class="elementor-element elementor-element-8b020c3 elementor-column elementor-col-20 elementor-inner-column"
                                                             data-id="8b020c3" data-element_type="column">
                                                            <div class="elementor-column-wrap  elementor-element-populated">
                                                                <div class="elementor-widget-wrap">
                                                                    <div class="elementor-element elementor-element-e3a2a5e elementor-widget elementor-widget-button"
                                                                         data-id="e3a2a5e" data-element_type="widget"
                                                                         data-widget_type="button.default">
                                                                        <div class="elementor-widget-container">
                                                                            <div class="elementor-button-wrapper">
                                                                                <a href="#edit-draft-settings"
                                                                                   rel="modal:open"
                                                                                   class="elementor-button-link elementor-button elementor-size-sm custom-button"
                                                                                   role="button">
                                                                                    Edit Draft Settings
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                if ($is_commissioner):
                    $query_join_requests = "SELECT * FROM {$wpdb->prefix}kwl_join_league_requests "
                        . "WHERE league_id={$post->ID} AND status < 2";
                    $join_requests = $wpdb->get_results($query_join_requests);
                    if (count($join_requests) > 0):
                        ?>
                        <section
                                class="elementor-element elementor-element-58d4ed4 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section"
                                data-id="58d4ed4" data-element_type="section">
                            <div class="elementor-container elementor-column-gap-default">
                                <div class="elementor-row">
                                    <div class="elementor-element elementor-element-2355bfa elementor-column elementor-col-100 elementor-top-column"
                                         data-id="2355bfa" data-element_type="column">
                                        <div class="elementor-column-wrap  elementor-element-populated joun_league_area">
                                            <div class="elementor-widget-wrap">
                                                <section
                                                        class="join-request customer-join-request-area elementor-element elementor-element-c35b109 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section"
                                                        data-id="c35b109" data-element_type="section">
                                                    <div class="elementor-container elementor-column-gap-default">
                                                        <div class="elementor-row whole_background">
                                                            <div class="elementor-element elementor-element-5176ef8 elementor-column elementor-col-20 elementor-inner-column"
                                                                 data-id="5176ef8" data-element_type="column">
                                                                <div class="elementor-column-wrap  elementor-element-populated">
                                                                    <div class="elementor-widget-wrap background_title">
                                                                        <div class="elementor-element elementor-element-7688601 elementor-widget elementor-widget-heading"
                                                                             data-id="7688601"
                                                                             data-element_type="widget"
                                                                             data-widget_type="heading.default">
                                                                            <div class="elementor-widget-container">
                                                                                <h3 class="elementor-heading-title elementor-size-xl request_title">
                                                                                    Join Requests:</h3></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                                <?php
                                                foreach ($join_requests as $join_request):

                                                    ?>
                                                    <section
                                                            class="join-request elementor-element elementor-element-c35b109 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section"
                                                            data-id="c35b109" data-element_type="section">
                                                        <div class="elementor-container elementor-column-gap-default">
                                                            <div class="elementor-row whole_background">
                                                                <div class="elementor-element elementor-element-9df0f9c elementor-column elementor-col-40 elementor-inner-column"
                                                                     data-id="9df0f9c" data-element_type="column">
                                                                    <div class="elementor-column-wrap  elementor-element-populated">
                                                                        <div class="elementor-widget-wrap">
                                                                            <div class="elementor-element elementor-element-10a2eb6 elementor-widget elementor-widget-heading"
                                                                                 data-id="10a2eb6"
                                                                                 data-element_type="widget"
                                                                                 data-widget_type="heading.default">
                                                                                <div class="elementor-widget-container">
                                                                                    <h4 class="elementor-heading-title elementor-size-default">
                                                                                        Team
                                                                                        Name: <?= $join_request->team_name ?></h4>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="elementor-element elementor-element-e6b8b91 elementor-column elementor-col-40 elementor-inner-column"
                                                                     data-id="e6b8b91" data-element_type="column">
                                                                    <div class="elementor-column-wrap  elementor-element-populated">
                                                                        <div class="elementor-widget-wrap">
                                                                            <div class="elementor-element elementor-element-31e497c elementor-widget elementor-widget-heading"
                                                                                 data-id="31e497c"
                                                                                 data-element_type="widget"
                                                                                 data-widget_type="heading.default">
                                                                                <div class="elementor-widget-container">
                                                                                    <h4 class="elementor-heading-title elementor-size-default">
                                                                                        Email: <?= $join_request->client_email ?></h4>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                if ($join_request->status == 0):
                                                                    ?>
                                                                    <div class="elementor-element elementor-element-8b020c3 elementor-column elementor-col-10 elementor-inner-column"
                                                                         data-id="8b020c3" data-element_type="column">
                                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                                            <div class="elementor-widget-wrap">
                                                                                <div class="elementor-element elementor-element-e3a2a5e elementor-widget elementor-widget-button"
                                                                                     data-id="e3a2a5e"
                                                                                     data-element_type="widget"
                                                                                     data-widget_type="button.default">
                                                                                    <div class="elementor-widget-container">
                                                                                        <div class="elementor-button-wrapper">
                                                                                            <a href="#"
                                                                                               class="elementor-button-link elementor-button elementor-size-sm accepted"
                                                                                               role="button"
                                                                                               onclick="acceptJoinRequest(<?= $join_request->ID ?>)">
                                                                                                Accept
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="elementor-element elementor-element-8b020c3 elementor-column elementor-col-10 elementor-inner-column"
                                                                         data-id="8b020c3" data-element_type="column">
                                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                                            <div class="elementor-widget-wrap">
                                                                                <div class="elementor-element elementor-element-e3a2a5e elementor-widget elementor-widget-button"
                                                                                     data-id="e3a2a5e"
                                                                                     data-element_type="widget"
                                                                                     data-widget_type="button.default">
                                                                                    <div class="elementor-widget-container">
                                                                                        <div class="elementor-button-wrapper">
                                                                                            <a href="#"
                                                                                               class="elementor-button-link elementor-button elementor-size-sm rejected"
                                                                                               role="button"
                                                                                               onclick="rejectJoinRequest(<?= $join_request->ID ?>)">
                                                                                                Reject
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                else:
                                                                    ?>
                                                                    <div class="elementor-element elementor-element-e6b8b91 elementor-column elementor-col-20 elementor-inner-column"
                                                                         data-id="e6b8b91" data-element_type="column">
                                                                        <div class="elementor-column-wrap  elementor-element-populated">
                                                                            <div class="elementor-widget-wrap">
                                                                                <div class="elementor-element elementor-element-31e497c elementor-widget elementor-widget-heading"
                                                                                     data-id="31e497c"
                                                                                     data-element_type="widget"
                                                                                     data-widget_type="heading.default">
                                                                                    <div class="elementor-widget-container">
                                                                                        <h4 class="elementor-heading-title elementor-size-default">
                                                                                            <?= $join_request->status == 1 ? "Waiting client" : "Declined" ?></h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <?php
                                                                endif;
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <?php
                                                endforeach;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <?php
                    endif;
                endif;
                ?>
                <div class="team_list_area">
                    <section
                            class="elementor-element elementor-element-8793d97 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section section_margin"
                            data-id="8793d97" data-element_type="section">
                        <div class="elementor-container elementor-column-gap-default">
                            <div class="elementor-row">
                                <div class="elementor-element elementor-element-ddba832 elementor-column elementor-col-100 elementor-top-column"
                                     data-id="ddba832" data-element_type="column">
                                    <div class="elementor-column-wrap  elementor-element-populated">
                                        <div class="elementor-widget-wrap">
                                            <div class="elementor-element elementor-element-a3df4bf elementor-widget elementor-widget-heading"
                                                 data-id="a3df4bf" data-element_type="widget"
                                                 data-widget_type="heading.default">
                                                <div class="elementor-widget-container">
                                                    <h2 class="elementor-heading-title elementor-size-xl team_name">
                                                        Teams
                                                        of <?= $post->post_title ?></h2></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php
                    $i = 0;
                    foreach ($teams as $team):
                        $i++;
                        $query_wrestlers = "SELECT w.ID AS wrestler_id, w.post_title AS wrestler_name FROM {$wpdb->prefix}kwl_league_wrestler AS l "
                            . "JOIN {$wpdb->prefix}posts AS w ON l.wrestler_id=w.ID "
                            . "WHERE l.league_id={$post->ID} AND l.user_id={$team->user_id}";
                        $wrestlers = $wpdb->get_results($query_wrestlers);
                        ?>
                        <section
                                class="single_team_area elementor-element elementor-element-b951835 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section section_margin"
                                data-id="b951835" data-element_type="section">
                            <div class="elementor-container elementor-column-gap-default">
                                <div class="elementor-row">
                                    <div class="elementor-element elementor-element-115f787 elementor-column elementor-col-100 elementor-top-column"
                                         data-id="115f787" data-element_type="column">
                                        <div class="elementor-column-wrap  elementor-element-populated">
                                            <div class="elementor-widget-wrap">
                                                <div class="elementor-element elementor-element-4bfbb74 elementor-widget elementor-widget-premium-addon-title"
                                                     data-id="4bfbb74" data-element_type="widget"
                                                     data-widget_type="premium-addon-title.default">
                                                    <div class="elementor-widget-container">

                                                        <div class="premium-title-container">
                                                            <h1 class="premium-title-header premium-title-style4 extra_title">
                                                                <span>#<?= $i ?> <?= $team->team_name ?>            </span>
                                                            </h1>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="elementor-element elementor-element-28f9a09 elementor-widget elementor-widget-premium-addon-title"
                                                     data-id="28f9a09" data-element_type="widget"
                                                     data-widget_type="premium-addon-title.default">
                                                    <div class="elementor-widget-container">

                                                        <div class="premium-title-container">
                                                            <h2 class="premium-title-header">
                                                                <span>Owned by <?= $team->user_name ?></span>
                                                            </h2>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="elementor-element elementor-element-64bfb39 elementor-widget elementor-widget-bdt-tablepress"
                                                     data-id="64bfb39" data-element_type="widget"
                                                     data-widget_type="bdt-tablepress.default">
                                                    <div class="elementor-widget-container">
                                                        <div class="bdt-tablepress">
                                                            <div id="tablepress-9_wrapper"
                                                                 class="dataTables_wrapper no-footer">
                                                                <div class="dataTables_length" id="tablepress-9_length">
                                                                    <label>Show <select name="tablepress-9_length"
                                                                                        aria-controls="tablepress-9"
                                                                                        class="">
                                                                            <option value="10">10</option>
                                                                            <option value="25">25</option>
                                                                            <option value="50">50</option>
                                                                            <option value="100">100</option>
                                                                        </select> entries</label></div>
                                                                <div id="tablepress-9_filter" class="dataTables_filter">
                                                                    <label>Search:<input type="search" class=""
                                                                                         placeholder=""
                                                                                         aria-controls="tablepress-9"></label>
                                                                </div>
                                                                <table id="tablepress-9"
                                                                       class="tablepress tablepress-id-9 dataTable no-footer"
                                                                       role="grid" aria-describedby="tablepress-9_info">
                                                                    <thead>
                                                                    <tr class="row-1 odd" role="row">
                                                                        <th class="column-1 sorting" tabindex="0"
                                                                            aria-controls="tablepress-9" rowspan="1"
                                                                            colspan="1"
                                                                            aria-label="Wrestlers: activate to sort column ascending"
                                                                            style="width: 210px;">Wrestlers
                                                                        </th>
                                                                        <th class="column-2 sorting" tabindex="0"
                                                                            aria-controls="tablepress-9" rowspan="1"
                                                                            colspan="1"
                                                                            aria-label="Season Points: activate to sort column ascending"
                                                                            style="width: 227px;">Season Points
                                                                        </th>
                                                                        <th class="column-3 sorting" tabindex="0"
                                                                            aria-controls="tablepress-9" rowspan="1"
                                                                            colspan="1"
                                                                            aria-label="Points Last 30: activate to sort column ascending"
                                                                            style="width: 224px;">Points Last 30
                                                                        </th>
                                                                        <th class="column-4 sorting" tabindex="0"
                                                                            aria-controls="tablepress-9" rowspan="1"
                                                                            colspan="1"
                                                                            aria-label="Points Last 7: activate to sort column ascending"
                                                                            style="width: 206px;">Points Last 7
                                                                        </th>
                                                                        <th class="column-5 sorting" tabindex="0"
                                                                            aria-controls="tablepress-9" rowspan="1"
                                                                            colspan="1"
                                                                            aria-label="Brand: activate to sort column ascending"
                                                                            style="width: 172px;">Brand
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="row-hover">
                                                                    <?php
                                                                    $j = 0;
                                                                    foreach ($wrestlers as $wrestler):
                                                                        $j++;
                                                                        ?>
                                                                        <tr class="row-<?= $j ?> <?= $j % 2 == 0 ? "even" : "odd" ?>"
                                                                            role="row">
                                                                            <td class="column-1"><?= $wrestler->wrestler_name ?></td>
                                                                            <td class="column-2">80</td>
                                                                            <td class="column-3">60</td>
                                                                            <td class="column-4">20</td>
                                                                            <td class="column-5">205live</td>
                                                                        </tr>
                                                                        <?php
                                                                    endforeach;
                                                                    for (; $j < $limit_wrestlers_team_meta; $j++):
                                                                        ?>
                                                                        <tr class="row-<?= $j ?> <?= $j % 2 == 0 ? "even" : "odd" ?>"
                                                                            role="row">
                                                                            <td class="column-1">Pending selection...
                                                                            </td>
                                                                            <td class="column-2">0</td>
                                                                            <td class="column-3">0</td>
                                                                            <td class="column-4">0</td>
                                                                            <td class="column-5"></td>
                                                                        </tr>
                                                                        <?php
                                                                    endfor;
                                                                    ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <?php
                    endforeach;
                    ?>
                </div>

                <div id="edit-draft-settings" class="modal" style="overflow: unset;">
                    <form method="post">
                        <div class="elementor-form-fields-wrapper elementor-labels-above">
                            <input type="hidden" name="action" value="save_draft_settings">
                            <input type="hidden" name="post_id" value="<?=$post->ID?>">
                            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100">
                                <label for="input-draft-time">Draft Time</label>
                                <input type="datetime-local" name="draft_time" id="input-draft-time"
                                       class="input-draft-time headline"
                                       value="<?= date("Y-m-d\TH:i:s", $teams[0]->draft_time) ?>">
                            </div>
                            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100">
                                <label for="input-limit-teams">Number of Players</label>
                                <input type="number" name="limit_teams" id="input-limit-teams"
                                       class="input-limit-teams headline" value="<?= $limit_teams_league_meta ?>">
                            </div>
                            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100">
                                <label for="input-limit-wrestlers">Number of Wrestlers Each Player</label>
                                <input type="number" name="limit_wrestlers" id="input-limit-wrestlers"
                                       class="input-limit-wrestlers headline" value="<?= $limit_wrestlers_team_meta ?>">
                            </div>
                            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100">
                                <label for="input-minutes-selection">Minutes Per Selection</label>
                                <input type="number" name="minutes_selection" id="input-minutes-selection"
                                       class="input-minutes-selection headline"
                                       value="<?= $teams[0]->minutes_selection ?>">
                            </div>
                            <div style="text-align: center; margin: 10px auto;"
                                 class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                                <button class="button btn-save-draft-settings elementor-button elementor-size-sm"
                                        style="margin: auto;">Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php do_action('ocean_after_content_wrap'); ?>

<?php get_footer(); ?>
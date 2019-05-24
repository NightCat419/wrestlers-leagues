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

<div data-elementor-type="post" data-elementor-id="3191" class="elementor elementor-3191"
     data-elementor-settings="[]">
    <div class="elementor-inner">
        <div class="elementor-section-wrap">

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
                                                <h2 class="elementor-heading-title elementor-size-xl team_name">Your Leagues</h2></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
                $i = 0;
                foreach ($leagues_joined as $league):
                    $custom = get_post_custom($league->league_id);
                    $limit_wrestlers_team_meta = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
                    $limit_wrestlers_team_meta = intval($limit_wrestlers_team_meta);

                    $wrestlers = $wpdb->get_results("SELECT lw.wrestler_id, p.post_title wrestler_name FROM {$wpdb->prefix}kwl_league_wrestler lw "
                        . "JOIN {$wpdb->prefix}posts p ON lw.wrestler_id = p.ID "
                        . "WHERE lw.league_id = {$league->league_id} AND lw.user_id = {$user_id} "
                        ."ORDER BY lw.ID ASC");
                    $wrestlers_count = count($wrestlers);
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
                                                            <span><a href="<?= get_permalink($league->league_id) ?>"><?= $league->name ?></a></span>
                                                        </h1>
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
                                                                        <td class="column-1">Pending selection...</td>
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


        </div>
    </div>
</div>

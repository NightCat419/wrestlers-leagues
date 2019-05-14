<?php
/**
 * The template for displaying all pages, single posts and attachments
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @package OceanWP WordPress theme
 */

get_header(); ?>

<?php do_action('ocean_before_content_wrap'); ?>

<div data-elementor-type="kwl_wrestlers" class="elementor">
    <?php do_action('ocean_before_primary'); ?>

    <div class="elementor-inner">
        <?php do_action('ocean_before_content'); ?>

        <div id="content" class="site-content clr">

            <?php do_action('ocean_before_content_inner'); ?>

            <?php
            global $post, $wpdb;
            $wrestlerImage = get_the_post_thumbnail_url( $post, 'post-thumbnail' );
            $brand = get_the_terms($post, Wrestlers_Leagues::$taxonomy_wrestlers_brand);
            if (count($brand) > 0) {
                $logo = $wl_instance->brand_logos[$brand[0]->slug];
                $back = $wl_instance->brand_back_images[$brand[0]->slug];
            }
            $recent_date = strtotime('-30 days');
            $season_start = strtotime(date('Y-01-01'));
            $recent_matches = $wpdb->get_results(
                    "SELECT m.*, w.post_title winner_name, l.post_title loser_name FROM {$wpdb->prefix}kwl_matches m "
                    ."JOIN {$wpdb->prefix}posts w ON m.winner_id = w.ID "
                    ."JOIN {$wpdb->prefix}posts l ON m.loser_id=l.ID "
                    ."WHERE (winner_id={$post->ID} OR loser_id={$post->ID}) AND dateline > {$recent_date} "
                    ."ORDER BY dateline DESC");

            $recent_bonuses = $wpdb->get_results(
                "SELECT b.*, w.post_title wrestler_name FROM {$wpdb->prefix}kwl_bonuses b "
                ."JOIN {$wpdb->prefix}posts w ON b.wrestler_id = w.ID "
                ."WHERE wrestler_id={$post->ID} AND dateline > {$recent_date} "
                ."ORDER BY dateline DESC");

            $all_matches = $wpdb->get_results(
                    "SELECT SUM(winner_points) points, COUNT(name) matches, name FROM {$wpdb->prefix}kwl_matches "
                    ."WHERE winner_id={$post->ID} AND dateline > {$season_start} "
                    ."GROUP BY name"
            );

            $all_bonuses = $wpdb->get_results(
                "SELECT SUM(bonus_points) points, COUNT(provider_name) instances, provider_name FROM {$wpdb->prefix}kwl_bonuses "
                ."WHERE wrestler_id={$post->ID} AND dateline > {$season_start} "
                ."GROUP BY provider_name"
            );
            ?>
            <div class="elementor-section-wrap wrestler-content">
                <section
                        class="elementor-element elementor-element-3de57d4 elementor-hidden-phone elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section"
                        data-id="3de57d4" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-row">
                            <div class="elementor-element elementor-element-598f344 elementor-column elementor-col-100 elementor-top-column"
                                 data-id="598f344" data-element_type="column">
                                <div class="elementor-column-wrap">
                                    <div class="elementor-widget-wrap"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section
                        class="elementor-element elementor-element-49a0d2e elementor-section-content-middle elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section"
                        data-id="49a0d2e" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-row">
                            <div class="elementor-element elementor-element-f8415c6 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="f8415c6" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-1392d6b7 bdt-flip-box-effect-push bdt-flip-box-direction-left elementor-widget elementor-widget-bdt-flip-box"
                                             data-id="1392d6b7" data-element_type="widget"
                                             data-widget_type="bdt-flip-box.default">
                                            <div class="elementor-widget-container">
                                                <div class="bdt-flip-box">
                                                    <div class="bdt-flip-box-layer bdt-flip-box-front" style="background-image: url(<?=$wrestlerImage?>);">
                                                        <div class="bdt-flip-box-layer-overlay">
                                                            <div class="bdt-flip-box-layer-inner"></div>
                                                        </div>
                                                    </div>
                                                    <div class="bdt-flip-box-layer bdt-flip-box-back" style="background-image: url(<?=$back?>);">
                                                        <div class="bdt-flip-box-layer-overlay">
                                                            <div class="bdt-flip-box-layer-inner"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-0d7262b elementor-widget elementor-widget-heading"
                                             data-id="0d7262b" data-element_type="widget"
                                             data-widget_type="heading.default">
                                            <div class="elementor-widget-container"><h2
                                                        class="elementor-heading-title elementor-size-default"><?=$post->post_title?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-9bdbc62 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="9bdbc62" data-element_type="column">
                                <div class="elementor-column-wrap">
                                    <div class="elementor-widget-wrap"></div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-6b0be97 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="6b0be97" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-e84cd2e elementor-hidden-phone elementor-widget elementor-widget-image"
                                             data-id="e84cd2e" data-element_type="widget"
                                             data-widget_type="image.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-image"><img
                                                            src="<?= $logo; ?>"
                                                            title="Raw Transparent Background"
                                                            alt="Raw Transparent Background"></div>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-349756e elementor-icon-list--layout-traditional elementor-widget elementor-widget-icon-list"
                                             data-id="349756e" data-element_type="widget"
                                             data-widget_type="icon-list.default">
                                            <div class="elementor-widget-container">
                                                <ul class="elementor-icon-list-items">
                                                    <li class="elementor-icon-list-item"><span
                                                                class="elementor-icon-list-icon"> <i class="fa fa-check"
                                                                                                     aria-hidden="true"></i> </span>
                                                        <span class="elementor-icon-list-text">0 points this season</span>
                                                    </li>
                                                    <li class="elementor-icon-list-item"><span
                                                                class="elementor-icon-list-icon"> <i class="fa fa-check"
                                                                                                     aria-hidden="true"></i> </span>
                                                        <span class="elementor-icon-list-text">0 points last 30 days</span>
                                                    </li>
                                                    <li class="elementor-icon-list-item"><span
                                                                class="elementor-icon-list-icon"> <i class="fa fa-check"
                                                                                                     aria-hidden="true"></i> </span>
                                                        <span class="elementor-icon-list-text">Owned in 1.2% of leagues</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section
                        class="elementor-element elementor-element-5b5ef0f elementor-hidden-tablet elementor-hidden-phone elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section"
                        data-id="5b5ef0f" data-element_type="section"
                        data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-row">
                            <div class="elementor-element elementor-element-66ac016 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="66ac016" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-7f1e188 elementor-widget elementor-widget-heading"
                                             data-id="7f1e188" data-element_type="widget"
                                             data-widget_type="heading.default">
                                            <div class="elementor-widget-container"><h2
                                                        class="elementor-heading-title elementor-size-default">Recent
                                                    Matches</h2></div>
                                        </div>
                                        <div class="elementor-element elementor-element-6ee409d elementor-widget elementor-widget-bdt-tablepress"
                                             data-id="6ee409d" data-element_type="widget"
                                             data-widget_type="bdt-tablepress.default">
                                            <div class="elementor-widget-container">
                                                <div class="bdt-tablepress">
                                                    <table id="tablepress-7" class="tablepress tablepress-id-7">
                                                        <tbody class="row-hover">
                                                        <?php
                                                        $row = 0;
                                                        foreach ($recent_matches as $match):
                                                            $row++;
                                                        ?>
                                                            <tr class="row-<?=$row?>">
                                                                <td class="column-1"><?=date('m/d/Y', $match->dateline)?></td>
                                                                <td class="column-2"><?php echo "{$match->winner_name} {$match->name} Win vs {$match->loser_name} {$match->desc}" ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        endforeach;
                                                        ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-36a6264 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="36a6264" data-element_type="column">
                                <div class="elementor-column-wrap">
                                    <div class="elementor-widget-wrap"></div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-66ac016 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="66ac016" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-7f1e188 elementor-widget elementor-widget-heading"
                                             data-id="b40f775" data-element_type="widget"
                                             data-widget_type="heading.default">
                                            <div class="elementor-widget-container"><h2
                                                        class="elementor-heading-title elementor-size-default">Recent
                                                    Bonus</h2></div>
                                        </div>
                                        <div class="elementor-element elementor-element-6ee409d elementor-widget elementor-widget-bdt-tablepress"
                                             data-id="60f1b6d" data-element_type="widget"
                                             data-widget_type="bdt-tablepress.default">
                                            <div class="elementor-widget-container">
                                                <div class="bdt-tablepress">
                                                    <table id="tablepress-6" class="tablepress tablepress-id-6">
                                                        <tbody class="row-hover">
                                                        <?php
                                                        $row = 0;
                                                        foreach ($recent_bonuses as $bonus):
                                                            $row++;
                                                            ?>
                                                            <tr class="row-<?=$row?>">
                                                                <td class="column-1"><?=date('m/d/Y', $bonus->dateline)?></td>
                                                                <td class="column-2"><?php echo "{$bonus->wrestler_name} receives {$bonus->bonus_points} points for {$bonus->provider_name}" ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        endforeach;
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
                </section>
                <section
                        class="elementor-element elementor-element-5b5ef0f elementor-hidden-tablet elementor-hidden-phone elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section"
                        data-id="5b5ef0f" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-row">
                            <div class="elementor-element elementor-element-2e4b743 elementor-column elementor-col-33 elementor-top-column"
                                 data-id="2e4b743" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-34994bb elementor-widget elementor-widget-heading"
                                             data-id="34994bb" data-element_type="widget"
                                             data-widget_type="heading.default">
                                            <div class="elementor-widget-container"><h2
                                                        class="elementor-heading-title elementor-size-default">Match
                                                    Points (all-time)</h2></div>
                                        </div>
                                        <div class="elementor-element elementor-element-46d6c3d elementor-widget elementor-widget-bdt-tablepress"
                                             data-id="46d6c3d" data-element_type="widget"
                                             data-widget_type="bdt-tablepress.default">
                                            <div class="elementor-widget-container">
                                                <div class="bdt-tablepress">
                                                    <div id="tablepress-5_wrapper" class="dataTables_wrapper no-footer">
                                                        <table id="tablepress-5"
                                                               class="tablepress tablepress-id-5 dataTable no-footer"
                                                               role="grid" aria-describedby="tablepress-5_info">
                                                            <thead>
                                                            <tr class="row-1 odd" role="row">
                                                                <th class="column-1 sorting" tabindex="0"
                                                                    aria-controls="tablepress-5" rowspan="1" colspan="1"
                                                                    aria-label="Points: activate to sort column ascending"
                                                                    style="width: 84px;">Points
                                                                </th>
                                                                <th class="column-2 sorting" tabindex="0"
                                                                    aria-controls="tablepress-5" rowspan="1" colspan="1"
                                                                    aria-label="Matches: activate to sort column ascending"
                                                                    style="width: 107px;">Matches
                                                                </th>
                                                                <th class="column-3 sorting" tabindex="0"
                                                                    aria-controls="tablepress-5" rowspan="1" colspan="1"
                                                                    aria-label="Stipulation: activate to sort column ascending"
                                                                    style="width: 266px;">Stipulation
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="row-hover">
                                                            <?php
                                                            $row = 1;
                                                            $sum_match_points = 0;
                                                            $sum_matches = 0;
                                                            foreach ($all_matches as $match):
                                                                $row++;
                                                                $sum_match_points += $match->points;
                                                                $sum_matches += $match->matches;
                                                                ?>
                                                                <tr class="row-<?=$row?> <?= $row%2 == 0 ? "even" : "odd" ?>">
                                                                    <td class="column-1"><?=$match->points?></td>
                                                                    <td class="column-2"><?=$match->matches?></td>
                                                                    <td class="column-3"><?=$match->name?></td>
                                                                </tr>
                                                                <?php
                                                            endforeach;
                                                            $row++;
                                                            ?>
                                                            <tr class="row-<?=$row?> <?= $row%2 == 0 ? "even" : "odd" ?>">
                                                                <td class="column-1"><?=$sum_match_points?></td>
                                                                <td class="column-2"><?=$sum_matches?></td>
                                                                <td class="column-3">SUM</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-ffd6f1d elementor-column elementor-col-33 elementor-top-column"
                                 data-id="ffd6f1d" data-element_type="column">
                                <div class="elementor-column-wrap">
                                    <div class="elementor-widget-wrap"></div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-28453de elementor-column elementor-col-33 elementor-top-column"
                                 data-id="28453de" data-element_type="column">
                                <div class="elementor-column-wrap  elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-58efad1 elementor-widget elementor-widget-heading"
                                             data-id="58efad1" data-element_type="widget"
                                             data-widget_type="heading.default">
                                            <div class="elementor-widget-container"><h2
                                                        class="elementor-heading-title elementor-size-default">Bonus
                                                    Points (all-time)</h2></div>
                                        </div>
                                        <div class="elementor-element elementor-element-46d6c3d elementor-widget elementor-widget-bdt-tablepress"
                                             data-id="48f4356" data-element_type="widget"
                                             data-widget_type="bdt-tablepress.default">
                                            <div class="elementor-widget-container">
                                                <div class="bdt-tablepress">
                                                    <div id="tablepress-8_wrapper" class="dataTables_wrapper no-footer">
                                                        <table id="tablepress-8"
                                                               class="tablepress tablepress-id-8 dataTable no-footer"
                                                               role="grid" aria-describedby="tablepress-8_info">
                                                            <thead>
                                                            <tr class="row-1 odd" role="row">
                                                                <th class="column-1 sorting" tabindex="0"
                                                                    aria-controls="tablepress-8" rowspan="1" colspan="1"
                                                                    aria-label="Points: activate to sort column ascending"
                                                                    style="width: 91px;">Points
                                                                </th>
                                                                <th class="column-2 sorting" tabindex="0"
                                                                    aria-controls="tablepress-8" rowspan="1" colspan="1"
                                                                    aria-label="Instances: activate to sort column ascending"
                                                                    style="width: 133px;">Instances
                                                                </th>
                                                                <th class="column-3 sorting" tabindex="0"
                                                                    aria-controls="tablepress-8" rowspan="1" colspan="1"
                                                                    aria-label="Description: activate to sort column ascending"
                                                                    style="width: 276px;">Description
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody class="row-hover">
                                                            <?php
                                                            $row = 1;
                                                            $sum_bonus_points = 0;
                                                            $sum_instances = 0;
                                                            foreach ($all_bonuses as $bonus):
                                                                $row++;
                                                                $sum_bonus_points += $bonus->points;
                                                                $sum_instances += $bonus->instances;
                                                                ?>
                                                                <tr class="row-<?=$row?> <?= $row%2 == 0 ? "even" : "odd" ?>">
                                                                    <td class="column-1"><?=$bonus->points?></td>
                                                                    <td class="column-2"><?=$bonus->instances?></td>
                                                                    <td class="column-3"><?=$bonus->provider_name?></td>
                                                                </tr>
                                                                <?php
                                                            endforeach;
                                                            $row++;
                                                            ?>

                                                            <tr class="row-<?=$row?> <?= $row%2 == 0 ? "even" : "odd" ?>">
                                                                <td class="column-1"><?=$sum_bonus_points?></td>
                                                                <td class="column-2"><?=$sum_instances?></td>
                                                                <td class="column-3">SUM</td>
                                                            </tr>

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
            </div>
            <?php do_action('ocean_after_content_inner'); ?>

        </div><!-- #content -->

        <?php do_action('ocean_after_content'); ?>

    </div><!-- #primary -->

    <?php do_action('ocean_after_primary'); ?>

</div><!-- #content-wrap -->

<?php do_action('ocean_after_content_wrap'); ?>

<?php get_footer(); ?>

<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/9/2019
 * Time: 3:16 AM
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header">
    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
</header><!-- .entry-header -->

<div class="entry-content">
    <?php
    the_content();

    ?>
    <strong>Custom Post Template Test</strong>
</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->

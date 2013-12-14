<a href="<?php echo $context->getURL(); ?>"><?php echo $context->getDateTime()->format('j')?></a>
<ul class="month-day-listing">
<?php
foreach ($context as $e) {
    //Start building an array of row classes
    $classes = array('event');

    if ($e->isAllDay()) {
        $classes[] = 'all-day';
    }

    if ($e->isInProgress()) {
        $classes[] = 'in-progress';
    }

    if ($e->isOnGoing()) {
        $classes[] = 'ongoing';
    }
    
    ?>
    <li class="<?php echo implode(' ', $classes); ?>">
        <?php echo $savvy->render($e, 'EventInstance/Date.tpl.php') ?> <span class="date-title-separator">:</span>
        <a href="<?php echo $e->getURL(); ?>"><?php echo $savvy->dbStringtoHtml($e->event->title)?></a>
    </li>
    <?php
}
?>
</ul>
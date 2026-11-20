<form id="searchform" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <div>
        <input id="s" name="s" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr__('Search', 'finesse'); ?>&hellip;"/>
        <input id="searchsubmit" type="submit" value="<?php echo esc_attr__('Search', 'finesse'); ?>"/>
    </div>
</form>
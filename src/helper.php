<?php

//  Check Function Exists
if(!function_exists('pds_add_filter')) {

    //  Add Filter
    function pds_add_filter($key, $callback, $order = 10) {

        //  Get the Filters
        global $pds_filters;
        if(!$pds_filters)   $pds_filters = array();

        //  Check Keys
        if(!isset($pds_filters[$key]))    $pds_filters[$key] = array();
        if(!isset($pds_filters[$key][$order]))    $pds_filters[$key][$order] = array();

        //  Store the Filter
        $pds_filters[$key][$order][] = $callback;
    }
}

//  Check Function Exists
if(!function_exists('pds_apply_filters')) {

    //  Apply Filters
    function pds_apply_filters($key, $output) {

        //  Get the Filters
        global $pds_filters;
        if(!$pds_filters)   $pds_filters = array();

        //  Check for Filters
        $filters = (isset($pds_filters[$key]) ? $pds_filters[$key] : array());

        //  Sort the Filters
        ksort($filters);

        //  Args to Pass
        $args = func_get_args();
        unset($args[0]);

        //  Loop Each
        foreach($filters as $thisFilters) {

            //  Loop Each Filters
            foreach($thisFilters as $filter) {

                //  Override
                $args[0] = $output;

                //  Run the Filter
                $output = call_user_func_array($filter, $args);
            }
        }

        //  Return
        return $output;
    }
}

//  Check Function Exists
if(!function_exists('pds_add_action')) {

    //  Add Action
    function pds_add_action($key, $callback, $order = 10) {

        //  Get the Actions
        global $pds_actions;
        if(!$pds_actions)   $pds_actions = array();

        //  Check Keys
        if(!isset($pds_actions[$key]))    $pds_actions[$key] = array();
        if(!isset($pds_actions[$key][$order]))    $pds_actions[$key][$order] = array();

        //  Store the Action
        $pds_actions[$key][$order][] = $callback;
    }
}

//  Check Function Exists
if(!function_exists('pds_do_action')) {

    //  Do Action
    function pds_do_action($key) {

        //  Get the Actions
        global $pds_actions;
        if(!$pds_actions)   $pds_actions = array();

        //  Check for Actions
        $actions = (isset($pds_actions[$key]) ? $pds_actions[$key] : array());

        //  Sort the Actions
        ksort($actions);

        //  Args to Pass
        $args = func_get_args();
        unset($args[0]);

        //  Loop Each
        foreach($actions as $thisActions) {

            //  Loop Each Filters
            foreach($thisActions as $filter) {

                //  Run the Action
                $break = call_user_func_array($filter, $args);

                //  Check
                if($break)  break;
            }
        }
    }
}
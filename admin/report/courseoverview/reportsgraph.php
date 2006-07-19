<?php // $Id$

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->dirroot.'/lib/graphlib.php');

    $report     = required_param('report', PARAM_INT);
    $time       = required_param('time', PARAM_INT);
    $numcourses = required_param('numcourses', PARAM_INT);

    require_login();

    if (!isadmin()) {
        error("You must be an admin to use this page");
    }

    stats_check_uptodate($course->id);

    $param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);

    $sql = "SELECT courseid,".$param->fields." FROM ".$CFG->prefix.'stats_'.$param->table
        ." WHERE timeend >= ".$param->timeafter
        ." GROUP BY courseid "
        .$param->extras
        ." ORDER BY ".$param->orderby
        ." LIMIT ".$numcourses;
    
    $courses = get_records_sql($sql);
    
    if (empty($courses)) {
        error(get_string('statsnodata'),$CFG->wwwroot.'/'.$CFG->admin.'/report/course/index.php');
    }
    

    $graph = new graph(750,400);

    $graph->parameter['legend'] = 'outside-right';
    $graph->parameter['legend_size'] = 10;
    $graph->parameter['x_axis_angle'] = 90;
    $graph->parameter['title'] = false; // moodle will do a nicer job.
    if ($report != STATS_REPORT_ACTIVE_COURSES) {
        $graph->parameter['y_decimal_left'] = 2;
    }

    foreach ($courses as $c) {
        $graph->x_data[] = get_field('course','shortname','id',$c->courseid);
        $graph->y_data['bar1'][] = $c->{$param->graphline};
    }
    $graph->y_order = array('bar1');
    $graph->y_format['bar1'] = array('colour' => 'blue','bar' => 'fill','legend' => $param->{$param->graphline});

    $graph->draw_stack();
    
?>

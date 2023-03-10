<?php

$rrd_filename = get_port_rrdfile_path($device['hostname'], $port['port_id'], 'xdsl2LineStatusActAtp');

$rrd_list[0]['filename'] = $rrd_filename;
$rrd_list[0]['descr'] = 'Central to CPE';
$rrd_list[0]['ds'] = 'ds';

$rrd_list[1]['filename'] = $rrd_filename;
$rrd_list[1]['descr'] = 'CPE to Central';
$rrd_list[1]['ds'] = 'us';

$unit_text = 'Dbm';

$units = '';
$total_units = '';
$colours = 'mixed';

$scale_min = '0';

$nototal = 1;

if ($rrd_list) {
    include 'includes/html/graphs/generic_multi_line.inc.php';
}

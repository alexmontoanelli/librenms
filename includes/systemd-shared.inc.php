<?php

// The 'load' and 'active' states only have
// two layers of depth in the systemd_mapper
// associative array.  The 'sub' state has three
// layers.  If another state type is introduced
// with three layers it must be added here.
$state_type_ternary_depth = ['sub'];

// Global variable used by the systemd application to
// build graphs, rrd names and descripts, and parse
// the systemd.py script results.
$systemd_mapper = [
    'load' => [
        'stub',
        'loaded',
        'not-found',
        'bad-setting',
        'error',
        'merged',
        'masked',
        'total',
    ],
    'active' => [
        'active',
        'reloading',
        'inactive',
        'failed',
        'activating',
        'deactivating',
        'maintenance',
        'total',
    ],
    'sub' => [
        'automount' => [
            'dead',
            'waiting',
            'running',
            'failed',
            'total',
        ],
        'device' => [
            'dead',
            'tentative',
            'plugged',
            'total',
        ],
        'freezer' => [
            'running',
            'freezing',
            'frozen',
            'thawing',
            'total',
        ],
        'mount' => [
            'dead',
            'mounting',
            'mounting-done',
            'mounted',
            'remounting',
            'unmounting',
            'remounting-sigterm',
            'remounting-sigkill',
            'unmounting-sigterm',
            'unmounting-sigkill',
            'failed',
            'cleaning',
            'total',
        ],
        'path' => [
            'dead',
            'waiting',
            'running',
            'failed',
            'total',
        ],
        'scope' => [
            'dead',
            'start-chown',
            'running',
            'abandoned',
            'stop-sigterm',
            'stop-sigkill',
            'failed',
            'total',
        ],
        'service' => [
            'dead',
            'condition',
            'start-pre',
            'start',
            'start-post',
            'running',
            'exited',
            'reload',
            'stop',
            'stop-watchdog',
            'stop-sigterm',
            'stop-sigkill',
            'stop-post',
            'final-watchdog',
            'final-sigterm',
            'final-sigkill',
            'failed',
            'auto-restart',
            'cleaning',
            'total',
        ],
        'slice' => [
            'dead',
            'active',
            'total',
        ],
        'socket' => [
            'dead',
            'start-pre',
            'start-chown',
            'start-post',
            'listening',
            'running',
            'stop-pre',
            'stop-pre-sigterm',
            'stop-pre-sigkill',
            'stop-post',
            'final-sigterm',
            'final-sigkill',
            'failed',
            'cleaning',
            'total',
        ],
        'swap' => [
            'dead',
            'activating',
            'activating-done',
            'active',
            'deactivating',
            'deactivating-sigterm',
            'deactivating-sigkill',
            'failed',
            'cleaning',
            'total',
        ],
        'target' => [
            'dead',
            'active',
            'total',
        ],
        'timer' => [
            'dead',
            'waiting',
            'running',
            'elapsed',
            'failed',
            'total',
        ],
    ],
];

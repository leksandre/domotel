<?php
if ($_REQUEST['type'] == 'init') {
    switch ($_REQUEST['step']) {
        case 'complex':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '13' ? 'building2.json' : 'building.json';
            break;
        case 'building':
        case 'section':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '2' ? 'floor2.json' : 'floor.json';
            break;
        case 'floor':
        case 'flat':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '2' ? 'flat2.json' : 'flat.json';
            break;
    }
} elseif ($_REQUEST['type'] == 'filter') {
    switch ($_REQUEST['step']) {
        default:
            $file = 'filter.json';
            break;
    }
} elseif ($_REQUEST['type'] == 'changeRotate') {
    switch ($_REQUEST['step']) {
        case 'complex':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '198' ? 'building.json' : 'building2.json';
            break;
        case 'building':
        case 'section':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '2' ? 'floor2.json' : 'floor.json';
            break;
        case 'floor':
        case 'flat':
            $file = isset($_REQUEST['rotateId']) && $_REQUEST['rotateId'] == '2' ? 'flat2.json' : 'flat.json';
            break;
    }
}

die(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/tests/visual/'.$file));

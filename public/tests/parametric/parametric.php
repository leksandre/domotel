<?php

if ($_REQUEST['type'] == 'init' || $_REQUEST['type'] == 'reset' || $_REQUEST['type'] == 'sort') {
    $file = 'parametric-test-pagination.json';
} elseif ($_REQUEST['type'] == 'pagination') {
    $file = 'parametric-test-more-pagination.json';
} elseif ($_REQUEST['type'] == 'filter') {
    $file = 'parametric-test-filter-pagination.json';
}

/* Раскоментировать для тестирования варианта без пагинации */
/* if ($_REQUEST['type'] == 'init') {
    $file = 'parametric-test.json';
} elseif ($_REQUEST['type'] == 'filter') {
    $file = 'parametric-test-filter.json';
} elseif ($_REQUEST['type'] == 'reset') {
    $file = 'parametric-test.json';
} */

die(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/tests/parametric/'.$file));

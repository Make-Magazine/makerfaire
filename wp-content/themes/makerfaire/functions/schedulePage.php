<?php

function add_query_vars($aVars) {
  $aVars[] = "sched_dow";
  $aVars[] = "sched_type";
  return $aVars;
}

// hook add_query_vars function into query_vars
add_filter('query_vars', 'add_query_vars');

function add_rewrite_rules($aRules) {
  $newrules = array();

  $newruleA = array('([^\/]*)/schedule/([^/]+)/?$' => 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]');
  $newruleB = array('([^\/]*)/schedule/([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]/schedule&sched_dow=$matches[2]&sched_type=$matches[3]');

  $aRules = $newruleA + $newruleB + $aRules;
return $aRules;
}

// hook add_rewrite_rules function into rewrite_rules_array
add_filter('rewrite_rules_array', 'add_rewrite_rules');
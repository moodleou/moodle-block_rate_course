<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    block
 * @subpackage rate_course
 * @copyright  2009 Jenny Gray
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Code was Rewritten for Moodle 2.X By Atar + Plus LTD for Comverse LTD.
 * @copyright &copy; 2011 Comverse LTD.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


if (isset($_SERVER['REMOTE_ADDR'])) {
    define('LINEFEED', "<br />");
} else {
    define('LINEFEED', "\n");
}

// List of patterns to search.

$dml = array (
    '(begin|commit|rollback)_sql',
    'count_records(_select|_sql)?',
    'delete_records(_select)?',
    'get_field(set)?(_select|sql)?',
    'get_record(s|set)?(_list|_menu|_select|_sql)?(_menu)?',
    'insert_record',
    'record_exists(_select|_sql)?',
    'records_to_menu',
    'recordset_to_(array|menu)',
    'rs_(EOF|close|fetch_next_record|fetch_record|next_record)',
    'set_field(_select)?',
    'update_record',
);

$helper = array (
    'db_(lowercase|uppercase)',
    'sql_(as|bitand|bitnot|bitor|bitxor|cast_char2int|ceil|compare_text|concat|concat_join|empty|fullname|ilike|isempty|isnotempty|length|max|null_from_clause|order_by_text|paging_limit|position|substr)'
);

$ddl = array (
    'add_(field|index|key)',
    'change_field_(default|enum|notnull|precision|type|unsigned)',
    'create_(table|temp_table)',
    'drop_(field|index|key|table)',
    'find_(check_constraint_name|index_name|key_name|sequence_name)',
    'rename_(field|index|key|table)',
    '(check_constraint|field|index|table)_exists'
);

$coreonly = array (
    'delete_tables_from_xmldb_file',
    'drop_plugin_tables',
    'get_db_directories',
    'get_used_table_names',
    'install_from_xmldb_file',
);

$internal = array (
    'change_db_encoding',
    'configure_dbconnection',
    'db_(detect_lobs|update_lobs)',
    'execute_sql(_arr)?',
    'onespace2empty',
    'oracle_dirty_hack',
    'rcache_(get|getforfill|releaseforfill|set|unset|unset_table)',
    'where_clause'
);

$unsupported = array (
    'column_type',
    'table_column',
    'modify_database',
    '(Execute|Connect|PConnect|ErrorMsg)',
    '(MetaTables|MetaColumns|MetaColumnNames|MetaPrimaryKeys|MetaIndexes)'
);

$other = array (
    '\$db[,; -]',
    "[^\$_'\"\.-]dbfamily",
    "[^\$_'\"\.-]dblibrary",
    "[^\$_'\"\.-]dbtype[^s]",
    'sql_substr\(\)'
);

// Getting current dir.
$dir = dirname(__FILE__);

echo $me . LINEFEED;

// Calculating megarules.
$dml_megarule        = calculate_megarule($dml, array('[ =@.]'), array('( )?\('), 'i');
$helper_megarule     = calculate_megarule($helper, array('[ =@.]'), array('( )?\('), 'i');
$ddl_megarule        = calculate_megarule($ddl, array('[ =@.]'), array('( )?\('), 'i');
$coreonly_megarule    = calculate_megarule($coreonly, array('[ =@.]'), array('( )?\('), 'i');
$internal_megarule   = calculate_megarule($internal, array('[ =@.]'), array('( )?\('), 'i');
$unsupported_megarule= calculate_megarule($unsupported, array('[ \>=@,.]'), array('( )?\('), 'i');
$other_megarule      = calculate_megarule($other);

// List of exceptions that aren't errors (function declarations, comments and some harcoded strings). False positives.
$excludes = '/(function |^\s*\*|^\s*\/\/|\$this-\>[a-zA-Z]*db-\>|^\s*\$CFG-\>(dbtype|dblibrary)\s*=|\$DB-\>(connect|execute)|\$this-\>execute|get_dbtype|protected \$[a-zA-Z]*db|Incorrect |check find_index_name|not available anymore|output|Replace it with the correct use of|where order of parameters is|_moodle_database|invaliddbtype)/';

// All rules.
$all_megarules = array(
    'DML'=>$dml_megarule,
    'HELPER'=>$helper_megarule,
    'DDL'=>$ddl_megarule,
    'COREONLY'=>$coreonly_megarule,
    'INTERNAL'=>$internal_megarule,
    'UNSUPPORTED'=>$unsupported_megarule,
    'OTHER'=>$other_megarule
);

// To store errors found.
$errors = array();
$counterrors = 0;

// Process starts here.

echo "Checking the $dir directory recursively" . LINEFEED;

$files = files_to_check($dir);

foreach ($files as $file) {
    echo "  - $file: ";

    // Read the file, line by line, applying all the megarules.
    $handle = @fopen($file, 'r');
    if ($handle) {
        $line = 0;
        while (!feof($handle)) {
            $buffer = fgets($handle, 65535); // Long lines supported on purpose.
            $line++;
            // Search for megarules.
            foreach ($all_megarules as $name => $megarule) {
                if (!empty($megarule) && preg_match($megarule, $buffer) && !preg_match($excludes, $buffer)) {
                    // Error found, add to errrors.
                    if (!isset($errors[$file])) {
                        $errors[$file] = array();
                        echo LINEFEED . "      * ERROR found!" . LINEFEED;
                    }
                    $errors[$file][] = "- ERROR ( $name ) - line $line : " . trim($buffer);
                    echo "          - ERROR ( $name ) - line $line : " . trim($buffer) . LINEFEED;
                    $counterrors++;
                    break;
                }
            }
        }
        if (!isset($errors[$file])) {
            echo "... OK" . LINEFEED;
        }
        fclose($handle);
    }

}

echo LINEFEED . LINEFEED;
echo "  SUMMARY: " . count($errors) . " files with errors ($counterrors ocurrences)" . LINEFEED;
foreach ($errors as $file => $errarr) {
    echo LINEFEED . "    * $file" . LINEFEED;
    foreach ($errarr as $err) {
        echo "        $err" . LINEFEED;
    }
}

// INTERNAL FUNCTIONS.

/**
 * Given an array of search patterns, create one "megarule", with the specified prefixes and suffixes
 */
function calculate_megarule($patterns, $prefixes=array(), $suffixes=array(), $modifiers='') {

    $megarule  = '';
    $totalrule = '';

    if (empty($patterns)) {
        return false;
    }

    foreach ($patterns as $pattern) {
        $megarule .= '|(' . $pattern . ')';
    }
    $megarule = trim($megarule, '|');

    // Add all the prefix/suffix combinations.
    foreach ($prefixes as $prefix) {
        foreach ($suffixes as $suffix) {
            $totalrule .= '|(' . $prefix . '(' . $megarule . ')' . $suffix . ')';
        }
    }
    $totalrule = trim($totalrule, '|');

    return '/' . (empty($totalrule) ? $megarule : $totalrule) . '/' . $modifiers;
}

/**
 * Given one full path, return one array with all the files to check
 */
function files_to_check($path) {

    $results = array();
    $pending = array();

    $dir = opendir($path);
    while (false !== ($file=readdir($dir))) {

        $fullpath = $path . '/' . $file;

        if (substr($file, 0, 1)=='.' || $file=='CVS') { // Exclude some dirs.
            continue;
        }

        if (is_dir($fullpath)) { // Process dirs later.
            $pending[] = $fullpath;
            continue;
        }

        if (is_file($fullpath) && strpos($file, basename(__FILE__))!==false) { // Exclude me.
            continue;
        }

        if (is_file($fullpath) && (strpos($fullpath, 'lib/adodb')!==false ||
                                   strpos($fullpath, 'lib/simpletest')!==false ||
                                   strpos($fullpath, 'lib/htmlpurifier')!==false ||
                                   strpos($fullpath, 'lib/memcached.class.php')!==false ||
                                   strpos($fullpath, 'lib/phpmailer')!==false ||
                                   strpos($fullpath, 'lib/soap')!==false ||
                                   strpos($fullpath, 'search/Zend/Search')!==false ||
                                   strpos($fullpath, 'lang/')!==false)) { // Exclude adodb, simpletest, htmlpurifier, memcached, phpmailer, soap and lucene libs and lang dir.
            continue;
        }

        if (is_file($fullpath) && strpos($file, '.php')===false && strpos($file, '.html')===false) { // Exclude some files.
            continue;
        }

        if (!in_array($fullpath, $results)) { // Add file if doesn't exists.
            $results[$fullpath] = $fullpath;
        }
    }
    closedir($dir);

    foreach ($pending as $pend) {
        $results = array_merge($results, files_to_check($pend));
    }

    return $results;
}
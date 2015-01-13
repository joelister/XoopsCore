<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * maintenance extensions
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         maintenance
 * @since           2.6.0
 * @author          Mage Grégory (AKA Mage), Cointin Maxime (AKA Kraven30)
 * @version         $Id$
 */

include __DIR__ . '/header.php';
// Get main instance
$system = System::getInstance();
$xoops = Xoops::getInstance();
$xoops->db();
global $xoopsDB;

// Get Action type
$op = $system->cleanVars($_REQUEST, 'op', 'list', 'string');

// Call Header
$xoops->header('admin:maintenance/maintenance_dump.tpl');

$admin_page = new \Xoops\Module\Admin();
$admin_page->renderNavigation('dump.php');

switch ($op) {

    case 'list':
    default:
        $files = glob(XOOPS_ROOT_PATH . '/modules/maintenance/dump/*.*');
        $count = 0;
        foreach ($files as $filename_path) {
            $filename = basename(strtolower($filename_path));
            if ($filename != 'index.html') {
                $file_arr[$count]['name'] = $filename;
                $stat = stat($filename_path);
                $file_arr[$count]['size'] = number_format($stat['size']/1024);
                $count++;
                unset($filename);
            }
        }
        if (isset($file_arr)) {
            $xoops->tpl()->assign('file_arr', array_reverse($file_arr));
        }
        if ($count == 0 && $op == 'list') {
            $form = $xoops->getModuleForm(null, 'maintenance');
            $form->getDump();
            $form->display();
        } else {
            $admin_page->addItemButton(_AM_MAINTENANCE_DUMP_FORM, 'dump.php?op=dump', 'cd');
            $admin_page->renderButton();
            $xoops->tpl()->assign('files', true);
        }
        break;

    case 'dump_save':
        // Check security
        if (!$xoops->security()->check()) {
            $xoops->redirect('dump.php', 3, implode('<br />', $xoops->security()->getErrors()));
        }
        $admin_page->addItemButton(_AM_MAINTENANCE_DUMP_LIST, 'dump.php', 'application-view-detail');
        $admin_page->addItemButton(_AM_MAINTENANCE_DUMP_FORM, 'dump.php?op=dump', 'cd');
        $admin_page->renderButton();
        $dump_modules = isset($_REQUEST['dump_modules']) ? $_REQUEST['dump_modules'] : false;
        $dump_tables = isset($_REQUEST['dump_tables']) ? $_REQUEST['dump_tables'] : false;
        $drop = $system->cleanVars($_REQUEST, 'drop', 1, 'int');

        if (($dump_tables == true && $dump_modules == true) || ($dump_tables == false && $dump_modules == false)) {
            $xoops->redirect("dump.php", 2, _AM_MAINTENANCE_DUMP_ERROR_TABLES_OR_MODULES);
        }
        $db = $xoopsDB;
        $dump = new Maintenance();
        $sql_text = "# \n";
        $sql_text .= "# Dump SQL, Generated by XOOPS \n";
        $sql_text .= "# Date : " . date(XoopsLocale::getFormatMediumDate()) . " \n";
        $sql_text .= "# \n\n";
        if ($dump_tables != false) {
            $result_module = array();
            for ($i = 0; $i < count($dump_tables); $i++) {
                //structure
                $result_tables[$i]['name'] = $db->prefix . '_' . $dump_tables[$i];
                $result_structure = $dump->dump_table_structure($db->prefix . '_' . $dump_tables[$i], $drop);
                $sql_text .= $result_structure['sql_text'];
                $result_tables[$i]['structure'] = $result_structure['structure'];
                //data
                $result_data = $dump->dump_table_datas($db->prefix . '_' . $dump_tables[$i]);
                $sql_text .= $result_data['sql_text'];
                $result_tables[$i]['records'] = $result_data['records'];
            }
            $xoops->tpl()->assign('result_t', $result_tables);
        }
        if ($dump_modules != false) {
            $result_module = array();
            for ($i = 0; $i < count($dump_modules); $i++) {
                $module_handler = $xoops->getHandlerModule();
                $module = $xoops->getModuleByDirname($dump_modules[$i]);
                $result_module[$i]['name'] = ucfirst($dump_modules[$i]);
                $modtables = $module->getInfo('tables');
                if ($modtables != false && is_array($modtables)) {
                    $count = 0;
                    foreach ($modtables as $table) {
                        //structure
                        $result_tables[$count]['name'] = $db->prefix . '_' . $table;
                        $result_structure = $dump->dump_table_structure($db->prefix . '_' . $table, $drop);
                        $sql_text .= $result_structure['sql_text'];
                        $result_tables[$count]['structure'] = $result_structure['structure'];

                        //data
                        $result_data = $dump->dump_table_datas($db->prefix . '_' . $table);
                        $sql_text .= $result_data['sql_text'];
                        $result_tables[$count]['records'] = $result_data['records'];
                        $count++;
                    }
                    $result_module[$i]['table'] = $result_tables;
                } else {
                    $result_module[$i]['table'] = false;
                }
                unset($result_tables);
            }
            $xoops->tpl()->assign('result_m', $result_module);
        }
        $xoops->tpl()->assign('result_write', true);
        $result_write = $dump->dump_write($sql_text);
        $xoops->tpl()->assign('write', $result_write['write']);
        $xoops->tpl()->assign('file_name', $result_write['file_name']);
        break;

    case 'dump_delete':
        $filename = $system->cleanVars($_REQUEST, 'filename', '', 'string');
        if ($filename == '') {
            $xoops->redirect("dump.php", 2, _AM_MAINTENANCE_DUMP_NOFILE);
        }
        unlink(XOOPS_ROOT_PATH . '/modules/maintenance/dump/' . $filename);
        $xoops->redirect("dump.php", 2, _AM_MAINTENANCE_DUMP_DELETED);
        break;

    case 'dump_deleteall':
        $files = glob(XOOPS_ROOT_PATH . '/modules/maintenance/dump/*.*');
        $count = 0;
        foreach ($files as $filename_path) {
            if (basename(strtolower($filename_path)) != 'index.html') {
                unlink($filename_path);
            }
        }
        $xoops->redirect("dump.php", 2, _AM_MAINTENANCE_DUMP_DELETEDALL);
        break;

    case 'dump':
        $form = $xoops->getModuleForm(null, 'maintenance');
        $form->getDump();
        $form->display();
        break;
}
$xoops->footer();

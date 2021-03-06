<?php
//  ------------------------------------------------------------------------ //
//                       mysearch - MODULE FOR XOOPS 2                        //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://xoops.instant-zero.com>                      //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

require_once XOOPS_ROOT_PATH . '/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/modules/mysearch/include/functions.php';

class Searches extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('mysearchid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('keyword', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('datesearch', XOBJ_DTYPE_TXTBOX, null, false, 19);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 32);
    }

    /**
     * Returns the user name for the current keyword (if the parameter is null)
     * @param int $uid
     * @return mixed
     */
    public function uname($uid = 0)
    {
        global $xoopsConfig;
        static $tblusers = [];
        $option = -1;
        if (empty($uid)) {
            $uid = $this->getVar('uid');
        }

        if (is_array($tblusers) && array_key_exists($uid, $tblusers)) {
            return $tblusers[$uid];
        }
        $tblusers[$uid] = XoopsUser::getUnameFromId($uid);

        return $tblusers[$uid];
    }
}

/**
 * mysearch Handler
 */
class MysearchSearchesHandler extends XoopsObjectHandler
{
    public function &create($isNew = true)
    {
        $searches = new searches();
        if ($isNew) {
            $searches->setNew();
        }

        return $searches;
    }

    public function &get($id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('mysearch_searches') . ' WHERE mysearchid=' . (int)$id;
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $searches = new searches();
            $searches->assignVars($this->db->fetchArray($result));

            return $searches;
        }

        return false;
    }

    public function insert(XoopsObject $searches, $force = false)
    {
        if ('searches' != get_class($searches)) {
            return false;
        }
        if (!$searches->isDirty()) {
            return true;
        }
        if (!$searches->cleanVars()) {
            foreach ($searches->getErrors() as $oneerror) {
                echo '<br><h2>' . $oneerror . '</h2>';
            }

            return false;
        }
        foreach ($searches->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($searches->isNew()) {
            $proxy_ip = '';
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
                $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
            } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
                $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
            } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
                $proxy_ip = $_SERVER['HTTP_FORWARDED'];
            } elseif (!empty($_SERVER['HTTP_VIA'])) {
                $proxy_ip = $_SERVER['HTTP_VIA'];
            } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
                $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
            } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
                $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
            }

            //if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}\.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0) {
            if (!empty($proxy_ip) && filter_var($proxy_ip, FILTER_VALIDATE_IP) && count($regs) > 0) {
                $the_IP = $regs[0];
            } else {
                $the_IP = $_SERVER['REMOTE_ADDR'];
            }
            $ip = $the_IP;

            $format = "INSERT INTO %s (mysearchid, keyword, datesearch, uid, ip) VALUES (%u, '%s', '%s', %u, %s)";
            $sql    = sprintf($format, $this->db->prefix('mysearch_searches'), $this->db->genId($this->db->prefix('mysearch_searches') . '_mysearchid_seq'), $keyword, $datesearch, $uid, $this->db->quoteString($ip));
            $force  = true;
        } else {
            $format = "UPDATE %s SET keyword='%d', datesearch='%s', uid=%u, ip=%s WHERE mysearchid = %u";
            $sql    = sprintf($format, $this->db->prefix('mysearch_searches'), $keyword, $datesearch, $uid, $this->db->quoteString($ip), $mysearchid);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($mysearchid)) {
            $mysearchid = $this->db->getInsertId();
        }
        $searches->assignVar('mysearchid', $mysearchid);

        return $mysearchid;
    }

    public function delete(XoopsObject $searches, $force = false)
    {
        if ('searches' != get_class($searches)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE mysearchid = %u', $this->db->prefix('mysearch_searches'), $searches->getVar('mysearchid'));
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     *  Returns the lowest date and the higher date
     * @param $min
     * @param $max
     */
    public function getMinMaxDate(&$min, &$max)
    {
        $sql    = "SELECT min(date_format(datesearch,'%X-%m-%d')) AS mindate, max(date_format(datesearch,'%X-%m-%d')) AS maxdate FROM " . $this->db->prefix('mysearch_searches');
        $result = $this->db->query($sql);
        list($min, $max) = $this->db->fetchRow($result);
    }

    /**
     * Count the number of unique days in the database
     */
    public function getUniqueDaysCount()
    {
        $count  = 0;
        $sql    = "SELECT count(DISTINCT(date_format(datesearch,'%X-%m-%d'))) AS cpt  FROM " . $this->db->prefix('mysearch_searches');
        $result = $this->db->query($sql);
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Returns the number of searches per day
     * @param $start
     * @param $limit
     * @return array
     */
    public function getCountPerDay($start, $limit)
    {
        $ret    = [];
        $sql    = "SELECT count(date_format(datesearch,'%X-%m-%d')) AS cpt, date_format(datesearch,'%X-%m-%d') AS shdate FROM " . $this->db->prefix('mysearch_searches') . " GROUP BY date_format(datesearch,'%X-%m-%d') ORDER BY date_format(datesearch,'%X-%m-%d') DESC";
        $result = $this->db->query($sql, $limit, $start);
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['shdate']] = $myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Get the unique number of different IPs
     */
    public function getIPsCount()
    {
        $sql    = 'SELECT count(DISTINCT(ip)) AS cpt FROM ' . $this->db->prefix('mysearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return $myrow['cpt'];
    }

    /**
     * Returns IPs count
     * @param      $start
     * @param      $limit
     * @param bool $id_as_key
     * @return array
     */
    public function getIPs($start, $limit, $id_as_key = false)
    {
        $ret    = [];
        $sql    = 'SELECT Count(*) AS cpt, ip FROM ' . $this->db->prefix('mysearch_searches') . ' GROUP BY ip ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['ip']] = $myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Get the unique number of people who used the search
     */
    public function getBiggestContributorsCount()
    {
        $sql    = 'SELECT count(DISTINCT(uid)) AS cpt FROM ' . $this->db->prefix('mysearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return $myrow['cpt'];
    }

    /**
     * Returns users according to their use of the search
     * @param      $start
     * @param      $limit
     * @param bool $id_as_key
     * @return array
     */
    public function getBiggestContributors($start, $limit, $id_as_key = false)
    {
        $ret    = [];
        $sql    = 'SELECT Count(*) AS cpt, uid FROM ' . $this->db->prefix('mysearch_searches') . ' GROUP BY uid ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['uid']] = $myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Returns the number of unique keywords in the database
     */
    public function getMostSearchedCount()
    {
        $sql    = 'SELECT Count(DISTINCT(keyword)) AS cpt FROM ' . $this->db->prefix('mysearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return $myrow['cpt'];
    }

    /**
     * Returns statistics about keywords, ordered on the number of time they are searched
     * @param      $start
     * @param      $limit
     * @param bool $id_as_key
     * @return array
     */
    public function getMostSearched($start, $limit, $id_as_key = false)
    {
        $ts     = MyTextSanitizer::getInstance();
        $ret    = [];
        $sql    = 'SELECT Count(keyword) AS cpt, keyword, mysearchid FROM ' . $this->db->prefix('mysearch_searches') . ' GROUP BY keyword ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[$myrow['mysearchid']] = [
                'keyword' => $ts->htmlSpecialChars($myrow['keyword']),
                'count'   => $myrow['cpt']
            ];
        }

        return $ret;
    }

    /**
     * Hack by Smart Returns  keywords matches found for ajax autocompletion
     * @param $start
     * @param $limit
     * @param $searchword
     * @return array
     */
    public function ajaxMostSearched($start, $limit, $searchword)
    {
        $ts     = MyTextSanitizer::getInstance();
        $ret    = [];
        $sql    = 'SELECT Count(keyword) AS cpt, keyword FROM ' . $this->db->prefix('mysearch_searches') . ' WHERE keyword LIKE \'' . $searchword . '%\' GROUP BY keyword ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = ['keyword' => $ts->htmlSpecialChars($myrow['keyword']), 'count' => $myrow['cpt']];
        }

        return $ret;
    }

    /**
     * End hack by Smart
     * @param null $criteria
     * @param bool $id_as_key
     * @return array
     */

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('mysearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $searches = new searches();
            $searches->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $searches;
            } else {
                $ret[$myrow['mysearchid']] =& $searches;
            }
            unset($searches);
        }

        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('mysearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('mysearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}

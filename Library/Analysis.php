<?php
/**
 * Copyright 2010 Cyrille Mahieux
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations
 * under the License.
 *
 * ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°>
 *
 * Analysis of memcached command response
 *
 * @author c.mahieux@of2m.fr
 * @since 20/03/2010
 */
class Library_Analysis
{
    /**
     * Merge two arrays of stats from MemCacheAdmin_ServerCommands::stats()
     *
     * @param $array Statistic from MemCacheAdmin_ServerCommands::stats()
     * @param $stats Statistic from MemCacheAdmin_ServerCommands::stats()
     *
     * @return Array
     */
    public static function merge($array, $stats)
    {
        # Checking input
        if(!is_array($array))
        {
            return $stats;
        }
        elseif(!is_array($stats))
        {
            return $array;
        }

        # Merging Stats
        foreach($stats as $key => $value)
        {
            if(isset($array[$key]) && ($key != 'version'))
            {
                $array[$key] += $value;
            }
            else
            {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Diff two arrays of stats from MemCacheAdmin_ServerCommands::stats()
     *
     * @param Array $array Statistic from MemCacheAdmin_ServerCommands::stats()
     * @param Array $stats Statistic from MemCacheAdmin_ServerCommands::stats()
     *
     * @return Array
     */
    public static function diff($array, $stats)
    {
        # Checking input
        if(!is_array($array))
        {
            return $stats;
        }
        elseif(!is_array($stats))
        {
            return $array;
        }

        # Diff for each key
        foreach($stats as $key => $value)
        {
            if(isset($array[$key]))
            {
                $stats[$key] = $value - $array[$key];
            }
        }

        return $stats;
    }

    /**
     * Analyse and return memcache stats command
     *
     * @param Array $stats Statistic from MemCacheAdmin_ServerCommands::stats()
     *
     * @return Array
     */
    public static function stats($stats)
    {
        if(!is_array($stats) || (count($stats) == 0))
        {
            return false;
        }
        # Command set()
        $stats['set_rate'] = ($stats['cmd_set'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_set'] / $stats['uptime'], 1);

        # Command get()
        $stats['get_hits_percent'] = ($stats['cmd_get'] == 0) ? ' - ':sprintf('%.1f', $stats['get_hits'] / $stats['cmd_get'] * 100, 1);
        $stats['get_misses_percent'] = ($stats['cmd_get'] == 0) ? ' - ':sprintf('%.1f', $stats['get_misses'] / $stats['cmd_get'] * 100, 1);
        $stats['get_rate'] = ($stats['cmd_get'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_get'] / $stats['uptime'], 1);

        # Command delete()
        $stats['cmd_delete'] = isset($stats['delete_hits']) ? $stats['delete_hits'] : null   + isset($stats['delete_misses']) ? $stats['delete_misses'] : null;
        $stats['delete_hits_percent'] = ($stats['cmd_delete'] == 0) ?' - ':sprintf('%.1f', $stats['delete_hits'] / $stats['cmd_delete'] * 100, 1);
        $stats['delete_misses_percent'] = ($stats['cmd_delete'] == 0) ?' - ':sprintf('%.1f', isset($stats['delete_misses']) ? $stats['delete_misses'] : null / $stats['cmd_delete'] * 100, 1);
        $stats['delete_rate'] = ($stats['cmd_delete'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_delete'] / $stats['uptime'], 1);

        # Command cas()
        $stats['cmd_cas'] = isset($stats['cas_hits']) ? $stats['cas_hits'] : null + isset($stats['cas_misses']) ? $stats['cas_misses'] : null + isset($stats['cas_badval']) ? $stats['cas_badval'] : null;
        $stats['cas_hits_percent'] = ($stats['cmd_cas'] == 0) ?' - ':sprintf('%.1f', $stats['cas_hits'] / $stats['cmd_cas'] * 100, 1);
        $stats['cas_misses_percent'] = ($stats['cmd_cas'] == 0) ?' - ':sprintf('%.1f', $stats['cas_misses'] / $stats['cmd_cas'] * 100, 1);
        $stats['cas_badval_percent'] = ($stats['cmd_cas'] == 0) ?' - ':sprintf('%.1f', $stats['cas_badval'] / $stats['cmd_cas'] * 100, 1);
        $stats['cas_rate'] = ($stats['cmd_cas'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_cas'] / $stats['uptime'], 1);

        # Command increment()
        $stats['cmd_incr'] = isset($stats['incr_hits']) ? $stats['incr_hits'] : null + isset($stats['incr_misses']) ? $stats['incr_misses'] : null;
        $stats['incr_hits_percent'] = ($stats['cmd_incr'] == 0) ?' - ':sprintf('%.1f', $stats['incr_hits'] / $stats['cmd_incr'] * 100, 1);
        $stats['incr_misses_percent'] = ($stats['cmd_incr'] == 0) ?' - ':sprintf('%.1f', $stats['incr_misses'] / $stats['cmd_incr'] * 100, 1);
        $stats['incr_rate'] = ($stats['cmd_incr'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_incr'] / $stats['uptime'], 1);

        # Command decrement()
        $stats['cmd_decr'] = isset($stats['decr_hits']) ? $stats['decr_hits'] : null + isset($stats['decr_misses']) ? $stats['decr_misses'] : null;
        $stats['decr_hits_percent'] = ($stats['cmd_decr'] == 0) ?' - ':sprintf('%.1f', $stats['decr_hits'] / $stats['cmd_decr'] * 100, 1);
        $stats['decr_misses_percent'] = ($stats['cmd_decr'] == 0) ?' - ':sprintf('%.1f', $stats['decr_misses'] / $stats['cmd_decr'] * 100, 1);
        $stats['decr_rate'] = ($stats['cmd_decr'] == 0) ? '0.0':sprintf('%.1f', $stats['cmd_decr'] / $stats['uptime'], 1);

        # Total hit & miss
        $stats['cmd_total'] = $stats['cmd_get'] + $stats['cmd_set'] + $stats['cmd_delete'] + $stats['cmd_cas'] + $stats['cmd_incr'] + $stats['cmd_decr'];
        $stats['hit_percent'] = ($stats['cmd_total'] == 0) ? '0.0':sprintf('%.1f', ($stats['cmd_set'] + $stats['get_hits'] + (isset($stats['delete_hits']) ? $stats['delete_hits'] : null) + (isset($stats['cas_hits']) ? $stats['cas_hits'] : null) + (isset($stats['incr_hits']) ? $stats['incr_hits'] : null) + (isset($stats['decr_hits']) ? $stats['decr_hits'] : null)  ) / $stats['cmd_total'] * 100, 1);
        $stats['miss_percent'] = ($stats['cmd_total'] == 0) ? '0.0':sprintf('%.1f', ($stats['get_misses'] + (isset($stats['delete_misses']) ? $stats['delete_misses'] : null) + (isset($stats['cas_misses']) ? $stats['cas_misses'] : null) + (isset($stats['cas_badval']) ? $stats['cas_badval'] : null) + (isset($stats['incr_misses']) ? $stats['incr_misses'] : null) + (isset($stats['decr_misses']) ? $stats['decr_misses'] : null)) / $stats['cmd_total'] * 100, 1);

        # Cache size
        $stats['bytes_percent'] = ($stats['limit_maxbytes'] == 0) ? '0.0' : sprintf('%.1f', $stats['bytes'] / $stats['limit_maxbytes'] * 100, 1);

        # Request rate
        $stats['request_rate'] = sprintf('%.1f', ($stats['cmd_get'] + $stats['cmd_set'] + $stats['cmd_delete'] + $stats['cmd_cas'] + $stats['cmd_incr'] + $stats['cmd_decr']) / $stats['uptime'], 1);
        $stats['hit_rate'] = sprintf('%.1f', (isset($stats['cmd_set']) ? $stats['cmd_set'] : null + $stats['get_hits'] + $stats['delete_hits'] + $stats['cas_hits'] + $stats['incr_hits'] + $stats['decr_hits']) / $stats['uptime'], 1);
        $stats['miss_rate'] = sprintf('%.1f', ($stats['get_misses'] + (isset($stats['delete_misses']) ? $stats['delete_misses'] : 0) + (isset($stats['cas_misses']) ? $stats['cas_misses'] : null) + (isset($stats['cas_badval']) ? $stats['cas_badval'] : 0) + (isset($stats['incr_misses']) ? $stats['incr_misses'] : 0) + (isset($stats['decr_misses']) ? $stats['decr_misses'] : 0)) / $stats['uptime'], 1);

        # Eviction & reclaimed rate
        $stats['eviction_rate'] = ($stats['evictions'] == 0) ? '0.0':sprintf('%.1f', $stats['evictions'] / $stats['uptime'], 1);
        $stats['reclaimed_rate'] = (!isset($stats['reclaimed']) || ($stats['reclaimed'] == 0)) ? '0.0':sprintf('%.1f', $stats['reclaimed'] / $stats['uptime'], 1);

        return $stats;
    }

    /**
     * Analyse and return memcache slabs command
     *
     * @param Array $slabs Statistic from MemCacheAdmin_ServerCommands::slabs()
     *
     * @return Array
     */
    public static function slabs($slabs)
    {
        # Initializing Used Slabs
        $slabs['used_slabs'] = 0;
        $slabs['total_wasted'] = 0;
        # Request Rate par Slabs
        foreach($slabs as $id => $slab)
        {
            # Check if it's a Slab
            if(is_numeric($id))
            {
                # Check if Slab is used
                if($slab['used_chunks'] > 0)
                {
                    $slabs['used_slabs']++;
                }
                $slabs[$id]['request_rate'] = sprintf('%.1f', ($slab['get_hits'] + $slab['cmd_set'] + $slab['delete_hits'] + $slab['cas_hits'] + $slab['cas_badval'] + $slab['incr_hits'] + $slab['decr_hits']) / $slabs['uptime'], 1);
                $slabs[$id]['mem_wasted'] = (($slab['total_chunks'] * $slab['chunk_size']) < $slab['mem_requested']) ?(($slab['total_chunks'] -  $slab['used_chunks']) * $slab['chunk_size']):(($slab['total_chunks'] * $slab['chunk_size']) - $slab['mem_requested']);
                $slabs['total_wasted'] += $slabs[$id]['mem_wasted'];
            }
        }
        return $slabs;
    }

    /**
     * Calculate Uptime
     *
     * @param Integer $uptime Uptime timestamp
     *
     * @return String
     */
    public static function uptime($uptime)
    {
        if($uptime > 0)
        {
            $days = floor($uptime/60/60/24);
            $hours = $uptime/60/60%24;
            $mins = $uptime/60%60;
            if(($days + $hours + $mins) == 0)
            {
                return ' less than 1 min';
            }
            return $days . ' days ' . $hours . ' hrs ' . $mins . ' min';
        }
        return ' - ';
    }

    /**
     * Resize a byte value
     *
     * @param Integer $value Value to resize
     *
     * @return String
     */
    public static function byteResize($value)
    {
        # Unit list
        $units = array('', 'K', 'M', 'G', 'T');

        # Resizing
        foreach($units as $unit)
        {
            if($value < 1024)
            {
                break;
            }
            $value /= 1024;
        }
        return sprintf('%.1f %s', $value, $unit);
    }

    /**
     * Resize a value
     *
     * @param Integer $value Value to resize
     *
     * @return String
     */
    public static function valueResize($value)
    {
        # Unit list
        $units = array('', 'K', 'M', 'G', 'T');

        # Resizing
        foreach($units as $unit)
        {
            if($value < 1000)
            {
                break;
            }
            $value /= 1000;
        }
        return sprintf('%.1f%s', $value, $unit);
    }

    /**
     * Resize a hit value
     *
     * @param Integer $value Hit value to resize
     *
     * @return String
     */
    public static function hitResize($value)
    {
        # Unit list
        $units = array('', 'K', 'M', 'G', 'T');

        # Resizing
        foreach($units as $unit)
        {
            if($value < 10000000)
            {
                break;
            }
            $value /= 1000;
        }
        return sprintf('%.0f%s', $value, $unit);
    }
}
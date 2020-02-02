<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
class System
{
    
    private $_hostname = "localhost";

    
    private $_ip = "127.0.0.1";

    
    private $_kernel = "Unknown";

    
    private $_distribution = "Unknown";

    
    private $_distributionIcon = "unknown.png";

    
    private $_machine = "";

    
    private $_uptime = 0;

   
    private $_users = 0;

    
    private $_load = "";

    
    private $_loadPercent = null;

    
    private $_cpus = array();

    
    private $_netDevices = array();

    
    private $_pciDevices = array();

    
    private $_ideDevices = array();

    
    private $_scsiDevices = array();

    
    private $_usbDevices = array();

    
    private $_tbDevices = array();

    
    private $_i2cDevices = array();

    
    private $_diskDevices = array();

   
    private $_memFree = 0;

    
    private $_memTotal = 0;

    
    private $_memUsed = 0;

    
    private $_memApplication = null;

    
    private $_memBuffer = null;

    
    private $_memCache = null;

    
    private $_swapDevices = array();

    
    private $_processes = array();

   
    public static function removeDupsAndCount($arrDev)
    {
        $result = array();
        foreach ($arrDev as $dev) {
            if (count($result) === 0) {
                array_push($result, $dev);
            } else {
                $found = false;
                foreach ($result as $tmp) {
                    if ($dev->equals($tmp)) {
                        $tmp->setCount($tmp->getCount() + 1);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    array_push($result, $dev);
                }
            }
        }

        return $result;
    }

    
    public function getMemPercentUsed()
    {
        if ($this->_memTotal > 0) {
            return round($this->_memUsed / $this->_memTotal * 100);
        } else {
            return 0;
        }
    }

    
    public function getMemPercentApplication()
    {
        if ($this->_memApplication !== null) {
            if (($this->_memApplication > 0) && ($this->_memTotal > 0)) {
                return round($this->_memApplication / $this->_memTotal * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    
    public function getMemPercentCache()
    {
        if ($this->_memCache !== null) {
            if (($this->_memCache > 0) && ($this->_memTotal > 0)) {
                if (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                    return round(($this->_memCache + $this->_memApplication) / $this->_memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->_memCache / $this->_memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    
    public function getMemPercentBuffer()
    {
        if ($this->_memBuffer !== null) {
            if (($this->_memBuffer > 0) && ($this->_memTotal > 0)) {
                if (($this->_memCache !== null) && ($this->_memCache > 0)) {
                    if (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                        return round(($this->_memBuffer + $this->_memApplication + $this->_memCache) / $this->_memTotal * 100) - $this->getMemPercentApplication() - $this->getMemPercentCache();
                    } else {
                        return round(($this->_memBuffer + $this->_memCache) / $this->_memTotal * 100) - $this->getMemPercentCache();
                    }
                } elseif (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                    return round(($this->_memBuffer + $this->_memApplication) / $this->_memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->_memBuffer / $this->_memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    
    public function getSwapFree()
    {
        if (count($this->_swapDevices) > 0) {
            $free = 0;
            foreach ($this->_swapDevices as $dev) {
                $free += $dev->getFree();
            }

            return $free;
        }

        return null;
    }

    
    public function getSwapTotal()
    {
        if (count($this->_swapDevices) > 0) {
            $total = 0;
            foreach ($this->_swapDevices as $dev) {
                $total += $dev->getTotal();
            }

            return $total;
        } else {
            return null;
        }
    }

    
    public function getSwapUsed()
    {
        if (count($this->_swapDevices) > 0) {
            $used = 0;
            foreach ($this->_swapDevices as $dev) {
                $used += $dev->getUsed();
            }

            return $used;
        } else {
            return null;
        }
    }

    
    public function getSwapPercentUsed()
    {
        if ($this->getSwapTotal() !== null) {
            if ($this->getSwapTotal() > 0) {
                return round($this->getSwapUsed() / $this->getSwapTotal() * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

   
    public function getDistribution()
    {
        return $this->_distribution;
    }

    
    public function setDistribution($distribution)
    {
        $this->_distribution = $distribution;
    }

   
    public function getDistributionIcon()
    {
        return $this->_distributionIcon;
    }

  
    public function setDistributionIcon($distributionIcon)
    {
        $this->_distributionIcon = $distributionIcon;
    }

    
    public function getHostname()
    {
        return $this->_hostname;
    }

    
    public function setHostname($hostname)
    {
        $this->_hostname = $hostname;
    }

    
    public function getIp()
    {
        return $this->_ip;
    }

    
    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    
    public function getKernel()
    {
        return $this->_kernel;
    }

    
    public function setKernel($kernel)
    {
        $this->_kernel = $kernel;
    }

    
    public function getLoad()
    {
        return $this->_load;
    }

    
    public function setLoad($load)
    {
        $this->_load = $load;
    }

    
    public function getLoadPercent()
    {
        return $this->_loadPercent;
    }

    
    public function setLoadPercent($loadPercent)
    {
        $this->_loadPercent = $loadPercent;
    }

    
    public function getMachine()
    {
        return $this->_machine;
    }

    
    public function setMachine($machine)
    {
        $this->_machine = $machine;
    }

   
    public function getUptime()
    {
        return $this->_uptime;
    }

   
    public function setUptime($uptime)
    {
        $this->_uptime = $uptime;
    }

    
    public function getUsers()
    {
        return $this->_users;
    }

    
    public function setUsers($users)
    {
        $this->_users = $users;
    }

   
    public function getCpus()
    {
        return $this->_cpus;
    }

    
    public function setCpus($cpus)
    {
        array_push($this->_cpus, $cpus);
    }

    
    public function getNetDevices()
    {
        return $this->_netDevices;
    }

    
    public function setNetDevices($netDevices)
    {
        array_push($this->_netDevices, $netDevices);
    }

    
    public function getPciDevices()
    {
        return $this->_pciDevices;
    }

    
    public function setPciDevices($pciDevices)
    {
        array_push($this->_pciDevices, $pciDevices);
    }

   
    public function getIdeDevices()
    {
        return $this->_ideDevices;
    }

    
    public function setIdeDevices($ideDevices)
    {
        array_push($this->_ideDevices, $ideDevices);
    }

 
    public function getScsiDevices()
    {
        return $this->_scsiDevices;
    }

    
    public function setScsiDevices($scsiDevices)
    {
        array_push($this->_scsiDevices, $scsiDevices);
    }

   
    public function getUsbDevices()
    {
        return $this->_usbDevices;
    }

    
    public function setUsbDevices($usbDevices)
    {
        array_push($this->_usbDevices, $usbDevices);
    }

    
    public function getTbDevices()
    {
        return $this->_tbDevices;
    }

    
    public function setTbDevices($tbDevices)
    {
        array_push($this->_tbDevices, $tbDevices);
    }

    
    public function getI2cDevices()
    {
        return $this->_i2cDevices;
    }

    
    public function setI2cDevices($i2cDevices)
    {
        array_push($this->_i2cDevices, $i2cDevices);
    }

    
    public function getDiskDevices()
    {
        return $this->_diskDevices;
    }

   
    public function setDiskDevices($diskDevices)
    {
        array_push($this->_diskDevices, $diskDevices);
    }

   
    public function getMemApplication()
    {
        return $this->_memApplication;
    }

    
    public function setMemApplication($memApplication)
    {
        $this->_memApplication = $memApplication;
    }

    
    public function getMemBuffer()
    {
        return $this->_memBuffer;
    }

   
    public function setMemBuffer($memBuffer)
    {
        $this->_memBuffer = $memBuffer;
    }

    
    public function getMemCache()
    {
        return $this->_memCache;
    }

    
    public function setMemCache($memCache)
    {
        $this->_memCache = $memCache;
    }

   
    public function getMemFree()
    {
        return $this->_memFree;
    }

    
    public function setMemFree($memFree)
    {
        $this->_memFree = $memFree;
    }

    
    public function getMemTotal()
    {
        return $this->_memTotal;
    }

    
    public function setMemTotal($memTotal)
    {
        $this->_memTotal = $memTotal;
    }

    
    public function getMemUsed()
    {
        return $this->_memUsed;
    }

    
    public function setMemUsed($memUsed)
    {
        $this->_memUsed = $memUsed;
    }

   
    public function getSwapDevices()
    {
        return $this->_swapDevices;
    }

    
    public function setSwapDevices($swapDevices)
    {
        array_push($this->_swapDevices, $swapDevices);
    }

   
    public function getProcesses()
    {
        return $this->_processes;
    }

   
    public function setProcesses($processes)
    {
        $this->_processes = $processes;
/*
        foreach ($processes as $proc_type=>$proc_count) {
            $this->_processes[$proc_type] = $proc_count;
        }
*/
    }
}

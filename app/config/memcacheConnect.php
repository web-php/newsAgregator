<?php

/**
 * Description of memcacheConnect
 *
 * @author Михаил Орехов
 */
class cache extends Memcached
{
    public function __construct($config)
    {
        parent::__construct();
        $servers = $this->getServerList();
        if (empty($servers)) {
            $this->addServer(
                    $config['host'], $config['port']
            );
        }
    }
    
    /** 
     * 
     */
    public function getRequest(){
        
    }

}

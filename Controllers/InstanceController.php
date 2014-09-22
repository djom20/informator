<?php
use Aws\Ec2\Ec2Client;
use Aws\Common\Enum\Region;

/**
 * Description of InstanceController
 *
 * @author edinson
 */
class InstanceController extends ControllerBase {
    //put your code here
    var $client = null;
    
    public function _Always() {
        if(!isset($_SESSION['user'])) {
            HTTP::JSON(401);
        }
        
        $this->client = Ec2Client::factory(
            array(
                'key' => $_SESSION['user']['accesskey'],
                'secret' => $_SESSION['user']['secretkey'],
                'region' => 'us-east-1'
            )
        );
    }
    
    public function start () {
        if(!empty($this->get['instanceids'])) {
            $instanceids = explode('|', urldecode($this->get['instanceids']));
            
            $this->client->startInstances(array (
                'InstanceIds' => $instanceids
            ));
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    public function stop () {
        if(!empty($this->get['instanceids'])) {
            $instanceids = explode('|', urldecode($this->get['instanceids']));
            
            $this->client->stopInstances(array (
                'InstanceIds' => $instanceids
            ));
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    public function reboot () {
        if(!empty($this->get['instanceids'])) {
            $instanceids = explode('|', urldecode($this->get['instanceids']));
            
            $this->client->rebootInstances(array (
                'InstanceIds' => $instanceids
            ));
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
}

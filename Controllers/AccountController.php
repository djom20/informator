<?php
use Aws\Ec2\Ec2Client;
use Aws\Common\Enum\Region;

/**
 * Description of AccountController
 *
 * @author edinson
 */
class AccountController extends ControllerBase {
    //put your code here
    public function _Always() {
        if(in_array(ActionName, array ('get', 'register', 'user', 'update', 'delete'))) {
            if(!isset($_SESSION['admin'])) {
                HTTP::JSON(401);
            }
        } elseif(!isset($_SESSION['user'])) {
            if(!in_array(ActionName, array ('users')) || !isset($_SESSION['admin'])) {
                HTTP::JSON(401);
            }
        }
    }
    
    public function users () {
        $user = $this->getModel('user');
        
        if($_SESSION['user']['rol'] == 'root') {
            $result = $user->select(array (
                ':idaccount' => $_SESSION['user']['idaccount']
            ));
            
            $response = Partial::arrayNames($result, array (
                'idaccount', 'pass', 'secretkey', 'accesskey')
            );
            
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
        } elseif(isset ($_SESSION['admin'])) {
            $result = $user->select(array (
                ':rol' => 'root'
            ));
            
            $response = Partial::arrayNames($result, array ('pass', 'secretkey', 'accesskey'));
            
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
        }
        
        HTTP::JSON(403);
    }
    
    public function instances() {
        $client = Ec2Client::factory(array(
                'key' => $_SESSION['user']['accesskey'],
                'secret' => $_SESSION['user']['secretkey'],
                'region' => 'us-east-1'
            )
        );
        
        $scheduler = $this->getModel('scheduler_group');
        $result = $client->DescribeInstances();
        $response = array ();
        
        /*SELECT INSTANCES INFORMATION*/
        $reservations = $result['Reservations'];
        foreach ($reservations as $reservation) {
            $instances = $reservation['Instances'];
            foreach ($instances as $instance) {
                $instanceName = '';
                foreach ($instance['Tags'] as $tag) {
                    if ($tag['Key'] == 'Name') {
                        $instanceName = $tag['Value'];
                    }
                }
                
                $result = $scheduler->count(array(
                    ':instanceID' => $instance['InstanceId']
                ));

                // print_r($instance);
                // echo '<br>';
                
                array_push($response, array(
                    'Name' => $instanceName,
                    'InstanceId' => $instance['InstanceId'],
                    'ImageId' => $instance['ImageId'],
                    'State' => $instance['State'],
                    'PrivateDns' => $instance['PrivateDnsName'],
                    'PublicDns' => $instance['PublicDnsName'],
                    // 'StateTransitionReason' => $instance['StateTransitionReason'],
                    'KeyName' => $instance['KeyName'],
                    // 'AmiLaunchIndex' => $instance['AmiLaunchIndex'],
                    // 'ProductCodes' => $instance['ProductCodes'],
                    'Type' => $instance['InstanceType'],
                    'Launch' => $instance['LaunchTime'],
                    // 'Placement' => $instance['Placement'],
                    // 'Monitoring' => $instance['Monitoring'],
                    // 'SubnetId' => $instance['SubnetId'],
                    // 'VpcId' => $instance['VpcId'],
                    'PrivateIp' => $instance['PrivateIpAddress'],
                    'PublicIp' => $instance['PublicIpAddress'],
                    'Architecture' => $instance['Architecture'],
                    // 'RootDeviceType' => $instance['RootDeviceType'],
                    // 'RootDeviceName' => $instance['RootDeviceName'],
                    'DeviceMapping' => $instance['BlockDeviceMappings'],
                    'VirtualizationType' => $instance['VirtualizationType'],
                    // 'ClientToken' => $instance['ClientToken'],
                    // 'Tags' => $instance['Tags'],
                    // 'SecurityGroups' => $instance['SecurityGroups'],
                    // 'SourceDestCheck' => $instance['SourceDestCheck'],
                    // 'Hypervisor' => $instance['Hypervisor'],
                    // 'NetworkInterfaces' => $instance['NetworkInterfaces'],
                    'schedulers' => $result[0][0],
                    'Alarms' => $result[0][0]
                ));
            }
        }
        
        HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
    }
    
    
    /*Configuracion de superadministrador*/
    function get () {
        $result = $this->getModel('account')->select();
        
        $response = Partial::arrayNames($result);
        HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
    }
    
    function update () {
        $_filled = Partial::_filled($this->put, array ('idaccount'));
        if($_filled) {
            $params = Partial::prefix($this->put, ':');
            
            $this->getModel('account')->update($this->put['idaccount'], $params);
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    function register () {
        $_filled = Partial::_filled($this->post, array ('idaccount', 'name', 'secretkey', 'accesskey'));
        if($_filled) {
            $params = Partial::prefix($this->post, ':');
            $account = $this->getModel('account');
            $account->insert($params);
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    function delete () {
        $_filled = Partial::_filled($this->delete, array ('idaccount'));
        
        if($_filled) {
            $this->getModel('account')->delete($this->delete['idaccount']);
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    function user () {
        $_filled = Partial::_filled($this->post, array (
                'name', 'user', 'pass', 'idaccount'
            )
        );
        
        $_empty = Partial::_empty($this->post, array ('iduser'));
        
        if($_filled && $_empty) {
            $user = $this->getModel('user');
            $params = Partial::prefix($this->post, ':');
            $params[':pass'] = md5($params[':pass']);
            $params[':rol'] = 'root';

            $r = $this->getModel('account')->select(array (
                ':idaccount' => $this->post['idaccount']
            ));
            
            if(count($r) > 0) {
                $account = $r[0];
                if(empty($params[':accesskey'])) {
                    $params[':accesskey'] = $account['accesskey'];
                }

                if(empty($params[':secretkey'])) {
                    $params[':secretkey'] = $account['secretkey'];
                }

                $user->insert($params);

                if($user->lastID() > 0) {
                    HTTP::JSON(200);
                }

                HTTP::JSON(424);
            }
            
            HTTP::JSON(404);
        }
        
        HTTP::JSON(400);
    }
}

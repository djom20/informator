<?php
// use Aws\Ec2\Ec2Client;
use Aws\Sns\SnsClient;
use Aws\Common\Enum\Region;

/**
 * Description of SnsController
 *
 * @author djom202
 */
class SnsController extends ControllerBase {
    //put your code here
    var $client = null;

    public function _Always() {
        if(!isset($_SESSION['user'])){
            HTTP::JSON(401);
        }

        $this->client = SnsClient::factory(array(
            'key' => $_SESSION['user']['accesskey'],
            'secret' => $_SESSION['user']['secretkey'],
            'region' => 'us-east-1'
        ));
    }

    public function get(){
        $result = $this->client->listSubscriptions();
        HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result['Subscriptions']));
    }

    public function subscribe(){
        if(!empty($this->post)){
            try{
                $result = $this->client->subscribe(array(
                    'TopicArn' => $this->post['TopicArn'], // TopicArn is required
                    'Protocol' => 'email', //            Protocol is required
                    'Endpoint' => $this->post['Endpoint'],
                ));
                HTTP::JSON(200);
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(500), $e->getMessage()));
            }
        }
        HTTP::JSON(400);
    }

    public function unsubscribe(){
        if(!empty($this->post)){
            try{
                $result = $this->client->unsubscribe(array(
                    'SubscriptionArn' => $this->post['SubscriptionArn'] // SubscriptionArn is required
                ));
                HTTP::JSON(200);
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(500), $e->getMessage()));
            }
        }
        HTTP::JSON(400);
    }
}

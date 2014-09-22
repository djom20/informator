<?php
// use Aws\Ec2\Ec2Client;
use Aws\Sns\SnsClient;
use Aws\Common\Enum\Region;

/**
 * Description of SnsController
 *
 * @author djom202
 */
class SnsadminController extends ControllerBase {
    //put your code here
    var $client = null;

    public function _Always() {
        // if(!isset($_SESSION['admin'])){
        //     HTTP::JSON(401);
        // }
    }

    public function subscribeAccount(){
        if(!empty($this->post)){
            try{
                $client = SnsClient::factory(array(
                    'key' => $this->post['Accesskey'],
                    'secret' => $this->post['Secretkey'],
                    'region' => 'us-east-1'
                ));

                // Create Topic or Replace
                $client->createTopic(array(
                    'Name' => $this->post['Name'] // Name is required
                ));

                // // Subscribe Email
                $result = $client->subscribe(array(
                    'TopicArn' => $this->post['TopicArn'], // TopicArn is required
                    'Protocol' => 'email', // Protocol is required
                    'Endpoint' => $this->post['Endpoint'],
                ));
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result['SubscriptionArn']));
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(500), $e->getMessage()));
            }
        }
        HTTP::JSON(400);
    }
}

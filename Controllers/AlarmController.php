<?php
// use Aws\Ec2\Ec2Client;
use Aws\CloudWatch\CloudWatchClient;
use Aws\Common\Enum\Region;

/**
 * Description of AlarmController
 *
 * @author djom202
 */
class AlarmController extends ControllerBase {
    //put your code here
    var $client = null;

    public function _Always() {
        if(!isset($_SESSION['user'])) {
            HTTP::JSON(401);
        }

        $this->client = CloudWatchClient::factory(
            array(
                'key' => $_SESSION['user']['accesskey'],
                'secret' => $_SESSION['user']['secretkey'],
                'region' => 'us-east-1'
            )
        );
    }

    public function get(){
        $result = $this->client->describeAlarms();
        HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result['MetricAlarms']));
    }

    public function getHistory(){
        if(!empty($this->post['AlarmName'])) {
            try{
                $result = $this->client->describeAlarmHistory('AlarmName' => $this->post['AlarmName']);
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result['AlarmHistoryItems']));
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(400), 'No existe la alarma'));
            }
        }
        HTTP::JSON(400);
    }

    public function add () {
        if(!empty($this->post)) {
            try{
                $result = $this->client->putMetricAlarm(array(
                    // AlarmName is required
                    'AlarmName' => $this->post['AlarmName'],
                    'AlarmDescription' => 'Created from EC2 Console',
                    'ActionsEnabled' => true, // true || false
                    'OKActions' => $this->post['OKActions'] ? array($this->post['OKActions']) : array(),
                    // 'OKActions' => array('arn:aws:automate:us-east-1:ec2:stop'),
                    'AlarmActions' => $this->post['AlarmActions'] ? array($this->post['AlarmActions']) : array(), // arn:aws:automate:us-east-1:ec2:terminate
                    'InsufficientDataActions' => $this->post['InsufficientDataActions'] ? array($this->post['InsufficientDataActions']) : array(),
                    'MetricName' => $this->post['MetricName'], // MetricName is required
                    'Namespace' => 'AWS/EC2', // Namespace is required
                    // Statistic is required
                    'Statistic' => $this->post['Statistic'], // (string: SampleCount | Average | Sum | Minimum | Maximum )
                    'Dimensions' => array(array(                        
                        'Name' => 'InstanceId', // Name is required                        
                        'Value' => $this->post['InstanceId'], // Value is required
                    )),
                    // Period is required
                    'Period' => $this->post['Period'], // The period in seconds
                    // EvaluationPeriods is required
                    'EvaluationPeriods' => $this->post['EvaluationPeriods'], // Minutes
                    // Threshold is requiredr
                    'Threshold' => $this->post['Threshold'],
                    // ComparisonOperator is required
                    'ComparisonOperator' => $this->post['ComparisonOperator'] //(string: GreaterThanOrEqualToThreshold | GreaterThanThreshold | LessThanThreshold | LessThanOrEqualToThreshold )
                ));
                HTTP::JSON(200);
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(400), $e->getMessage());
            }
        }
        HTTP::JSON(400);
    }

    // public function enableAlarm(){
    //     $result = $this->client->enableAlarmActions(array(
    //         // AlarmNames is required
    //         'AlarmNames' => array('awsec2-i-b1e6569c-CPU-Utilization')
    //     ));
    //     HTTP::JSON(200);
    // }

    // public function disableAlarm(){
    //     $result = $this->client->disableAlarmActions(array(
    //         // AlarmNames is required
    //         'AlarmNames' => array('awsec2-i-b1e6569c-CPU-Utilization')
    //     ));
    //     HTTP::JSON(200);
    // }

    // public function changeState(){
    //     $result = $this->client->setAlarmState(array(
    //         // AlarmName is required
    //         'AlarmName' => 'awsec2-i-b1e6569c-CPU-Utilization',
    //         // StateValue is required
    //         'StateValue' => 'INSUFFICIENT_DATA', /* (string: OK | ALARM | INSUFFICIENT_DATA ) */
    //         // StateReason is required
    //         'StateReason' => 'State changed to ALARM at 2014/09/11'
    //     ));
        
    //     HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result));
    // }

    public function delete () {
        if(!empty($this->delete['AlarmName'])) {
            try{
                $result = $this->client->deleteAlarms(array(
                    'AlarmNames' => array($this->delete['AlarmName']) // AlarmNames is required
                ));
                HTTP::JSON(200);
            }catch(Exception $e){
                HTTP::JSON(Partial::createResponse(HTTP::Value(400), 'No existe la alarma'));
            }
        }
        HTTP::JSON(400);
    }
}

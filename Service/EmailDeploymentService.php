<?php
namespace EloquaApi\Service;

class EmailDeploymentService extends AbstractService
{

    /**
    *  Create Contact  
    */
    public function create($preMerge, $options = array() ) {
        $preMerge = $preMerge ? "true" : "false";
        return $this->client->request('/api/REST/2.0/assets/email/deployment?preMerge='.$preMerge , 'post', $options);
    }

    public function sendEmail($name, $isTest, $toContactId, $emailId){
        $options = array();
        $options["name"] = $name;
        $options['type'] = $isTest ? "EmailTestDeployment" : "EmailLowVolumeDeployment";
        $options["contactId"] = $toContactId;
        $options["sendOptions"] = array(
            "allowResend"=>true,
            "allowSendToUnsubscribe"=>true
        );
        $options["email"] = array(
            "id"=>$emailId,
            "name"=>$name
        );
        return $this->create(true, $options);
    }
}
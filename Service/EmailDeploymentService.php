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

    public function sendEmail($name, $isTest, $toContactId, $subject, $from_email, $from_name, $html){
        $options = array();
        $options["name"] = $name;
        $options['type'] = $isTest ? "EmailTestDeployment" : "EmailLowVolumeDeployment";
        $options["contactId"] = $toContactId;
        $options["sendOptions"] = array(
            "allowResend"=>true,
            "allowSendToUnsubscribe"=>true
        );
        $options["email"] = array(
            "htmlContent" => array(
                "contentSource"=>$html,
                "type"=>"html"
            ),
            "name"=>$name,
            "plainText"=>strip_tags($html),
            "senderEmail"=>$from_email,
            "senderName"=>$from_name,
            "subject"=>$subject

        );

        return $this->create(true, $options);
    }

}
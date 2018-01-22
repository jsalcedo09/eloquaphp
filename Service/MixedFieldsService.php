<?php
namespace EloquaApi\Service;

class MixedFieldsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function ContactFields( $options = array() ) {
        return $this->client->request( 'api/REST/1.0/assets/contact/fields', 'get', $options );
    }
    
    public function CustomObjects( $options = array() ) {
        return $this->client->request( 'api/REST/1.0/assets/customObjects', 'get', $options );
    }
    
    public function createContact( $options = array() ) {
        return $this->client->request( '/api/REST/1.0/data/contact', 'post', $options );
    }    
    
    public function all( $options = array() ) {
        return $this->client->request( '/api/REST/1.0/data/contacts', 'get', $options );
    }   

    
    public function updateContact($id, $contact = array(), $options = array() ) {

        $contact = array_merge($contact, array("id"=>$id));
        $options = array_merge($contact, $options);
        return $this->client->request( '/api/REST/1.0/data/contact/'.$id, 'put', $options );

    }    
    
    
    public function contactInList($contact_id, $list_id) {

        $lists = $this->client->request( '/api/REST/2.0/data/contact/'.$contact_id.'/membership', 'get');
        foreach($lists as $list){
            if($list->id == trim("".$list_id)){
                return true;
            }
        }
        return false;
    }

    public function contactSearch($value, $term='', $operator='', $options=[]) {
        $search = $value;
        if(!empty($term) && !empty($operator)){
            $search = $term.$operator.$search;
        }
        $options = array_merge($options, array(
            'search'=>$search
        ));
        return $this->all($options);
    }
    
    public function updateCustom($user_id, $contact = array(), $options = array() ) {
        $contact = array_merge($contact, array("user_id"=>$user_id));
        $options = array_merge($contact, $options);        
        $final_response_array = array();       
        if(!empty($options['fieldValues'])){
            foreach($options['fieldValues'] as $key => $fieldValues){
                if($fieldValues['rowID'] > 0){
                    $response_array = $this->client->request( '/api/REST/2.0/data/customObject/'.$fieldValues['id'].'/instance/'.$fieldValues['rowID'], 'put',$fieldValues);
                }else{
                    $createresponse_array = $this->client->request( '/api/REST/2.0/data/customObject/'.$fieldValues['id'].'/instance', 'post',$fieldValues);
                    $createresponse_array->uniqueCode = $fieldValues['id'];
                    $final_response_array[] = $createresponse_array;
                }
                
                
            }
        }        
        return $final_response_array;
    }    

}
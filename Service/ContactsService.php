<?php
namespace EloquaApi\Service;

class ContactsService extends AbstractService
{
    /**
     * Returns all campaigns
     *
     * @param int $page
     *
     * @return \stdClass
     */
    public function all( $options = array() ) {

        return $this->client->request( '/api/REST/1.0/data/contacts', 'get', $options );

    }

    /**
    *  Create Contact  
    */
    public function create( $options = array() ) {
        return $this->client->request( '/api/REST/1.0/data/contact', 'post', $options );
    }

    public function search($value, $term='', $operator='', $options=[]) {
        $search = $value;
        if(!empty($term) && !empty($operator)){
            $search = $term.$operator.$search;
        }
        $options = array_merge($options, array(
            'search'=>$search
        ));
        return $this->all($options);
    }

    public function update($id, $contact = array(), $options = array() ) {

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


}
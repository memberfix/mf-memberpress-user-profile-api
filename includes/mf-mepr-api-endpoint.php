<?php

class MF_Mepr_API_Endpoint{
    private $all_fields = array(
        'mepr-address-one',
        'mepr-address-two',
        'mepr-address-city',
        'mepr-address-state',
        'mepr-address-zip',
        'mepr-address-country'
    );
    private $required_fields = array(
        'user_id'
    );
    private $request;

    public function __construct(){
        $this->create_properties();
    }

    public function process($request){
        $this->request = $request;
        $this->validate_api_key();
        $this->validate_user_id();
        $this->validate_request_fields();
        $this->update_fields();
        $this->response('success','User updated successfully!');
    }

    private function validate_api_key(){
    	if ($this->request->get_header('MEMBERPRESS-API-KEY') != get_option('mpdt_api_key')) {
    		$this->response('error','API key is incorrect');
    		exit;
	    }
    }

    private function validate_user_id(){
	    if (isset($this->request['user_id'])){
		    $this->user_id = $this->request['user_id'];
	    }else{
		    $this->response('error','Please set the user ID field');
		    exit;
	    }
    }

    private function validate_request_fields(){
        foreach ($this->required_fields as $field){
            if(!isset($this->request[$field])){
                $this->response('error', "$field is missing");
                exit;
            }
        }
    }

    private function update_fields(){
        foreach ($this->all_fields as $field){
            update_user_meta($this->user_id, $field,$this->request[$field]);
        }
    }

    private function response($type,$message){
    	$value = array(
    	    'type' => $type,
    	    'message' => $message
    	);

    	echo json_encode($value);
    }

    private function create_properties(){
        $options = MeprOptions::fetch();
        $fields = $options->custom_fields;

        foreach ($fields as $field){
            array_push($this->all_fields, $field->field_key);

            if ($field->required){
                array_push($this->required_fields, $field->field_key);
            }
        }
    }
}

new MF_Mepr_API_Endpoint();
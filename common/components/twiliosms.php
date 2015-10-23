<?php

class twiliosms {
    
    public $twilio_auth_token;
    public $twilio_account_sid;
    public $twilio_from_phone;
    public $country_code = '+234';
    
    public function sendSMS($message, $receiver_phone = 10, $sms_type = 'account_updated') {
        include_once(dirname(__FILE__) . '/../../vendor/yiisoft/yii2-twilio/Services/Twilio.php');
        
        $client = new Services_Twilio($this->twilio_account_sid, $this->twilio_auth_token);
        
        /*
         * 234 is Nigeria's country code, strip preceeding zeros and append 234
         */
        $receiver_phone = preg_replace('/[^A-Za-z0-9]/', '', $receiver_phone); //remove any formatting
        $receiver_phone = preg_replace('/^234/', '', $receiver_phone);         //remove country code if already present
        $receiver_phone = preg_replace('/^0+/', '', $receiver_phone);
        $receiver_phone = $this->country_code . $receiver_phone;        
        
        $sms_text = $message;

        try {
            $res = $client->account->sms_messages->create($this->twilio_from_phone, $receiver_phone, $sms_text);
        } catch (Exception $e) {
            return array('error' => true, 
                         'description' => $e->getMessage()
                    );
        } 
        
        return array('sid' => $res->sid, 
                     'created_date' => $res->date_created, 
                     'cost' => $res->price, 
                     'cost_unit' => $res->price_unit, 
                     'account_sid' => $res->account_sid, 
                     'status' => $res->status, 
                     'to' => $res->to,
                     'error' => false
                );
    }

}

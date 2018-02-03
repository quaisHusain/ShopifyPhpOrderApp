<?php
class Chatfuel extends CI_Controller{
	  public function __construct()
    {
        parent::__construct();
        $this->load->model('Global_model');
  		$this->load->library('form_validation');
        $this->load->library('session');
  		//$data=shopifyData();
    	//$this->load->library('Shopify' , $data);
    	//$this->load->library('Chatfuel');
        include APPPATH . 'libraries/chatfuel_lib/config.php';
    }

    public function register()
    {
        $cf = new ChatFuelClient();
        $refs=$cf->ref;
        $ref = explode("|",$refs);
        //echo $cf->messenger_user_id;
        //print_r($ref);
        $shopifyID = null;
        $cartToken = null;
         if(count($ref) > 0 && !empty($cf->messenger_user_id))
         {
             $shopifyID = $ref[3];
             $cartToken = $ref[2];
             $ss=$refs."|".$cf->messenger_user_id;
             $checkdata=array('data'=>$ss,'data_type'=>'register');
             $this->Global_model->testData($checkdata);
             $cartData=array('shopify_cart_token'=>$cartToken, 'chatfuel_messenger_id'=>$cf->messenger_user_id,'ref_data'=>$refs,'shopify_domain'=>$shopifyID);
             $this->Global_model->addCart_register($cartData);
             $cf->createAttributes();
             $cf->addAttribute('cartFuelShopID',$shopifyID);
             $cf->addAttribute('cartFuelCartID',$cartToken);
             $cf->addAttribute('cartFuelSubscribed','true');
             $cf->attachAttsToMsg();
             $cf->addRedirect('CartFuel SubScribe');
             $cf->render();
         }
       
        // echo $cf->ref;
       /* echo "Test";
        
        $shopifyID = null;
        $cartToken = null;
        if(count($ref) > 0 && !empty($cf->messenger_user_id)){
            $qrrs = "insert into tbl_test_details set comment='user registerd by checkbox', custom_data='".$cf->messenger_user_id."'";
    $ress = mysqli_query($con_new,$qrrs);

    $shopifyID = $ref[3];
    $cartToken = $ref[2];
    $userSubs=CheckCart($con_new, $cartToken,$cf->messenger_user_id);
    if(!$userSubs){
        // add shop ID, cart token, and $cf->messenger_user_id to the database
        // for future reference

        $qrr1="insert into tbl_cart_details set
        store_name='".$ref[3]."',
        cart_token='".$ref[2]."',
        user_ref='".$ref[1]."',
        user_messenger_id='".$cf->messenger_user_id."',
        shopify_cust_id='".$ref[4]."',
        first_name='".$ref[5]."',
        last_name='".$ref[6]."'";

        $res = mysqli_query($con_new,$qrr1);

        $cf->createAttributes();
        $cf->addAttribute('cartFuelShopID',$shopifyID);
        $cf->addAttribute('cartFuelCartID',$cartToken);
        $cf->addAttribute('cartFuelSubscribed','true');
        $cf->attachAttsToMsg();
        $cf->addRedirect('CartFuel SubScribe');
        $cf->render();
    } else {
        $cf->createAttributes();
        $cf->addAttribute('cartFuelShopID',$shopifyID);
        $cf->addAttribute('cartFuelCartID',$cartToken);
        $cf->attachAttsToMsg();
        $cf->addRedirect('CartFuel Silent');
    }


        }
    	//echo $this->chatfuel->ref;*/
    }
}
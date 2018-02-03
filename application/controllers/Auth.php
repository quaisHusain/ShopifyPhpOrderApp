<?php
   class Auth extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        //Do your magic here
        $this->load->model('Global_model');
        //$this->load->model('Home_model');
        $this->load->library('form_validation');
    }
    
    //App Install View Page
    public function Install_login()
    {
         $this->load->view('auth/appinstall');
    }


    //Check For Login
    public function check_login()
    {
         $shop = $this->input->get('shop');
         if($shop!='')
         {
             $this->auth($shop);
         }
         else
         {
            redirect('Auth/Install_login');
         }
    }


    //Authenticate Shopify Store
    public function auth($shop){

        $data = array(
            'API_KEY' => $this->config->item('shopify_api_key'),
            'API_SECRET' => $this->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $shop,
            'ACCESS_TOKEN' => ''
        );

        $this->load->library('Shopify' , $data); //load shopify library and pass values in constructor

        $scopes = array('read_checkouts','read_content', 'write_content', 'read_themes', 'write_themes', 'read_products', 'write_products', 'read_customers', 'write_customers', 'read_orders', 'write_orders', 'read_script_tags', 'write_script_tags', 'read_fulfillments', 'write_fulfillments', 'read_shipping', 'write_shipping',); //what app can do
        $redirect_url = $this->config->item('redirect_url'); 

         //redirect url specified in app setting at shopify
        $paramsforInstallURL = array(
            'scopes' => $scopes,
            'redirect' => $redirect_url
        );

        $permission_url = $this->shopify->installURL($paramsforInstallURL);
         
        $this->load->view('auth/escapeIframe', ['installUrl' => $permission_url]);
      
    }


    // After Installation callback function
    public function auth_callBack()
    {
        $code = $this->input->get('code');
        $shop = $this->input->get('shop');

        if(isset($code)){
            $data = array(
            'API_KEY' => $this->config->item('shopify_api_key'),
            'API_SECRET' => $this->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $shop,
            'ACCESS_TOKEN' => ''
        );
            $this->load->library('Shopify' , $data); //load shopify library and pass values in constructor
        }
       
        $accessToken = $this->shopify->getAccessToken($code);
        $this->session->set_userdata(['shop' => $shop , 'access_token' => $accessToken]);
        $this->updateAccess_Token();
        if($accessToken!='')
        {
            redirect('Auth/home');
        }else
        {
            redirect('Auth/Install_login');
        }
       
    }

    //Home Page Controller
    public function home()
    {
        $code = $this->input->get('code');
        $shop = $this->input->get('shop');
        //echo $this->session->userdata('shop');
        if(isset($shop))
        {
            if($shop!='')
            {   //echo $shop;
                //exit;
                $this->session->set_userdata('shop',$shop);
            }
        }

        $access_token=$this->session->userdata('access_token');

        if($code!='' || $access_token!='')
        {
            //echo $access_token=$this->session->userdata('access_token');
           // echo "logged in";
            $data = array(
                'api_key' => $this->config->item('shopify_api_key'),
                'shop' => $this->session->userdata('shop'),
                'access_token'=>$this->session->userdata('access_token')
            );
             $data1 = array(
                'API_KEY' => $this->config->item('shopify_api_key'),
                'API_SECRET'=>$this->config->item('shopify_secret'),
                'SHOP_DOMAIN' => $this->session->userdata('shop'),
                'ACCESS_TOKEN' =>$this->session->userdata('access_token')
            );


           
            $this->load->library('Shopify' , $data1); 
             $ordersCount = $this->shopify->call(['METHOD' => 'GET', 'URL' =>'/admin/orders/count.json?'],TRUE);

          
             $data['orderCount']=$ordersCount->count;
             $this->load->load_admin('welcome',$data);

            //$data['productsCount']=$productsCount;
            //print_r($data['productsCount']);
            //$this->load->load_admin('app/home',$data); 
            //print_r($data);
            //$this->updateStore_details($data);
            //$this->load->load_admin('welcome',$data);
            // echo "App installed";
            //$this->dashabord($data);
        }
        else
        {    
            redirect('Auth/Install_login');
        }

    }


    //DashBoard
    public function dashabord($data)
    {
      # Redirect To DashBoard
      $this->load->load_admin('app/dashboard',$data);

    }

     public function updateAccess_Token()
    {
      
       $accessToken= $this->session->userdata('access_token'); 
      
       if($_GET['shop']!='' && $_GET['code']!=''&& $_GET['hmac']!='')
            {
               $shopdata= array('shop'=>$_GET['shop'],'code'=>$_GET['code'],'hmac'=>$_GET['hmac']);
                 $check_shop_exist= $this->Global_model->check_ShopExist($_GET['shop']);
                if($check_shop_exist)
                {
                    $this->Global_model->update_Shop($shopdata);
                }else
                { 
                    $this->Global_model->add_newShop($shopdata);
                }
            }
           
    }
}
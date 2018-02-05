<?php
require __DIR__.'/domparser/simple_html_dom.php';
class Home extends CI_Controller{
	  public function __construct()
    {
        parent::__construct();
        $this->load->model('Global_model');
  		$this->load->library('form_validation');
        $this->load->library('session');
  		//$data=shopifyData();
    	//$this->load->library('Shopify' , $data);


    }



    public function updateFullfilstatus()
    {   
        
        $input = file_get_contents('php://input');
        $testdad=array('data'=>$input, 'data_type'=>'FullfileOrder');
        $data=json_decode($input);
        $this->Global_model->testData($testdad);
        $data->status;
        $data->tracking_number;
        if($data->status="success"){
            $fullfillmentsss="fulfilled";
        }else{
           $fullfillmentsss=''; 
        }
       $update=array('orderid'=>$data->order_id,
          'tracking_number'=> $data->tracking_number,
          'fulfillments'=> $fullfillmentsss
        );
       $this->updateOrderFulfillStatus($update);
       echo '{"status":"ok"}';
    }

    public function OrderUpade()
    {
        $input = file_get_contents('php://input');
        $testdad=array('data'=>$input, 'data_type'=>'OrderUpade');
        $data=json_decode($input);
       // print_r($data);
       
        $orderid=$data->id;
        $card=$data->note;
        $tags=$data->tags;
        $fulfillments=$data->fulfillment_status;
         $fulfillmentss=$data->fulfillments;
        if(isset($fulfillmentss[0]->tracking_number))
        {
             $tracking_number= $fulfillmentss[0]->tracking_number;
        }else
        {
            $tracking_number= '';
        }
         $ordersdata=array('orderid'=>$data->id,'card'=>$data->note,'tags'=>$tags,'fulfillments'=>$data->fulfillment_status,'tracking_number'=>$tracking_number);
        $this->updateOrderAllField($ordersdata);
         echo '{"status":"ok"}';
    }

    public function NewOrderNotification()
    {
        $input = file_get_contents('php://input');
        $testdad=array('data'=>$input, 'data_type'=>'newOrder');
        $data=json_decode($input);
        //$this->Global_model->testData($testdad);
        $this->insert_order($data);
        print_r($data);
        exit;
    }

    function updateOrderAllField($update)
    {
        $sql="update tbl_orders set card='".$update['card']."', tags='".$update['tags']."', tracking_number='".$update['tracking_number']."', fulfillments='".$update['fulfillments']."' where orderid='".$update['orderid']."'";
        $this->db->query($sql);
    }




    public function updateOrderFulfillStatus($update)
    {
        $sql="update tbl_orders set tracking_number='".$update['tracking_number']."', fulfillments='".$update['fulfillments']."' where orderid='".$update['orderid']."'";
        $this->db->query($sql);
    }

    public function syncShopiFyorder()
    {   

        //print_r($_POST);
        $ordercount= $this->getcount_appOrders();
        if($ordercount>0)
        {
             
            if($ordercount < $_POST['toalOrders'])
            {
                 $lastOrder=$this->getLastOrderId();
                 $apiconfig= getShop_accessToken();
                 //print_r($apiconfig);
                 $this->load->library('Shopify' , $apiconfig);
                 //$param=array('limit'=>250,'status'=>'any','since_id'=>$lastOrder);
                 $orders=$this->shopify->call(['METHOD' => 'GET','URL'=>'admin/orders.json?since_id='.$lastOrder.'&limit=250&status=any'],TRUE);
                if(count($orders)>0)
                {
                
                     foreach($orders->orders as $order)
                     {
                         $this->insert_order($order);
                     }
              }

            }
            echo '{"msg":"Total sync orders: '. $ordercount.'"}';
        }
        else
        { 
            if($_POST['toalOrders']>$ordercount)
            {
                //Call FirstOrder
                $this->callInitialOrder($_POST);
            }
          
        }
       
        
    }

    public function callInitialOrder($data)
    {
          $apiconfig= getShop_accessToken();
          $this->load->library('Shopify' , $apiconfig);
         // /admin/orders.json
          //$param=array('limit'=>250,'status'=>'any');
          $orders=$this->shopify->call(['METHOD' => 'GET','URL'=>'admin/orders.json?limit=1&order=created_at%20asc'],TRUE);
          if(count($orders)>0){
            
             foreach($orders->orders as $order)
             {
                 $this->insert_order($order);
             }
          }
         
         
    }


    public function getLastOrderId()
    {
        $sql="select * from tbl_orders order by _id desc limit 0,1";
        $qurey=$this->db->query($sql);
        $row=$qurey->row();
        return $row->orderid;
    }

    public function insert_order($orders)
    {
         
         $orderid='';
         $orderNumber='';
         $name='';
         $zipcode='';
         $total='';
         $tags='';
         $note='';
         $fulfillments='';
         $fraud='';
         $paid='';
         $card='';
         $tracking_number='';
         $fulfillments=$orders->fulfillment_status;
        if(isset($orders->id))
        {
            $orderid=$orders->id;
        }

         if(isset($orders->name))
        {
            $orderNumber=$orders->name;
        }
        if(isset($orders->shipping_address->name))
        {
            $name=$orders->shipping_address->name;
        }
        if(isset($orders->shipping_address->zip))
        {
             $zipcode=$orders->shipping_address->zip;
        }
         $total=$orders->total_price;
         $tags=$orders->tags;
         if(isset($order->fulfillment_status))
         {
             $fulfillments=$orders->fulfillment_status;

         }
         $fraud=$orders->financial_status;
        
         $paid =$orders->financial_status;

         $card=$orders->note;

          $fulfillmentss=$orders->fulfillments;

        if(isset($fulfillmentss[0]->tracking_number))
        {
             $tracking_number= $fulfillmentss[0]->tracking_number;
        }
        $order_created=date("Y-m-d H:i:s", strtotime($orders->created_at));
        $data=array('orderNumber'=>$orderNumber,
            'name'=>$name,
            'zipcode'=>$zipcode,
            'total'=>$total,
            'tags'=>$tags,
            'fulfillments'=>$fulfillments,
            'fraud'=>$fraud,
            'paid'=>$paid,
            'card'=>$card,
            'tracking_number'=>$tracking_number,
            'orderid'=>$orderid,
            'order_created'=>$order_created
        );
        //print_r($data);
        $this->db->insert('tbl_orders',$data);
    }





    public function getOrderlist()
    {
        $this->getCountryList();
    }

     public function getCountryList()
  {
     $sort = array(
            'orderNumber','name','zipcode','total','tags','fulfillments','fraud','paid','card','tracking_number','order_created'
        );
         
        if ($_POST['search']['value'])
          {
            $search = "orderNumber like '%" . $_POST['search']['value'] . "%'
            or orderNumber like '%" . $_POST['search']['value'] . "%'
            or name like '%" . $_POST['search']['value'] . "%'
            or zipcode like '%" . $_POST['search']['value'] . "%'
            or total like '%" . $_POST['search']['value'] . "%'
            or tags like '%" . $_POST['search']['value'] . "%'
            or tracking_number like '%" . $_POST['search']['value'] . "%'
            or order_created like '%" . $_POST['search']['value'] . "%'
             ";
          }
        
        $get['fields'] = array(
            'orderNumber',
            'name',
            'zipcode',
            'total',
            'tags',
            'fulfillments',
            'fraud',
            'paid',
            'card',
            'tracking_number',
            'orderid',
            'order_created'
           
        );
        if (isset($search))
          {
             $get['search'] = $search;
          }
        $get['myll']   = $_POST['start'];
        $get['offset'] = $_POST['length'];
        if (isset($_POST['order'][0]))
          {
            $orrd         = $_POST['order'][0];
            $get['title'] = $orrd['column'];
            $get['order'] = $orrd['dir'];
          }
          $get['table']='tbl_orders';
          $list= $this->Global_model->GetSingleDataRecord($get);
        //print_r($list);
        $cc        = $list['count'];
        $data      = array();
        $no        = $_POST['start'];
        $total_rec = array_pop($list);
        foreach ($list as $missing)
          {
            $no++;
            $row    = array();

            if (strpos($missing->tags, 'ufc') !== false) {
                $tagss='<div class="circle_red"></div>';
                }else{
                    $tagss='<div class="circle_green"></div>';
                }
                if($missing->fulfillments=="fulfilled"){
                    $fullfillmentss='<div class="circle_green"></div>';
                }else
                {
                     $fullfillmentss='<div class="circle_red"></div>';
                }

                if($missing->card!='')
                {
                    $card='<div class="circle_green"></div>';
                }else{
                     $card='<div class="circle_red"></div>';
                }
                if($missing->paid=="paid"){
                    $paid='<div class="circle_green"></div>';
                }
                else if($missing->paid=="unpaid"){
                    $paid='<div class="circle_red"></div>';
                }
                else{
                   $paid='<div class="circle_yellow"></div>';  
                }



            $row[]  = $missing->orderNumber;
            $row[]  = $missing->name;
            $row[]  = $missing->zipcode;
            $row[]  = $missing->total;
            $row[]  = $missing->tags;
            $row[]  = $fullfillmentss;
            $row[]  = $tagss;
            $row[]  = $paid;
            $row[]  = $card;
            $row[]  = $missing->tracking_number;
            $row[]  = $missing->order_created;
            $data[] = $row;
          }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $cc,
            "recordsFiltered" => $total_rec,
            "data" => $data
        );
        echo json_encode($output);
    }


    public function updateOrders()
    {
      

    }

    public function getOrdersByPage()
    {

    }

    public function getcount_appOrders()
    {
         $sql="select * from tbl_orders";
        $query=$this->db->query($sql);
        return $query->num_rows();

    }





    public function getOrders()
    {   
        //$this->search_by_tag();
        //exit;
        //$this->create_demoOrder();
        //exit;
        //$this->updateImage(); 
        //exit;   
        $limit=$_POST['length'];
        //print_r($_POST);
        //exit;
        $requestData= $_REQUEST;
         $data = array(
            'API_KEY' => $this->config->item('shopify_api_key'),
            'API_SECRET' => $this->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $_GET['shop'],
            'ACCESS_TOKEN' => $_GET['access_token']
         );
         //print_r($data);
         $start=$_POST['start']/$limit;
         $page=$start+1;
         $this->load->library('Shopify' , $data);
            //$searchs=$this->shopify->call(['METHOD' => 'GET','URL'=>'/admin/orders.json?name=1031'],TRUE);
                                //print_r($searchs);
                                //exit;


         $htmlarray=array(
                                'limit'=>$limit,
                                'status'=>$_POST['status'],
                                'financial_status'=>$_POST['financial_status'],
                                'fulfillment_status'=>$_POST['fulfillment_status'],
                                'page'=>$page,
                                'tag'=>'blue'
                                ///''=>
                                );
         $search=array(
                            'status'=>$_POST['status'],
                            'financial_status'=>$_POST['financial_status'],
                            'fulfillment_status'=>$_POST['fulfillment_status'],
                                );

                                $orders=  $this->shopify->call(['METHOD' => 'GET', 'URL' =>'/admin/orders.json','DATA'=>$htmlarray],TRUE);



                                $ordersCount=  $this->shopify->call(['METHOD' => 'GET', 'URL' =>'/admin/orders/count.json','DATA'=>$search],TRUE);
                             
                               
                                   // print_r($search);
                                $reco=array();
                                if($orders){
                                    //print_r($orders->orders);
                                     count($orders->orders);
                                     $nestedData=array();
                                   foreach($orders->orders as $order){
                                        //print_r($order->shipping_address->name);
                                    if($order->fulfillment_status=="fulfilled"){
                                            $sfulfil='<div class="circle_green"></div>';
                                            }else{
                                                $sfulfil='<div class="circle_red"></div>';
                                            }
                                            if($order->fulfillment_status=="unpaid"){
                                                    $finan='<div class="circle_red"></div>';
                                            }else if($order->fulfillment_status=="paid"){
                                                 $finan='<div class="circle_green"></div>';
                                            }else{
                                                 $finan='<div class="circle_yellow"></div>';
                                            }
                                            if($order->note=="")
                                            {
                                                $note='<div class="circle_red"></div>';
                                            }
                                            else
                                            {
                                                 $note='<div class="circle_green"></div>';
                                            }
                                            $trackingId='';
                                            $fulfillments=$order->fulfillments;
                                            if(isset($fulfillments[0]->tracking_number)){
                                               $trackingId= $fulfillments[0]->tracking_number;
                                            }
                                            if(isset($order->shipping_address->name)){
                                                $shipping=$order->shipping_address->name;
                                            }else{
                                                $shipping='';
                                            }
                                            if(isset($order->shipping_address->zip)){
                                                $zip=$order->shipping_address->zip;
                                            }else{
                                                $zip='';
                                            }
                                            $nestedData[] = $order->number;
                                            $nestedData[] = $shipping;
                                            $nestedData[] = $zip;
                                            $nestedData[] = $order->total_price;
                                            //$nestedData[] = $order->note;
                                            $nestedData[] = $order->tags;
                                            $nestedData[] = $sfulfil;
                                            $nestedData[] = $order->note;
                                            $nestedData[] = $finan;
                                            $nestedData[] = $note;
                                            $nestedData[] = $trackingId;
                                           
                                            $reco[] = $nestedData;
                                            $nestedData=array();
                                        }
                                    //echo count($orders['order']);
                                }
                               // print_r($orders-);
                               
                                 
                       $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $ordersCount->count ),  // total number of records
            "recordsFiltered" => intval( $ordersCount->count ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            =>$reco   // total data array
            );

                echo json_encode($json_data);          
                                


    }

 


  

    	
}


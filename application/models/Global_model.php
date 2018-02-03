<?php

class Global_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
  
    /*********start comman function for all*******************/


    public function check_ShopExist($shop=NULL)
    {
      $query= $this->db->query("SELECT * FROM `shopify_stores` where  domain='".$shop."'");
     $rows= $query->num_rows();
     if($rows>0)
     {
      return TRUE;
     }
     else
      {
         return FALSE;
      }
    }


    public function update_Shop($data)
    {
      if($this->session->userdata('access_token')!=''){
      $sql="update  shopify_stores set code='".$data['code']."', hmac='".$data['code']."', token='".$this->session->userdata('access_token')."' where  domain='".$data['shop']."' ";
        $this->db->query($sql);
      }
    }
    public function add_newShop($data)
    {
      $sql="insert into shopify_stores set code='".$data['code']."', hmac='".$data['code']."', domain='".$data['shop']."', token='".$this->session->userdata('access_token')."' ";
      $this->db->query($sql);
    }

    

    
    
   
    

   

    public function update_shop_accessToken($shop,$accessToken)
    {
      $sql="update shopify_stores set token='".$accessToken."' where domain='".$shop."'";
      $this->db->query($sql);

    }

    public function checkStorActive($cartData)
    {
      $sql="select * from shopify_stores where domain='".$cartData['shopify_domain']."'";
      $query=$this->db->query($sql);
      return $query->row();
    }

    
   

    public function testData($data)
    {
      $sql="insert into testtable set data='".$data['data']."', data_type='".$data['data_type']."'";
      $this->db->query($sql);
    }

     public function GetSingleDataRecord($data)
    {
        $this->db->select($data['fields']);
        $this->db->from($data['table']);
        if(isset($data['search'])){
        $this->db->where($data['search']);
        }
        if(isset($data['order'])){
            $this->db->order_by($data['title'], $data['order']);
        }
        else{
            $this->db->order_by('_id', 'desc');
        }
        $this->db->limit($data['offset'],$data['myll']);
        $query = $this->db->get();
        //print_r($this->db->last_query());
        $row = $query->result();
        //For total number of records
        $this->db->select($data['fields']);
        $this->db->from($data['table']);
        if(isset($data['search'])){
        $this->db->where($data['search']);
        }
        $count = $this->db->count_all_results();
        $row['count'] = $count;
        return $row;
    }

 }
?>

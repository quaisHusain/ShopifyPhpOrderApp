<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function shopifyData(){
	$ci =& get_instance();
      $data = array(
            'API_KEY' => $ci->config->item('shopify_api_key'),
            'API_SECRET' => $ci->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $ci->session->userdata('shop'),
            'ACCESS_TOKEN' => $ci->session->userdata('access_token')
            );
     return $data;

     }
   function getShop_accessToken()
    {
           $ci =& get_instance();
      $query=$ci->db->query("SELECT * FROM `shopify_stores` limit  0,1");
      $rowdata=$query->row();
      if(count($rowdata)>0)
      {
           $data = array(
            'API_KEY' => $ci->config->item('shopify_api_key'),
            'API_SECRET' => $ci->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $rowdata->domain,
            'ACCESS_TOKEN' => $rowdata->token
            );
     return $data;
      }
    }

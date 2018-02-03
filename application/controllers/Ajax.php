<?php
require __DIR__.'/domparser/simple_html_dom.php';
class Ajax extends CI_Controller{
	  public function __construct()
    {
        parent::__construct();
        $this->load->model('Global_model');
  		$this->load->library('form_validation');
        $this->load->library('session');
  		//$data=shopifyData();
    	//$this->load->library('Shopify' , $data);


    }
   
    public function updateChatFuelSettings()
    {

        //print_r($_POST);
        if($_POST['checkbox']!='' && $_POST['chatbotid']!='')
        { 
            $html = new simple_html_dom();
            $html_string=$_POST['checkbox'];
            $html->load($html_string);
            $datas=$html->find('div',0);
            if(isset($datas->attr))
            {
                $attribute=$datas->attr;
                if(isset($attribute['messenger_app_id']) && isset($attribute['page_id']))
                {
                    if($_POST['chatbotid']!='')
                    {
                        $urlex=explode('/',$_POST['chatbotid']);
                        if(isset($urlex[5]))
                        {
                           $checkoboxCode= $this->generateCheckBoxCode($attribute);
                           $shopThemedata= $this->Global_model->getShopThemeId($_POST['shop']);
                            if(count($shopThemedata)>0)
                            {
                                $shopThemedata->theme_id;
                                $shopThemedata->product_page_old;
                                 $htmlarray=array('asset'=>array(
                                'key'=>'templates/product.liquid',
                                'value'=>$shopThemedata->product_page_old.$checkoboxCode
                                ));
                                 $apidata=array( 'API_KEY' => $this->config->item('shopify_api_key'),
                                    'API_SECRET' => $this->config->item('shopify_secret'),
                                    'SHOP_DOMAIN' => $_POST['shop'],
                                    'ACCESS_TOKEN' => $_POST['access_token']);
                                 print_r($apidata);
                                 $this->load->library('Shopify' , $apidata);
                                $themes=  $this->shopify->call(['METHOD' => 'PUT', 'URL' =>'/admin/themes/'.$shopThemedata->theme_id.'/assets.json',
                                    'DATA'=>$htmlarray],TRUE);
                               print_r($themes);
                            }
                            else
                            {
                                  echo '{"code":"100","msg":"Product Page Not Found."}';  
                            }
                             /*$themes=  $this->shopify->call(['METHOD' => 'PUT', 'URL' =>'/admin/themes/'.$shopThemeId.'/assets.json'],TRUE);
                                $htmlarray=array('asset'=>array(
                                'key'=>'templates/product.liquid',
                                'value'=>$data['prdpage'].$custom_html
                                ));
                                */
                                

                        }
                        else
                        {
                            echo '{"code":"100","msg":"Invalid ChatFuel Bot URL"}';  
                        }
                    }
                    else
                    {
                        echo '{"code":"100","msg":"Invalid Checkbox Code"}';  
                    }

                }
                else
                {
                  echo '{"code":"100","msg":"Invalid Checkbox Code"}';  
                }
            }
            else
            {
                 echo '{"code":"100","msg":"Invalid Checkbox Code"}';  
            }
        }
        else
        {
            echo '{"code":"100", "msg":"Invalid Data"}';
        }
        
      
        //$html->load($html_string);
        //$datas=$html->find('div',0);
        //$attribute=$datas->attr;

    }

   

    public function generateCheckBoxCode($attribute){

      $custom_html='

        <input type="hidden" value="{{shop.domain}}" id="shop_name">
        <input type="hidden" value="" id="crttoken">
            <script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<script>
  var urlredirect="{{shop.url}}";

 function testMyfunction()
  {
        
         var cartForm1 =document.querySelector("form[action='."'".'/cart/add'."'".']"); 
        
     $.ajax({
            type: "POST",
            url: "/cart/add.js",
            data:$("form[action='."'".'/cart/add'."'".']").serialize(),
            dataType:"JSON",
            success:function(dd)
            {
          

                $.ajax({
                type: "GET",
                url: "/cart.js",
                dataType:"JSON",
                success:function(cartdata)
                {
                    $("#crttoken").val(cartdata["token"]);
                    window.location.href=urlredirect+"/cart"; 
                     window.confirmOptIn(); 
                },
                 });


            },
    });
        
   
  }

</script>
        
<script> 
var cartForm =document.querySelector("form[action='."'".'/cart/add'."'".']");
  var buttonn=cartForm.querySelector('."'".'button[type="submit"]'."'".');
  buttonn.setAttribute("type", "button");
  buttonn.setAttribute("onclick", "testMyfunction();");
 // console.log(buttonn);
  //console.log();
  var prnt=buttonn.parentElement;
var parent=prnt.parentElement;
  //console.log(parent);
    var mydiv = document.createElement("div");
  mydiv.setAttribute("id", "FbDDDiv1");
  parent.appendChild(mydiv);
  var el = document.querySelector("#FbDDDiv1");
  el.innerHTML += '."'".'<div  class="fb-messenger-checkbox" origin="" page_id="'.$attribute['page_id'].'" messenger_app_id="'.$attribute['messenger_app_id'].'" user_ref="" prechecked="true" allow_login="true" size="large"></div>'."'".';
window.fbMessengerPlugins = window.fbMessengerPlugins || {
    init: function() {
        FB.init({
            appId: "'.$attribute['messenger_app_id'].'",
            xfbml: true,
            version: "v2.6"
        });
    },
    callable: []
};
window.fbMessengerPlugins.callable.push(function() {
    var ruuid, fbPluginElements = document.querySelectorAll(".fb-messenger-checkbox[page_id='."'".$attribute['page_id']."'".']");
    if (fbPluginElements) {
        for (i = 0; i < fbPluginElements.length; i++) {
            ruuid = "cf_" + (new Array(16).join().replace(/(.|$)/g, function() {
                return ((Math.random() * 36) | 0).toString(36)[Math.random() < .5 ? "toString" : "toUpperCase"]();
            }));
            fbPluginElements[i].setAttribute("user_ref", ruuid);
            fbPluginElements[i].setAttribute("origin", window.location.href);
            window.confirmOptIn = function() {
            var shopify_cust_id= $("#shopify_cust_id").val();
            var first_name= $("#first_name").val();
            var last_name= $("#last_name").val();
            var storeName=$("#shop_name").val();
            var cartdata=$("#crttoken").val(); 
            console.log(JSON.stringify(cartdata));
            window.refBlock = "cartFuel|"+ruuid+"|"+cartdata+"|"+storeName+"|"+shopify_cust_id+"|"+first_name+"|"+last_name;
                FB.AppEvents.logEvent("MessengerCheckboxUserConfirmation", null, {
                    app_id: "'.$attribute['messenger_app_id'].'",
                    page_id: "'.$attribute['page_id'].'",
                    ref: window.refBlock,
                    user_ref: ruuid
                });
          


             
            };
        }
    }
});
window.fbAsyncInit = window.fbAsyncInit || function() {
    window.fbMessengerPlugins.callable.forEach(function(item) {
        item();
    });
    window.fbMessengerPlugins.init();
};
setTimeout(function() {
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, "script", "facebook-jssdk"));
}, 0);

</script>
';  
    return $custom_html;
    }
		
}


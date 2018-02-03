<?php

// image sizes:
// landscape gallery image & first list item image: 1.91:1 (1920x1005, 909x476, 500x262)
// image cards & square gallery images: 1:1 (1000x1000, 900x900, 450x450)

// gallery image aspect ratio: for square gallery images, pass "true" to the newGallery() method
// list sizes: large, compact (default compact)
// webview heights: compact, tall, full (default tall)

if(!class_exists('ChatFuelClient')){

	// helper class
	class ChatFuelHelper {

		// trim text to the required length
		public function trimText($txt,$len){
			if(empty($txt)){ return ''; }
			return substr($txt,0,$len);
		}

		// returns object type, template, buttons, etc
		public function getObjStats($obj){
			$objType = ''; // text, image, template
			$objTemplate = ''; // (empty for text and image), generic, list, button
			$lastItemIdx = -1;
			$buttonCount = -1;
			$quickReplyCount = -1;

			// get type & template
			if(isset($obj['attachment'])){
				$objType = $obj['attachment']['type'];
				$objTemplate = (isset($obj['attachment']['payload']['template_type']))? $obj['attachment']['payload']['template_type'] : '';
			} else {
				$objType = 'text';
			}

			// get button counts
			if($objTemplate == 'generic' || $objTemplate == 'list'){
				$lastItemIdx = count($obj['attachment']['payload']['elements'])-1;
				if($lastItemIdx >= 0){
					if(empty($obj['attachment']['payload']['elements'][$lastItemIdx]['buttons'])){
						$buttonCount = -1;
					} else {
						$buttonCount = count($obj['attachment']['payload']['elements'][$lastItemIdx]['buttons']);
					}

				} else {
					$buttonCount = -1;
				}
				$quickReplyCount = (isset($obj['attachment']['quick_replies']))? count($obj['attachment']['quick_replies']) : -1;
			} else if($objTemplate == 'button'){
				$buttonCount = (is_array($obj['attachment']['payload']['buttons']))? count($obj['attachment']['payload']['buttons']) : -1;
			} else if($objType == 'image'){
				$quickReplyCount = (isset($obj['attachment']['quick_replies']))? count($obj['attachment']['quick_replies']) : -1;
			} else if($objType == 'text'){
				$quickReplyCount = (isset($obj['quick_replies']))? count($obj['quick_replies']) : -1;
			}
			return array(
				'objType' => $objType,
				'objTemplate' => $objTemplate,
				'lastIdx' => $lastItemIdx,
				'buttonCount' => $buttonCount,
				'quickReplyCount' => $quickReplyCount
			);
		}
	}

	// button builder class for creating buttons for text, galleries, lists
	class ChatFuelButtonBuilder {

		private $helper;

		public function __construct(){
			$this->helper = new ChatFuelHelper();
		}

		public function addLinkBtn(&$obj,$title,$url,$msgExt = false,$webViewHeight = 'tall'){
			$button = array(
				'type' => 'web_url',
				'url' => $url,
				'title' => $this->helper->trimText($title,20)
			);
			if($msgExt){
				$webViewHeight = (empty($webViewHeight))? 'tall' : $webViewHeight;
				$button['messenger_extensions'] = true;
				$button['webview_height_ratio'] = $webViewHeight;
			}
			$this->addButton($obj,$button);
		}

		public function addBlockBtn(&$obj,$title,$block,&$atts = null){
			$multiBlocks = (strpos($block,',') !== false);
			$button = array(
				'type' => 'show_block',
				'title' => $this->helper->trimText($title,20)
			);
			if($multiBlocks){
				$button['block_names'] = explode(',',$block);
			} else {
				$button['block_name'] = $block;
			}
			if(isset($atts)){
				$button['set_attributes'] = $atts;
			}
			$this->addButton($obj,$button);
		}

		public function addJSONBtn(&$obj,$title,$url,&$atts = null){
			$button = array(
				'type' => 'json_plugin_url',
				'url' => $url,
				'title' => $this->helper->trimText($title,20)
			);
			if(isset($atts)){
				$button['set_attributes'] = $atts;
			}
			$this->addButton($obj,$button);
		}

		public function addCallBtn(&$obj,$title,$phone){
			$button = array(
				'type' => 'phone_number',
				'phone_number' => $phone,
				'title' => $this->helper->trimText($title,20)
			);
			$this->addButton($obj,$button);
		}

		public function addShareBtn(&$obj){
			$button = array(
				'type' => 'element_share'
			);
			$this->addButton($obj,$button);
		}

		private function addButton(&$obj,$button){
			$objStats = $this->helper->getObjStats($obj);
			if($objStats['buttonCount'] < 3){
				if($objStats['lastIdx'] >= 0 && ($objStats['objTemplate'] == 'generic' || $objStats['objTemplate'] == 'list')){
					$obj['attachment']['payload']['elements'][$objStats['lastIdx']]['buttons'][] = $button;
				} else if($objStats['objTemplate'] == 'button') {
					$obj['attachment']['payload']['buttons'][] = $button;
				}
			}
		}

	}

	class ChatFuelQuickReply {

		private $quick_replies;
		private $helper;

		public function __construct(){
			$this->helper = new ChatFuelHelper();
			$this->quick_replies = array();
		}

		public function newQuickReply(){
			$this->quick_replies = array();
		}

		public function getQuickReplies(){
			return $this->quick_replies;
		}

		public function addLocationBtn(){
			$button = array(
				'content_type' => 'location'
			);
			$this->addButton($button);
		}

		public function addAttBtn($title,&$atts = null){
			$button = array(
				'title' => $this->helper->trimText($title,20)
			);
			if(isset($atts)){
				$button['set_attributes'] = $atts;
			}
			$this->addButton($button);
		}

		public function addBlockBtn($title,$block,&$atts = null){
			$button = array(
				'title' => $this->helper->trimText($title,20)
			);
			$button['block_names'] = explode(',',$block);
			if(isset($atts)){
				$button['set_attributes'] = $atts;
			}
			$this->addButton($button);
		}

		public function addJSONBtn($title,$url,&$atts = null){
			$button = array(
				'title' => $this->helper->trimText($title,20),
				'url' => $url,
				'type' => 'json_plugin_url'
			);
			if(isset($atts)){
				$button['set_attributes'] = $atts;
			}
			$this->addButton($button);
		}

		private function addButton($button){
			if(count($this->quick_replies) < 11){
				$this->quick_replies[] = $button;
			}
		}
	}

	class ChatFuelGallery {
		private $data;

		private $buttonBuilder;
		private $helper;

		public function __construct(){
			$this->helper = new ChatFuelHelper();
			$this->buttonBuilder = new ChatFuelButtonBuilder();

			$this->data = array(
				'attachment' => array(
					'type' => 'template',
					'payload' => array(
						'template_type' => 'generic',
						'elements' => array()
					)
				)
			);
		}

		public function addItem($title,$subtitle,$img){
			$this->data['attachment']['payload']['elements'][] = array(
				'title' => $this->helper->trimText($title,80),
				'subtitle' => $this->helper->trimText($subtitle,80),
				'image_url' => $img/*,
				'buttons' => array()*/
			);
		}

		public function newGallery($squareImage){
			if($squareImage){
				$this->data['attachment']['payload']['image_aspect_ratio'] = 'square';
			}
			$this->data['attachment']['payload']['elements'] = array();
		}

		public function getGallery(){
			return $this->data;
		}

		public function addLinkBtn($title,$url,$msgrExt = false, $webViewHeight = 'tall'){
			$this->buttonBuilder->addLinkBtn($this->data,$title,$url,$msgrExt,$webViewHeight);
		}

		public function addBlockBtn($title,$block,&$atts = null){
			$this->buttonBuilder->addBlockBtn($this->data,$title,$block,$atts);
		}

		public function addJSONBtn($title,$url,&$atts = null){
			$this->buttonBuilder->addJSONBtn($this->data,$title,$url,$atts = null);
		}

		public function addCallBtn($title,$phone){
			$this->buttonBuilder->addCallBtn($this->data,$title,$phone);
		}

		public function addShareBtn(){
			$this->buttonBuilder->addShareBtn($this->data);
		}

	}

	class ChatFuelList {
		private $data;

		private $buttonBuilder;
		private $helper;

		public function __construct(){
			$this->helper = new ChatFuelHelper();
			$this->buttonBuilder = new ChatFuelButtonBuilder();

			$this->data = array(
				'attachment' => array(
					'type' => 'template',
					'payload' => array(
						'template_type' => 'list',
						'top_element_style' => 'compact',
						'elements' => array()
					)
				)
			);
		}

		public function addItem($title,$subtitle,$img){
			$this->data['attachment']['payload']['elements'][] = array(
				'title' => $this->helper->trimText($title,80),
				'subtitle' => $this->helper->trimText($subtitle,80),
				'image_url' => $img,
				'buttons' => array()
			);
		}

		public function newList($size = 'compact'){
			$this->data['attachment']['payload']['elements'] = array();
			$this->data['attachment']['payload']['top_element_style'] = $size;
		}

		public function getList(){
			return $this->data;
		}

		public function addLinkBtn($title,$url,$msgExt = false,$webViewHeight = 'tall'){
			if($this->checkButtonCount()){
				$this->buttonBuilder->addLinkBtn($this->data,$title,$url,$msgExt,$webViewHeight);
			}
		}

		public function addBlockBtn($title,$block,&$atts = null){
			if($this->checkButtonCount()){
				$this->buttonBuilder->addBlockBtn($this->data,$title,$block,$atts);
			}
		}

		public function addJSONBtn($title,$url,&$atts = null){
			if($this->checkButtonCount()){
				$this->buttonBuilder->addJSONBtn($this->data,$title,$url,$atts = null);
			}
		}

		public function addCallBtn($title,$phone){
			if($this->checkButtonCount()){
				$this->buttonBuilder->addCallBtn($this->data,$title,$phone);
			}
		}

		public function addShareBtn(){
			/*
			if($this->checkButtonCount()){
				$this->buttonBuilder->addShareBtn($this->data);
			}
			*/
			return false;
		}
		private function checkButtonCount(){
			$underLimit = false;
			$curIdx = 0;
			if(count($this->data['attachment']['payload']['elements']) > 0){
				$curIdx = count($this->data['attachment']['payload']['elements']) - 1;
				if(empty($this->data['attachment']['payload']['elements'][$curIdx]['buttons'])){
					$underLimit = true;
				} elseif (count($this->data['attachment']['payload']['elements'][$curIdx]['buttons']) < 1){
					$underLimit = true;
				}

			}
			return $underLimit;
		}

	}

	class ChatFuelButtonBlock {

		private $data;
		private $buttonBuilder;
		private $helper;

		public function __construct(){
			$this->helper = new ChatFuelHelper();
			$this->buttonBuilder = new ChatFuelButtonBuilder();
			$this->data = array(
				'attachment' => array(
					'type' => 'template',
					'payload' => array(
						'template_type' => 'button',
						'text' => '',
						'buttons' => array()
					)
				)
			);
		}

		public function newButtonBlock($txt = ''){
			$this->data['attachment']['payload']['text'] = $this->helper->trimText($txt,640);
			$this->data['attachment']['payload']['buttons'] = array();
		}

		public function getBlock(){
			return $this->data;
		}

		public function addLinkBtn($title,$url,$msgExt = false,$webViewHeight = 'tall'){
			$this->buttonBuilder->addLinkBtn($this->data,$title,$url,$msgExt,$webViewHeight);
		}

		public function addBlockBtn($title,$block,&$atts = null){
			$this->buttonBuilder->addBlockBtn($this->data,$title,$block,$atts);
		}

		public function addJSONBtn($title,$url,&$atts = null){
			$this->buttonBuilder->addJSONBtn($this->data,$title,$url,$atts);
		}

		public function addCallBtn($title,$phone){
			$this->buttonBuilder->addCallBtn($this->data,$title,$phone);
		}

		public function addShareBtn(){
			$this->buttonBuilder->addShareBtn($this->data);
		}
	}

	class ChatFuelClient {

		private $me;
		private $helper;
		private $maxMsgs;
		private $cfRequest;

		public $cfVersion;

		public $chatfuel_user_id;
		public $messenger_user_id;
		public $profile_pic_url;
		public $first_name;
		public $last_name;
		public $gender;
		public $locale;
		public $timezone;
		public $last_clicked_button_name;
		public $last_user_freeform_input;
		public $last_visited_block_id;
		public $last_visited_block_name;
		public $ref;
		public $rss_and_search_subscriptions;
		public $botID;
		public $broadcastToken;

		public $gallery;
		public $list;
		public $buttonBlock;
		public $quickReply;
		public $attributes;
		public $callBackUrl;

		public function __construct(){

			$this->cfVersion = '201712041324';

			// get the request (post/get)
			switch($_SERVER['REQUEST_METHOD']){
				case 'GET':
					$this->cfRequest = &$_GET;
				break;
				case 'POST':
					$this->cfRequest = &$_POST;
				break;
				default:
					$this->cfRequest = &$_GET;
			}
			// parse Querystring as well in case it's a post + query string
			// query string overrides posted values since they may have
			// been passed back from the JSON
			if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SERVER['QUERY_STRING'])){
				parse_str($_SERVER['QUERY_STRING'], $qs);
				foreach($qs as $key => $value){
					$this->cfRequest[$key] = $value;
				}
			}

			/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
			/* standard/default ChatFuel request variabels
			/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
			$this->chatfuel_user_id = $this->getRequest('chatfuel_user_id');
			$this->messenger_user_id = $this->getRequest('messenger_user_id');
			$this->profile_pic_url = $this->getRequest('profile_pic_url');
			$this->first_name = $this->getRequest('first_name');
			$this->last_name = $this->getRequest('last_name');
			$this->gender = $this->getRequest('gender');
			$this->locale = $this->getRequest('locale');
			$this->timezone = $this->getRequest('timezone');
			$this->last_clicked_button_name = $this->getRequest('last_clicked_button_name');
			$this->last_user_freeform_input = $this->getRequest('last_user_freeform_input');
			$this->last_visited_block_id = $this->getRequest('last_visited_block_id');
			$this->last_visited_block_name = $this->getRequest('last_visited_block_name');
			$this->ref = $this->getRequest('ref');
			$this->rss_and_search_subscriptions = $this->getRequest('rss_and_search_subscriptions');

			$this->maxMsgs = 10;
			$this->helper = new ChatFuelHelper();
			$this->gallery = new ChatFuelGallery();
			$this->list = new ChatFuelList();
			$this->buttonBlock = new ChatFuelButtonBlock();
			$this->quickReply = new ChatFuelQuickReply();

			if(!empty($this->messenger_user_id)){
				session_id($this->messenger_user_id);
			}
			if(session_status() == PHP_SESSION_NONE){
				session_start();
			}

			$this->me = array(
				'messages' => array()
			);

		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* get GET/POST values
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function getRequest($req = ''){
			if(empty($req)){
				return http_build_query($this->cfRequest);
			} else {
				return (empty($this->cfRequest[$req]))? '' : trim($this->cfRequest[$req]);
			}
		}

		public function getFullRequest(){
			return http_build_query($this->cfRequest);
		}

		public function getFullURL(){
			$domain = $_SERVER['HTTP_HOST'];
			$path = $_SERVER['SCRIPT_NAME'];
			$queryString = (!empty($_SERVER['QUERY_STRING']))? '?' . $_SERVER['QUERY_STRING'] : '' ;
			$url = $domain . $path . $queryString;
			return $url;
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* simple text block
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addText($txt){
			if(count($this->me['messages']) < $this->maxMsgs){
				$this->me['messages'][] = array(
					'text' => $this->helper->trimText($txt,640)
				);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* attach image
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addImage($payload){
			if(count($this->me['messages']) < $this->maxMsgs){
				$this->me['messages'][] = array(
					'attachment' => array(
						'type' => 'image',
						'payload' => array(
							'url' => $payload
						)
					)
				);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* attach a video
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addVideo($payload){
			if(count($this->me['messages']) < $this->maxMsgs){
				$this->me['messages'][] = array(
					'attachment' => array(
						'type' => 'video',
						'payload' => array(
							'url' => $payload
						)
					)
				);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* attach an audio
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addAudio($payload){
			if(count($this->me['messages']) < $this->maxMsgs){
				$this->me['messages'][] = array(
					'attachment' => array(
						'type' => 'audio',
						'payload' => array(
							'url' => $payload
						)
					)
				);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* attach a file
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addFile($payload){
			if(count($this->me['messages']) < $this->maxMsgs){
				$this->me['messages'][] = array(
					'attachment' => array(
						'type' => 'file',
						'payload' => array(
							'url' => $payload
						)
					)
				);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* add a redirect block
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function addRedirect($block){
			if(!empty($block)){
				if(!isset($this->me['redirect_to_blocks'])){
					$this->me['redirect_to_blocks'] = array();
				}
				if(strpos($block,',') !== false){
					$block = explode (',',$block);
					foreach($block as $redir){
						$this->me['redirect_to_blocks'][] = $redir;
					}
				} else {
					$this->me['redirect_to_blocks'][] = $block;
				}
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* create text block with buttons
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function createButtonBlock($txt = ''){
			$this->buttonBlock->newButtonBlock($txt);
		}

		public function attachButtonBlock(){
			$data = $this->buttonBlock->getBlock();
			if(is_array($data) &&
			   is_array($data['attachment']['payload']['buttons']) &&
			   count($data['attachment']['payload']['buttons']) > 0 &&
			   $data['attachment']['payload']['text'] != ''){
				$this->me['messages'][] = $data;
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* quick reply functions
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function createQuickReply(){
			$this->quickReply->newQuickReply();
		}

		public function attachQuickReply(){
			$data = $this->quickReply->getQuickReplies();
			$objCount = count($this->me['messages']);
			if(count($data) > 0 && count($this->me['messages']) > 0){
				$obj = $this->me['messages'][$objCount-1];
				$objStats = $this->helper->getObjStats($obj);

				if(
					$objStats['quickReplyCount'] < 0 &&
					(
						$objStats['objType'] == 'text' ||
						$objStats['objType'] == 'image' ||
						$objStats['objTemplate'] == 'generic' ||
						$objStats['objTemplate'] == 'list'
					)
				){
						$this->me['messages'][$objCount-1]['quick_replies'] = $data;
				}
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* gallery functions
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function createGallery($squareImage = false){
			$this->gallery->newGallery($squareImage);
		}

		public function attachGallery(){
			if(count($this->me['messages']) < $this->maxMsgs){
				$data = $this->gallery->getGallery();
				if(is_array($data) &&
				   is_array($data['attachment']['payload']['elements']) &&
				   count($data['attachment']['payload']['elements']) > 0){
					$this->me['messages'][] = $data;
				}
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* list functions
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function createList($size = 'compact'){
			$this->list->newList($size);
		}

		public function attachList(){
			if(count($this->me['messages']) < $this->maxMsgs){
				$data = $this->list->getList();
				if(is_array($data) &&
				   is_array($data['attachment']['payload']['elements']) &&
				   count($data['attachment']['payload']['elements']) > 0){
					$this->me['messages'][] = $data;
				}
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* set user attributes
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function createAttributes(){
			$this->attributes = array();
		}

		public function getAttributes(){
			return $this->attributes;
		}

		public function addAttribute($key,$val){
			$this->attributes[$key] = $val;
		}

		public function clearAttributes(){
			$this->createAttributes();
		}

		public function attachAttsToMsg(){
			if(is_array($this->attributes)){
				$this->me['set_attributes'] = $this->attributes;
				$this->clearAttributes();
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* session functions
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function hasSession(){
			return (session_status() == PHP_SESSION_NONE)? false : true;
		}

		public function session($key = '', $val = null){
			if(session_status() == PHP_SESSION_NONE){
				return '';
			} else {
				if(empty($val)){
					return (isset($_SESSION[$key]))? $_SESSION[$key] : '';
				} else {
					$_SESSION[$key] = $val;
				}
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* emnoji functions
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function emoji($emoji){
			global $cfEmojiList;
			if(isset($cfEmojiList)){
				if(!empty($cfEmojiList[$emoji])){
					return $this->uniCodeToUTF16($cfEmojiList[$emoji]);
				}
			}
			return '';
		}

		function uniCodeToUTF16($emoji) {
			$emoji  = str_replace('U+','0x',$emoji);
			$emoji  = explode(' ',$emoji);
			$output = '';
			foreach ($emoji as $unicode) {
				if($unicode > 0x10000) {
					$first = (($unicode - 0x10000) >> 10) + 0xD800;
					$second = (($unicode - 0x10000) % 0x400) + 0xDC00;
					$output .= sprintf("\\u%X\\u%X", $first, $second);
				} else {
					$output .= "\\u" . str_replace("0x","",$unicode);
				}
			}
			return json_decode('"' . $output . '"');
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* broadcast to users
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		function broadcast($bot,$token,$user,$block,$data = array()){
			if(!empty($bot) && !empty($token) && !empty($user)){
				$sendUrl  = 'https://api.chatfuel.com/bots/' . $bot . '/users/' . $user . '/send';
				$sendUrl .= '?chatfuel_token=' . $token;
				$sendUrl .= '&chatfuel_block_name=' . $this->fullUrlEncode($block);
				$options = array(
					'http' => array(
						'method' => 'POST',
						'header'=> 'Content-Type: application/json' . "\r\n" . 'Accept: application/json' . "\r\n"
					)
				);
				if(count($data) > 0){
					$options['http']['content'] = json_encode($data);
				}
				$context = stream_context_create($options);
				$result  = file_get_contents($sendUrl,false,$context);
			}
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* perform a full URL encode to ensure links work in ChatFuel
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function fullUrlEncode($string) {
			$replaceWith = array('%20','%21','%2A','%27','%28','%29','%3B','%3A','%40','%26','%3D','%2B','%24','%2C','%2F','%3F','%25','%23','%5B','%5D');
			$toReplace = array(' ','!','*',"'",'(',')',';',':','@','&','=','+','$',',','/','?','%','#','[',']');
			return str_replace($replaceWith,$toReplace,$string);
		}

		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		/* render the JSON
		/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
		public function render(){
			if(count($this->me['messages']) <= 0){
				unset($this->me['messages']);
			}
			echo json_encode($this->me);
		}

	}
}
?>
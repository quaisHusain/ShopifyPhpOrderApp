<article class="content forms-page">
	<div class="title-block">
	</div>
 <div class="subtitle-block">
			<h3 class="subtitle"> Enter ChatFuel details.</h3>
	</div>
	<section class="section">
		<div class="row sameheight-container">
			<div class="col-md-6">
				<div class="card card-block sameheight-item" style="height: 710px;">
					<form action="" id="frm" method="post" accept-charset="utf-8">
						<div class="col-md-12"> 
							<p>
								<input class="form-control" id="chatbotid" value=""  placeholder="Enter ChatFUel Bot Url" type="text">
								</p>
								<p>
									<label>ChatFuel Checkbox Code</label>
									<textarea class="form-control" id="checkbox" name="checkbox" rows="3" placeholder="Enter Checkbox Code"></textarea>
								</p>
								<p>
									<div class="col-md-4">
										<input class="btn btn-block btn-primary btn-flat" name="submit" onclick="updateThemeFile();" id="submit" value="Submit" type="button">
									</div>
									<div class="col-md-8" id="msg">
									</div>
								</p>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	</article>              
<script>
   function updateThemeFile()
   {
		   var error=0;
			  var checkbox=$('#checkbox').val();
			  var chatbotid=$('#chatbotid').val();
			  if(checkbox=='')
			  {
			  		$('#msg').css('color','red');
			  		$('#msg').html('Please Enter Valid ChatFUel checkbox code.');
			    
			    error++;
			  }
			  if(chatbotid=='')
			  {
			  		$('#msg').css('color','red');
			    $('#msg').html('Please Enter Valid ChatFUel Bot Url.');
			    error++;
			  }
  
	  if(error==0)
	  	{		
										$('#msg').html('');
					$.ajax(
					{
							type : 'POST',
							url : '<?php echo base_url(); ?>Ajax/updateChatFuelSettings',
							data : {checkbox:checkbox,chatbotid:chatbotid,shop:shop,access_token:access_token},
							dataType:"json",
						beforeSend: function(data)
						{
							//	$('.loading').fadeIn();
						},
					success: function(data)
					{
								if(data['code'] == 200)
							{
									$('#msg').css('color','green');
									$('#msg').html(data['msg']);
									$('.loading').hide();
							}
								else
							{		//alert('invalid');
										$('#msg').css('color','red');
										$('#msg').html(data['msg']);
										$('.loading').hide();
							}
				
					},async: false
				
			});
		}
 }

</script>
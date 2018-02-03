 <style type="text/css">
   .dataTables_filter input {
    width: 300px !important;
}

 </style>
 <article class="">
 <div class="col-md-12">   
 <h3>Orders List</h3> 
 <h5 id="msgs"></h5>               
<table id="employee-grid" class="table table-striped table-bordered table-hover" >
   <thead>
    <tr>
      <th>Order </th>
      <th>Name </th>
      <th>Zip Code </th>
      <th>Total </th>
      <th>Tags</th>
      <th>Fulfillment</th>
      <th>Fraud</th>
      <th>Paid</th>
      <th>Card</th>
      <th>Tracking Number</th>
    </tr>
  </thead>
  <tbody>
  
    </tbody>
  </table> 
  </div>         
                 
 </article>              
<div>

<input type="hidden" name="">


</div>

<script type="text/javascript">

setInterval( LoadOrder, 30000);

function LoadOrder()
{
  syncShopiFy_order();
}

function syncShopiFy_order(){
    $.ajax(
          {
              type : 'POST',
              url : '<?php echo base_url(); ?>Home/syncShopiFyorder',
              data : {shop:shop,access_token:access_token,toalOrders:'<?php echo $orderCount; ?>'},
              dataType:"json",
            beforeSend: function(data)
            {
              //  $('.loading').fadeIn();
            },
           success: function(data)
           {  if(data['msg']!=''){
               $('#msgs').html(data['msg']);
              }
        
           },async: false
        
      });

}

function getdataTable(){


}
	
 $(document).ready(function() {

table = $('#employee-grid').DataTable({
                "lengthMenu": [[100, 500, 1000, 2000, 3000, -1], [100, 500, 1000, 2000, 3000, "All"]],
               // dom: 'lBfrtip',
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                //"order": [], //Initial no order.
                // Load data for the table's content from an Ajax source
                "ajax": {
                        "url": '<?php echo base_url(); ?>Home/getOrderlist',
                        "type": "POST"
                },
                "columnDefs": [
    { "name": "orderNumber",   "targets": 0 },
    { "name": "name",  "targets": 1 },
    { "name": "zipcode", "targets": 2 },
    { "name": "total",  "targets": 3 },
    { "name": "tags",    "targets": 4 },
    { "name": "fulfillments",   "targets": 5 },
    { "name": "fraud",  "targets": 6 },
    { "name": "paid", "targets": 7 },
    { "name": "tracking_number",  "targets": 8 },
     { "name": "order_created",  "targets": 8 }
  ]

        });

    } );
 
</script>
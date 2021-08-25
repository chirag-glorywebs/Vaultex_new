

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="viewport" content="width=device-width" /><!-- IMPORTANT -->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Register Success</title>

<style type="text/css">
   .mail-container{
      background: #f7f7f7;
      width: 100%;
      padding: 5px 0;
    }
    .mail-header .mail-logo{
      text-align: left;
      padding: 20px 10px;
    }
  .mail-body{
      padding: 10px;
      background: #ffffff;
    }
     .mail-header .mail-header-title{
      background:#5ebac9;
      color:#FFFFFF;
      text-align:center;
      padding:15px;
      letter-spacing: 2px;
    }
    .mail-header .mail-header-title h1{
      padding: 0;
      margin: 0;
    }
    .body-title{}
    .body-title h4{
      padding: 5px 0;
      color: #404040;
      font-weight: normal;
      font-size: 18px;
      line-height: 30px;
      margin: 0;
      text-align: center;
    }
    .body-title h4 span{
      color: #f16334;
    }
    .body-title p{
      padding: 10px 0 5px 0;
      margin: 0;
      font-size: 14px;
      color: #777777;
      text-align: center;
    }


  .mail-footer{
      text-align: center;
    }
    .mail-footer p{
      font-size: 11px;
      padding: 5px 0;
      margin: 5px 0 5px 0;
      line-height: 20px;
      color: #999999;
      letter-spacing: 1px;
    }

    .mail-inner-content{
      width: 700px;
      margin: 20px auto;
      /*border: 1px solid #efefef;*/
      border-radius: 0px;
    }
    .mail-header .mail-logo{
      text-align: left;
      padding: 20px 10px;
    }
     .order-summary{
      padding: 25px 0;
    }
    .order-summary .bg-down,
    .order-summary .bg-up {
      height: 7px;
    }
    .order-summary .bg-down img{
      width: 100%;
      height: 12px;
    }
    .order-summary .bg-up img{
      width: 100%;
    }
    .order-summary .order-body{
      background: #ededed;
      min-height: 15px;
      padding: 18px 20px;
    }
 .order-body table{
      width:100%;
      margin-bottom:12px
    }
    .order-body h2{
      color:#404040;
      font-weight:300;
      margin:0 0 12px 0;
      font-size:24px;
      line-height:30px;
    }
    .bottom-dashed{
      border-bottom:1px dashed #d3d3d3;
    }
      .order-body .table-th{
      border-bottom:1px dashed #d3d3d3;
      text-align:left;
      padding:5px 10px 12px 0;
      padding-right:10px;
    }
    .order-body .table-td{
      padding:12px 10px 12px 0;
    }
    .order-body .table-th .title{
      color:#666666;
      font-weight:bold;
      font-size:15px;
      line-height:21px;
      letter-spacing: 1px;
    }
    .order-body .table-td .title{
      color:#666666;
      font-weight:400;
      font-size:15px;
      line-height:21px;
    }


</style>
</head>
<body>

  <div class="mail-container">
    <div class="mail-inner-content">
      <!-- HEADER -->
      <div class="mail-header">
        <div class="mail-logo">
            <?php $settingLogo = \App\Models\Settings::where('name', 'logo')->first(); ?>
            <img src="{{ url($settingLogo->value) }}" alt="vaultex logo" height="50"/>
        </div>
        <div class="mail-header-title" >
          <h1>Vaultex</h1>
        </div>
      </div>
      <div class="mail-body">
        <div class="body-title">
          <h4>
          <strong>order successfully placed</strong>
          </h4>

          <p><strong>Hey {{$user_info[0]['first_name']}} {{$user_info[0]['last_name']}} ,</strong> </p>
        <p><strong>we've got your order! your world is about to look a whole lot better.</strong><br/></p>
             <p><strong>we'll drop you another email when your order ships.</strong></p>

          <p><strong>Order Number. :{{$order->id}}</strong></p>
          <p><strong>Order Date. {{$order->date_purchased}}</strong></p>

          <p><strong> Estimate Delivery Date. : {{$order->estimate_delivery_date}}</strong></p>
          <br/>

          <div class="order-summary">

          <div class="order-body">
            <table cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td class="bottom-dashed">
               <h4 style="text-align:left"><strong> ORDERED ITEMS</strong></h4>
                  </td>
                  <td style="text-align:right;border-bottom:1px dashed #d3d3d3">
                    <div class="date">
                      </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                  <table cellspacing="0" cellpadding="0" border="0">
                      <thead>
                        <tr>
                          <th class="table-th" style="text-align:center;">
                              <div class="title">Product Image</div>
                          </th>
                          <th class="table-th" style="text-align:center;">
                            <div class="title">Product Name</div>
                          </th>
                          <th class="table-th" style="text-align:right;">
                            <div class="title">Quantity</div>
                          </th>
                          <th class="table-th" style="text-align:right;">
                            <div class="title">Price</div>
                          </th>
                          <th class="table-th" style="text-align:right;">
                            <div class="title">SubTotal</div>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach($cart_data as $data)
                        <tr>
                            <td class="table-td" style="text-align:center;width: 30%;">
                              <div class="title"><img src="{{asset($data->main_image)}}" alt="vaultex" height="50" /></div>
                            </td>
                            <td class="table-td" style="text-align:center;width: 30%;">
                              <div class="title">{{$data->product_name}}
                              </div>
                            </td>
                              <td class="table-td" style="text-align:right;width: 20%;">
                              <div class="title">{{$data->product_quantity}}
                              </div>
                            </td>
                            <td class="table-td" style="text-align:right;width: 30%;">
                              <div class="title">
                              @if(!empty($data->sale_price))
                              {{$data->sale_price}}  {{$currancy->value}}
                              @else
                              !empty($data->regular_price)
                              {{$data->regular_price}}  {{$currancy->value}}
                              @endif
                              </div>
                            </td>
                            <td class="table-td" style="text-align:right;width: 30%;">
                              <div class="title">{{$data->sub_total}} {{$currancy->value}}
                              </div>
                            </td>
                          </tr>
                          @endforeach
                       </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>

          <br/>
        </div>
      </div>

      <div style="width:70%; float: left; height:100px;">
      <strong>Discount [just in case]</strong>  : @if(!empty($order->coupon_amount)){{$order->coupon_amount}} {{$currancy->value}}@endif<br/>
      <strong>Shipping cost</strong> :    {{$order->shipping_cost}} {{$currancy->value}}<br/>
       <strong>  VAT </strong>    :   {{$order->total_tax}}   {{$currancy->value}}<br/>
      <strong>Payable amount </strong> : {{$order->order_price}}  {{$currancy->value}}
      </div>
      <div style="width:30%; float:right; height:100px;">
      <strong >Shipping Address</strong><br/>
      {{$user_info[0]['address']}}
  </div>
      <pre>



      </pre>
      <br/>

      <div class="order-summary">

         <div class="order-body">
           <table cellspacing="0" cellpadding="0" border="0" >
             <tbody>
               <tr>
                 <td class="bottom-dashed">
              <h4 style="text-align:left"><strong>Payment Method & Delivery Details</strong></h4>
                 </td>
                 <td style="text-align:right;border-bottom:1px dashed #d3d3d3">
                   <div class="date">
                     </div>
                 </td>
               </tr>
               <tr>
                 <td colspan="2">
                 <table cellspacing="0" cellpadding="0" border="0"  >
                     <tbody>
                     <tr>
                        <div class="title" ><p style="text-align: left;"><strong> Payment Method   : Cash On Delivery</div>
                             <div class="title" ><p style="text-align: left;"><strong> Delivery Details   : Standered Delivery</div>
                           </td>
                         </tr>
                      </tbody>

                 </td>
               </tr>
             </tbody>
           </table>
    <br/>

      </div>
      <p style="text-align: center;"><b>Thank You</b></p>
        <!-- FOOTER -->
      <div class="mail-footer">
        <p><b>if you need help with anything please don't hesitate to drop us an email : vaultex@vaultex.com :)
             <br/>
          Copyright © <?php echo date("Y"); ?> SBM. All right reserved.  </b></p>
    </div>
  </div>
</body>
</html>



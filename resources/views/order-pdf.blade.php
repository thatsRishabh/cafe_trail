<?php

$data= App\Models\Order::select('*')->with('orderContains')->where('id', $order_id)->first();

$date = date_format($data['created_at'], 'Y-m-d');
?>

<style type="text/css">
    @font-face {
  font-family: SourceSansPro;
  src: url(SourceSansPro-Regular.ttf);
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #0087C3;
  text-decoration: none;
}

body {
  position: relative;
  width: 21cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #555555;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 14px; 
  font-family: SourceSansPro;
}

header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #AAAAAA;
}

#logo {
  float: left;
  margin-top: 8px;
}

#logo img {
  height: 70px;
}

#company {
  float: center;
  text-align: center;
  margin-left: 40px;
}


#details {
  margin-bottom: 50px;
}

#client {
  padding-left: 6px;
  float: center;
  text-align: center;
  margin-left: 40px;
}

#client .to {
  color: #777777;
}

h2.name {
  font-size: 1.4em;
  font-weight: normal;
  margin: 0;
}

#invoice {
  float: right;
  text-align: right;
  margin-right: 59px;
}

#invoice h1 {
  color: #0087C3;
  font-size: 2.4em;
  line-height: 1em;
  font-weight: normal;
  margin: 0  0 10px 0;
}

#invoice .date {
  font-size: 1.1em;
  color: #777777;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

#table1 {
  margin-right: 59px;
}

table th,
table td {
  padding: 20px;
  background: #EEEEEE;
  text-align: center;
  border-bottom: 1px solid #FFFFFF;
}

table th {
  white-space: nowrap;        
  font-weight: normal;
}

table td {
  text-align: right;
}

table td h3{
  color: #57B223;
  font-size: 1.2em;
  font-weight: normal;
  margin: 0 0 0.2em 0;
}

table .no {
  color: #FFFFFF;
  font-size: 1.6em;
  background: #57B223;
}

table .desc {
  text-align: left;
}

table .unit {
  background: #DDDDDD;
}


table .total {
  background: #57B223;
  color: #FFFFFF;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table tbody tr:last-child td {
  border: none;
}

table tfoot td {
  padding: 10px 20px;
  background: #FFFFFF;
  border-bottom: none;
  font-size: 1.2em;
  float: center;
  text-align: center;
  margin-left: 40px;
  white-space: nowrap; 
  border-top: 1px solid #AAAAAA; 
}

table tfoot tr:first-child td {
  border-top: none; 
}

table tfoot tr:last-child td {
  color: #57B223;
  font-size: 1.4em;
  border-top: 1px solid #57B223; 

}

table tfoot tr td:first-child {
  border: none;
}

#thanks{
  font-size: 2em;
  margin-bottom: 50px;
}

#notices{
  padding-left: 6px;
  border-left: 6px solid #0087C3;  
}

#notices .notice {
  font-size: 1.2em;
}

footer {
  color: #777777;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #AAAAAA;
  padding: 8px 0;
  text-align: center;
}

</style>


<
  <body>
    <header class="clearfix">
      <!-- <div id="logo">
        <img src="https://drfranchises.com/wp-content/uploads/2021/07/chai-ho-jaye-franchise.jpg">
      </div> -->
      <div id="company">
        <h2 class="name">Chai Ho Jaye</h2>
        <div>Near D-Mart, Beside Prakesh Dhaba</div>
        <div>Hoshangabad Road, Bhopal</div>
        <div>	+91 9171 333 851</div>
        <div>	chaihojaye.bpl@gmail.com</div>
       
      </div>
      </div>
    </header>
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">INVOICE TO: Table No. {{($data['table_number'])}} </div>

          <div class="address">Invoice no. {{($data['id'])}} </div>
          <div class="address">Total Quantity: {{($data['cartTotalQuantity'])}}</div>
          <div class="address">Date of Invoice: {{($date)}} </div>
        </div>
      </div>
      <div id="table1">
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">PRODUCT</th>
            <th class="unit">UNIT PRICE</th>
            <th class="qty">QUANTITY</th>
            <th class="total">TOTAL</th>
          </tr>
        </thead>
        <tbody>
        @foreach(@$data->orderContains as $key => $info)
          <tr>
            <td class="no">{{ $key+1 }}</td>
            <td class="desc"><h3>{{$info->name}}</h3></td>
            <td class="unit">Rs. {{$info->price}}</td>
            <td class="qty">{{$info->quantity}}</td>
            <td class="total">Rs. {{$info->netPrice}}</td>
          </tr>
          @endforeach
        
        </tbody>

       
        <tfoot>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">SUBTOTAL</td>
            <td>Rs. {{($data['cartTotalAmount'])}}</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">TAX </td>
            <td>Rs. {{($data['taxes'])}}</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">GRAND TOTAL</td>
            <td>Rs. {{($data['netAmount'])}}</td>
          </tr>
        </tfoot>

        
        
        

      </table>
      </div>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">Under any circumstance no refund will be made.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Monthwise Report</title>
<style>
* {
	margin: 0;
	padding: 0;
}
table {
	border-collapse: collapse;
}
td, th {
	padding: 10px 15px;
	border: #ddd 1px solid;
	text-align: center;
}
.p0 {
	padding: 0;
}
.noBdr {
	border: 0;
}
.text-left {
	text-align: left;
}
</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; color:#333; font-size:14px;">
  <tr>
    <td colspan="13" class="noBdr text-left">Daily Receipts and Sales</td>
  </tr>
  <tr>
    <td colspan="13" class="noBdr text-left">Name of Licence : <strong> {{$shop_name}}</strong></td>
  </tr>
  <tr>
    <td colspan="13" class="noBdr text-left">For The Month of :<strong> {{@$month}}</strong></td>
  </tr>
  <tr>
    <th rowspan="2" valign="top">Date</th>
    @forelse ($items[0]['categorie_data'] as $category)
    <th colspan="4">{{$category['category_name']}}</th>
    @empty
    @endforelse
    
  </tr>
  <tr>
  @forelse ($items[0]['categorie_data'] as $category)
    <td>Opening</td>
    <td>Purchase</td>
    <td>Sale</td>
    <td>Closing</td>
    @empty
    @endforelse
  </tr>
  @forelse ($items as $item)
  <tr>
    <td>{{$item['sell_date']}}</td>
    @foreach($item['categorie_data'] as $category)
    <td>{{$category['opening_stock']}}</td>
    <td>{{$category['purchase_stock']}}</td>
    <td>{{$category['total_sale']}}</td>
    <td>{{$category['closing_stock']}}</td>
   @endforeach
  </tr>
  @empty
  @endforelse
</table>
</body>
</html>
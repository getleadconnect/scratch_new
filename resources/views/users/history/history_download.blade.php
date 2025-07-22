<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=gl_scratch_web_history.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h3>SCRATCH HISTORY REPORT</h3>
From Date :{{ $fromdate }}<br>
To Date   :{{ $todate }}

<table border='1' width="100%">
  <tr>
    <td><b>Sl.No<b></td>
    <td><b>Date & Time<b></td>
    <td><b>Name</b></th>
    <td><b>Link<b></td>
    <td><b>Country Code<b></td>
	<td><b>Mobile No<b></td>
	<td><b>Email<b></td>
    <td><b>Offer<b></td>
    <td><b>Redeem Status<b></td>
  </tr>
    <?php
      foreach ($customers as $key => $value)
    {
    ?>
    <!-- ('name','mobile','short_link','offer_text','created_at','redeem' -->
  <tr>
    <td>{{ ++$key}}</td>
    <td>{{ $value->created_at }}</td>
    <td>{{ $value->name }}</td>
    <td>{{ $value->short_link}}</td>
	<td>{{ $value->country_code }}</td>
    <td>{{ $value->mobile }}</td>
	<td>{{ $value->email }}</td>
    <td>{{ $value->offer_text }}</td>
    @if($value->redeem==1)
      <td>Redeemed</td>
    @else
      <td>Pending</td>
    @endif
    
  </tr>
  <?php 
  } 

?>
</table>


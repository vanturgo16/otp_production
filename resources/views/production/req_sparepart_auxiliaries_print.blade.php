<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <title>REQUEST SPAREPART & AUXILIARIES</title>
  <style type="text/css">
    th {
      background-color: #ecf0f1;
    }
  </style>
</head>

<body cz-shortcut-listen="true">
  <div class="container-fluid mt-3">
    <div class="row">
      <div class="col-md-12 text-center">
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
          <tbody>
            <tr>
              <td rowspan="4" style="width: 20%;" class="text-center">
                <img src="http://eks.olefinatifaplas.my.id/img/otp-icon.jpg" width="60" height="60">
                <br>
                <strong><small>PT OLEFINA TIFAPLAS POLIKEMINDO</small></strong>
              </td>
            </tr>
            <tr>
              <td>FORM</td>
            </tr>
            <tr>
              <td style="background-color: #ecf0f1;">LAPORAN REQUEST SPAREPART & AUXILIARIES</td>
            </tr>
            <tr>
              <td><small>FM-SM-PO EXT 02, REV 03, 22 Januari 2018</small></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-6" style="font-size: 13px;">
        <table class="">
          <tbody>
            <tr>
              <td>Request Number</td>
              <td>: {{ $data[0]->created_at; }}</td>
            </tr>
            <tr>
              <td>Departement</td>
              <td>: {{ $data[0]->shift; }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-6" style="font-size: 13px;">
        <table>
          <tbody>
            <tr>
              <td>STATUS</td>
              <td>: {{ $data[0]->work_center; }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <table class="table table-bordered mt-3" style="font-size: 12px;">
		<thead>
			<tr>
			<tr>
				<th width="20%">Request Sparepart & Auxiliaries</th>
				<th width="20%">QTY</th>
				<th width="20%">Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($data_detail[0])){ ?>
			@foreach ($data_detail as $data)
			<tr>
				<td>{{ $data->description }}</td>
				<td>{{ $data->qty }}</td> 
				<td>{{ $data->remarks }}</td>
			</tr>
			@endforeach
		<?php }else{ ?>
			<tr>
				<td class="pt-3 pb-3" colspan="14" align="center">Belum Ada Data Detail Yang Ditambahkan</td>
			</tr>
		<?php }; ?>
		</tbody>
    </table>

  </div>



  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>
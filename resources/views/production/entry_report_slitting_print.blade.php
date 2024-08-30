<!DOCTYPE html>
<html lang="en"><head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>REPORT SLITTING</title>
    <style type="text/css">
      th{
        background-color: #ecf0f1;
      }
      @media  print{@page  {size: landscape}}
    </style>
  </head>
  <body cz-shortcut-listen="true">
    <div class="container-fluid mt-3">
      <div class="row">
        <div class="col-md-12 text-center">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tbody><tr>
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
              <td style="background-color: #ecf0f1;">LAPORAN PRODUKSI  SLITTING</td>
            </tr>
            <tr>
              <td><small>FM-SM-PO EXT 02, REV 03, 22 Januari 2018</small></td>
            </tr>
          </tbody></table>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-3" style="font-size: 13px;">
          <table class="">
            <tbody><tr>
              <td>Hari/Tanggal</td>
              <td>: {{ $data[0]->date }}</td>
            </tr>
            <tr>
              <td>Nama Order</td>
              <td>: </td>
            </tr>
            <tr>
              <td>Customer</td>
              <td style="vertical-align: text-top;">: {{ $data[0]->name }}</td>
            </tr>
          </tbody></table>
        </div>
        <div class="col-md-3" style="font-size: 13px;">
          <table>
            <tbody><tr>
              <td>Regu/Shift</td>
              <td>: {{ $data[0]->shift }}</td>
            </tr>
            <tr>
              <td>Nomer Mesin</td>
              <td>: {{ $data[0]->work_center_code }}</td>
            </tr>
            <tr>
              <td>Operator</td>
              <td>: </td>
            </tr>
          </tbody></table>
        </div>
        <div class="col-md-6" style="font-size: 13px;">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tbody><tr>
	        	<td><p style="margin-left: 10px;">Keterangan Mesin Mati:</p></td>
	        	<td><p style="margin-left: 10px;margin-right: 40px;">Catatan:</p></td>
              	<th class="text-center">Dibuat</th>
              	<th class="text-center">Diketahui</th>
            </tr>
            <tr>
            	<td></td>
            	<td></td>
            	<td class="text-center"><p style="padding: 10px;"></p></td>
            	<td class="text-center"><p style="padding: 10px;">{{ $data[0]->nama_know_by }}</p></td>
            </tr>
            <tr>
            	<td></td>
            	<td></td>
              	<th class="text-center">Operator</th>
              	<th class="text-center">Pengawas</th>
            </tr>
          </tbody></table>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-4" style="font-size: 12px;">
          <strong>Pemeriksaan Persiapan Awal</strong>
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 95%">
            <tbody><tr>
              <th rowspan="2" class="text-center">No.</th>
              <th rowspan="2" class="text-center">Item Pemeriksaan</th>
              <th rowspan="2" class="text-center">Standar</th>
              <th colspan="2" class="text-center">Hasil</th>
            </tr>
            <tr>
              <th class="text-center">Ok</th>
              <th class="text-center">Not Ok</th>
            </tr>
            <tr>
              <td class="text-center">1</td>
              <td class="pl-2">Material</td>
              <td class="pl-2">Sesuai WO</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->material=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->material=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td class="pl-2">Ukuran</td>
              <td class="pl-2">Sesuai WO</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->ukuran=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->ukuran=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">3</td>
              <td class="pl-2">Press roll</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->press_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->press_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Rubber roll</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->rubber_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->rubber_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
          </tbody></table>
        </div>
        <div class="col-md-4" style="font-size: 12px;">
          <strong>Pemeriksaan Kebersihan</strong>

          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tbody><tr>
              <th rowspan="2" class="text-center">No.</th>
              <th rowspan="2" class="text-center">Item Pemeriksaan</th>
              <th colspan="2" class="text-center">Hasil</th>
            </tr>
            <tr>
              <th class="text-center">OK</th>
              <th class="text-center">Not Ok</th>
            </tr>
            <tr>
              <td class="text-center">1</td>
              <td class="pl-2">Press &amp; rubber roll</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->press_rubber_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->press_rubber_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td class="pl-2">Body mesin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Not Ok"?'✓':''; }} </td>
              
            </tr>
            <tr>
              <td class="text-center">3</td>
              <td class="pl-2">Lantai mesin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Cutter</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->cutter=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->cutter=="Not Ok"?'✓':''; }} </td>
            </tr>
          </tbody></table>
        </div>
      </div>
	  
      <div class="row mt-3">
        <div class="col-md-12" style="font-size: 13px;">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tbody><tr>
              <th colspan="2" class="text-center">Jam Kerja</th>
              <th colspan="3" class="text-center">Bahan Awal</th>
              <th colspan="7" class="text-center">Hasil Produksi</th>
              <th colspan="7" class="text-center">Waste Produksi</th>
            </tr>
            <tr>
              <th class="text-center">Mul</th>
              <th class="text-center">Sel</th>
              <th class="text-center">Ukuran</th>
              <th class="text-center">Kg</th>
              <th class="text-center">Barcode</th>
              <th class="text-center">Ukuran</th>
              <th class="text-center">Kg</th>
              <th class="text-center">Good</th>
              <th class="text-center">Hold</th>
              <th class="text-center">Reject</th>
              <th class="text-center">Barcode</th>
              <th class="text-center">No. WO</th>
              <th class="text-center">Kg</th>
              <th class="text-center">Penyebab Waste</th>
            </tr>
			
			<?php if(!empty($data_detail_production[0])){ ?>
				@foreach ($data_detail_production as $data_detail)
				<?php $order_name = explode('|', $data_detail->order_name_blow) ?>
				<?php $note = explode('|', $data_detail->note) ?>
				<tr>
				</tr>
				<tr class="text-center">
					<td class="pl-2">{{ $data_detail->start_time }}</td>
					<td class="pl-2">{{ $data_detail->finish_time }}</td>
					<td class="pl-2">{{ $order_name[3] }}</td>
					<td class="pl-2">{{ $data_detail->weight_blow }}</td>
					<td class="pl-2">{{ $data_detail->barcode_start }}</td>
					<td class="pl-2">{{ $note[3] }}</td>
					<td class="pl-2">{{ $data_detail->weight }}</td>
					
					<td class="text-center"> {{ $data_detail->status=="Good"?'✓':''; }} </td>
					<td class="text-center"> {{ $data_detail->status=="Hold"?'✓':''; }} </td>
					<td class="text-center"> {{ $data_detail->status=="Reject"?'✓':''; }} </td>
				  
					<td class="pl-2">{{ $data_detail->barcode }}</td>
					<td class="pl-2">{{ $data_detail->wo_number }}</td>
					<td class="pl-2">{{ $data_detail->waste }}</td>
					<td class="pl-2">{{ $data_detail->cause_waste }}</td>
				</tr>

				@endforeach
			<?php }else{ ?>
				<tr>
					<td class="pt-3 pb-3" colspan="14" align="center">Belum Ada Data Detail Yang Ditambahkan</td>
				</tr>
			<?php }; ?>
			

            <tr>
              <th colspan="2" rowspan="3" class="text-center">Jumlah</th>
              <th rowspan="3"></th>
              <td rowspan="3" class="text-center"><strong>Kg</strong></td>
              <th rowspan="2">Pemakaian Bahan Awal</th>
              <th colspan="2" rowspan="2">Sisa bahan untuk regu<br>Selanjutnya</th>
              <td></td>
              <td></td>
              <td></td>
              <th rowspan="3">Roll<br>Mtr<br>Kg</th>
              <th rowspan="3"></th>
              <td rowspan="3" class="text-center"><strong>Kg</strong></td>
              <th rowspan="3"></th>
            </tr>
            
            <tr>
            	<td></td>
            	<td></td>
            </tr>
            <tr>
              <td class="text-right"><strong>Mtr</strong></td>
            	<td colspan="2" class="text-right"><strong>Mtr</strong></td>
            	<td></td>
            	<td></td>
            	<td></td>
            </tr>
          </tbody></table>
        </div>
      </div> 
    </div>
    
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  
</body></html>

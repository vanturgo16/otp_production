<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>REPORT BAG MAKING</title>
    <style type="text/css">
      th{
        background-color: #ecf0f1;
      }
      @media  print{@page  {size: landscape}}
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row mt-3">
        <div class="col-md-12 text-center">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tr>
              <td rowspan="4" style="width: 20%;" class="text-center">
                <img src="http://eks.olefinatifaplas.my.id/img/otp-icon.jpg" width="60" height="60">
                <br/>
                <strong><small>PT OLEFINA TIFAPLAS POLIKEMINDO</small></strong>
              </td>
            </tr>
            <tr>
              <td>FORM</td>
            </tr>
            <tr>
              <td style="background-color: #ecf0f1;">LAPORAN PRODUKSI BAG MAKING</td>
            </tr>
            <tr>
              <td><small>FM-SM-PO EXT 02, REV 03, 22 Januari 2018</small></td>
            </tr>
          </table>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-3" style="font-size: 13px;">
          <table class="">
            <tr>
              <td>Hari/Tanggal</td>
              <td>: {{ $data[0]->date }}</td>
            </tr>
            <tr>
              <td>Customer</td>
              <td>: {{  $data[0]->name }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-3" style="font-size: 13px;">
          <table>
          	<tr>
              <td>Ketua Regu</td>
              <td>
                : {{ $data[0]->ketua_regu }}
              </td>
            </tr>
            <tr>
              <td>Operator</td>
              <td>: {{ $data[0]->operator }}</td>
            </tr>
            <tr>
              <td>Regu/Shift</td>
              <td>: {{ $data[0]->shift }}</td>
            </tr>
            <tr>
              <td>No. Mesin</td>
              <td>: {{ $data[0]->work_center_code }}</td>
            </tr>
            
          </table>
        </div>
        <div class="col-md-6" style="font-size: 13px;">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tr>
	        	<td><p style="margin-left: 10px;" >Keterangan Mesin Mati:</p></td>
	        	<td><p style="margin-left: 10px;margin-right: 40px;" >Catatan:</p></td>
              	<th class="text-center">Dibuat</th>
              	<th class="text-center">Diketahui</th>
            </tr>
            <tr>
            	<td class="text-center">{{ $data[0]->engine_shutdown_description }}</td>
            	<td class="text-center">{{ $data[0]->note }}</td>
            	<td class="text-center"><p style="padding: 15px;">&nbsp;</p></td>
            	<td class="text-center"><p style="padding: 15px;">&nbsp;</p></td>
            </tr>
            <tr>
            	<td></td>
            	<td></td>
              	<th class="text-center" style="padding: 5px;">{{ $data[0]->operator }}<br><small> ( Operator )</small></th>
                <th class="text-center" style="padding: 5px;">{{ $data[0]->pengawas }}<br><small> ( Pengawas )</small></th>
            </tr>
          </table>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-4" style="font-size: 12px;">
          <strong>Pemeriksaan Persiapan Awal</strong>
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 95%">
            <tr>
              <th rowspan="2" class="text-center">No.</th>
              <th rowspan="2" class="text-center">Item Pemeriksaan<br/>(Std. Sesuai Standar)</th>
              <th rowspan="2" class="text-center">Standar</th>
              <th colspan="2" class="text-center">Verifikasi Oleh</th>
            </tr>
            <tr>
              <th class="text-center">Ok</th>
              <th class="text-center">Not<br/>Ok</th>
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
              <td class="pl-2">Pisau Seal</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->pisau_seal=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->pisau_seal=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Jarum Perforasi</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->jarum_perforasi=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->jarum_perforasi=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">5</td>
              <td class="pl-2">Pembuka Plastik</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->pembuka_plastik=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->pembuka_plastik=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">6</td>
              <td class="pl-2">Guide/rubber roll</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->guide_rubber_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->guide_rubber_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
          </table>
        </div>
        <div class="col-md-4" style="font-size: 12px;">
          <strong>Pemeriksaan Kebersihan</strong>

          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tr>
              <th rowspan="2" class="text-center">No.</th>
              <th rowspan="2" class="text-center">Item Pemeriksaan<br/>(Std. Bersih)</th>
              <th rowspan="2" class="text-center">Standar</th>
              <th colspan="2" class="text-center">Verifikasi Oleh</th>
            </tr>
            <tr>
              <th class="text-center">OK</th>
              <th class="text-center">Not<br/>Ok</th>
            </tr>
            <tr>
              <td class="text-center">1</td>
              <td class="pl-2">Roll guide & roll karet</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->roll_guide_roll_karet=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->roll_guide_roll_karet=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td class="pl-2">Jarum Perforasi</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->jarum_perforasi=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->jarum_perforasi=="Not Ok"?'✓':''; }} </td>
              
            </tr>
            <tr>
              <td class="text-center">3</td>
              <td class="pl-2">Pisau seal</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->pisau_seal=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->pisau_seal=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Pembuka plastik</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->pembuka_plastik=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->pembuka_plastik=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">5</td>
              <td class="pl-2">Lantai mesin</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">5</td>
              <td class="pl-2">Body mesin</td>
              <td class="pl-2">Bersih</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Not Ok"?'✓':''; }} </td>
            </tr>
          </table>
        </div>
        <!--div class="col-md-4" style="font-size: 12px;">
          <strong>Waste Produksi</strong>

          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            
            <tr>
              <th class="text-center">Kg</th>
              <th class="text-center">Penyebab Waste</th>
            </tr>
			<?php if(!empty($data_detail_waste[0])){ ?>
				@foreach ($data_detail_waste as $data_waste)
				<tr>
				  <td class="text-center">{{ $data_waste->waste }}</td>
				  <td class="pl-2">{{ $data_waste->cause_waste }}</td>
				</tr>
				@endforeach
			<?php }else{ ?>
				<tr>
					<td class="pt-1 pb-1" colspan="2" align="center">Belum Ada Data Waste Yang Ditambahkan</td>
				</tr>
			<?php }; ?>
            
          </table>
        </div-->
      </div>

      <div class="row mt-3">
        <div class="col-md-12" style="font-size: 13px;">
          <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
            <tr>
				<th colspan="2" class="text-center">Jam Kerja</th>
				<th colspan="5" class="text-center">Bahan Awal</th>
				<th colspan="7" class="text-center">Hasil Produksi</th>
            </tr>
            <tr>
				<th class="text-center">Mul</th>
				<th class="text-center">Sel</th>
				<th class="text-center">Roll</th>
				<th class="text-center">Ukuran</th>
				<th class="text-center">Kg</th>
				<th class="text-center">Barcode Start</th>
				<th class="text-center">Used Next Shift</th>
				<th class="text-center">No. Work Order</th>
				<th class="text-center">Barcode End</th>
				<th class="text-center">Ukuran</th>
				<th class="text-center">Amount Result<br/>(Pcs)</th>
				<th class="text-center">Wrap<br/>(Bungkus)</th>
				<th class="text-center">Waste<br/>(Kg)</th>
				<th class="text-center">Cause<br/>Waste</th>
            </tr>
			<!--tr>
				<td class="text-center pt-2 pb-2" colspan="12">Sedang Di Desain Ulang</td>
			</tr-->
			<?php if(!empty($data_detail_production[0])){ ?>
				<?php  $sum_amount_result = 0; $sum_wrap_pcs = 0; $sum_wrap = 0; $sum_waste = 0; ?>
				@foreach ($data_detail_production as $data_detail)
				<?php $order_names = explode('|', $data_detail->order_name_sf) ?>
				<?php $note = explode('|', $data_detail->note) ?>
				<tr>
					<td class="pl-2">{{ $data_detail->start_time }}</td>
					<td class="pl-2">{{ $data_detail->finish_time }}</td>
					<td class="pl-2">1</td>
					<td class="pl-2">
						<?php 
							if(!empty($order_names[0])){
								echo $order_names[3];
							}else{
								echo '-';
							}
						?>
					</td>
					<td class="pl-2">{{ $data_detail->weight_sf }}</td>
					<td class="pl-2">{{ $data_detail->barcode_start }}</td>
					<td class="pl-2">{{ $data_detail->used_next_shift=='1'?'Yes':'No' }}</td>
					<td class="text-center"><b>{{ $data_detail->wo_number }}</b></td>
					<td class="text-center">{{ $data_detail->barcode }}</td>
					<td class="pl-2">{{ $note[3] }}</td>
					<td class="pl-2">{{ $data_detail->amount_result }}</td><?php $sum_amount_result += $data_detail->amount_result; ?>
					<td class="pl-2">{{ $data_detail->wrap }}</td><?php $sum_wrap += $data_detail->wrap; ?>
					<td class="pl-2">{{ $data_detail->waste>0?$data_detail->waste:0; }}</td>
					<td class="pl-2">{{ $data_detail->cause_waste=""?'-':$data_detail->cause_waste; }}</td>
					<?php 
						if(!empty($data_detail->waste)){
							$sum_waste += $data_detail->waste; 
						}else{
							$sum_waste = 0;
						}
					?>
				</tr>
				@endforeach
			<?php }else{ ?>
				<tr>
					<td class="pt-3 pb-3" colspan="14" align="center">Belum Ada Data Detail Yang Ditambahkan</td>
				</tr>
			<?php }; ?>
			<tr>
				<th colspan="2" rowspan="3"></th>
				<th colspan="5" rowspan="3"></th>
				<th colspan="1" rowspan="3" class="text-center">Jumlah</th>
				<th colspan="2" class="text-right pr-1">Pcs </th>
				<th class="text-left pl-2"> <b>{{ $sum_amount_result }}<b> </th>
				<th class="text-left pl-2"> <b> - <b> </th>
				<th class="text-left pl-2"> <b> - <b> </th>
				<th colspan="1" rowspan="3" class="text-center"></th>
            </tr>
            <tr>
            	<th colspan="2" class="text-right pr-1">Bungkus </th>
            	<th class="text-left pl-2"> <b> - <b> </th>
				<th class="text-left pl-2"> <b>{{ $sum_wrap }}<b> </th>
            	<th class="text-left pl-2"> <b>-<b> </th>
            </tr>
            <tr>
            	<th colspan="2" class="text-right pr-1">Kg </th>
            	<th class="text-left pl-2"> <b> - <b> </th>
            	<th class="text-left pl-2"> <b>-<b> </th>
				<th class="text-left pl-2"> <b>{{ $sum_waste }}<b> </th>
            </tr>
          </table>
        </div>
      </div>
      
    </div>
    
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
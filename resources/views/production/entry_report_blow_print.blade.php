<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.75, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>PRINT ENTRY REPORT MATERIAL USE</title>
  <!-- Bootstrap Css -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style type="text/css">
    th {
      background-color: #ecf0f1;
    }

    @media print {
      @page {
        size: landscape
      }
    }
  </style>
</head>

<body>
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
              <td style="background-color: #ecf0f1;">LAPORAN PRODUKSI BLOW POF</td>
            </tr>
            <tr>
              <td><small>FM-SM-PO EXT 02, REV 03, 22 Januari 2018</small></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-5" style="font-size: 13px;">
        <table class="">
          <tbody>
            <tr>
              <td>No. WO</td>
              <td>: {{ $data[0]->wo_number }}</td>
            </tr>
            <tr>
              <td>Nama Order</td>
              <td>: {{ explode('|', $data[0]->order_name)[2] }}</td>
            </tr>
            <tr>
              <td>Pemesan</td>
              <td>: {{ $data[0]->name }}</td>
            </tr>
            <tr>
              <td>Jenis</td>
              <td>: {{ $data[0]->type }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-5" style="font-size: 13px;">
        <table>
          <tbody>
            <tr>
              <td>Hari/Tanggal</td>
              <td>: 2023-12-27</td>
            </tr>
            <tr>
              <td>Shift</td>
              <td>: {{ $data[0]->shift }}</td>
            </tr>
            <tr>
              <td>Nomer Mesin</td>
              <td>: {{ $data[0]->work_center_code }}</td>
            </tr>
            <tr>
              <td>Operator</td>
              <td>
                :
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-2" style="font-size: 13px;">
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
          <tbody>
            <tr>
              <th class="text-center">Dibuat</th>
              <th class="text-center">Diketahui</th>
            </tr>
            <tr>
              <td>
                <p style="padding: 10px;">&nbsp;</p>
              </td>
              <td></td>
            </tr>
            <tr>
              <th class="text-center">Operator</th>
              <th class="text-center">Pengawas</th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-4" style="font-size: 12px;">
        <strong>Pemeriksaan Persiapan Awal</strong>
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 95%">
          <tbody>
            <tr>
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
              <td class="pl-2">Ratio Camp Resin</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->ratio_camp_resin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->ratio_camp_resin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Temp Heater</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->temp_heater=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->temp_heater=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">5</td>
              <td class="pl-2">Guide roll</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->guide_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->guide_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">6</td>
              <td class="pl-2">Rubber roll</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->rubber_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->rubber_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">7</td>
              <td class="pl-2">Saringan Resin</td>
              <td class="pl-2">Sesuai Standar</td>
              <td class="text-center"> {{ $data_detail_preparation[0]->saringan_resin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_preparation[0]->saringan_resin=="Not Ok"?'✓':''; }} </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-4" style="font-size: 12px;">
        <strong>Pemeriksaan Kebersihan</strong>

        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
          <tbody>
            <tr>
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
              <td class="pl-2">Guide &amp; rubber roll</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->guide_rubber_roll=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->guide_rubber_roll=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">2</td>
              <td class="pl-2">Bak resin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->bak_resin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->bak_resin=="Not Ok"?'✓':''; }} </td>

            </tr>
            <tr>
              <td class="text-center">3</td>
              <td class="pl-2">Mixer resin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->mixer_resin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->mixer_resin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">4</td>
              <td class="pl-2">Ember resin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->ember_resin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->ember_resin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">5</td>
              <td class="pl-2">Body mesin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->body_mesin=="Not Ok"?'✓':''; }} </td>
            </tr>
            <tr>
              <td class="text-center">6</td>
              <td class="pl-2">Lantai mesin</td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Ok"?'✓':''; }} </td>
              <td class="text-center"> {{ $data_detail_hygiene[0]->lantai_mesin=="Not Ok"?'✓':''; }} </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-4" style="font-size: 12px;">
        <strong>&nbsp;</strong>
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
          <tbody>
            <tr>
              <th colspan="2" class="text-center">Waste Produksi</th>
            </tr>
            <tr>
              <th class="text-center">Kg</th>
              <th class="text-center">Penyebab Waste</th>
            </tr>
			@foreach ($data_detail_waste as $data_detail)
            <tr>
              <td class="pl-2 text-center"> - </td>
              <td class="pl-2"> {{ $data_detail->cause_waste }} </td>
            </tr>
			@endforeach
            <!-- <tr>
              <th>Kg</th>
              <th>Kg</th>
            </tr> -->
          </tbody>
        </table>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12" style="font-size: 13px;">
        <table border="1" cellpadding="0" cellspacing="0" style="table-layout: auto;width: 100%">
          <tbody>
            <tr>
              <th colspan="2" class="text-center">Jam Kerja</th>
              <td>&nbsp;</td>
              <th colspan="8" class="text-center">Hasil Produksi</th>
            </tr>
            <tr>
              <th class="text-center">Mul</th>
              <th class="text-center">Sel</th>
              <td></td>
              <th class="text-center">Roll</th>
              <th class="text-center">µ</th>
              <th class="text-center">Panjang (Mtr)</th>
              <th class="text-center">Lebar (Cm)</th>
              <th class="text-center">Berat (Kg)</th>
              <th class="text-center">Barcode</th>
              <!--th class="text-center">Berat Standar (Kg)</th-->
              <th class="text-center">Keterangan</th>
            </tr>
            <tr>
            </tr>
			@foreach ($data_detail_production as $data_detail)
            <tr>
			  <td class="pl-2">{{ $data_detail->start_time }}</td>
              <td class="pl-2">{{ $data_detail->finish_time }}</td>
              <td></td>
              <td class="pl-2"></td>
              <td class="pl-2">
				Ukuran Standar : <b>{{ $data_product[0]->thickness }}</b><br>
				Hasil Produksi : <b>{{ $data_detail->thickness }}</b>
			  </td>
              <td class="pl-2">
				Ukuran Standar : <b>{{ $data_product[0]->length }}</b><br>
				Hasil Produksi : <b>{{ $data_detail->length }}</b>
			  </td>
              <td class="pl-2">
				Ukuran Standar : <b>{{ $data_product[0]->width }}</b><br>
				Hasil Produksi : <b>{{ $data_detail->width }}</b>
			  </td>
              <td class="pl-2">
				Ukuran Standar : <b>{{ $data_product[0]->weight }}</b><br>
				Hasil Produksi : <b>{{ $data_detail->weight }}</b>
			  </td>
              <td class="pl-2">{{ $data_detail->barcode }}</td>
              <!--td class="pl-2 text-danger">301.76</td-->
              <td class="pl-2"></td>
			  
            </tr>
			@endforeach
			
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>
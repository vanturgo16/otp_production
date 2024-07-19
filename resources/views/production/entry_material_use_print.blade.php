<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <title>REPORT MATERIAL USE</title>
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
              <td style="background-color: #ecf0f1;">LAPORAN PEMAKAIAN BAHAN (EXTRUDER)</td>
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
              <td>Tanggal</td>
              <td>: {{ $data[0]->created_at; }}</td>
            </tr>
            <tr>
              <td>Shift</td>
              <td>: {{ $data[0]->shift; }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-6" style="font-size: 13px;">
        <table>
          <tbody>
            <tr>
              <td>No. Mesin</td>
              <td>: {{ $data[0]->work_center; }}</td>
            </tr>
            <tr>
              <td>No. WO</td>
              <td>: {{ $data[0]->wo_number; }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <table class="table table-bordered" style="font-size: 12px;">
      <tbody>
        <tr>
          <th>No</th>
          <th>Nama Material</th>
          <th>Ratio</th>
          <th>Sisa Camp (Kg)</th>
          <th>Pengambilan (Kg)</th>
          <th>Pakai (Kg)</th>
          <th>Sisa (Kg)</th>
          <th>Barcode</th>
        </tr>


        @forelse ($data_detail as $data)

        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $data->rm_name }}</td>
          <td>-</td>
          <td>
            <pre>{{ $data->sisa_camp==0?0:$data->sisa_camp }}</pre>
          </td>
          <td>{{ $data->taking==0?0:$data->taking }}</td>
          <td>{{ $data->usage==0?0:$data->usage }}</td>
          <td>{{ $data->remaining==0?0:$data->remaining }}</td>
          <td>{{ $data->lot_number }} ( EXT : {{ $data->ext_lot_number<>""?$data->ext_lot_number:'Tidak Tersedia'; }} )</td>

        </tr>
        @empty
        <tr>
          <td colspan="8" align="center">-</td>
        </tr>

        @endforelse


      </tbody>
    </table>

    <div class="row" style="font-size: 12px;">
      <div class="col-md-4 text-center">
        <p>Operator</p>
        <p>&nbsp;</p>
        <p>(...............)</p>
      </div>
      <div class="col-md-4 text-center">
        <p>Kepala Produksi</p>
        <p>&nbsp;</p>
        <p>(...............)</p>
      </div>
      <div class="col-md-4 text-center">
        <p>Kepala Shift</p>
        <p>&nbsp;</p>
        <p>(...............)</p>
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
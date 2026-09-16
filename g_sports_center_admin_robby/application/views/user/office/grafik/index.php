<?php 
$kumpul_tgl_js = [];
$kumpul_data_js = [];


foreach ($tgl['data'] as $k => $v) {
 if ($filter=='harian') {
      array_push($kumpul_tgl_js, $tgl_filter);
     # code...
 }
 else if ($filter=='periode') {
      array_push($kumpul_tgl_js, $tgl_periode);
     # code...
 }
 elseif ($filter=='tahunan') {
      array_push($kumpul_tgl_js, bulan_global(($k+1)));
     # code...
 }else{
      array_push($kumpul_tgl_js, ($k+1).' '.bulan_global($bulan_filter));

 }
  array_push($kumpul_data_js, $v);


}


$kumpul_show_json = [];



  foreach ($id_kategori as $kik => $vik) {
        // $kumpul_pendapatan[$vik] = $v2[$vik] ; 
    $kumpul_pendapatan = [];
        foreach ($kumpul_data_js as $k2 => $v2) {
            array_push($kumpul_pendapatan, $v2[$vik]);

        }
        $kumpul_show_json[$vik] = $kumpul_pendapatan;
    }





$show_categori = json_encode($kumpul_tgl_js);
$show_data = json_encode($kumpul_show_json[1]);
 ?>
        


<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">

                       

                        <li class="nav-item">
                            <a href="#" class="nav-link"  aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"  >
                                <span>Filter</span>
                            </a>
                              <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-hover-primary dropdown-menu"><h6 tabindex="-1" class="dropdown-header">Jenis Filter</h6>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_grafik_harian" >Harian</button>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_grafik_bulanan" >Bulanan</button>
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_grafik_tahunan" >Tahunan</button>
                                                    <!-- <div tabindex="-1" class="dropdown-divider"></div> -->
                                                    <button type="button" tabindex="0" class="dropdown-item"  data-toggle="modal" data-target="#filter_grafik_periode" >Periode</button>
                                                </div>
                        </li> 
                     
                    </ul>




                            <div class="row">
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                       
                                        <div class="card-body">
                                          
    <div id="container"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
  // Data retrieved from https://www.ssb.no/statbank/table/10467/
const chart = Highcharts.chart('container', {

    chart: {
        type: '<?php echo $tipe ?>'
    },

    title: {
        text: 'Grafik Pendapatan '
    },

    subtitle: {
        text: '<?php echo $text_subtitle ?>'
    },

    legend: {
        align: 'right',
        verticalAlign: 'middle',
        layout: 'vertical'
    },

    xAxis: {
        categories: <?php echo $show_categori ?>,
        labels: {
            x: -10
        }
    },

    yAxis: {
        allowDecimals: false,
        title: {
            text: 'Total Pendapatan'
        }
    },

    series: [
    <?php foreach($kategori as $k => $v){ 
        $pendapatan = $kumpul_show_json[$v['id_akun']];
        $tampilkan = json_encode($pendapatan);
        ?>
    {
        name: '<?php echo $v['nama_akun'] ?>',
        data: <?php echo $tampilkan ?>
    },
    //  {
    //     name: 'Dina',
    //     data: [27, 21, 22]
    // }, {
    //     name: 'Malin',
    //     data: [41, 34, 32]
    // }
  <?php } ?>
    ],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal'
                },
                yAxis: {
                    labels: {
                        align: 'left',
                        x: 0,
                        y: -5
                    },
                    title: {
                        text: null
                    }
                },
                subtitle: {
                    text: null
                },
                credits: {
                    enabled: false
                }
            }
        }]
    }
});

document.getElementById('small').addEventListener('click', function () {
    chart.setSize(400);
});

document.getElementById('large').addEventListener('click', function () {
    chart.setSize(600);
});

document.getElementById('auto').addEventListener('click', function () {
    chart.setSize(null);
});

</script>
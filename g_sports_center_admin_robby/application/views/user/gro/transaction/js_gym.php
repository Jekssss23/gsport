<script>



 function new_transaction(fasilitas , kelompok_fasilitas='', id_les=''){
  $('#btn_tab_cek_data').attr('class','nav-link');
  // $('#btn_tab_new_transaction').attr('class','nav-link active');
  $('#new_transaction').attr('class','tab-pane tabs-animation active fade show');
  $('#btn_tab_new_transaction').attr('class','nav-link active');

  $('#nama_fasilitas').html("Fasilitas : "+fasilitas);
  $('#input_fasilitas').val(fasilitas);
    if (kelompok_fasilitas=='Les') {
      form_transaction_les(fasilitas, id_les);

    }else{
      form_transaction(fasilitas);

    }



 }


function form_transaction(fasilitas){
            $.ajax(
            {
              url     : baseUrl('/user/gro/new_transaction/form_transaction'),
              type    : 'POST',
              data    : { 
                fasilitas : fasilitas
              },
              success : function(data)
              {
                $('#detail_order_fasilitas').html(data);
              },
              error : function(){
                alert('error');
                
              }
            });

}


function form_transaction_les(fasilitas, id_les){
            $.ajax(
            {
              url     : baseUrl('/user/gro/new_transaction/form_transaction_les'),
              type    : 'POST',
              data    : { 
                fasilitas : fasilitas,
                id_les : id_les,
              },
              success : function(data)
              {
                $('#detail_order_fasilitas').html(data);
              },
              error : function(){
                alert('error');
                
              }
            });

}


$('#jenis_pilihan').change(function(){
    var id_jenis_member = $('#jenis_pilihan').val();
});


function pilihan_member_gym(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/gym/jenis_member'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih Jenis Member--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_jenis_member+`">`+v.jenis_member+`</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}

function pilihan_member_gym_spesial(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/gym/jenis_member_gym_spesial'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih Jenis Member--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_jenis_member+`">`+v.jenis_member+`</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}

function pilihan_member_swimming_bulanan(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/swimming/jenis_member_swimming_bulanan'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih Jenis Member--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_swimming+`">`+v.jenis_member+`</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}

function pilihan_les_renang(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/les/jenis/Renang'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih jumlah pertemuan--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_les+`">`+v.jumlah_pertemuan+` Kali</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}
function pilihan_les_futsal_academy(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/les/jenis/Futsal Academy'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih jumlah pertemuan--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_les+`">`+v.jumlah_pertemuan+` Kali</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}
function pilihan_les_aikido(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/les/jenis/Aikido'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih jumlah pertemuan--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_les+`">`+v.jumlah_pertemuan+` Kali</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}
function pilihan_les_silat_harimau(){

            $.ajax(
            {
              url     : baseUrl('/user/gro/transaction/les/jenis/Silat Harimau'),
              type    : 'POST',
              dataType : 'JSON',
              data    : { 
              },
              success : function(data)
              {
                $('#jenis_pilihan').html('<option value="">--Pilih jumlah pertemuan--</option>');
                $('#form_jenis_pilihan').show();
                $.each(data, function(k,v){
                  $('#jenis_pilihan').append(`<option value="`+v.id_les+`">`+v.jumlah_pertemuan+` Kali</option>`);
                });
              },
              error : function(){
                alert('error');
                
              }
            });
}
function pilihan_member_swimming_harian(){

                $('#jenis_pilihan').html('<option value="">--Pilih Jenis Member--</option>');
                  $('#jenis_pilihan').append(`<option value="Dewasa">Dewasa</option>`);
                  $('#jenis_pilihan').append(`<option value="Anak-anak">Anak-anak</option>`);


}



// function detail_member_gym(id_jenis_member, jenis_transaksi){
//   $('#form_transaksi_gym').show();
//   $('#nama_fasilitas').html(jenis_transaksi);
//   alert('Selanjutnya');
// }


</script>

<center><h3><strong>Pesan Pengaduan <br> #<?php echo $pesan['noTiket'];?></strong></h3></center>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4><strong>Detail Pengiriman</strong></h4>
	</div>
	<div class="panel-body">
		<table class="table table-hover table-bordered table-striped table-condensed">
		<tr>
			<th colspan="2"><h4><strong>Sumber</strong></h4></th>
			<th colspan="2"><h4><strong>Tujuan</strong></h4></th>
		</tr>
		<tr>
			<th>Dari :</th>
				<td colspan="3"><?php echo $pesan['dari'];?></td>
		</tr>
		<tr>
			<th>Unit Kerja Asal :</th>
				<td><?php echo $pesan['unitAsal'];?></td>
			<th>Unit Kerja Tujuan :</th>
				<td><?php echo $pesan['unitTujuan'];?></td>
		</tr>
		<!--
		departemen tidak ditampilkan
		permintaan Pak Nono
		4/10/2016
		<tr>
			<th>Departmen Asal :</th>
				<td><?php echo $pesan['departemenAsal'];?></td>
			<th>Departmen Tujuan :</th>
				<td><?php echo $pesan['departemenTujuan'];?></td>
		</tr>
		-->
		<tr>
			<th>Status :</th>
				<td><?php echo $pesan['keterangan']?></td>
		</tr>
		<tr>
			<th colspan="4">&nbsp;</th>
		</tr>
		<tr>
			<th>Waktu Buat :</th>
				<td><?php echo $pesan['waktuBuat']?></td>
			<th>Waktu Tutup :</th>
				<td><?php echo $pesan['waktuTutup']!='0000-00-00 00:00:00'? $pesan['waktuTutup']:'';?></td>
		</tr>
		<tr>
			<th>Respon Terakhir :</th>
				<td><?php echo $pesan['responAkhir']!='0000-00-00 00:00:00'? $pesan['responAkhir']:'';?></td>
			<th>Lama Respon :</th>
				<td><?php
					//hitung selisih waktu 
					if($pesan['waktuTutup']!='0000-00-00 00:00:00'){
						$time1=new DateTime($pesan['waktuBuat']);
						$time2=new DateTime($pesan['waktuTutup']);
						$interval=$time1->diff($time2);
						//print_r($interval);
						echo $interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik';
					}else{
						echo '';
					}
				?></td>
		</tr>
		</table>
	</div>
	<div class="panel-heading">
		<h4><strong>Data Pelapor</strong></h4>
	</div>
	<div class="panel-body">
	<table class="table table-hover table-striped table-condensed">
	<tr>
		<th width="22%">Nama Pelapor :</th>
			<td><?php echo $pesan['namaPelapor'];?></td>
	</tr>
	<tr>
		<th>Nomor Telepon / Handphone :</th>
			<td><?php echo $pesan['noTelpon'];?></td>
	</tr>
	<tr>
		<th>NPA :</th>
			<td colspan="3"><?php echo $pesan['npa'];?></td>
	</tr>
	<tr>
		<th>Nama Pelanggan :</th>
			<td><?php echo $pesan['namaPelanggan'];?></td>
	</tr>
	<tr>
		<th>Alamat :</th>
			<td><?php echo $pesan['alamat'];?></td>
	</tr>
	</table>
	</div>
	<div class="panel-heading">
		<h4><strong>Detail Pengaduan</strong></h4>
	</div>
	<div class="panel-body">
		<table class="table table-hover table-striped">
			<tr>
				<th width="22%">Subjek :</th>
					<td><?php echo $pesan['subjek'];?></td>
			</tr>
			<tr>
				<th>Pesan :</th>
					<td><p><?php echo nl2br($pesan['detail']);?></p></td>			
			</tr>
		</table>
		<div class="btn-group">
			<button class="btn btn-primary btn-md" data-toggle="modal" data-target="#modal_form" <?php
				if($this->session->userdata('role')=='kcab'&&$this->session->userdata('kodeUnit')==$pesan['kodeUnitTujuan']&&$pesan['idStatus']==1){
					echo '';
				}elseif($this->session->userdata('role')=='kbag'&&$this->session->userdata('kodeUnit')==$pesan['kodeUnitTujuan']&&$pesan['idStatus']==1){
					echo '';
				}else{
					echo 'style="display:none"';
			}?>>Teruskan Sebagai Tiket Order</button>
			<button class="btn btn-primary btn-md" data-toggle="modal" data-target="#modal_form2" <?php 
				if($this->session->userdata('role')=='kcab'&&$this->session->userdata('kodeUnit')==$pesan['kodeUnitTujuan']&&$pesan['idStatus']==1){
					echo '';
				}elseif($this->session->userdata('role')=='kbag'&&$this->session->userdata('kodeUnit')==$pesan['kodeUnitTujuan']&&$pesan['idStatus']==1){
					echo '';
				}else{
					echo 'style="display:none"';
				}
			?>>Tutup Pesan Pengaduan</button>			
		</div>
		<button class="btn btn-danger" onclick="history.go(-1)">Kembali</button>
	</div>
</div>

<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Pilih Divisi/Bagian</h3>
			</div>
			<div class="modal-body form">
			<?php echo form_open('tiket/tambahOrderFwd/'.$this->uri->segment(3).'/'.str_replace('/', '-', $pesan['noTiket']),array('class'=>'form-horizontal', 'id'=>'form', 'onSubmit'=>'return validasiOrder()'));?>
					<input type="hidden" value="<?php echo $pesan['id'];?>" name="id"></input>
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-3">Bagian :</label>
							<div class="col-md-9">
								<select class="form-control" name="departemen" id="departemen" onchange="tampilKbag()">
									<option value="0">-- Pilih Bagian --</option>
									<?php
										foreach($bagian as $b){
											echo '<option value="'.$b->id.'">'.$b->namaDepartemen.'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Pegawai :</label>
							<div class="col-md-9">
								<select class="form-control" name="pegawai" id="pegawai">
									<option value="0">-- Pilih Pegawai --</option>
								</select>
							</div>
						</div>						
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" id="submit" name="submit" class="btn btn-primary">Buat Tiket Order</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
			</div>
			<?php echo form_close();?>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_form2" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Tutup Pesan Pengaduan</h3>
			</div>
			<div class="modal-body form">
			<?php echo form_open('tiket/tutupLi/'.$this->uri->segment(3),array('class'=>'form-horizontal', 'id'=>'form'));?>
					<input type="hidden" value="<?php echo $pesan['jenisInformasi'];?>" name="jenisInformasi"></input>
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-3">Jam :</label>
							<div class="col-md-5">
								<input readonly value="<?php echo date("H:i:s")?>" class="form-control" type="text" name="jam">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Tanggal :</label>
							<div class="col-md-5">
								<input readonly value="<?php echo date("Y-m-d")?>" class="form-control" type="text" name="tanggal">
							</div>
						</div>						
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" id="submit" name="submit" class="btn btn-primary">Tutup Pesan</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
			</div>
			<?php echo form_close();?>
		</div>
	</div>
</div>

<script type="text/javascript">
//pilihan dengan role: kcab dan kdiv
function tampilKbag(){
	var idDept = $('#departemen').val();
	//alert(idDept);
	$.ajax({
		url:"<?php echo base_url();?>tiket/pilihBagian/"+idDept+"",
		success: function(html){
			$("#pegawai").html(html);
		},
		pesanType:"html"
	});
}

function validasiOrder(){
	if($('#departemen').val()==0||$('#pegawai').val()==0){
		alert('Lengkapi pilihan Divisi/Bagian dan Pegawai');
		return false;
	}
}

</script>
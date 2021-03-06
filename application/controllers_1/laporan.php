<?php if (!defined('BASEPATH')) exit ('No direct script access allowed');

class Laporan extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('tiket_model');
		$this->load->model('user_model');
		$this->load->model('unit_model');
		$this->load->model('departemen_model');
		$this->load->model('sop_model');
		$this->load->model('wo_model');
		$this->load->model('sla_model');
		$this->load->model('report_model');
	}

	public function tgl_indo($tanggal){
		$bulan = array (
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $tanggal);
		
		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun
	 
		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}


	function cetakTiketDetail(){
		ini_set('memory_limit','-1');
		$sessionData = $this->session->userdata('login');		
		//$data = $this->tiket->tampilharian($id, $tanggal_mulai);
		$nipp=$this->input->post('pegawai');
		//print_r($nipp);die;
		$nama=$this->user_model->getUserData($nipp)->row_array();
		$jabatan=strtolower($nama['namaRole']);
		$jabatan=ucwords($jabatan);
		//print_r($nama);die;
		$report=$this->report_model->getReportDetailByNipp($nipp)->result();
		//print_r($report);die;
		//print_r($data);die;
		$this->load->library('mPdf');
		$mpdf = new mPDF("en-GB-x","A4","","",10,10,15,15,1,1);//,'arial');
		//$mpdf = new mPDF('c','A4-P', 0, '', 10, 20, 10, 5, 1, 1,'arial');
		$mpdf->setWatermarkImage('./asset/images/logo.png', 1, array(60,10), array(5,2));
		$mpdf->showWatermarkImage = true;
		if($nama['namaDepartemen']==null){
			$htmlHeader = '
			<h1 style="font-family: arial;  font-size: 18pt; text-align:center"><strong>Report Detail</strong></h1>
				<table style="font-size:10pt; text-align:center">
					<tr>
						<td align="left">Nama</td><td>:</td><td align="left">'.$nama['nama'].'</td>
					</tr>
					<tr>
						<td align="left">Jabatan</td><td>:</td><td align="left">'.$jabatan.'</td>
					</tr>
					<tr>
						<td align="left">Unit Kerja</td><td>:</td><td align="left">'.$nama['namaUnit'].'</td>
					</tr>
				</table>
				<br>';
		}else{
			$htmlHeader = '
			<h1 style="font-family: arial;  font-size: 18pt; text-align:center"><strong>Report Detail</strong></h1>
				<table style="font-size:10pt; text-align:center">
					<tr>
						<td align="left">Nama</td><td>:</td><td align="left">'.$nama['nama'].'</td>
					</tr>
					<tr>
						<td align="left">Jabatan</td><td>:</td><td align="left">'.$jabatan.'</td>
					</tr>
					<tr>
						<td align="left">Unit Kerja</td><td>:</td><td align="left">'.ucfirst($nama['namaDepartemen']).' / '.$nama['namaUnit'].'</td>
					</tr>
				</table>
				<br>';				
		}

		$htmlContent .='
			<table width="100%" style="border:1px solid black; border-collapse:collapse"  border="1" cellpadding="5" cellspacing="0">
			<thead>
				<tr>
					<th style="background-color:#ededda" rowspan="2" width="3%" align="center">No</th>
					<th style="background-color:#ededda" rowspan="2" width="10%" align="center">No. Tiket</th>
					<th style="background-color:#ededda" rowspan="2" width="10%" align="center">Work Order</th>
					<th style="background-color:#ededda" rowspan="2" width="5%" align="center">SLA</th>
					<th style="background-color:#ededda" rowspan="2" width="10%" align="center">Sumber</th>				
					<th style="background-color:#ededda" rowspan="2" width="5%" align="center">Aksi</th>
					<th style="background-color:#ededda" rowspan="2" width="10%" align="center">Waktu</th>
					<th style="background-color:#ededda" colspan="2" align="center">Lama Waktu</th>
					<th style="background-color:#ededda" rowspan="2" width="10%" align="center">Selisih SLA (Detik)</th>
				</tr>
				<tr>
					<th style="background-color:#ededda" width="15%">Per Aksi</th>
					<th style="background-color:#ededda" width="15%" >Keseluruhan</th>
				</tr>	
			</thead>
			<tbody>
		';
		//print_r($report);die;

		if(!empty($report)){
						//cek db
			$no=1;
			$preNoTik=null;
			$prePerson=null;
			$preTime=null;
			$begTime=null;
			$finTime=null;

			foreach ($report as $row){
				$htmlContent .='<tr>';

				if($row->noTiket==$preNoTik){
					$htmlContent .='<td></td>';
				}else{
					$htmlContent .='<td>'.$no.'</td>';
				}

				if($row->noTiket==$preNoTik){
					$htmlContent .='<td></td>';
				}else{
					$htmlContent .='<td>'.$row->noTiket.'</td>';
				}

				if($row->noTiket==$preNoTik){
					$htmlContent .='<td></td>';
				}else{
					$htmlContent .='<td>'.$row->namaWo.'</td>';
				}

				if($row->noTiket==$preNoTik){
					$htmlContent .='<td></td>';
				}else{
					$htmlContent .='<td>'.$row->namaSla.'</td>';
				}

				if ($row->nama==$prePerson&&$row->noTiket==$preNoTik) {
					$htmlContent .='<td></td>';
				}else{
					$htmlContent .='<td>'.$row->nama.'</td>';
				}

				$htmlContent .='<td>'.$row->keterangan.'</td>';
				$htmlContent .='<td>'.$row->waktuBuat.'</td>';

				if($row->noTiket==$preNoTik){
					$time1=new DateTime($preTime);
					$time2=new DateTime($row->waktuBuat);
					$interval=$time1->diff($time2);
					//print_r($interval);
					$htmlContent .='<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';
				}else{
					$htmlContent .='<td></td>';
				}

				if($row->noTiket==$preNoTik){
					if($row->idStatus==4||$row->idStatus==5){
						$time1=new DateTime($begTime);
						$time2=new DateTime($row->waktuBuat);
						$interval=$time1->diff($time2);
						$htmlContent .='<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';	
					}else{
						$htmlContent .='<td></td>';
					}							
				}else{
					$begTime=$row->waktuBuat;
					$htmlContent .='<td></td>';
				}

				if($row->noTiket==$preNoTik){
					if($row->idStatus==4||$row->idStatus==5){
						if($row->nilaiSla!=0||$row->nilaiSla!=null){
							$time1=new DateTime($begTime);
							$time2=new DateTime($row->waktuBuat);
							//$interval=$time1->diff($time2);
							//print_r((strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla));
							//echo '<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';
							$selisih=(strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla);

							if($selisih==$row->nilaiSla){
								$htmlContent .='<td style="background-color:#ffff38"> T = '.$selisih.'</td>';
							}elseif($selisih>$row->nilaiSla){
								$htmlContent .='<td style="background-color:#ff4b38"> T > '.$selisih.'</td>';
							}elseif($selisih<$row->nilaiSla){
								$htmlContent .='<td  style="background-color:#42f46e"> T < '.$selisih.'</td>';
							}				
							
						}else{
							$htmlContent .='<td style="background-color:#ffff38">T = 0</td>';
						}
					}else{
						$htmlContent .='<td></td>';
					}							
				}else{
					$begTime=$row->waktuBuat;
					$htmlContent .='<td></td>';
				}

				$htmlContent .='</tr>';
				/*
				if($row->noTiket==$preNoTik){
					if($row->idStatus==4||$row->idStatus==5){
						$time1=new DateTime($begTime);
						$time2=new DateTime($row->waktuBuat);
				$interval=$time1->diff($time2);
				//print_r((strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla));
				echo '<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';
						$selisih=(strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla);
						if($selisih==$row->nilaiSla){
							$htmlContent .='<td> T = '.$selisih.'</td>';
						}elseif($selisih>$row->nilaiSla){
							$htmlContent .='<td> T > '.$selisih.'</td>';
						}elseif($selisih<$row->nilaiSla){
							$htmlContent .='<td> T < '.$selisih.'</td>';
						}
					}else{
						$htmlContent .='<td></td>';
					}							
				}else{
					$begTime=$row->waktuBuat;
					$htmlContent .='<td></td>';
				}				
				echo $row->noTiket==$preNoTik?'<td></td>':'<td>'.$no.'</td>';
				echo $row->noTiket==$preNoTik?'<td></td>':'<td>'.$row->noTiket.'</td>';
				echo $row->noTiket==$preNoTik?'<td></td>':'<td>'.$row->namaWo.'</td>';
				echo $row->noTiket==$preNoTik?'<td></td>':'<td>'.$row->namaSla.'</td>';
				echo $row->nama==$prePerson&&$row->noTiket==$preNoTik?'<td></td>':'<td>'.$row->nama.'</td>';
				echo '<td>'.$row->keterangan.'</td>';
				echo '<td>'.$row->waktuBuat.'</td>';
				//cek waktu tiap aksi pada tiket yang sama
				if($row->noTiket==$preNoTik){
					$time1=new DateTime($preTime);
					$time2=new DateTime($row->waktuBuat);
					$interval=$time1->diff($time2);
					//print_r($interval);
					echo '<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';
				}else{
					echo '<td></td>';
				}
				//cek keseluruhan waktu
				if($row->noTiket==$preNoTik){
					if($row->idStatus==4||$row->idStatus==5){
						$time1=new DateTime($begTime);
						$time2=new DateTime($row->waktuBuat);
						$interval=$time1->diff($time2);
						echo '<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';	
					}else{
						echo '<td></td>';
					}							
				}else{
					$begTime=$row->waktuBuat;
					echo '<td></td>';
				}

				if($row->noTiket==$preNoTik){
					if($row->idStatus==4||$row->idStatus==5){
						$time1=new DateTime($begTime);
						$time2=new DateTime($row->waktuBuat);
						//$interval=$time1->diff($time2);
						//print_r((strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla));
						//echo '<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';
						$selisih=(strtotime($row->waktuBuat)-strtotime($begTime))-($row->nilaiSla);
						if($selisih==$row->nilaiSla){
							echo '<td> T = '.$selisih.'</td>';
						}elseif($selisih>$row->nilaiSla){
							echo '<td> T > '.$selisih.'</td>';
						}elseif($selisih<$row->nilaiSla){
							echo '<td> T < '.$selisih.'</td>';
						}
					}else{
						echo '<td></td>';
					}							
				}else{
					$begTime=$row->waktuBuat;
					echo '<td></td>';
				}
				*/					
				//tutup tabel
				//echo '</tr>';
				//tambah no indeks jika no berubah
				if($row->noTiket!=$preNoTik){
					$no++;
				}
				//simpan notik sebelumnya
				$preNoTik=$row->noTiket;
				//cek nama sumber informasi
				if($row->nama!=$prePerson){
					$prePerson=$row->nama;
				}
				$preTime=$row->waktuBuat; //simpan waktu sebelum
			}
		}

		$htmlContent .= '</tbody></table>';
		
		$htmlfooter = 
		'<br>
			<table style="font-size:12px">
				<tr>
					<td><strong><u>Keterangan</u></strong></td>
					<td>:</td>
					<td></td>
				</tr>
				<tr>
					<td style="background-color:#ff4b38" width="20px" height="15px"></td>
					<td></td>
					<td>Order selesai melewati dari waktu <i>SLA</i></td>
				</tr>
				<tr>
					<td style="background-color:#ffff38" width="20px" height="15px"></td>
					<td></td>
					<td>Tidak ada <i>SLA</i></td>
				</tr>
				<tr>
					<td style="background-color:#42f46e" width="20px" height="15px"></td>
					<td></td>
					<td>Order selesai kurang dari waktu <i>SLA</i></td>
				</tr>
			</table><br>
			<table width="100%">
			<tr>
				<td width="40%" align="center"></td>
				<td width="20%"></td>
				<td width="40%" align="left">Tanggal Cetak '.date("d - m - Y H:i").'</td>
			</tr>
			<tr>
				<td width="20%" align="center"></td>				
				<td width="60%" align="center"></td>
				<td width="20%" align="center">DIKETAHUI OLEH :</td>
				
			</tr>
			<tr>
				<td width="20%" align="center"></td>				
				<td width="60%" align="center"></td>
				<td width="20%" align="center">KEPALA CABANG</td>
				
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="20%" align="center"><br><br><br><br></td>
				
			</tr>
			</table>
			<br><br>	
			<table width="100%">
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"></td>
				<td width="40%" align="center"></td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"></td>
				<td width="40%" align="center"></td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="40%" align="center"><br><br><br><br></td>
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
			</tr>
			
		</table>
		<htmlpagefooter name="MyFooter1">
			<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
				<tr>
					<td width="33%" align="center" style="font-weight: bold; font-style: italic;">Tanggal Cetak '.date("d/m/Y H:i:s").',  Halaman {PAGENO} dari {nbpg}</td>
				</tr>
			</table>
		</htmlpagefooter>
		<sethtmlpagefooter name="MyFooter1" page="all" value="on" />
		';
		//print_r($htmlContent);die;
		$html = $htmlHeader;
		$html .= $htmlContent;
		$html .=  $htmlfooter;
		//print_r($html);die;
		/*
		$html = '
		<htmlpagefooter name="MyFooter1">
			<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
				<tr>
					<td width="33%" align="center" style="font-weight: bold; font-style: italic;">Tanggal Cetak '.date("d/m/Y H:i:s").',  Halaman {PAGENO} dari {nbpg}</td>
				</tr>
			</table>
		</htmlpagefooter>

		<sethtmlpagefooter name="MyFooter1" value="on" />
		<div style="font-size:20px; font-weight:bold"></div>
		<div style="font-weight:bold;"></div>
		<div style="font-size:20px; font-weight:bold; text-align:center">Laporan Order</div>
		<div style="font-weight:bold; text-align:center">NOMOR : </div>
		<br>
			<td width="50%" align="center">Pekerjaan ;;;: </td>
		</br>
		<br>
			<td width="50%" align="center">Lokasi ;;;;;;;;: </td>
		</br>
		<br>	
			<td width="50%" align="center">Keterangan : </td>	
		</br><br>';

		$html .='
		<table width="100%" border="1">
			  <tr>
				<td height="32" width="4%" align="center"><strong>No</strong></td>
				<td width="14%" align="center"><strong>NO_TIKET</strong></td>
				<td width="14%" align="center"><strong>DIBUAT</strong></td>
				<td width="14%" align="center"><strong>DITERUSKAN</strong></td>
				<td width="14%" align="center"><strong>DITUGASKAN</strong></td>
				<td width="14%" align="center"><strong>DITUTUP</strong></td>
				<td width="14%" align="center"><strong>DIVERIFIKASI</strong></td>
				<td width="14%" align="center"><strong>KETERANGAN</strong></td>
			  
			
		  </tr>';
		 
		$no= 1;
		foreach($data as $row){
		$html .='  
		  <tr>
			<td height="20" align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$no++.'</td>
			<td align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->noTiket.'</td>
			<td align="left" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
			<td align="right" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
			<td align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
			<td align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
			<td align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
			<td align="center" style="border-top:none;border-left:solid black 1.0pt;border-bottom:none;">'.$row->waktuBuat.'</td>
		</tr>';
		}
			
		$html .= '</table>';
	
		$html .=' </tr> 
		
			<br>
				<td width="50%" align="center">Pekerjaan ;;;: </td>
			</br>
			<br>
				<td width="50%" align="center">Lokasi ;;;;;;;;: </td>
			</br>
			<br>	
				<td width="50%" align="center">Keterangan : </td>				
			</br>';
		$html .= '<br><br><br><br>	
			<table width="100%">
			<tr>
				<td width="40%" align="center"></td>
				<td width="20%"></td>
				<td width="40%" align="left">Tanggal Cetak '.date("d - m - Y H:i").'</td>
			</tr>
			<tr>
				<td width="20%" align="center">DIBUAT OLEH :</td>				
				<td width="60%" align="center">DISETUJUI OLEH :</td>
				<td width="20%" align="center">DIKELUARKAN OLEH :</td>
				
			</tr>
			<tr>
				<td width="20%" align="center">KABAG.</td>				
				<td width="60%" align="center">KEPALA CABANG</td>
				<td width="20%" align="center">KABAG. UMUM</td>
				
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="20%" align="center"><br><br><br><br></td>
				
			</tr>
			</table>';
			$html .= '<br><br>	
			<table width="100%">
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center">DIVALIDASI OLEH :</td>
				<td width="40%" align="center">DITERIMA OLEH :</td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center">PETUGAS GUDANG</td>
				<td width="40%" align="center">VENDOR</td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="40%" align="center"><br><br><br><br></td>
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
			</tr>
			
		</table>';
		*/
		//$pdfFilePath="reportDetail".$nipp.".pdf";
		//print_r($html);die;
		$mpdf->WriteHTML($html);
		$mpdf->Output();//$pdfFilePath,"D");
		//echo $html;
	}
 

	public function cetakReportPerbagian(){
		ini_set('memory_limit','-1');
		//print_r($this->input->post('bagian'));die;
		$idUnit=$this->session->userdata('kodeUnit');//$this->input->post('unit');
		$idDept=$this->input->post('bagian');
		$periode=$this->input->post('periode');
		$report=$this->report_model->getReportDetailPerbagian($idDept,$periode);
		//$namaUnit=$this->
		$periode=explode("-", $periode);
		$periodeAwal="01"."-".$periode[0]."-".$periode[1];
		$periodeAkhir=date("t-m-Y", strtotime($periodeAwal));
		$namaDepartemen=$this->departemen_model->getNameById($idDept)->row_array();
		//$namaUnit=$this->unit_model->getNameById($idUnit)->row_array();
		$namaUnit=$this->unit_model->get_by_unit($idUnit)->row_array();
		//print_r($namaUnit);die;
		//print_r($namaUnit);die;
		//print_r(count($report));die;
		//		print_r($report);die;
		$this->load->library('mPdf');
		$mpdf = new mPDF("en-GB-x","A4","","",10,10,15,15,1,1);
		//print_r($namaDepartemen);die;
		$mpdf->setWatermarkImage('./asset/images/logo.png', 1, array(60,10), array(5,2));
		$mpdf->showWatermarkImage = true;
		//Print HEADER atau KOP SURAT
		if (!empty($namaDepartemen)) {
			$htmlHeader='
			<h1 style="font-family: arial;  font-size: 18pt; text-align:center"><strong>Report Detail/Bagian</strong></h1>
				<table style="font-size:10pt; text-align:center">
					<tr>
						<td align="left">Unit</td><td>:</td><td align="left">'.$namaUnit['namaUnit'].'</td>
					</tr>
					<tr>
						<td align="left">Departemen</td><td>:</td><td align="left">'.ucfirst($namaDepartemen['namaDepartemen']).'</td>
					</tr>
					<tr>
						<td align="left">Periode</td><td>:</td><td align="left">'.ucfirst($periodeAwal).' / '.ucfirst($periodeAkhir).'</td>
					</tr>
				</table>
				<br>';
		}

		$htmlContent.= '
			<table width="100%" style="border:1px solid black; border-collapse:collapse"  border="1" cellpadding="5" cellspacing="0">
			<thead>
				<tr>
					<th style="background-color:#ededda" rowspan="2" width="5%" align="center">No</th>
					<th style="background-color:#ededda" rowspan="2" width="30%" align="center">Nama</th>
					<th style="background-color:#ededda" rowspan="2" width="15%" align="center">Tanggal</th>
					<th style="background-color:#ededda" rowspan="2" width="30%" align="center">Work Order</th>
					<th style="background-color:#ededda" colspan="3" width="10%" align="center">Volume</th>
					<th style="background-color:#ededda" colspan="2" align="center">SLA</th>
				</tr>
				<tr>
					<th style="background-color:#ededda" width="5%">Nama Item</th>
					<th style="background-color:#ededda" width="5%">Jumlah</th>
					<th style="background-color:#ededda" width="5%">Satuan</th>
					<th style="background-color:#ededda" width="10%">Waktu Selesai</th>
					<th style="background-color:#ededda" width="10%" >Target</th>
				</tr>	
			</thead>
			<tbody>
		';

		//print_r($report);die;
		if(!empty($report)){
			//parsing data query ke tabel
			//beri nilai default pada variabel awal
			$iterasi1=0;
			$iterasi2=0;
			$noUrut=1;
			$preNoUrut=null;
			$preName=null;
			$preDate=null;
			$preIdOrder=null;
			$preWo=null;
			$preDari=null;

			//print_r(count($report));die;
			//print_r($report[$iterasi1]);die;
			//buat perulangan sesuai jumlah array report
			while($iterasi1<count($report)) {
				//parsing objek pada masing-masing array report
				//print_r($report[$iterasi1]);die;
				//print_r(count($report[$iterasi1]));die;
				while($iterasi2<count($report[$iterasi1])){
					//print_r(count($report[$iterasi1]));
					//print_r($report[$iterasi1][$iterasi2]['dari']);die;
					$tanggal=date('d-M-Y', strtotime($report[$iterasi1][$iterasi2]['waktuBuat']));
					//BUKA ROW
					//echo "<tr>";
					$htmlContent.="<tr>";
					//NO URUT
					//echo $report[$iterasi1][$iterasi2]['dari']==$preDari?'<td></td>':'<td>'.$noUrut.'</td>';
					if($report[$iterasi1][$iterasi2]['dari']==$preDari){
						$htmlContent.='<td></td>';
					}else{
						$htmlContent.='<td>'.$noUrut.'</td>';
					}
					//NAMA
					//echo $report[$iterasi1][$iterasi2]['dari']==$preDari?'<td></td>':'<td>'.$report[$iterasi1][$iterasi2]['nama'].' '.$report[$iterasi1][$iterasi2]['dari'].'</td>';
					if($report[$iterasi1][$iterasi2]['dari']==$preDari){
						$htmlContent.='<td></td>';
					}else{
						$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['nama'].' '.$report[$iterasi1][$iterasi2]['dari'].'</td>';
					}
					//TANGGAL
					//echo $tanggal==$preDate?'<td></td>':'<td>'.$tanggal.'</td>';
					if($tanggal==$preDate){
						$htmlContent.='<td></td>';
					}else{
						$htmlContent.='<td>'.$tanggal.'</td>';
					}
					//WORK ORDER
					//echo $report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate?'<td></td>':'<td>'.$report[$iterasi1][$iterasi2]['namaWo'].'</td>';
					if($report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate){
						$htmlContent.='<td></td>';
					}else{
						$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['namaWo'].'</td>';//' '.$report[$iterasi1][$iterasi2]['idWo'].'</td>';
					}
					//VOLUME
					//NAMA ITEM
					//echo '<td>'.$report[$iterasi1][$iterasi2]['namaItem'].'</td>';
					$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['namaItem'].'</td>';
					//JUMLAH
					//echo '<td>'.$report[$iterasi1][$iterasi2]['jumlah'].'</td>';
					$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['jumlah'].'</td>';
					//SATUAN
					//echo '<td>'.$report[$iterasi1][$iterasi2]['namaSatuan'].'</td>';
					$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['namaSatuan'].'</td>';
					//SLA
					//LAMA WAKTU
					//echo $report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate?'<td></td>':'<td>'.$report[$iterasi1][$iterasi2]['jam'].' Jam '.$report[$iterasi1][$iterasi2]['menit'].' Menit'.'</td>';
					if($report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate){
						$htmlContent.='<td></td>';
					}else{
						//$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['jam'].' Jam '.$report[$iterasi1][$iterasi2]['menit'].' Menit'.'</td>';
						
						$timeCreated=$report[$iterasi1][$iterasi2]['waktuBuat'];
						$timeClosed=$report[$iterasi1][$iterasi2]['waktuTutup'];
						$time1=new DateTime($timeCreated);
						$time2=new DateTime($timeClosed);
						$interval=$time1->diff($time2);
						$htmlContent.='<td>'.$interval->d.' hari, '.$interval->h.' jam '.$interval->i.' menit '.$interval->s.' detik</td>';

					}
					//TARGET
					//echo $report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate?'<td></td>':'<td>'.$report[$iterasi1][$iterasi2]['namaSla'].'</td>';
					if($report[$iterasi1][$iterasi2]['idOrder']==$preIdOrder && $tanggal==$preDate){
						$htmlContent.='<td></td>';
					}else{
						$htmlContent.='<td>'.$report[$iterasi1][$iterasi2]['namaSla'].'</td>';
					}
					//TUTUP ROW
					//echo "</tr>";
					$htmlContent.="</tr>";

					//simpan data sebelumnya
					$preIdOrder=$report[$iterasi1][$iterasi2]['idOrder'];
					$preWo=$report[$iterasi1][$iterasi2]['idWo'];
					$preDate=$tanggal;
					$preDari=$report[$iterasi1][$iterasi2]['dari'];
					$iterasi2++;
				}
				$noUrut++;
				$iterasi1++;
				$iterasi2=0;
			}
		}else{
			echo "Data Kosong";
		};

		$htmlContent .= '</tbody></table>';
		
		$htmlfooter = 
		'<br>
			<table width="100%">
			<tr>
				<td width="40%" align="center"></td>
				<td width="20%"></td>
				<td width="40%" align="left">Tanggal Cetak '.date("d - m - Y H:i").'</td>
			</tr>
			<tr>
				<td width="20%" align="center"></td>				
				<td width="60%" align="center"></td>
				<td width="20%" align="center">DIKETAHUI OLEH :</td>
				
			</tr>
			<tr>
				<td width="20%" align="center"></td>				
				<td width="60%" align="center"></td>
				<td width="20%" align="center">KEPALA CABANG</td>
				
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="20%" align="center"><br><br><br><br></td>
				
			</tr>
			</table>
			<br><br>	
			<table width="100%">
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"></td>
				<td width="40%" align="center"></td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"></td>
				<td width="40%" align="center"></td>
			</tr>
			<tr>
				<td width="40%" ></td>				
				<td width="60%" align="center"><br><br><br><br></td>
				<td width="40%" align="center"><br><br><br><br></td>
			</tr>
			<tr>
				<td width="20%" align="center"><br><br><br><br></td>
			</tr>
			
		</table>
		<htmlpagefooter name="MyFooter1">
			<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
				<tr>
					<td width="33%" align="center" style="font-weight: bold; font-style: italic;">Tanggal Cetak '.date("d/m/Y H:i:s").',  Halaman {PAGENO} dari {nbpg}</td>
				</tr>
			</table>
		</htmlpagefooter>
		<sethtmlpagefooter name="MyFooter1" page="all" value="on" />
		';

		$html = $htmlHeader;
		$html .= $htmlContent;
		$html .=  $htmlfooter;		
		$mpdf->WriteHTML($html);
		$mpdf->Output();
	}

	public function cetakLogInOutPeriode(){
		// ini_set('memory_limit', '-1');
		// ini_set('MAX_EXECUTION_TIME', '-1');
		// print_r($_POST);
		$date = explode(" - ", $_POST['daterange']);
		$dept = $_POST['dept'];
		$myUnit = $this->session->userdata('kodeUnit');

		// print_r($date);
		$awal = new DateTime($date['0']);
		$akhir = new DateTime($date['1']);
		$interval = new DateInterval('P1D');

		// ambil setiap tanggal dari batas range yang diinputkan
		$datePeriod = new DatePeriod($awal,$interval,$akhir);
		// print_r($datePeriod);
		foreach ($datePeriod as $d => $value) {
			$datePeriodVal[] =  $value->format('Y-m-d');echo'<br/>';
		}
		// print_r($datePeriodVal);

		// ambil data pegawai
		if ($dept===$this->session->userdata('kodeUnit')) {
			//semua unit
			// echo 'semua pegawai unit';
			$peg = $this->user_model->get_by_unit($dept)->result();
		}else{
			// echo 'hanya departemen';
			$peg = $this->user_model->user_by_dept($myUnit,$dept)->result();
			// $peg = $this->user_model->get_by_dept($myUnit,$dept)->result();
		}

		// print_r($peg);
		// echo '<br/>';
		//periksa nipp masing2 pertanggal
		foreach ($peg as $p) {
			// echo $p->nipp; echo '<br/>';
			foreach ($datePeriodVal as $d) {
				// echo '$d = '.$d; echo '<br/>';
				$data[$p->nama][$d] = $this->user_model->getAbsenNippEachDate($p->nipp,$d)->row_array();
			}
		}

		// print_r($data);

		$this->load->library('mpdf/mPdf');
		$mpdf = new mPDF('c','A4-L', 0, '', 10, 20, 5, 5, 1, 1,'arial');
		// $mpdf->setWatermarkImage('./images/logo.png', 1, '', array(10,2));
		$mpdf->setWatermarkImage('./asset/images/logo-old1.png', 1, array(10,10), array(10,2));
		$mpdf->showWatermarkImage = true;
		//print_r($namaDepartemen);die;
		// $mpdf->setWatermarkImage('./asset/images/logo.png', 1, array(60,10), array(5,2));
		// $mpdf->showWatermarkImage = true;
		$html .= '<div style="font-size:16pt; font-weight:bold; text-align:center; margin-bottom:0px">REKAPITULASI ABSEN LOG IN/OUT WORK ORDER</div>';
		$html .= '<div style="font-size:11pt; font-weight:bold; text-align:center; margin-top:0px">TANGGAL '.date("d-m-Y",strtotime($date[0])).' s/d '.date("d-m-Y",strtotime($date[1])).'</div>
			<hr style="width:80%; margin-bottom=0px"><br/><br/>';
		$html .= '<table width="100%" style="border-collapse:collapse" cellpadding="3" border="1">
				<tr>
					<th rowspan="2" style="font-size:9pt;font-weight:bold;">No.</th>
					<th rowspan="2" style="font-size:9pt;font-weight:bold;">Nama</th>';
		foreach ($datePeriodVal as $d) {
			$html .= '<th colspan="3" align="center" style="font-size:9pt;font-weight:bold;"> Tgl '.date("d-m-Y",strtotime($d)).'</th>';
		}
		$html .='</tr><tr>';
		foreach ($datePeriodVal as $d) {
			$html .= '<th style="font-size:9pt;font-weight:bold;" align="center">IN</th><th style="font-size:9pt;font-weight:bold;" align="center">OUT</th><th style="font-size:9pt;font-weight:bold;" align="center">DUR</th>';
		}
		$html .='</tr>';
		$no=0;
		foreach ($data as $dt => $key) {
			$html .='<tr>';
			$html .='<td align="center" style="font-size:8pt;">'.++$no.'.</td>';
			$html .='<td width="250px" style="font-size:9pt;">'.$dt.'</td>';
			foreach ($datePeriodVal as $d) {
				if ($d==$key[$d]['Date']) {
					if ($key[$d]['loginTime']) {
						$html .= '<td style="font-size:8pt;font-weight:bold;" align="center">'.date("H:i",strtotime($key[$d]['loginTime'])).'</td>';
					}else{
						$html .= '<td style="font-size:5pt;" align="center">TAD</td>';
					}
					if ($key[$d]['logoutTime']) {
						$html .= '<td style="font-size:8pt;font-weight:bold;" align="center">'.date("H:i",strtotime($key[$d]['logoutTime'])).'</td>';
					}else{
						$html .= '<td style="font-size:5pt;" align="center">TAD</td>';
					}
					if ($key[$d]['duration']) {
						$html .= '<td style="font-size:8pt;font-weight:bold;" align="center">'.substr($key[$d]['duration'], 0, -4).'</td>';
					}else{
						$html .= '<td style="font-size:5pt;" align="center">-</td>';					}
					
				}else{
					$html .= '<td style="font-size:5pt;" align="center">TAD</td>';
					$html .= '<td style="font-size:5pt;" align="center">TAD</td>';
					$html .= '<td style="font-size:5pt;" align="center">-</td>';
				}
			}
			$html .='</tr>';
		}
		$html .='</table><br/>';
		$html .='<table>';
		$html .='</table>';
		$mpdf->simpleTables = true;
		$mpdf->WriteHTML($html);
		$mpdf->Output();
		// echo $html;
		// print_r($data);
		// foreach ($data as $dt => $key) {
		// 	echo $dt.' ';
		// 	foreach ($datePeriodVal as $d) {
		// 		echo $key[$d]['loginTime'];
		// 	}
		// 	echo '<br/>';
		// }
		// foreach ($data as $dt => $key) {
		// 	echo $key;
		// 	echo $dt;
		// }

		// var_dump($data);
	}

	public function cetakLogInOutPeriode2(){
		// ini_set('memory_limit', '-1');
		// ini_set('MAX_EXECUTION_TIME', '-1');
		// print_r($_POST);
		$date = explode(" - ", $_POST['daterange']);
		$dept = $_POST['dept'];
		$myUnit = $this->session->userdata('kodeUnit');

		// print_r($date);
		$awal = new DateTime($date['0']);
		$akhir = new DateTime($date['1']);
		$interval = new DateInterval('P1D');

		// ambil setiap tanggal dari batas range yang diinputkan
		$datePeriod = new DatePeriod($awal,$interval,$akhir);
		// print_r($datePeriod);
		foreach ($datePeriod as $d => $value) {
			$datePeriodVal[] =  $value->format('Y-m-d');echo'<br/>';
		}
		// print_r($datePeriodVal);

		// ambil data pegawai
		if ($dept===$this->session->userdata('kodeUnit')) {
			//semua unit
			// echo 'semua pegawai unit';
			$peg = $this->user_model->get_by_unit($dept)->result();
		}else{
			// echo 'hanya departemen';
			$peg = $this->user_model->user_by_dept($myUnit,$dept)->result();
			// $peg = $this->user_model->get_by_dept($myUnit,$dept)->result();
		}

		// print_r($peg);
		// echo '<br/>';
		//periksa nipp masing2 pertanggal
		foreach ($peg as $p) {
			// echo $p->nipp; echo '<br/>';
			foreach ($datePeriodVal as $d) {
				// echo '$d = '.$d; echo '<br/>';
				$data[$p->nama][$d] = $this->user_model->getAbsenNippEachDate($p->nipp,$d)->row_array();
			}
		}

		// print_r($data);

		$this->load->library('mpdf/mPdf');
		$mpdf = new mPDF('c','A4-L', 0, '', 10, 20, 5, 5, 1, 1,'arial');
		// $mpdf->setWatermarkImage('./images/logo.png', 1, '', array(10,2));
		$mpdf->setWatermarkImage('./asset/images/logo-old1.png', 1, array(10,10), array(10,2));
		$mpdf->showWatermarkImage = true;
		$mpdf->simpleTables = true;
		//print_r($namaDepartemen);die;
		// $mpdf->setWatermarkImage('./asset/images/logo.png', 1, array(60,10), array(5,2));
		// $mpdf->showWatermarkImage = true;
		$html .= '<div style="font-size:16pt; font-weight:bold; text-align:center; margin-bottom:0px">REKAPITULASI ABSEN LOGIN / LOGOUT</div>';
		$html .= '<div style="font-size:11pt; font-weight:bold; text-align:center; margin-top:0px">TANGGAL '.date("d-m-Y",strtotime($date[0])).' s/d '.date("d-m-Y",strtotime($date[1])).'</div>
			<hr style="width:80%; margin-bottom=0px"><br/><br/>';
		$html .= '<table width="100%" style="border-collapse:collapse" cellpadding="5" border="1">
				<tr>
					<th rowspan="2" style="font-size:9pt;font-weight:bold;">No.</th>
					<th rowspan="2" style="font-size:9pt;font-weight:bold;">Nama</th>';
		foreach ($datePeriodVal as $d) {
			$html .= '<th colspan="3" align="center" style="font-size:9pt;font-weight:bold;"> Tgl '.date("d-m-Y",strtotime($d)).'</th>';
		}
		$html .='</tr><tr>';
		foreach ($datePeriodVal as $d) {
			$html .= '<th style="font-size:9pt;font-weight:bold;" align="center">IN</th><th style="font-size:9pt;font-weight:bold;" align="center">OUT</th><th style="font-size:9pt;font-weight:bold;" align="center">DUR</th>';
		}
		$html .='</tr>';
		$no=0;
		foreach ($data as $dt => $key) {
			$html .='<tr>';
			$html .='<td align="center">'.++$no.'</td>';
			$html .='<td width="35px">'.$dt.'</td>';
			foreach ($datePeriodVal as $d) {
				if ($d==$key[$d]['Date']) {
					$html .= '<td style="font-size:8pt;" align="center">'.date("h:i",strtotime($key[$d]['loginTime'])).'</td>';
					$html .= '<td style="font-size:8pt;" align="center">'.date("h:i",strtotime($key[$d]['logoutTime'])).'</td>';
					$html .= '<td style="font-size:8pt;" align="center">'.substr($key[$d]['duration'], 0, -4).'</td>';
				}else{
					$html .= '<td style="font-size:8pt;" align="center">TAD</td>';
					$html .= '<td style="font-size:8pt;" align="center">TAD</td>';
					$html .= '<td style="font-size:8pt;" align="center">TAD</td>';
				}
			}
			$html .='</tr>';
		}
		$html .='</table><br/>';
		$html .='';

		$mpdf->WriteHTML($html);
		$mpdf->Output();
		// print_r($data);
		// foreach ($data as $dt => $key) {
		// 	echo $dt.' ';
		// 	foreach ($datePeriodVal as $d) {
		// 		echo $key[$d]['loginTime'];
		// 	}
		// 	echo '<br/>';
		// }
		// foreach ($data as $dt => $key) {
		// 	echo $key;
		// 	echo $dt;
		// }

		// var_dump($data);
	}
	
}
?>
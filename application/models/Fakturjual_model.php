<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fakturjual_model extends CI_Model
{

	public function getNo($id_user, $nm_user)
	{
		$q = $this->db->query("SELECT MAX(RIGHT(NoJual,2)) AS id_max FROM temp_mjual WHERE LEFT(NoJual,2) <> 'JL' AND NoJual LIKE '%W%' AND NmUser = '$nm_user' ");
		$kd = "";
		$hrf = "0ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$jh = (strlen($hrf) - 1);
		if ($id_user <= 99) {
			$hsl = sprintf("%02d", $id_user);
		} else {
			$angka = $id_user - 99;
			if ($angka <= $jh) {
				$h = substr($hrf, $angka, 1);
				$hsl = 0 . "$h";
			} else {
				$a1 = floor($angka / $jh);
				$a2 = $angka - ($a1 * $jh);
				$hsl = substr($hrf, $a1, 1) . substr($hrf, $a2, 1);
			}
		}
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int) $k->id_max) + 1;
				$kd = sprintf("%02s", $tmp);
			}
		} else {
			$kd = "01";
		}
		return $hsl . 'W' . $kd;
	}


	public function getNoFaktur($ymd, $id_user, $nm_user)
	{
		$q = $this->db->query("SELECT MAX(RIGHT(NoJual,5)) AS id_max FROM mjual WHERE LEFT(NoJual,2) LIKE 'JL' AND NoJual LIKE '%W%' AND NoJual NOT LIKE '%B%' AND substr(NoJual,3,4) = '$ymd' AND NmUser = '$nm_user' ");
		$kd = "";
		$kodeawal = "JL";
		$hrf = "0ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$jh = (strlen($hrf) - 1);
		if ($id_user <= 99) {
			$hsl = sprintf("%02d", $id_user);
		} else {
			$angka = $id_user - 99;
			if ($angka <= $jh) {
				$h = substr($hrf, $angka, 1);
				$hsl = 0 . "$h";
			} else {
				$a1 = floor($angka / $jh);
				$a2 = $angka - ($a1 * $jh);
				$hsl = substr($hrf, $a1, 1) . substr($hrf, $a2, 1);
			}
		}
		//$user = sprintf("%02d", $id_user);
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $k) {
				$tmp = ((int) $k->id_max) + 1;
				$kd = sprintf("%05s", $tmp);
			}
		} else {
			$kd = "00001";
		}
		return $kodeawal . $ymd . 'W' . $hsl . $kd;
		//echo "$q";
	}



	public function cek_go_simpan($noresi, $nm_user)
	{
		$this->db->where('NoJual', $noresi);
		$this->db->where('NmUser', $nm_user);
		//$this->db->where('Flag', '0');
		return $this->db->get('temp_mjual');
	}

	public function getDataPenjualan($noresi, $username)
	{
		$this->db->where('NoJual', $noresi);
		$this->db->where('NmUser', $username);
		$this->db->where('Flag', '0');
		return $this->db->get('temp_mjual');
	}
	public function getitem()
	{
		//$this->db->limit(20);
		return $this->db->get('barang');
	}
	public function getitems($departemen)
	{
		$this->db->where('KdDept', $departemen);
		//$this->db->limit(20);
		return $this->db->get('barang');
	}
	public function departemen_barang()
	{
		return $this->db->get('departemen');
	}

	public function getdetailbarang()
	{
		return $this->db->query("SELECT B.KdBrg, B.NmBrg, S.KdLokasi, S.Akhir FROM barang AS B Inner Join stocklokasi AS S ON B.KdBrg = S.KdBrg	");
	}

	public function getcustomer($idcust)
	{
		$this->db->where('KdCust', $idcust);
		return $this->db->get('customer');
	}


	public function cek_cust_ada($idcust, $nofaktur)
	{
		return $this->db->query("SELECT * FROM temp_mjual WHERE KdCust='$idcust' AND NoJual='$nofaktur'");
	}

	// database sales
	public function getsales($idsales)
	{
		$this->db->where('KdSales', $idsales);
		return $this->db->get('sales');
	}
	public function cek_sales_ada($idsales, $nofaktur)
	{
		return $this->db->query("SELECT * FROM temp_mjual WHERE KdSales='$idsales' AND NoJual='$nofaktur'");
	}

	// mencari kode barang untuk di masukkan ke temp_tjual
	public function getbarang($idbarang)
	{
		$this->db->where('KdBrg', $idbarang);
		$this->db->or_where('Barcode', $idbarang);
		return $this->db->get('barang');
	}
	/* =============== untuk ambil data stock berdasarkan lokasi */
	public function stocklokasi($idbarang)
	{
		$this->db->where('KdBrg', $idbarang);
		$this->db->or_where('KdLokasi', $idbarang);
		return $this->db->get('stocklokasi');
	}
	public function cek_disc_customer($kdcust, $kdept)
	{
		return $this->db->query("SELECT * FROM customerdiscont WHERE KdCust='$kdcust' AND KdDept='$kdept'");
	}

	public function cek_tpesan($nofaktur)
	{
		return $this->db->query("SELECT * FROM temp_tjual WHERE NoJual='$nofaktur'");
	}

	public function cek_sudah_ada($kdb, $nofaktur)
	{
		return $this->db->query("SELECT * FROM temp_tjual WHERE KdBrg='$kdb' AND NoJual='$nofaktur'");
	}

	public function cek_satuan_ada($idbarang, $nofaktur)
	{
		return $this->db->query("SELECT * FROM temp_tjual WHERE KdBrg='$idbarang' AND NoJual='$nofaktur'");
	}

	public function ceksales_detail($idsales, $idbarang, $nofaktur)
	{
		return $this->db->query("SELECT KdSales FROM temp_tjual WHERE KdSales='$idsales'AND KdBrg='$idbarang' AND NoJual='$nofaktur'");
	}

	public function cek_jenis_harga($nofaktur)
	{
		return $this->db->query("SELECT JenisHrg, KdCust, NamaCust FROM temp_mjual WHERE NoJual='$nofaktur'");
	}
	public function cek_lokasi($nofaktur)
	{
		return $this->db->query("SELECT KdLokasi FROM temp_mjual WHERE NoJual='$nofaktur'");
	}

	public function cek_jumlah_stok($idbarang)
	{
		return $this->db->query("SELECT barang.Stock_Akhir AS stok FROM barang WHERE barang.KdBrg='$idbarang'");
	}


	public function getListPenjualan($noresi)
	{
		return $this->db->query("SELECT * FROM temp_tjual WHERE NoJual='$noresi' ORDER BY nomor DESC");
	}

	public function listsales($noresi)
	{
		return $this->db->query("SELECT
        MP.NoJual,
        MP.NmUser,
        MP.Flag,
        MP.KdSales,
        S.NmSales
        FROM
        temp_mjual AS MP
        Inner Join sales AS S ON MP.KdSales = S.KdSales
        WHERE NoJual = '$noresi'");
	}

	public function getTotalBelanja($noresi)
	{
		return $this->db->query("SELECT SUM(Harga*Jumlah) AS tot_bel FROM temp_tjual WHERE NoJual='$noresi'");
	}

	public function cari_cust($NmCust)
	{
		if (strpos($NmCust, " ") !== false) {
			$sql = "SELECT KdCust, NmCust FROM customer WHERE (NmCust LIKE '%" . str_replace(" ", "%' and NmCust LIKE '%", "$NmCust") . "%') LIMIT 20";
		} else {
			$sql = "SELECT KdCust, NmCust FROM customer WHERE NmCust LIKE '%$NmCust%' LIMIT 20 ";
		}
		return $this->db->query($sql)->result();
	}

	public function cari_sales($Nmsales)
	{
		if (strpos($Nmsales, " ") !== false) {
			$sql = "SELECT KdSales, Nmsales FROM sales WHERE (Nmsales LIKE '%" . str_replace(" ", "%' and Nmsales LIKE '%", "$Nmsales") . "%') LIMIT 10";
		} else {
			$sql = "SELECT KdSales, Nmsales FROM sales WHERE Nmsales LIKE '%$Nmsales%' LIMIT 10 ";
		}
		return $this->db->query($sql)->result();
	}
	public function cari_salesxxx($Nmsales)
	{
		$this->db->like('NmSales', $Nmsales, 'both');
		$this->db->order_by('KdSales', 'ASC');
		$this->db->limit(10);
		return $this->db->get('sales')->result();
	}

	public function cari_brg($NmBrg)
	{
		if (strpos($NmBrg, " ") !== false) {
			$sql = "SELECT KdBrg, NmBrg, Barcode FROM barang Where (NmBrg LIKE '%" . str_replace(" ", "%' AND NmBrg LIKE '%", "$NmBrg") . "%') limit 20";
		} else {
			$sql = "SELECT KdBrg, NmBrg, Barcode FROM barang Where NmBrg LIKE '%$NmBrg%' limit 20";
		}
		return $this->db->query($sql)->result();
	}
	public function cari_brgxxx($NmBrg)
	{
		$this->db->like('NmBrg', $NmBrg, 'both');
		$this->db->order_by('KdBrg', 'ASC');
		$this->db->limit(20);
		return $this->db->get('barang')->result();
	}

	public function antrianpesan($id_user, $now, $before)
	{
		return $this->db->query("SELECT * FROM temp_mjual WHERE Flag='0' AND NmUser='$id_user' AND LEFT(NoJual,2) <> 'JL' AND NoJual LIKE '%W%' AND Tanggal BETWEEN '" . $before . "' AND  '" . $now . "' ORDER BY NoJual DESC");
	}

	public function daftarfakturjual($id_user, $now)
	{
		return $this->db->query("SELECT * FROM mjual WHERE NmUser='$id_user' AND LEFT(Tanggal,7) = LEFT('$now',7) ORDER BY NoJual DESC");
	}

	public function reprintStruk($nofaktur)
	{
		return $this->db->query("SELECT NoJual, Tanggal, KdCust, NamaCust, SubTotal, Potongan, PotonganRp, PPn, PPnRp, Bayar, Piutang, Ket, NmUser, TglJTP, Jam, KdSales, NamaSales, KdLokasi, Flag, Ket2, Ket3, IF((SubTotal-PotonganRp > 0 AND SubTotal-PotonganRp > Bayar) OR (SubTotal-PotonganRp < 0 AND SubTotal-PotonganRp < Bayar), (SubTotal-PotonganRp)-(Bayar), (Bayar)-(SubTotal-PotonganRp)) AS Kembali, IF((SubTotal-PotonganRp+PPnRp>0 AND SubTotal-PotonganRp+PPnRp>Bayar+Voucher+Transfer+Giro) OR (SubTotal-PotonganRp+PPnRp<0 AND SubTotal-PotonganRp+PPnRp<Bayar+Voucher+Transfer+Giro),'Piutang','Kembali') AS KetKembali  FROM mjual WHERE NoJual = '$nofaktur' ");
	}

	public function getProdukDijual($nofaktur)
	{
		$this->db->where('NoJual', $nofaktur);
		return $this->db->get('tjual');
	}


	public function cek_nopj($nofaktur)
	{
		return $this->db->query("SELECT MJ.NoJual, MJ.Tanggal, MJ.SubTotal, TJ.KdBrg, B.NmBrg, TJ.Jumlah, TJ.Disc, TJ.Sat, TJ.Harga, 
		TJ.Jumlah*(TJ.Harga-TJ.Disc) AS Total, MJ.KdCust, C.NmCust, C.Alamat, C.Telp, MJ.Jam, TJ.Keterangan, MJ.NmUser, B.Barcode, 
		IF(MJ.CetakNotaKe=0,'NOTA ASLI',CONCAT('NOTA COPY ',MJ.CetakNotaKe)) AS JenisNota FROM (tusd_202011.temp_tjual TJ 
		LEFT JOIN tusd_202011.Barang B ON TJ.KdBrg=B.KdBrg) LEFT JOIN (tusd_202011.temp_mjual MJ 
		LEFT JOIN tusd_202011.Customer C ON MJ.KdCust=C.KdCust) ON MJ.NoJual=TJ.NoJual WHERE MJ.NoJual='$nofaktur' 
		");
	}
}

/* End of file Kasir_model.php */
/* Location: ./application/models/Kasir_model.php */
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OpnameViaKirim_model extends CI_Model
{

  public function getNoFaktur($ymd, $id_user, $nm_user)
  {
    $q = $this->db->query("SELECT MAX(RIGHT(NoKirim,5)) AS id_max FROM mkirim WHERE LEFT(NoKirim,2) LIKE 'KR' AND NoKirim LIKE '%W%' AND substr(NoKirim,3,4) = '$ymd' AND NmUser = '$nm_user' ");
    $kd = "";
    $kodeawal = "KR";
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
  }

  public function getNo($id_user, $nm_user)
  {
    $q = $this->db->query("SELECT MAX(RIGHT(NoKirim,2)) AS id_max FROM temp_mkirim WHERE LEFT(NoKirim,2) <> 'KR' AND NoKirim LIKE '%W%' AND NmUser = '$nm_user' ");
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

  public function getDataOpname($noresi, $username)
  {
    $this->db->where('NoKirim', $noresi);
    $this->db->where('NmUser', $username);
    $this->db->where('Flag', '0');
    return $this->db->get('temp_mkirim');
  }

  public function getListOpname($noresi)
  {
    return $this->db->query("SELECT * FROM temp_tkirim WHERE NoKirim='$noresi' ORDER BY nomor DESC");
  }
  public function getListLokasi()
  {
    return $this->db->query(" SELECT * FROM Lokasi ");
  }

  public function getbarang($idbarang)
  {
    return $this->db->query("SELECT
        b.KdBrg,
        b.NmBrg,
        b.Barcode,
        b.Sat_1,
        b.Sat_2,
        b.Sat_3,
        b.Sat_4,
        b.Isi_2,
        b.Isi_3,
        b.Isi_4,
        b.Jasa,
        b.Ket1,
        s.KdLokasi,
        s.Akhir,
        b.Hrg_Beli_Akhir,
        b.Hrg_Beli_Rata,
        b.Hrg_Beli
        FROM
        barang AS b
        Left Join stocklokasi AS s ON b.KdBrg = s.KdBrg
        WHERE b.KdBrg = '$idbarang' or b.Barcode = '$idbarang'
        ");
  }
  public function viewstock($idbarang, $asal)
  {
    return $this->db->query("SELECT KdBrg, KdLokasi, Akhir FROM vstocklokasi WHERE KdBrg = '$idbarang' AND KdLokasi LIKE '%$asal%' ");
  }

  public function cari_brg($NmBrg)
  {
    $this->db->like('NmBrg', $NmBrg, 'both');
    $this->db->order_by('KdBrg', 'ASC');
    $this->db->limit(50);
    return $this->db->get('barang')->result();
  }

  public function cek_sudah_ada($kdb, $nofaktur)
  {
    return $this->db->query("SELECT * FROM temp_tkirim WHERE KdBrg='$kdb' AND NoKirim='$nofaktur'");
  }
  public function antrianopname($id_user, $now, $before)
  {
    return $this->db->query("SELECT * FROM temp_mkirim WHERE Flag='0' AND NmUser='$id_user' AND Tanggal BETWEEN '" . $before . "' AND  '" . $now . "' ORDER BY NoKirim DESC");
  }
}

/* End of file Kasir_model.php */
/* Location: ./application/models/Kasir_model.php */
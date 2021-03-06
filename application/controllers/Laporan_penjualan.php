<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_penjualan extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //validasi jika user belum login
        if ($this->session->userdata('masuk') != TRUE) {
            $url = base_url();
            redirect($url);
        }

        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('laporan_penjualan_model');
        $this->load->model('login_model');
        $this->load->helper('random');
    }

    public function laporan_fakturjual()
    {
        $tgl_sekarang = date('Y-m-d');
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $data['faktur'] = $this->laporan_penjualan_model->laporanfakturjual($tgl_sekarang);
        $data['no'] = 1;

        $this->load->view('header', $setting);
        $this->load->view('laporan_penjualan/laporan-fakturjual', $data);
        $this->load->view('footers');
    }
    public function laporan_detailjual()
    {
        $tgl_sekarang = date('Y-m-d');
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $data['detail'] = $this->laporan_penjualan_model->laporandetailjual();
        $data['no'] = 1;

        $this->load->view('header', $setting);
        $this->load->view('laporan_penjualan/laporan-detailjual', $data);
        $this->load->view('footers');
    }





    /* ===================================================================================================================================== */

    public function index()
    {
        $data['faktur'] = $this->laporan_penjualan_model->getpesanjualtanggal();
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/grafik', $data);
        $this->load->view('footer');
    }


    public function penjualan_transaksi()
    {
        //$KodeData = 'tusd_';
        $tanggal = date('Y-m-d');
        $filter = $this->input->get('filter');
        $a = $this->input->get('a'); //tanggal awal
        $b = $this->input->get('b'); //bulan awal
        $c = $this->input->get('c'); //tahun awal

        $d = $this->input->get('d'); //tanggal akhir
        $e = $this->input->get('e'); //bulan akhir
        $f = $this->input->get('f'); //tahun akhir
        $tgl_awal = $c . "-" . $b . "-" . $a;
        $tgl_akhir = $f . "-" . $e . "-" . $d;
        $data['tgl'] = date('d');
        $data['bln'] = date('m');
        $data['thn'] = date('Y');
        $data['no'] = 1;
        $data['subtot'] = 0;
        $data['diskon'] = 0;
        $data['grandtot'] = 0;
        $data['cash'] = 0;
        $data['debet'] = 0;
        $data['tanggal'] = $tanggal;
        $data['awal'] = $tgl_awal;
        $data['akhir'] = $tgl_akhir;
        $data['filter'] = $filter;
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $seting['seting'] = $this->login_model->seting()->row();

        if ($filter == "ok") {
            $data['penjualan'] = $this->laporan_penjualan_model->getDataPenjualanTransaksiFilter($tgl_awal, $tgl_akhir);
        } else {
            $data['penjualan'] = $this->laporan_penjualan_model->getDataPenjualanTransaksi($tanggal);
        }
        $this->load->view('header', $seting);
        $this->load->view('laporan/penjualan/penjualan_transaksi', $data);
        $this->load->view('footer');
    }

    public function penjualan_barang()
    {
        //$KodeData = 'tusd_';
        $KodeData = substr($this->db->database, 0, 5);
        $tanggal = date('Y-m-d');
        $filter = $this->input->get('filter');
        $a = $this->input->get('a'); //tanggal awal
        $b = $this->input->get('b'); //bulan awal
        $c = $this->input->get('c'); //tahun awal

        $d = $this->input->get('d'); //tanggal akhir
        $e = $this->input->get('e'); //bulan akhir
        $f = $this->input->get('f'); //tahun akhir
        $tgl_awal = $c . "-" . $b . "-" . $a;
        $tgl_akhir = $f . "-" . $e . "-" . $d;
        // tambahan
        $bawal = $b;
        $bakhir = $e;
        $tawal = $c;
        $takhir = $f;


        $data['tgl'] = date('d');
        $data['bln'] = date('m');
        $data['thn'] = date('Y');
        $data['no'] = 1;
        $data['tot'] = 0;
        $data['tanggal'] = $tanggal;
        $data['awal'] = $tgl_awal;
        $data['akhir'] = $tgl_akhir;
        $data['filter'] = $filter;

        if ($filter == "ok") {
            $data['penjualan'] = $this->laporan_penjualan_model->getDataPenjualanBarangFilter($tawal, $takhir, $bawal, $bakhir, $KodeData);
        } else {
            $data['penjualan'] = $this->laporan_penjualan_model->getDataPenjualanBarang($tanggal);
        }
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/penjualan_barang', $data);
        $this->load->view('footer');
    }

    public function profit()
    {
        $tanggal = date('Y-m-d');
        $filter = $this->input->get('filter');
        $startdate = $this->input->get('startdate'); //tahun bulan tanggal awal
        $enddate = $this->input->get('enddate'); //tahun bulan tanggal akhir

        if ($filter == "ok") {
            if ($startdate == "" or $enddate == "") {
                $data['no'] = 1;
                $data['noo'] = 1;
                $data['tot_item'] = 0;
                $data['tot_modal'] = 0;
                $data['tot_pendapatan'] = 0;
                $data['tot_profit'] = 0;
                $data['totbiaya'] = 0;
                $data['tanggal'] = $tanggal;
                $data['awal'] = $startdate;
                $data['akhir'] = $enddate;
                $data['filter'] = $filter;
                $data['profit'] = $this->laporan_penjualan_model->getDataProfit1($tanggal);
                $data['subdiskon'] = $this->laporan_penjualan_model->getDiskonBarang1($tanggal)->row();
                $data['subdisakhir'] = $this->laporan_penjualan_model->getDiskonAkhir1($tanggal)->row();
            } else {
                $data['no'] = 1;
                $data['noo'] = 1;
                $data['tot_item'] = 0;
                $data['tot_modal'] = 0;
                $data['tot_pendapatan'] = 0;
                $data['tot_profit'] = 0;
                $data['totbiaya'] = 0;
                $data['tanggal'] = $tanggal;
                $data['awal'] = $startdate;
                $data['akhir'] = $enddate;
                $data['filter'] = $filter;
                $data['profit'] = $this->laporan_penjualan_model->getDataProfit($startdate, $enddate);
                $data['subdiskon'] = $this->laporan_penjualan_model->getDiskonBarang($startdate, $enddate)->row();
                $data['subdisakhir'] = $this->laporan_penjualan_model->getDiskonAkhir($startdate, $enddate)->row();
            }
        } else {
            $data['no'] = 1;
            $data['noo'] = 1;
            $data['tot_item'] = 0;
            $data['tot_modal'] = 0;
            $data['tot_pendapatan'] = 0;
            $data['tot_profit'] = 0;
            $data['totbiaya'] = 0;
            $data['tanggal'] = $tanggal;
            $data['awal'] = $startdate;
            $data['akhir'] = $enddate;
            $data['filter'] = $filter;
            $data['profit'] = $this->laporan_penjualan_model->getDataProfit1($tanggal);
            $data['subdiskon'] = $this->laporan_penjualan_model->getDiskonBarang1($tanggal)->row();
            $data['subdisakhir'] = $this->laporan_penjualan_model->getDiskonAkhir1($tanggal)->row();
        }

        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/profit', $data);
        $this->load->view('footers');
    }

    public function rekap_perbarang()
    {
        $data['year'] = date('Y');
        $data['bulan'] = date('m');
        $data['tahun'] = $this->laporan_penjualan_model->getTahunJual()->result_array();
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/pilih_bulan_barang', $data);
        $this->load->view('footers');
    }
    public function rekap_penjualan_perbarang()
    {
        $KodeData = substr($this->db->database, 0, 5);
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $data_rekap = $this->laporan_penjualan_model->getDataRekapPerbarang($KodeData, $tahun, $bulan)->result();
        $data['diskon'] = $this->laporan_penjualan_model->getDiskon($KodeData, $tahun, $bulan)->result();
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['rekap'] = $data_rekap;
        $data['aa'] = 0;
        $data['bb'] = 0;
        $data['cc'] = 0;
        $data['dd'] = 0;
        $data['ee'] = 0;

        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();
        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/lap_rekap_perbarang', $data);
        $this->load->view('footers');
    }


    public function rekap()
    {
        $data['year'] = date('Y');
        $data['bulan'] = date('m');
        $data['tahun'] = $this->laporan_penjualan_model->getTahunJual()->result_array();
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/pilih_bulan', $data);
        $this->load->view('footers');
    }

    public function rekap_penjualan()
    {
        $KodeData = substr($this->db->database, 0, 5);
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $data_rekap = $this->laporan_penjualan_model->getDataRekap($KodeData, $tahun, $bulan)->result();
        $data['diskon'] = $this->laporan_penjualan_model->getDiskon($KodeData, $tahun, $bulan)->result();
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['rekap'] = $data_rekap;
        $data['aa'] = 0;
        $data['bb'] = 0;
        $data['cc'] = 0;
        $data['dd'] = 0;
        $data['ee'] = 0;
        $data['ff'] = 0;
        $data['gg'] = 0;
        $data['tot'] = 0;
        $data['tot_a'] = 0;
        $data['tot_b'] = 0;
        $data['tot_c'] = 0;
        $data['tot_d'] = 0;
        $data['tot_tot'] = 0;
        //$data['biaya'] = $this->laporan_penjualan_model->getDataPengeluaranRekapitulasi($tahun, $bulan);
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/lap_rekap', $data);
        $this->load->view('footer');
    }


    public function pilih_rekap_tahun()
    {
        $data['year'] = date('Y');
        $data['bulan'] = date('m');
        $data['tahun'] = $this->laporan_penjualan_model->getTahunJual()->result_array();
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/pilih_rekap_tahun', $data);
        $this->load->view('footers');
    }
    public function rekap_penjualan_pertahun()
    {
        $KodeData = substr($this->db->database, 0, 5);
        $tahun = $this->input->post('tahun');
        //$bulan = $this->input->post('bulan');
        $data_rekap = $this->laporan_penjualan_model->getDataRekapTahun($KodeData, $tahun)->result();
        $data['diskon'] = $this->laporan_penjualan_model->getDiskonTahun($KodeData, $tahun)->result();
        //$data['toko'] = $this->laporan_penjualan_model->get_toko();
        $data['tahun'] = $tahun;
        //$data['bulan'] = $bulan;
        $data['rekap'] = $data_rekap;
        $data['aa'] = 0;
        $data['bb'] = 0;
        $data['cc'] = 0;
        $data['dd'] = 0;
        $data['ee'] = 0;
        $data['ff'] = 0;
        $data['gg'] = 0;
        $data['tot'] = 0;
        $data['tot_a'] = 0;
        $data['tot_b'] = 0;
        $data['tot_c'] = 0;
        $data['tot_d'] = 0;
        $data['tot_tot'] = 0;
        //$data['biaya'] = $this->laporan_penjualan_model->getDataPengeluaranRekapitulasi($tahun, $bulan);
        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/lap_rekap_pertahun', $data);
        $this->load->view('footer');
    }




    /* ====================================================================================== */
















    public function laporan_faktur_jual()
    {
        $KodeData = substr($this->db->database, 0, 5);
        $tanggal = date('Y-m-d');
        $filter = $this->input->get('filter');
        $startdate = $this->input->get('startdate'); //tahun bulan tanggal awal
        $enddate = $this->input->get('enddate'); //tahun bulan tanggal akhir

        $bawal = substr($startdate, 5, 2);
        $awal = substr($startdate, 0, 4);
        $bakhir = substr($startdate, 5, 2);
        $akhir = substr($startdate, 0, 4);

        $username = $this->session->userdata('ses_username');
        $setting['user'] = $this->login_model->sistemuser($username)->row();
        $setting['seting'] = $this->login_model->seting()->row();
        if ($filter == "ok") {
            if ($startdate == "" or $enddate == "") {
                $data['no'] = 1;
                $data['subtot'] = 0;
                $data['tanggal'] = $tanggal;
                $data['awal'] = $tanggal;
                $data['akhir'] = $tanggal;
                $data['filter'] = $filter;
                $data['penjualan'] = $this->laporan_penjualan_model->getLaporanFakturPenjualan($tanggal);
            } else {
                $data['no'] = 1;
                $data['subtot'] = 0;
                $data['tanggal'] = $tanggal;
                $data['awal'] = $startdate;
                $data['akhir'] = $enddate;
                $data['filter'] = $filter;
                $data['penjualan'] = $this->laporan_penjualan_model->getLaporanFakturPenjualanFilter($KodeData, $awal, $akhir, $bawal, $bakhir, $startdate, $enddate);
            }
        } else {
            $data['no'] = 1;
            $data['subtot'] = 0;
            $data['tanggal'] = $tanggal;
            $data['awal'] = $tanggal;
            $data['akhir'] = $tanggal;
            $data['filter'] = $filter;
            $data['penjualan'] = $this->laporan_penjualan_model->getLaporanFakturPenjualan($tanggal);
        }

        $this->load->view('header', $setting);
        $this->load->view('laporan/penjualan/laporan_faktur_penjualan', $data);
        $this->load->view('footers');
    }

    public function laporan_pdf()
    {
        $data = array(
            "dataku" => array(
                "nama" => "Petani Kode",
                "url" => "http://petanikode.com"
            )
        );

        $data['profit'] = $this->laporan_penjualan_model->laporanpdf();

        $this->load->library('pdf');

        $this->pdf->setPaper('A4', 'potrait');
        $this->pdf->filename = "laporan-petanikode.pdf";
        $this->pdf->load_view('laporan/laporan_pdf', $data);
    }
}

/* End of file Laporan.php */
/* Location: ./application/controllers/Laporan.php */
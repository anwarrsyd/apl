<?php

namespace App\Http\Controllers;

//use Request;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Uuid;
use Auth;
use Input;
use Session;
use Validator;
use DB;
use App\pesertadidik;
use App\dailylog;
use App\companylist;
use App\kelompok;
use App\kelompokkp;
use App\ajuankp;


class HomeController extends Controller
{
 public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
  public function dashboard(){
    return view('index');
  }
    public function proposal(){
      
      $iduser=Auth::user()->id;
      $ajuankp=ajuankp::where('id_pd','=',$iduser)->first();
      $idajuankp=$ajuankp['id_ajuan_kp'];
      $idkelompok=$ajuankp['id_kel_pd'];
      $iddudi=$ajuankp['id_dudi'];
      $data=array();
      $data['kelompok']=kelompok::where('id_kel_pd','=',$idkelompok)->get();
      $data['dudi']=companylist::where('id_dudi','=',$iddudi)->get();
      $data['ajuan']=ajuankp::where('id_pd','=',$iduser)->get();

      //dd($data);
  return view('internship-proposal',$data);
  }

  public function intern(){
    $data=array();
    $data['list']=companylist::get();
    $data['mahasiswa']=pesertadidik::get();
    return view('internship-form',$data);
  }
  public function dailylog($id_akt=null){
    /*GET LOGS FROM CURRENT USER*/
    //get iduser from session
    $iduser=Auth::user()->id;
    //get idkp from iduser
    $idkp = ajuankp::where('id_pd','=',$iduser)->first();
    //get all the logs
    $data['logs']=dailylog::where('id_ajuan_kp','=',$idkp->id_ajuan_kp)->get();
    /*CHECK IF ALREADY SELECTED*/
    // if($id_akt){
    //   $idActivity=$id_akt;
    //   $data['activity'] = dailylog::where('id_akt_kp','=',$idActivity)->first();
    // }
    // dd($data);    
    return view ('daily-log',$data);
  }
  public function selectdailylog($id_akt){
    $idActivity=$id_akt;
    $data = dailylog::where('id_akt_kp','=',$idActivity)->first();
    // dd($data);
    return view ('daily-log',$data);
  }
  public function daftarkp(){
        $data=input::all();
        $idkelompok=Uuid::generate();
        $idajuankp=Uuid::generate();
        $iduser1=Auth::user()->id;
        $tanggal=date('y-m-d');
        kelompokkp::insert(array(
            'id_kel_pd' => $idkelompok,
            'id_pd1'    => $iduser1,
            'id_pd2'    => $data['friend'],
            'created_at'=> $tanggal
        ));

        ajuankp::insert(array(
            'id_ajuan_kp'     => $idajuankp,
            'id_pd'           => $iduser1,
            'id_dudi'         => $data['perusahaan'],
            'id_kel_pd'       => $idkelompok,
            'tgl_ajuan'       => $tanggal,
            'tanggal_mulai'   => $data['tanggalmulai'],
            'tanggal_selesai' => $data['tanggalselesai']
          ));

        kelompok::insert(array(
          'id_kel_pd' => $idkelompok,
          'nm_kel'    => $data['namakelompok']
          ));
     
    Session::flash('sukses','Login anda gagal, silahkan cek kembali username dan password');
    return redirect('internform');
                
  }

  public function listperusahaan(){
    $data=array();
    $data['list']=companylist::get();
    return view('company-list',$data);
  }

  public function posdailylog(){
    $data=input::all();
    $idlog=Uuid::generate();
    $iduser=Auth::user()->id;
    $idkp = ajuankp::where('id_pd','=',$iduser)->first();
    // dd($data);
      dailylog::insert(array(
            'id_akt_kp'=> $idlog,
            'id_ajuan_kp'=> $idkp->id_ajuan_kp,
            'konten'=> $data['konten'],
            'tanggal'=>$data['tanggal']
            ));
      return redirect('dailylog');
            
  }
  

  public function tambahperusahaan(){
     $data=input::all();
     $id = Uuid::generate();
      companylist::insert(array(
            'id_dudi'   => $id,
            'nm_lemb'   => $data['nama'],
            'jl'        => $data['alamat'],
            'jabatan_pic'=> $data['jabatan'],
            'profil'    => $data['profil'],
            'telpon'    => $data['telpon'],
            'jenis'     => $data['jenisbisnis'],
            'pic'       => $data['personincharge']
            ));
       
       session::flash('registersukses1','ok');
       return redirect('companylist');
  }

  public function login(Request $request){
      // $credentials = Input::only('username','password');
            $new = $request->only('nrp','password');
            // $this->data['username'] = Input::get('username');
            
            // dd($credentials);

            if (Auth::attempt($new,true))
            {
                   
               
                    $id=Auth::user()->nm_pd;
                
                return redirect()->intended('dashboard');
                //return $id;
                
                   
                //return 'asdfjhdsafasdf';
            }
            else{

                Session::flash('message','Login anda gagal, silahkan cek kembali username dan password');
                //return redirect('/');
                return 'sempak';

            } 
            // return redirect('loginadmin');
  }

  public function register(){
    // return Uuid::generate();
    return view('register');
  }

  public function regisform(){
    $data=input::all();
    $id = Uuid::generate();
    $password=bcrypt( $data['password']);
    

    pesertadidik::insert(array(
          'id'     => $id,
          'nm_pd'     =>$data['nama'],
          'jk'        =>$data['jeniskelamin'],
          'tgl_lahir' =>$data['tanggallahir'],
          'nrp'       =>$data['nrp'],
          'email'     =>$data['email'],
          'no_hp'     =>$data['telpon'],
          'password'  =>$password
          ));  
     session::flash('registersukses','ok');
     return redirect('/');

  }

}

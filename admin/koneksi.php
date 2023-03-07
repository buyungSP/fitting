<?php
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    //header('Content-Type: text/plan; charset=utf-8');
    header('Access-Control-Allow-Headers: X-Requested-With,Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding');
    header('Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE');
    clearstatcache();
    session_start();
    date_default_timezone_set("Asia/Jakarta");
    class Login{
        public bool $status;
        public string $username;
        private string $password;
        public string $header;
        public function __construct(){
            $this->status = $_SESSION['login'];
        }
        public function __destruct(){
            $page = new Page();
            if($_SESSION["login"]){
                $this->header = $page->sidebar();
                if(isset($_GET['keluar'])){
                    session_unset();
                    session_destroy();
                    $page->getLocation("/admin?out");
                }elseif(isset($_GET['gallery'])){
                    include 'gallery.php';
                    new Gallery();
                }elseif(isset($_GET['img'])){
                    include 'img.php';
                    new Img();
                }elseif(isset($_GET['edit'])){
                    include 'edit.php';
                    new Edit();
                }else{
                    include 'home.php';
                    new Home();
                }
            }else{
                $login = $page->login();
                if(isset($_GET['error'])){
                    echo '<script> alert("Username / Password salah")</script>';
                    print $login;
                }elseif(isset($_GET['keluar'])){
                    echo '<script> alert("Success logout")</script>';
                    print $login;
                }elseif(isset($_GET['login'])){
                    $a = $_POST["email"];
                    $b = $_POST["password"];
                    if(empty($a)||empty($b)){
                        $page->getLocation("/admin?error");
                    }else{
                        $json_data = json_decode(file_get_contents('login.json'),true);
                        $this->username = $json_data["email"];
                        if($a == $this->username||$b == $json_data["password"]){
                            $this->status = true;
                            $_SESSION['login'] = true;
                            $_SESSION['username'] = $a;
                            $page->getLocation("/admin");
                        }else{
                            $page->getLocation("/admin?error");
                        }
                    }
                }else{
                    print $login;
                }
            }
        }
    }
    class Database{
        public $db;
        public function __construct(){
            $this->db = (array)json_decode(file_get_contents('db.json'),true);
        }
        public function delete($a,$b){
            unset($this->db[$a][img][(int)$b]);
            sort($this->db[$a][img]);
            return file_put_contents("db.json",json_encode($this->db));
        }
        public function deleteP($a){
            unset($this->db[footer][phone][(int)$a]);
            sort($this->db[footer][phone]);
            return file_put_contents("db.json",json_encode($this->db));
        }
        public function deleteL($a){
            unset($this->db[menu][link][(int)$a]);
            sort($this->db[menu][link]);
            return file_put_contents("db.json",json_encode($this->db));
        }
        public function listMenuButton(){
            $d = "";
            foreach($this->db as $b => $c){
                $d .= '<li class="nav-item">
                        <a class="nav-link" href="?#'.$b.'" data-page="'.$b.'">
                            <span>'.$b.'</span>
                        </a>
                    </li>';
            }
            return $d;
        }
        public function findSection($a):iterable{
            return $this->db[$a];
        }
    }
    class Page extends Database{
        public function sidebar(){
            return '
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse pt-2">
                    <div class="container-fluid text-capitalize">
                        <a class="navbar-brand" href="?">
                            <h1 class="font-weight-bold text-primary">Smooth</h1>
                        </a>
                    </div>
                    <div class="position-sticky sidebar-sticky">
                        <ul class="nav nav-tabs flex-column mb-2">'.$this->listMenuButton().'
                        </ul>
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>general</span>
                            <a class="d-flex align-items-center text-muted" href="#">
                            </a>
                        </h6>
                        <ul class="nav nav-tabs flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link" href="?#setting" data-page="setting">
                                    <span>setting</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="/" target="_blank">
                                    <span>Preview</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="?keluar">
                                    <span>logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>';
        }
        public function dropdown(){
            foreach($this->db as $key => $value){
                if(is_array($value["img"])){
                    $a .= '<li><a class="dropdown-item" href="?gallery='.$key.'">'.$key.'</a></li>';
                }
            }
            return $a;
        }
        public function login(){
            return <<<HTML
                    <section class="container">
                        <div class="py-5">
                            <div class="container text-capitalize">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mt-5">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Smooth home Production</h5>
                                                <h3 class="card-subtitle font-weight-bold">Login Panel Admin</h3>
                                            </div>
                                            <form action="admin/index.php?login" class="panel-body wrapper-lg" method="post">
                                                <div class="card-body">
                                                    <div class="form-group mb-4">
                                                        <label class="control-label">Email</label>
                                                        <input type="text" name="email" placeholder="test@example.com" class="form-control input-lg">
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label class="control-label">Password</label>
                                                        <input type="password" name="password" id="inputPassword" placeholder="Password" class="form-control input-lg">
                                                    </div>
                                                       <button type="submit" class="btn btn-primary btn-block form-control">Sign in</button>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="card-text">Copyright 2023 <span class="font-weight-bold">Smooth home Production</span></div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                HTML;
        }
        public function getLocation($a){
            header('Location: '.$a);
        }
        public function text($a,$b){
            return $this->findSection($a)[$b];
        }
        public function listText($a){
            return implode(",",$this->findSection($a)["textList"]);
        }
        public function listImg($a):iterable{
            return $this->findSection($a)["img"];
        }
        public function getButton($a):iterable{
            return $this->findSection($a)["button"];
        }
        public function getPhone($a):iterable{
            return $this->findSection($a)["phone"];
        }
        public function getMenu($a):iterable{
            return $this->findSection($a)["link"];
        }
        private function getImgArray($a){
            $b = "";
            foreach ($this->listImg($a) as $key => $value) {
                $b .= "<tr><th scope='row' class='table-dark'>".($key + 1)."</th><td>".$value['alt']."</td><td><a href='".$value['src']."' target='_blank'>link</a></td></tr>";
            }
            return $b;
        }
        private function formatPhoneNumber($s) {
            $rx = "/(62)?\D*(\d{2})?\D*(\d{4})?\D*(\d*)/x";
            preg_match($rx, (int)$s, $matches);
            if(!isset($matches[0])) return false;
            $out = "+62".$matches[2]." ".$matches[3]." ".$matches[4];
            return $out;
        }
        private function getArray($a){
            $b = "";
            foreach ($this->getPhone($a) as $key => $value) {
                $b .= "<tr><th scope='row' class='table-dark'>".($key + 1)."</th><td>".$value['name']."</td><td>".$this->formatPhoneNumber($value['phone'])."</td><td><a class='btn btn-success' href='?edit=phone&section=footer&id=".$key."'><span class='fa fa-edit'></span></a><button class='btn btn-danger' data-id='".$key."' data-button='phone'><span class='fa fa-remove'></span></button></td></tr>";
            }
            return $b;
        }
        private function getArrayMenu($a){
            $b = "";
            foreach ($this->getMenu($a) as $key => $value) {
                $b .= "<tr><th scope='row' class='table-dark'>".($key + 1)."</th><td>".$value['alt']."</td><td>".$value['href']."</td><td><a class='btn btn-success' href='?edit=link&section=menu&id=".$key."'><span class='fa fa-edit'></span></a><button class='btn btn-danger' data-id='".$key."' data-button='menu'><span class='fa fa-remove'></span></button></td></tr>";
            }
            return $b;
        }
        public function section($a){
            $d = $this->findSection($a);
            foreach($d as $b => $e){
                switch($b){
                    case "title":
                        $f = match($a){
                            "section1" => 'konveksi <span class="text-primary">'.$this->text($a,$b).'</span> <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section2" => 'custom <span class="text-primary">'.$this->text($a,$b).'</span> kamu ? bikin di fitting indonesia aja !<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section4" => '4 langkah mudah produksi <span class="text-primary">'.$this->text($a,$b).'</span> custom di fitting indonesia <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section5" => 'konveksi yang bisa customize <span class="text-primary">'.$this->text($a,$b).'</span>  sesuai keinginanmu ! <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section6" => 'ragu akan kualitas produksi ?',
                            "section9" => 'Produksi <span class="text-primary">'.$this->text($a,$b).'</span> sesuai keinginan kamu dan percayakan kualitas terbaik pada kami.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section10" => 'proses produksi <span class="text-primary">'.$this->text($a,$b).'</span> fitting indonesia<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "footer" => '<strong>'.$b.' : </strong><span class="text-primary">'.$this->text($a,$b).'</span><a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            default => null
                        };
                        $c .= '<div class="card-text">'.$f.'</div>';
                        break;
                    case "subtitle":
                        $f = match($a){
                            "section1" => 'konveksi <span class="text-primary">'.$this->text($a,$b).'</span> <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section2" => 'konveksi <span class="text-primary">'.$this->text($a,$b).'</span> yang siap terima orderan Kamu dan Kami kirim ke Seluruh Indonesia.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section4" => 'Fitting Indonesia, Specially Fitted For You! Kami, Fitting Indonesia mendukung kebutuhan <span class="text-primary">'.$this->text($a,$b).'</span> sesuai keinginan, Fittmate.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            "section6" => 'Jangan khawatir, kami kirimkan sample bahan dan produk sebagai cerminan kualitas produksi kami. Dapatkan jaminan kesesuaian harga dengan pemilihan bahan dan kualitas jahitan baju custom kamu dari kami, Fitting Indonesia.',
                            "section9" => 'Diproduksi dengan kain yang berkualitas, premium, dan sesuai dengan kebutuhan fashion kamu sehingga nyaman digunakan dan melengkapi gaya fashion Fittmate. Ciptakan gaya yang simple dan stylish dengan beragam <span class="text-primary">'.$this->text($a,$b).'</span> produksi Fitting Indonesia. Tersedia dalam berbagai macam ukuran dan corak warna yang sesuai dengan kebutuhan gaya kamu.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            default => null
                        };
                        $c .= '<div class="card-text">'.$f.'</div>';
                        break;
                    case "text":
                        $f = match($a){
                            "section2" => 'yuk ngonbrol dengan super Fitteam kami untuk realisasikan Produk Custom sesuai idamanmu. Percayakan pemilihan bahan dan kualitas produk pada kami.',
                            "section9" => '<span class="text-primary">'.$this->text($a,$b).'</span> <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            default => null
                        };
                        $c .= '<div class="card-text">'.$f.'</div>';
                        break;
                    case "text1":
                        $f = match($a){
                            "section4" => 'Kami adalah sebuah perusahaan yang bergerak di bidang <span class="text-primary">'.$this->text($a,$b).'</span> dan produksi pakaian custom.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            default => null
                        };
                        $c .= '<div class="card-text">'.$f.'</div>';
                        break;
                    case "text2":
                        $f = match($a){
                            "section4" => 'Dengan senang hati, kami akan membantu menyediakan kebutuhan <span class="text-primary">'.$this->text($a,$b).'</span> kamu.<a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a>',
                            default => null
                        };
                        $c .= '<div class="card-text">'.$f.'</div>';
                        break;
                    case "button":
                        $c .= '<div class="card-text"><strong>button :</strong> <span class="text-primary" id="button">'.$this->getButton($a)['title'].'</span> / <span class="text-danger" id="link">'.$this->getButton($a)['href'].'</span> <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a></div>';
                        break;
                    case "google":
                    case "email":
                    case "address":
                    case "whatsapp":
                    case "description":
                        $f = match($b){
                            "google" => "google site verification",
                            "whatsapp" => "Floating button whatsapp",
                            default => $b
                        };
                        $c .= '<div class="card-text"><strong>'.$f.' : </strong><span class="text-primary">'.$this->text($a,$b).'</span><a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a></div>';
                        break;
                    case "textList":
                        $c .= '<div class="card-text">produksi <span class="text-primary">'.$this->listText($a).'</span> <a class="text-success" href="?edit='.$b.'&section='.$a.'"><span class="fa fa-edit"></span></a></div>';
                        break;
                    case "img":
                        $g = '
                            <div class="card-footer table-responsive p-0">
                                <table class="table table-striped table-light table-hover caption-top">
                                    <caption class="p-2">
                                        <div class="row m-0 w-100">
                                            <strong class="text-left col-6">'.$b.'</strong>
                                            <div class="text-right col-6"><a class="text-primary" href="?gallery='.$a.'">more <span class="fa fa-arrow-right"></span></a></div>
                                        </div>
                                    </caption>
                                    <thead class="thead-dark">
                                        <tr class="table-dark">
                                            <th class="font-weight-bold" scope="col">no</th>
                                            <th class="font-weight-bold" scope="col">alt</th>
                                            <th class="font-weight-bold" scope="col">preview</th>
                                        </tr>
                                    </thead>
                                    <tbody class="thead-dark">'.$this->getImgArray($a).'
                                    </tbody>
                                </table>
                            </div>';
                        break;
                    case "phone":
                    case "link":
                        $f = match($b){
                            "phone" => $this->getArray($a),
                            "link" => $this->getArrayMenu($a),
                        };
                        $i = '
                            <div class="card-footer table-responsive p-0">
                                <table class="table table-striped table-light table-hover caption-top">
                                    <caption class="p-2">
                                        <div class="row m-0 w-100">
                                            <strong class="text-left col-6">'.$b.'</strong>
                                            <div class="text-right col-6"><a class="text-primary" href="?edit='.$b.'&section='.$a.'">new <span class="fa fa-plus"></span></a></div>
                                        </div>
                                    </caption>
                                    <thead class="thead-dark">
                                        <tr class="table-dark">
                                            <th class="font-weight-bold" scope="col">no</th>
                                            <th class="font-weight-bold" scope="col">name</th>
                                            <th class="font-weight-bold" scope="col">'.$b.'</th>
                                            <th class="font-weight-bold" scope="col">menu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="thead-dark">'.$f.'
                                    </tbody>
                                </table>
                            </div>';
                        break;
                }
            }
            return '
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-2">
                        <div class="card" id="'.$a.'">
                            <div class="card-header">
                                <h2 class="card-title">'.$a.'</h2>
                            </div>
                            <div class="card-body">'.$c.'</div>'.$g.''.$i.'
                        </div>
                    </div>';
        }
        public function home(){
            $c = "";
            foreach($this->db as $a => $b){
                $c .= $this->section($a);
            }
            return $c;
        }
    }
?>
<?php
	require 'koneksi.php';
	$post = $_POST;
	if(isset($_GET["delete"])){
	    $db = new Database();
	    if($_GET["delete"] == "phone"){
		    if($db->deleteP($_POST["id"])){
				createHTML();
		    }else{
		    	echo "error";
		    }
	    }elseif($_GET["delete"] == "link"){
		    if($db->deleteL($_POST["id"])){
				createHTML();
		    }else{
		    	echo "error";
		    }
	    }else{
		    if($db->delete($_POST["section"],$_POST["id"])){
				createHTML();
		    }else{
		    	echo "error";
		    }
	    }
	}else{
		if((int)$post["img"]){
			if(isset($post["file"])){
				$pos  = strpos($post['file'], ';');
				$imgExten = explode('/', substr($post['file'], 0, $pos))[1];
				$extens = ['jpg', 'jpe', 'jpeg', 'jfif', 'png', 'bmp', 'dib', 'gif' ];
				if(in_array($imgExten, $extens)) {
					$b = explode("?",$post["name"])[0];
					$filepath = "img/".$b;
					$imgCont = explode(',', $post['file']);
				    $im = imagecreatefromstring(base64_decode($imgCont[1]));
				    $width = imagesx($im);
				    $height = imagesy($im);
				    switch($post["section"]){
				    	case "section1":
				    	case "section2":
				    		$c = 640;
				    		break;
				    	case "section6":
				    		switch((int)$post["id"]){
				    			case 0:
						    		$c = 640;
				    				break;
				    			default:
						    		$c = 1024;
				    				break;
				    		}
				    		break;
				    	case "footer":
				    		switch((int)$post["id"]){
				    			case 0:
						    		$c = 15;
				    				break;
				    			default:
						    		$c = 300;
				    				break;
				    		}
				    		break;
				    	default:
				    		$c = 300;
				    		break;
				    }
				    $d = $width/$height;
				    $newwidth = $d * $c;
				    $thumb = imagecreatetruecolor($newwidth, $c);
				    imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
					imagealphablending($thumb, false);
					imagesavealpha($thumb, true);
				    imagecopyresampled($thumb, $im, 0, 0, 0, 0, $newwidth, $c, $width, $height);
				    imagewebp($thumb,$filepath);
					if(isset($post["id"])){
						saveJsonImg($post);
					}else{
						newJsonImg($post);
					}
				}
			}else{
				saveJsonImg($post);
			}
		}else{
			saveJson($post);
		}
	}
	function saveJsonImg($a){
    	$dbJSON = (array)json_decode(file_get_contents('db.json'), true);
    	if($a["section"] == "section7"){
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["alt"] = $a["title"];
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["title"] = $a["title"];
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["subtitle"] = $a["subtitle"];
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["text"] = $a["text"];
    	}elseif($a["section"] == "section5"){
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["alt"] = $a["alt"];
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["href"] = $a["href"];
    	}else{
			$dbJSON[$a["section"]]["img"][(int)$a["id"]]["alt"] = $a["alt"];
    	}
		$times = time();
		if(isset($a["name"])){
			$b = explode("?",$a["name"])[0];
	    	$dbJSON[$a["section"]]["img"][(int)$a["id"]]["src"] = "https://".$_SERVER['HTTP_HOST']."/admin/img/".$b."?".$times;
		}
    	if(file_put_contents("db.json",json_encode($dbJSON))){
    		createHTML();
    	}else{
    		echo false;
    	}
	}
	function newJsonImg($a){
    	$dbJSON = (array)json_decode(file_get_contents('db.json'), true);
    	$times = time();
    	if($a["section"] == "section7"){
    		$b = explode("?",$a["name"])[0];
	    	array_push($dbJSON[$a["section"]]["img"], array(
	    		alt => $a["title"],
	    		title => $a["title"],
	    		subtitle => $a["subtitle"],
	    		text => $a["text"],
	    		src => "https://".$_SERVER['HTTP_HOST']."/admin/img/".$b."?".$times
	    	));
    	}elseif($a["section"] == "section5"){
    		$b = explode("?",$a["name"])[0];
	    	array_push($dbJSON[$a["section"]]["img"], array(
	    		alt => $a["alt"],
	    		href => $a["href"],
	    		src => "https://".$_SERVER['HTTP_HOST']."/admin/img/".$b."?".$times
	    	));
    	}else{
	    	array_push($dbJSON[$a["section"]]["img"], array(
	    		alt => $a[alt],
	    		src => "https://".$_SERVER['HTTP_HOST']."/admin/img/".$a["name"]."?".$time
	    	));
    	}
    	if(file_put_contents("db.json",json_encode($dbJSON))){
    		createHTML();
    	}else{
    		echo false;
    	}
	}
	function saveJson($a){
    	$dbJSON = (array)json_decode(file_get_contents('db.json'), true);
    	switch($a["edit"]){
    		case "textList":
		    	$dbJSON[$a["section"]][$a["edit"]] = explode(",",$a["textList"]);
    			break;
    		case "button":
		    	$dbJSON[$a["section"]][$a["edit"]]["title"] = $a["title"];
		    	$dbJSON[$a["section"]][$a["edit"]]["href"] = $a["href"];
    			break;
    		case "phone":
    			if(isset($a["id"])){
			    	$dbJSON[$a["section"]][$a["edit"]][$a["id"]]["name"] = $a["name"];
			    	$dbJSON[$a["section"]][$a["edit"]][$a["id"]]["phone"] = $a["phone"];	
    			}else{
			    	array_push($dbJSON[$a["section"]][$a["edit"]], array(
			    		name => $a["name"],
			    		phone => $a["phone"]
			    	));
    			}
    			break;
    		case "link":
    			if(isset($a["id"])){
			    	$dbJSON[$a["section"]][$a["edit"]][$a["id"]]["alt"] = $a["alt"];
			    	$dbJSON[$a["section"]][$a["edit"]][$a["id"]]["href"] = $a["href"];	
    			}else{
			    	array_push($dbJSON[$a["section"]][$a["edit"]], array(
			    		alt => $a["alt"],
			    		href => $a["href"]
			    	));
    			}
    			break;
    		default:
		    	$dbJSON[$a["section"]][$a["edit"]] = $a["alt"];
    			break;
    	}
    	if(file_put_contents("db.json",json_encode($dbJSON))){
    		createHTML();
    	}else{
    		echo false;
    	}
	}
	function sliderImg($a,$b,$c){
		if($b){
			$d = '<div class="carousel-indicators">
                <button type="button" data-bs-target="#'.$c.'" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#'.$c.'" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#'.$c.'" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>';
            $e = '<button class="carousel-control-prev" type="button" data-bs-target="#'.$c.'" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#'.$c.'" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>';
			$f = '<div id="'.$c.'" class="carousel slide" data-bs-ride="carousel">
					'.$d.'
	                <div class="carousel-inner">'.$a.'</div>'.$e.'
	            </div>';
		}else{
			$f = '<div id="'.$c.'">'.$a.'</div>';
		}
        return $f;
	}
	function arrayImg($a,$d){
		$e = 0;
		foreach($a as $c){
			if($d == "content"){
				$b .= '
                <div class="col">
                    <div class="card shadow-0" >
                		<img class="bd-placeholder-img card-img-top rounded-0" src="'.$c["src"].'" alt="'.$c["alt"].'"/>
                        <div class="card-header px-0">
                            <p class="card-title text-capitalize" id="user">'.$c["title"].'</p>
                            <p class="card-subtitle text-capitalize" id="user">'.$c["subtitle"].'</p>
                        </div>
                        <div class="card-body px-0 py-xl-4 py-1 py-lg-3 py-md-2 py-sm-1">
                            <p class="card-text fst-italic" id="user">"'.$c["text"].'"</p>
                        </div>
                    </div>
                </div>';
			}elseif($d == "product"){
				$b .= '<div class="col">
                	<a href="'.$c["href"].'" target="_blank"><img class="bd-placeholder-img card-img-top rounded-0" src="'.$c["src"].'" alt="'.$c["alt"].'"/></a>
                </div>';
			}elseif($d == "section2"){
				$b .= '
                    <div class="col-6">
                        <img src="'.$c["src"].'" alt="'.$c["alt"].'" class="d-block w-100">
                    </div>';
                 $e++;
			}elseif($d == "slider"){
				$b .= '
                    <div class="carousel-item '.(!$e? 'active' : '').'">
                        <img src="'.$c["src"].'" alt="'.$c["alt"].'" class="d-block w-100">
                    </div>';
                 $e++;
			}elseif($d == "client"){
				$b .= '
                    <div class="col-2 m-2">
                    	<div class="ratio ratio-4x3" style="background-image:url('.$c["src"].')"></div>
                    </div>';
                 $e++;
			}else{
				$b .= '<div class="col">
                	<img class="bd-placeholder-img card-img-top rounded-0" src="'.$c["src"].'" alt="'.$c["alt"].'"/>
                </div>';
			}
		}
		if($d == "slider"){
			return sliderImg($b,true,$d);
		}elseif($d == "client"){
			return sliderImg($b,false,$d);
		}else{
			return $b;
		}
	}
	function arrayMenu($a){
		$b = "";
		foreach($a as $c){
			$b .= '
          <li class="nav-item">
            <a class="nav-link text-white" target="_blank" href="'.$c["href"].'">'.$c["alt"].'</a>
          </li>';
		}
		return $b;
	}
	function formatPhoneNumber($s) {
		$rx = "/(62)?\D*(\d{2})?\D*(\d{4})?\D*(\d*)/x";
		preg_match($rx, (int)$s, $matches);
		if(!isset($matches[0])) return false;
		$out = "+62".$matches[2]." ".$matches[3]." ".$matches[4];
		return $out;
	}
	function keyframes($a){
		$b = 100/$a;
		$c = "";
		$e = -0.3;
		for($i=0; $i < $a; $i++) {
			$d = $i*$b;
			$f = $e -(1.4*$i);
			$c .= $d."%{
				top: ".$f."em
			}";
		}
		return $c;
	}
	function durationAnim($a){
		$b = 5.5/4;
		return $b*$a;
	}
	function listPhone($a){
		$b = "";
		foreach($a as $c){
			$b .= '<p>'.formatPhoneNumber($c['phone']).' ( '.$c['name'].' )</p>';
		}
		return $b;
	}
	function createHTML(){
        $dbJSON = json_decode(file_get_contents('db.json'),true);
    	$textList1 = implode("<br>",$dbJSON["section1"]["textList"]);
        $img1 = arrayImg($dbJSON["section1"]["img"],"slider");
        $img2 = arrayImg($dbJSON["section2"]["img"],"section2");
        $img3 = arrayImg($dbJSON["section3"]["img"],false);
        $img4 = arrayImg($dbJSON["section4"]["img"],false);
        $img5 = arrayImg($dbJSON["section5"]["img"],"product");
        $img6 = arrayImg($dbJSON["section6"]["img"],false);
        $img7 = arrayImg($dbJSON["section7"]["img"],"content");
        $img8 = arrayImg($dbJSON["section8"]["img"],"client");
        $img10 = arrayImg($dbJSON["section10"]["img"],false);
        $img11 = arrayMenu($dbJSON["menu"]["link"]);
        $keyframes = keyframes(count($dbJSON["section1"]["textList"]));
        $durationAnim = durationAnim(count($dbJSON["section1"]["textList"]));
		$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Content-Security-Policy" content="*">
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="google-site-verification" content="'.$dbJSON['footer']['google'].'" />
    <title>'.$dbJSON['footer']['title'].'</title>
    <link rel="icon" type="image/x-icon" href="'.$dbJSON['footer']['img']['0']['src'].'" />
    <meta itemprop="name" content="'.$dbJSON['footer']['title'].'">
    <meta itemprop="description" content="'.$dbJSON['footer']['description'].'">
    <meta itemprop="image" content="'.$dbJSON['section1']['img']['0']['src'].'">
    <meta property="og:url" content="https://'.$_SERVER['HTTP_HOST'].'">
    <meta property="og:type" content="website">
    <meta property="og:title" content="'.$dbJSON['footer']['title'].'">
    <meta property="og:description" content="'.$dbJSON['footer']['description'].'">
    <meta property="og:image" content="'.$dbJSON['section1']['img']['0']['src'].'">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="'.$dbJSON['footer']['title'].'">
    <meta name="twitter:description" content="'.$dbJSON['footer']['description'].'">
    <meta name="twitter:image" content="'.$dbJSON['section1']['img']['0']['src'].'">
    <link href="admin/css/bootstrap.css" rel="stylesheet">
    <link href="admin/css/mdb.css" rel="stylesheet" />
    <link href="admin/css/slider.css" rel="stylesheet"/>
    <script type="text/javascript" src="admin/js/jquery.js"></script>
    <script type="text/javascript" src="admin/js/bootstrap.js"></script>
    <script type="text/javascript" src="admin/js/mdb.js"></script>
    <script type="text/javascript" src="admin/js/slider.js"></script>
    <style>
    	section{
		    font-family:"TT Norms" !important;
		    color:#3f585a
    	}
    	.btns {
		    background-color:#3f585a !important;
    	}
    	p{
		    font-family:"arimo" !important;
    	}
    	@font-face {
		    font-family: "TT Norms";
		    src: local("TT Norms Extra Bold"), local("TT-Norms-Extra-Bold"),
	        url("admin/font/TTNorms-ExtraBold.woff2") format("woff2"),
	        url("admin/font/TTNorms-ExtraBold.woff") format("woff"),
	        url("admin/font/TTNorms-ExtraBold.ttf") format("truetype");
  		}
    	@font-face {
		    font-family: "arimo";
		    src: local("Arimo Regular"), local("Arimo Regular"),
	        url("admin/font/Arimo-Regular.ttf") format("truetype");
  		}
  		@keyframes slide{
  			'.$keyframes.'
  		}
		#client .ratio{
		  background-size: contain;
		  background-repeat: no-repeat;
		  background-position:center;
		}
        hr.border-danger{
        	border-color:#3f585a !important;
            width:0;
        }
        hr.active{
            animation: hr 1s ease 0s 1 normal forwards;
        }
        @keyframes hr{
            from{
                width: 0%;
            }
            to{
                width: 100%;
            }
        }
        .whatsapp{
            width:50px;
            height:50px;
            padding: 10px;
            color:#fff;
            background-color:#4FCE5D;
        }
        .whatsapp span{
            position: relative;
            display:block;
            background-image:url(admin/css/whatsapp.svg);
            background-repeat: no-repeat;
            background-position: center;
            width:100%;
            height:100%;
        }
        footer,header{
        	background-color:#3f585a;
        	color:#fff
        }
        header *{
            background-color:#3f585a;
            color:#fff
        }
        .navbar-toggler-icon{
            background-image:url(admin/css/bars.svg);
            width:1em;
            height:1em;
        }
        .btn-close{
            background-image:url(admin/css/xmark.svg);
            width:1em;
            height:1em;
        }
        .subtitleSize{
            font-size: 1rem;
        }
        .textSize{
            line-height:0.9em;
            font-size:1rem;
            font-weight:bold;
        }
        .textSize.roller {
            height: 1em;
            line-height: 1.4em;
            position: relative;
            overflow: hidden;
            width: 100%;
            display: flex;
            justify-content: left;
            align-items: left;
        }
        .textSize.roller #rolltext {
            position: absolute;
            top: -.3em;
            animation: slide '.$durationAnim.'s infinite;
        }
        .sectionText2{
            font-size:1.9rem;
            margin-top:0;
            margin-right:0;
        }
        #user.card-title{
            font-size:.7rem;
            line-height:.7rem;
            font-weight:bold;
        }
        #user.card-subtitle{
            font-size:.7rem;
            line-height:.7rem;
        }
        #user.card-text{
            font-size:.7rem;
            line-height:.7rem;
        }
        .navbar{
            background-size: 5rem;
            background-image:url('.$dbJSON['footer']['img'][1]['src'].');
            background-position: center;
            background-repeat: no-repeat;
        }
        @media (min-width:480px) {
            .titleSize{
                font-size: 2.5rem;
                line-height:1em;
                text-transform: uppercase;
                font-weight:bold;
            }
            .subtitleSize{
                font-size: 1rem;
            }
            .textSize{
                line-height:0.9em;
                font-size:1.5rem;
                font-weight:bold;
            }
            #user.card-title{
                font-size:.7rem;
                line-height:.7rem;
            }
            #user.card-subtitle{
                font-size:.7rem;
                line-height:.7rem;
            }
            #user.card-text{
                font-size:.7rem;
                line-height:.7rem;
            }
            .navbar{
                background-size: 6rem;
            }
        }
        @media (min-width:576px) {
            .titleSize{
                font-size: 3rem;
                line-height:1em;
                text-transform: uppercase;
                font-weight:bold;
            }
            .subtitleSize{
                font-size: 1.4rem;
            }
            .textSize{
                line-height:0.9em;
                font-size:2rem;
                font-weight:bold;
            }
            #user.card-title{
                font-size:.8rem;
                line-height:.8rem;
            }
            #user.card-subtitle{
                font-size:.8rem;
                line-height:.8rem;
            }
            #user.card-text{
                font-size:.73rem;
                line-height:.73rem;
            }
            .navbar{
                background-size: 7rem;
            }
        }
        @media (min-width:768px) {
            .titleSize{
                font-size: 4rem;
                line-height:1em;
                text-transform: uppercase;
                font-weight:bold;
            }
            .subtitleSize{
                font-size: 1.6rem;
            }
            .textSize{
                line-height:0.9em;
                font-size:2.5rem;
                font-weight:bold;
            }
            .sectionText{
                font-size:1.3rem;
                margin-top:0;
                margin-right:0;
            }
            .sectionText2{
                font-size:1.9rem;
                margin-top:0;
                margin-right:0;
            }
            #user.card-title{
                font-size:.9rem;
                line-height:.9rem;
            }
            #user.card-subtitle{
                font-size:.85rem;
                line-height:.85rem;
            }
            #user.card-text{
                font-size:.75rem;
                line-height:.75rem;
            }
            .navbar{
                background-size: 8rem;
            }
        }
        @media (min-width:992px) {
            .titleSize{
                font-size: 5rem;
                line-height:1em;
                text-transform: uppercase;
                font-weight:bold;
            }
            .subtitleSize{
                font-size: 1.8rem;
            }
            .textSize{
                line-height:0.9em;
                font-size:3rem;
                font-weight:bold;
            }
            .sectionText{
                font-size:1.4rem;
                margin-top:-150px;
                margin-right:-75px;
            }
            .sectionText2{
                font-size: 2.2rem;
                margin-left: 75px;
                margin-top: 75px;
            }
            #user.card-title{
                font-size:.1rem;
                line-height:.1rem;
            }
            #user.card-subtitle{
                font-size:.9rem;
                line-height:.9rem;
            }
            #user.card-text{
                font-size:.8rem;
                line-height:.8rem;
            }
            .navbar{
                background-size: 9rem;
            }
        }
        @media (min-width:1200px) {
            .titleSize{
                font-size: 6rem;
                line-height:1em;
                text-transform: uppercase;
                font-weight:bold;
            }
            .subtitleSize{
                font-size: 2rem;
            }
            .textSize{
                line-height:0.9em;
                font-size:4rem;
                font-weight:bold;
            }
            .sectionText{
                font-size:1.5rem;
                margin-top:-200px;
                margin-right:-100px;
            }
            .sectionText2{
                font-size: 2.5rem;
                margin-left: 100px;
                margin-top: 100px;
            }
            #user.card-title{
                font-size:1.25rem;
                line-height:1.25rem;
                font-weight:bold;
            }
            #user.card-subtitle{
                font-size:1rem;
                line-height:1rem;
            }
            #user.card-text{
                font-size:.8rem;
                line-height:.8rem;
            }
            .navbar{
                background-size: 10rem;
            }
        }
        @media (min-width:1400px) {
        }
        a.navbar-brand{
        	font-size:1rem
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar fixed-top">
          <div class="container px-3 bg-transparent">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand text-white text-uppercase d-none d-md-block" href="#">PT PRESISI BUSANA INDONESIA</a>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">'.$dbJSON['footer']['title'].'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">'.$img11.'</ul>
              </div>
            </div>
          </div>
        </nav>
    </header>
    <section class="mt-5">
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-6 p-0">
                    <h1 class="titleSize" style="line-height:1em">KONVEKSI<br>'.$dbJSON['section1']['title'].'</h1>
                    <p class="my-3 text-uppercase subtitleSize" >Fitting Indonesia</p>
                </div>
                <div class="col-6 p-0">
                    <div class="col-12 m-0">
                        <p class="text-end w-100" style="line-height:1em">20<br>23</p>
                    </div>
                    <div class="col-12 p-2">
                        <h1 class="textSize text-uppercase">Produksi</h1>
                        <h1 class="textSize roller">
                            <span id="rolltext">'.$textList1.'<span>
                        </h1>
                    </div>
                </div>
                <div class="col-12 px-0">'.$img1.'
                    <hr class="border border-danger border-2 mt-3">
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-xs-12 px-0 mx-auto">
                    <h3 class="text-center text-uppercase fw-bold">KEUNTUNGAN KAMU PRODUKSI DI FITTING INDONESIA</h3>
                </div>
            </div>
            <div class="row row-cols-5 g-3 col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 mx-auto d-flex justify-content-center">'.$img3.'</div>
            <div class="col-4 mx-auto">
	            <hr class="border border-danger border-2 mt-3">
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-12 px-0">
                    <h3 class="text-uppercase fw-bold">CUSTOM '.$dbJSON['section2']['title'].' KAMU ? BIKIN DI FITTING INDONESIA AJA !</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 my-auto">
                    <h3 class="mb-5">Konveksi '.$dbJSON['section2']['subtitle'].' yang siap terima orderan Kamu dan Kami kirim ke Seluruh Indonesia.</h3>
                    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-xs-12 mb-5">
                        <p>Yuk ngonbrol dengan super Fitteam kami untuk realisasikan Produk Custom Kemeja sesuai idamanmu. Percayakan pemilihan bahan dan kualitas produk pada kami.</p>
                    	<a href="'.$dbJSON['section2']['button']['href'].'" class="btn btn-primary btn-rounded w-100 btns">'.$dbJSON['section2']['button']['title'].'</a>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="ratio ratio-4x3">
                        <div class="row mx-auto">'.$img2.'</div>
                    </div>
                    <hr class="border border-danger border-2">
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-12 col-xl-6 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0">
                    <h3 class="text-uppercase fw-bold">4 LANGKAH MUDAH PRODUKSI '.$dbJSON['section4']['title'].' DI FITTING INDONESIA</h3>
                </div>
            </div>
            <div class="row row-cols-4 g-3">'.$img4.'</div>
            <div class="row p-3">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0">
                    <strong class="">Fitting Indonesia, Specially Fitted For You!</strong>
                    <p> Kami, Fitting Indonesia mendukung kebutuhan '.$dbJSON['section4']['subtitle'].' sesuai keinginan, Fittmate. Kami adalah sebuah perusahaan yang bergerak di bidang '.$dbJSON['section4']['text1'].' dan produksi pakaian custom. Dengan senang hati, kami akan membantu menyediakan kebutuhan '.$dbJSON['section4']['text2'].' kamu.</p>
                    <hr class="border border-danger border-2 mt-3">
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-xl-10 col-lg-10 col-md-12 col-sm-12 col-xs-12 px-0 mx-auto">
                    <h3 class="text-center text-uppercase fw-bold">KONVEKSI YANG BISA CUSTOMIZE '.$dbJSON['section5']['title'].' SESUAI KEINGINANMU !</h3>
                </div>
            </div>
            <div class="row row-cols-5 g-3">'.$img5.'</div>
            <hr class="border border-danger border-2 mt-3">
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="container col-12">
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 h-100 p-xl-2 p-lg-1 p-md-0">
                        <img data-tamplate="intro.html" id="img" data-src="poster" class="img-fluid rounded-lg shadow-none" src="'.$dbJSON['section6']['img'][0]['src'].'" alt="'.$dbJSON['section6']['img'][0]['alt'].'">
                    </div>
                    <div class="col-xl-9 col-lg-6 col-md-12 col-sm-12 px-2 py-xl-5 py-lg-4 py-md-3 py-sm-2">
                        <h3 class="text-uppercase fw-bold sectionText2">RAGU AKAN KUALITAS PRODUKSI ?</h3>
                    </div>
                    <div class="col-xl-5 col-lg-6 col-md-12 col-sm-12 h-100 p-xl-2 p-lg-1 p-md-0">
                        <img data-tamplate="intro.html" id="img" data-src="poster" class="img-fluid rounded-lg shadow-none"  src="'.$dbJSON['section6']['img'][1]['src'].'" alt="'.$dbJSON['section6']['img'][1]['alt'].'">
                    </div>
                    <div class="col-xl-7 col-lg-6 col-md-12 col-sm-12 mb-auto p-xl-5 p-lg-3 p-md-0 text-left">
                    	<div class="col-xl-7 col-lg-6 col-md-12 col-sm-12">
	                        <p class="p-2 sectionText fst-italic">Jangan khawatir, kami kirimkan sample bahan dan produk sebagai cerminan kualitas produksi kami. Dapatkan jaminan kesesuaian harga dengan pemilihan bahan dan kualitas jahitan baju custom kamu dari kami, Fitting Indonesia.</p>
	                        <a href="'.$dbJSON['section6']['button']['href'].'" class="btn btn-primary btn-rounded btns w-100">'.$dbJSON['section6']['button']['title'].'</a>
                    	</div>
                    </div>
                    <div class=" col-xl-5 col-lg-6 col-md-12 col-sm-12">
	                    <hr class="border border-danger border-2mt-3">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0 mx-auto">
                    <h3 class="text-center text-uppercase fw-bold">Apa kata fittmate</h3>
                </div>
            </div>
            <div class="row row-cols-3 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">'.$img7.'</div>
        </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0 mx-auto">
                    <hr class="border border-danger border-2 mb-3">
                    <h3 class="text-center text-uppercase fw-bold">CLIENT YANG TELAH BEKERJASAMA</h3>
                </div>
            </div>
            <div class="row">'.$img8.'</div>
            <div class="row p-3">
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0">
                    <p class="">Produksi '.$dbJSON['section9']['title'].' sesuai keinginan kamu dan percayakan kualitas terbaik pada kami. Diproduksi dengan kain yang berkualitas, premium, dan sesuai dengan kebutuhan fashion kamu sehingga nyaman digunakan dan melengkapi gaya fashion Fittmate. Ciptakan gaya yang simple dan stylish dengan beragam '.$dbJSON['section9']['subtitle'].' produksi Fitting Indonesia. Tersedia dalam berbagai macam ukuran dan corak warna yang sesuai dengan kebutuhan gaya kamu.</p>
                    <hr class="border border-danger border-2">
                    <h1 class="fst-italic">"'.$dbJSON['section9']['text'].'"</h1>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
            	<div class="col-12">
	                <hr class="border border-danger border-2 mb-3">
            	</div>
                <div class="col-xl-8 col-lg-8 col-md-10 col-sm-12 col-xs-12 px-0 mx-auto">
                    <h3 class="text-center text-uppercase fw-bold">PROSES PRODUKSI '.$dbJSON['section10']['title'].' FITTING INDONESIA</h3>
                </div>
            </div>
            <div class="row row-cols-5 mb-5">'.$img10.'</div>
            <div class="row p-3">
            	<div class="col-xl-6 col-lg-6 col-md-8 col-sm-12 col-xs-12 mx-auto">
            		<a href="'.$dbJSON['section10']['button']['href'].'" class="btn btn-primary btn-rounded btns w-100">'.$dbJSON['section10']['button']['title'].'</a>
            	</div>
            </div>
        </div>
    </section>
    <footer class="text-center text-lg-start">
        <div class="container py-xl-5 py-lg-4 py-md-3 py-sm-2">
            <div class="row p-3">
                <div class="col-6 col-md-1 col-lg-1 col-xl-1 mx-auto mb-4 p-xl-0 p-lg-0 p-md-5">
                    <div class="ratio ratio-1x1">
                        <img src="'.$dbJSON['footer']['img'][2]['src'].'" alt="">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">'.$dbJSON['footer']['title'].'</h6>
                    <p>'.$dbJSON['footer']['description'].'</p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Address</h6>
                    <p>'.$dbJSON['footer']['address'].'</p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-md-0 mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                    <p>'.$dbJSON['footer']['email'].'</p>'.listPhone($dbJSON['footer']['phone']).'
                </div>
            </div>
        </div>
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.025);">
            © 2023 Copyright: <a class="text-danger fw-bold" href="https://smooth.my.id">Smooth</a>
        </div>
        <div class="fixed-bottom my-5 mx-3 text-end">
            <a href="https://wa.me/+62'.$dbJSON['footer']['whatsapp'].'" class="btn btn-primary btn-rounded whatsapp"><span></span></a>
        </div>
    </footer>
    <script>
        slide = {
            slidesToShow: 6,
            infinite: true,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            dots: false,
            pauseOnHover: false,
        }
        $("#client").slick(slide)
        $(window).on("scroll",() => {
            for(a of document.querySelectorAll("hr")){
                b = window.innerHeight
                c = a.getBoundingClientRect().top
                d = 150
                if(c < b - d){
                    a.classList.add("active")
                }else{
                    a.classList.remove("active")
                }
            }
        })
    </script>
</body>

</html>';
		if(file_put_contents("../index.html", $html)){
			if(isset($_GET["delete"])){
				echo $_POST['id'];
			}else{
				echo 1;
			}
		}else{
			echo 0;
		}
	}
?>
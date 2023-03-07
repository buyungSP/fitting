<?php
class Gallery extends Login{
    public function __construct(){
        $this->page = new Page();
        $this->gallery = $_GET['gallery'];
    }
    private function getImgArray($a,$b){
        $display = match ($b) {
            'section1','section2','section4','section6','footer' => "hidden",
            default => null,
        };
        $c = "";
        foreach ($a as $key => $value) {
            $alt = match($b){
                "section7" => $value["title"],
                default => $value["alt"],
            };
            $e = basename($value['src'])."?".time();
            $c .= '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 mt-2" id="img'.$key.'">
                        <div class="card" id="setting">
                            <img class="card-img-top" src="'.$value['src'].'" onload="imgSize(this)"/>
                            <div class="card-body">
                                <h4 class="card-title">'.$alt.'</h4>
                                <a class="btn btn-success buttonMenu" href="?img&id='.$key.'&section='.$b.'"><span class="fa fa-edit"></span></a>
                                <button class="btn btn-danger buttonMenu '.$display.'" data-button="delete" data-bs-toggle="modal" data-bs-target="#delete"  data-section="'.$b.'" data-id="'.$key.'"><span class="fa fa-remove"></span></button>
                            </div>
                        </div>
                    </div>';
        }
        $c .= '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 mt-2 '.$display.'">
                    <div class="card" id="setting">
                            <a class="btn btn-primary w-100 btn-lg" href="?img&section='.$b.'">new img <span class="fa fa-plus"></span></a>
                    </div>
                </div>';
        return $c;
    }
    public function __destruct(){
        $listArray = $this->getImgArray($this->page->listImg($this->gallery),$this->gallery);
        $header = $this->page->sidebar();
        $selectSection = $this->page->dropdown();
        $tamplate =  <<<HTML
            <div class="container-fluid">
                <div class="row">
                    $header
                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-2">
                        <div class="container">
                            <ul class="breadcrumb no-border no-radius b-b b-light pull-in">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">$this->gallery</li>
                            </ul>
                        </div>
                        <div class="row card-deck text-capitalize" id="table">
                            <div class="col-12 mt-2">
                                <div class="card" id="setting">
                                    <div class="card-header">
                                        <div class="btn-toolbar justify-content-between">
                                            <div class="input-group-text border-0 bg-transparent">
                                                <div class="input-group-text border-0 bg-transparent">gallery</div>
                                            </div>
                                            <div class="input-group-text border-0 bg-transparent">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Section</button>
                                                    <ul class="dropdown-menu">
                                                        $selectSection
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            $listArray
                        </div>
                        <div class="modal fade" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="text-center">
                                            <p>Delete this file!</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="send" data-button="send" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">delete</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="text-center" id="loading"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal" disabled>Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <script type="text/javascript">
                imgSize = a =>{
                    b = document.createElement("div")
                    c = document.createElement("strong")
                    b.className = "card-header"
                    c.className = "card-text"
                    a.parentElement.appendChild(b)
                    b.appendChild(c)
                    c.innerHTML = "resolution file media: " + a.naturalWidth + "x" + a.naturalHeight
                }
            </script>
        HTML;
        print $tamplate;
    }
}
?>
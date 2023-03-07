<?php
class Img extends Login{
    public function __construct(){
        $this->page = new Page();
        $this->section = $_GET['section'];
        $this->id = $_GET['id'];
        $this->new = !isset($this->id)? false : true;
        $this->height = match($this->section){
            "section1","section2" => 640,
            "section6" => !(int)$this->id? 640 : 1024,
            "footer" => !(int)$this->id? 15 : 300,
            default => 300
        };
    }
    private function form(){
        return match ($this->section) {
            "section7" => '<div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" placeholder="Title" name="title" value="'.$this->title.'">
            </div><div class="form-group">
                <label>Subtitle</label>
                <input type="text" class="form-control" placeholder="Subtitle" name="subtitle" value="'.$this->subtitle.'">
            </div><div class="form-group">
                <label>Text</label>
                <input type="text" class="form-control" placeholder="Text" name="text" value="'.$this->text.'">
            </div>',
            "section5" => '<div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" placeholder="Title" name="alt" value="'.$this->alt.'">
            </div><div class="form-group">
                <label>redirect</label>
                <input type="text" class="form-control" placeholder="https://" name="href" value="'.$this->href.'">
            </div>',
            default => '<div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" placeholder="Title" name="alt" value="'.$this->alt.'">
            </div>',
        };
    }
    public function __destruct(){
        $section = $this->section;
        $header = $this->page->sidebar();
        if($this->new){
            $getData = "edit";
            $imgData = $this->page->listImg($section)[$this->id];
            $this->title = $imgData["title"];
            $this->subtitle = $imgData["subtitle"];
            $this->text = $imgData["text"];
            $this->alt = $imgData["alt"];
            $img = "<img class='col-sm-12' onload='imgSize(this)' src='".$imgData["src"]."'/>";
            $name = basename($imgData["src"]);
            $id = "data-id=".$this->id;
        }else{
            $getData = "new";
        }
        $form = $this->form();
        $height = $this->height."p / tinggi ".$this->height." pixel";
        $tamplate =  <<<HTML
            <div class="container-fluid">
                <div class="row">
                    $header
                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-2">
                        <div class="container">
                            <ul class="breadcrumb no-border no-radius b-b b-light pull-in">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="?gallery=$section">$section</a></li>
                                <li class="breadcrumb-item active">$getData</li>
                            </ul>
                        </div>
                        <form role="form" class="row card-deck text-capitalize" name="img" method="POST" data-file="$name" data-section="$section" $id>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="dropfile visible-lg">
                                            $img
                                            <small>Drag and Drop file here</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        $form
                                        <div class="form-group mt-3">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <p class="card-text text-danger">file akan tersimpan dengan ukuran <strong>$height</strong> berdasarkan ratio file yang di input.</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="text-center" id="loading"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-button="back">back</button>
                                        <button type="button" class="btn btn-secondary" id="closeModal" data-bs-dismiss="modal" disabled>Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </main>
                </div>
            </div>
            <script type="text/javascript" src="js/bs.js"></script>
            <script>
            </script>
        HTML;
        print $tamplate;
    }
}
?>
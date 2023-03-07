<?php
class Edit extends Login{
    public function __construct(){
        $this->page = new Page();
        $this->section = $_GET['section'];
        $this->edit = $_GET['edit'];
        $this->id = $_GET['id'];
    }
    private function form(){
        return match($this->edit){
            "textList" => '<div class="form-group">
                            <label>List</label>
                            <input type="text" class="form-control" placeholder="List" name="textList" value="'.$this->page->listText($this->section).'">
                        </div>',
            "button" => '<div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" placeholder="Title" name="title" value="'.$this->page->getButton($this->section)['title'].'">
                        </div>
                        <div class="form-group">
                            <label>redirect</label>
                            <input type="text" class="form-control" placeholder="https://" name="href" value="'.$this->page->getButton($this->section)['href'].'">
                        </div>',
            "phone" => '<div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" placeholder="Name" name="name" value="'.$this->page->getPhone($this->section)[$this->id]['name'].'">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" placeholder="89xxxx" name="phone" value="'.$this->page->getPhone($this->section)[$this->id]['phone'].'">
                        </div>',
            "link" => '<div class="form-group">
                            <label>Tile</label>
                            <input type="text" class="form-control" placeholder="Title" name="alt" value="'.$this->page->getMenu($this->section)[$this->id]['alt'].'">
                        </div>
                        <div class="form-group">
                            <label>redirect</label>
                            <input type="text" class="form-control" placeholder="https://" name="href" value="'.$this->page->getMenu($this->section)[$this->id]['href'].'">
                        </div>',
            default => '<div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" placeholder="Title" name="alt" value="'.$this->page->text($this->section,$this->edit).'">
                </div>',
        };
    }
    public function __destruct(){
        $header = $this->page->sidebar();
        $section = $this->section;
        $edit = $this->edit;
        if(isset($this->id)){
            $id = "data-id='".$this->id."'";
        }
        $form = $this->form();
        $tamplate =  <<<HTML
            <div class="container-fluid">
                <div class="row">
                    $header
                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-2">
                        <div class="container">
                            <ul class="breadcrumb no-border no-radius b-b b-light pull-in">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="?#$section">$section</a></li>
                                <li class="breadcrumb-item active">edit $edit</li>
                            </ul>
                        </div>
                        <form role="form" class="row card-deck text-capitalize" name="text" method="POST" data-file="$edit" data-section="$section" $id>
                            <div class="card">
                                <div class="card-body">
                                    $form
                                    <div class="form-group mt-3">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Submit</button>
                                        </div>
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
            <script type="text/javascript" src="js/bs.js?v=1"></script>
        HTML;
        print $tamplate;
    }
}
?>
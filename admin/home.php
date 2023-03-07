<?php
class Home extends Login{
    public function __construct(){
        $this->page = new Page();
    }
    public function __destruct(){
        $show = $this->page->home();
        $header = $this->page->sidebar();
        $tamplate =  <<<HTML
            <div class="container-fluid">
                <div class="row">
                    $header
                    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 py-2">
                        <div class="container">
                            <ul class="breadcrumb no-border no-radius b-b b-light pull-in">
                                <li class="breadcrumb-item active">Welcome, <strong>Admin</strong></li>
                            </ul>
                        </div>
                        <div class="row card-deck text-capitalize" data-masonry='{"percentPosition": true }'>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-2">
                                <div class="card" id="setting">
                                    <div class="card-header">
                                        <h4 class="card-title">Profile</h4>
                                    </div>
                                    <div class="card-body">
                                        <strong class="card-title">Username</strong>
                                        <p class="card-subtitle mb-2">$status->username</p>
                                        <p class="card-subtitle mb-2"><a class="text-primary" href=''>edit profile <span class="fa fa-arrow-right"></span></a></p>
                                    </div>
                                </div>
                            </div>
                            $show
                        </div>
                    </main>
                </div>
            </div>
        HTML;
        print $tamplate;
    }
}
?>
<?php include 'includes/header.php';?>
    <h1 class="mb-4"><span class="badge badge-pill badge-secondary">Simulation</span></h1>
<?php
    require('./utils/simulate.utils.php');
    if($_POST) {
        switch ($_POST['action']) {
            case 'daily-checkin':
                massCheckIn('today');
                break;
            case 'past-checkin':
                massCheckIn('past');
                break;
            case 'daily-checkout':
                massCheckOut('today');
                break;
            case 'past-checkout':
                massCheckOut('past');
                break;
            case 'book-soon':
                massBook('soon');
                break;
            case 'book-future':
                massBook('future');
                break;
        }

    }
?>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10 jumbotron">
            <div class="row">
                <div class="btn-group col-md-4 d-flex justify-content-center mb-3">
                    <button type="button" class="btn btn-lg btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Mass Check In
                    </button>
                    <div class="dropdown-menu">
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="daily-checkin">
                            <button type="submit" class="dropdown-item">Today</button>
                        </form>
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="past-checkin">
                            <button type="submit" class="dropdown-item">Past</button>
                        </form>
                    </div>
                </div>

                <div class="btn-group col-md-4 d-flex justify-content-center mb-3">
                    <button type="button" class="btn btn-lg btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Mass Check Out
                    </button>
                    <div class="dropdown-menu">
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="daily-checkout">
                            <button type="submit" class="dropdown-item">Today</button>
                        </form>
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="past-checkout">
                            <button type="submit" class="dropdown-item">Past</button>
                        </form>
                    </div>
                </div>

                <div class="btn-group col-md-4 d-flex justify-content-center mb-3">
                    <button type="button" class="btn btn-lg btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Mass Book
                    </button>
                    <div class="dropdown-menu">
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="book-soon">
                            <button type="submit" class="dropdown-item">Soon</button>
                        </form>
                        <form method="POST" action="<?php $_SERVER['PHP_SELF'];?>">
                            <input type="hidden" name="action" value="book-future">
                            <button type="submit" class="dropdown-item">Future</button>
                        </form>                    
                    </div>
                </div>

            </div>

        </div>
        <div class="col-1"></div>
    </div>




<?php include 'includes/footer.php';?>

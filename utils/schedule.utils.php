<?php
    require('../config/db.php');

    $q = $_REQUEST['q'];
    $schedule = "<div class='row d-flex justify-content-around mb-3'>";

    
    if ($q != '') {
        $rooms = [];
        $query = "
        SELECT r.Number, o.Status, c.Fname, c.Lname, o.ResID, re.NumGuests, re.CheckinTime, re.CheckoutTime, re.StartDate, re.EndDate
        FROM rooms r 
        LEFT JOIN occupancies o 
        ON r.Number = o.RoomNo AND o.Date = '$q' 
        LEFT JOIN reservations re 
        ON re.ID = o.ResID
        LEFT JOIN customers c
        ON c.ID = re.CustomerID
        ORDER BY r.Number;
        ";

        $result = mysqli_query($conn, $query);

        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        mysqli_free_result($result);

        foreach ($rooms as $index=>$room) {
            $number = $room['Number'];
            $fname = $room['Fname'];
            $lname = $room['Lname'];
            $color = $room['Status'] === 'in progress' ? 'danger' : ($room['Status'] == 'scheduled' ? 'warning' : ($room['Status'] == 'finished' ? 'secondary':'success'));
            
            $modalTrigger = $fname && $lname ? "data-toggle='modal' data-target='#detailsModal$number'" : "";

            $html = "<button type='button' class='btn btn-$color' $modalTrigger>$number</button>";
            $schedule .= $html;

            if ($fname && $lname) {
                $resID = $room['ResID'];
                $numGuests = $room['NumGuests'];
                $checkedInAt = $room['CheckinTime'];
                $checkedOutAt = $room['CheckoutTime'];
                $startDate = $room['StartDate'];
                $endDate = $room['EndDate'];
                $modal = "
                <div class='modal fade' id='detailsModal$number' tabindex='-1' role='dialog' aria-hidden='true'>
                    <div class='modal-dialog modal-sm' role='document'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title'>Room $number<i class='fas fa-circle text-$color text-xs ml-2 mb-1'></i></h5>
                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <p><b>Customer: </b>$fname $lname</p>
                                <p><b>Reservation ID: </b>$resID</p>
                                <p><b>Number of guests: </b>$numGuests</p>
                                <p><b>Start date: </b>$startDate</p>
                                <p><b>End date: </b>$endDate</p>
                                <p><b>Checked in at: </b><span class='text-xs'>$checkedInAt</span></p>
                                <p><b>Checked out at: </b><span class='text-xs'>$checkedOutAt</span></p>
                            </div>
                        </div>
                    </div>
                </div>                
                ";

                $schedule.= $modal;
            }
            

            if ($index % 6 == 5) {
                $schedule .= '</div><hr>';
                $schedule .= $index != count($rooms) - 1 ?  "<div class='row d-flex justify-content-around mb-3'>" : "";
            }
            

        }
        echo json_encode($schedule);

    }
?>
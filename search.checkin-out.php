<?php
    require('utils/checkin-out.utils.php');


    $reservations = [];
    if (isset($_POST['submit'])) {
        $firstName = ucwords(htmlentities($_POST['first-name']));
        $lastName = ucwords(htmlentities($_POST['last-name']));
        $reservations = searchRes( $firstName, $lastName);
        
        session_start();
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
    }
    
?>
<?php include 'includes/header.php';?>
    <div class="container-fluid">
        <h1 class="mb-4"><span class="badge badge-pill badge-secondary">Check In/Out</span></h1>
        <div class="card my-4">
            <div class="card-header lead">
                <i class="fas fa-search mr-1"></i>Search customer
                
            </div>
            <div class="card-body">
                <div class="checkin-search-box">
                    <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                        <input type="text" name="first-name" class="form-control mb-2 mr-sm-2" placeholder="First Name" value="<?php echo $_POST ? $firstName : '' ?>" onkeyup="suggestNames(event, this.value)">
                        <input type="text" name="last-name" class="form-control mb-2 mr-sm-2" placeholder="Last Name" value="<?php echo $_POST ? $lastName : '' ?>" onkeyup="suggestNames(event, this.value)">
                        <input type="submit" name="submit" class="btn btn-primary form-control mb-2 mr-sm-2" value="Search">
                    </form>
                    <div class="checkin-suggestion-box">
                        <ul id="suggestedNames" class="list-group"></ul>
                    </div>
                </div>
            </div>
            
        </div>

        <?php if (isset($_POST['submit'])): ?>
            <div class="card mb-4">
                <div class="card-header lead">
                    <i class="fas fa-table mr-1"></i>
                    <?php echo $firstName.' '.$lastName?>'s reservations: 
                </div>
                <div class="card-body table-responsive">
                    <?php if (count($reservations) > 0): ?>
                    <table class="table table-hover table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($reservations[0]) as $fieldName) : ?>
                                    <th scope="col"><?php echo $fieldName ?></th>
                                <?php endforeach; ?>
                                <th class="action"><span class="d-none"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reservations as $res) : ?>
                            <tr>
                                <?php foreach ($res as $field=>$val) : ?>
                                    <td><?php echo $val ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <form method="POST" action="./process.checkin-out.php">
                                        <input type="hidden" name="action" value="<?php echo $res['Status'] == 'confirmed' ? 'check-in' : 'check-out' ?>">
                                        <input type="hidden" name="resID" value="<?php echo $res['ID']?>">
                                        <input type="hidden" name="roomNo" value="<?php echo $res['RoomNo']?>">
                                        <input type="submit" class="btn btn-sm btn-<?php echo $res['Status'] == 'confirmed' ? 'success' : 'warning' ?>" 
                                        value="<?php echo $res['Status'] == 'confirmed' ? 'Check in' : 'Check out' ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php else: ?>
                    <div class="alert alert-warning">
                        No reservation for this customer. 
                        <a href="./avail.res.php">Create one</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        document.addEventListener("keyup", e => {
            if (e.key === 'Escape') {
                document.getElementById('suggestedNames').innerHTML = '';
            }
        })   

        function suggestNames(event, val) {
            document.getElementById('suggestedNames').innerHTML = '';
            if (event.key !== 'Escape' && val.length > 2) fetchNames(val);
        }

        function fetchNames(val) {
            fetch(`utils/name-suggestion.utils.php?q=${val}`, {method: 'GET'})
            .then(response => response.json())
            .then(data => {
                viewNames(data)
            })
            .catch(e => console.log(e))
        }

        function viewNames(names) {
            const dataViewer = document.getElementById('suggestedNames');

            for (let i = 0; i < names.length; i++) {
                const li = document.createElement("li");
                li.innerHTML = `${names[i]['Fname']} ${names[i]['Lname']}`;
                li.id = i;
                li.classList.add('suggested-item')
                li.classList.add('list-group-item');
                li.classList.add('list-group-item-action');
                li.addEventListener('click', event => {
                    const customer = names[event.target.id];
                    document.querySelector('input[name="first-name"]').value = customer['Fname'];
                    document.querySelector('input[name="last-name"]').value = customer['Lname'];
                    dataViewer.innerHTML = '';
                })
                dataViewer.appendChild(li);
            }
        }
    </script>
<?php include 'includes/footer.php';?>
